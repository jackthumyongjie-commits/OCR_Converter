<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

const MAX_BYTES = 10 * 1024 * 1024;
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp'];
const ALLOWED_MIMES = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/bmp',
    'image/x-ms-bmp',
    'image/tiff',
    'image/webp',
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.', 405);
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    respond(false, 'API is not configured. Copy config.example.php to config.php.', 500);
}

/** @var array{api_key?:string,api_url?:string} $config */
$config = require $configPath;
$apiKey = trim((string) ($config['api_key'] ?? ''));
$apiUrl = trim((string) ($config['api_url'] ?? ''));

if ($apiKey === '' || $apiUrl === '') {
    respond(false, 'API key or URL is missing in config.php.', 500);
}

if (!isset($_FILES['image']) || !is_array($_FILES['image'])) {
    respond(false, 'No image uploaded.', 400);
}

$file = $_FILES['image'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    respond(false, 'Image upload failed.', 400);
}

if (($file['size'] ?? 0) > MAX_BYTES) {
    respond(false, 'File is larger than 10 MB.', 400);
}

$originalName = (string) ($file['name'] ?? 'image');
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if ($extension === '' || !in_array($extension, ALLOWED_EXTENSIONS, true)) {
    respond(false, 'Unsupported file type.', 400);
}

$tmpPath = (string) ($file['tmp_name'] ?? '');
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
    respond(false, 'Invalid upload.', 400);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($tmpPath) ?: '';
if ($mimeType === '' || !in_array($mimeType, ALLOWED_MIMES, true)) {
    respond(false, 'Unsupported file type.', 400);
}

$ocrLang = isset($_POST['ocr_lang']) ? (string) $_POST['ocr_lang'] : 'eng';
if (!in_array($ocrLang, ['eng', 'chi_sim', 'eng+chi_sim'], true)) {
    $ocrLang = 'eng';
}

$imageData = file_get_contents($tmpPath);
if ($imageData === false) {
    respond(false, 'Could not read uploaded image.', 500);
}

$payload = [
    'contents' => [[
        'parts' => [
            ['text' => buildPrompt($ocrLang)],
            [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => base64_encode($imageData),
                ],
            ],
        ],
    ]],
    'generationConfig' => [
        'temperature' => 0,
        'topP' => 0.1,
    ],
];

$requestUrl = $apiUrl . (str_contains($apiUrl, '?') ? '&' : '?') . 'key=' . rawurlencode($apiKey);
$responseBody = callGeminiApi($requestUrl, $payload);

if ($responseBody === null) {
    respond(false, 'Could not connect to OCR API.', 502);
}

/** @var array<string,mixed>|null $decoded */
$decoded = json_decode($responseBody, true);
if (!is_array($decoded)) {
    respond(false, 'Invalid response from OCR API.', 502);
}

if (isset($decoded['error']) && is_array($decoded['error'])) {
    $message = trim((string) ($decoded['error']['message'] ?? 'OCR API error.'));
    respond(false, $message !== '' ? $message : 'OCR API error.', 502);
}

$text = extractGeminiText($decoded);
$text = normalizeText($text, $ocrLang);

respond(true, '', 200, ['text' => $text]);

function buildPrompt(string $ocrLang): string
{
    $languageRule = match ($ocrLang) {
        'chi_sim' => 'Focus on Simplified Chinese text only. Ignore English unless it clearly appears in the image.',
        'eng' => 'Focus on English text only.',
        default => 'Extract both English and Simplified Chinese text when present.',
    };

    return implode("\n", [
        'You are a precise OCR engine.',
        $languageRule,
        'Extract ONLY text that is clearly visible in the image.',
        'Do NOT invent, guess, or add characters that are not in the image.',
        'Do NOT output random fragments such as "El" or "DE" unless they truly appear in the image.',
        'Preserve line breaks and paragraph structure.',
        'Return plain text only. No markdown, no explanations, no labels.',
        'If there is no readable text, return an empty string.',
    ]);
}

function callGeminiApi(string $url, array $payload): ?string
{
    if (!function_exists('curl_init')) {
        return null;
    }

    $ch = curl_init($url);
    if ($ch === false) {
        return null;
    }

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 120,
    ]);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode >= 400) {
        return is_string($response) ? $response : null;
    }

    return is_string($response) ? $response : null;
}

function extractGeminiText(array $decoded): string
{
    $parts = $decoded['candidates'][0]['content']['parts'] ?? [];
    if (!is_array($parts)) {
        return '';
    }

    $chunks = [];
    foreach ($parts as $part) {
        if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
            $chunks[] = $part['text'];
        }
    }

    return trim(implode("\n", $chunks));
}

function normalizeText(string $text, string $ocrLang): string
{
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace('/^```(?:[a-zA-Z]*)?\n?|\n?```$/u', '', trim($text)) ?? trim($text);
    $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

    if ($ocrLang === 'chi_sim' || $ocrLang === 'eng+chi_sim') {
        $text = preg_replace('/([\x{3400}-\x{9FFF}])(?:[ \t]+(?=[\x{3400}-\x{9FFF}]))+/u', '$1', $text) ?? $text;
    }

    return trim($text);
}

/**
 * @param array<string,mixed> $extra
 */
function respond(bool $success, string $error, int $status, array $extra = []): never
{
    http_response_code($status);
    echo json_encode(array_merge([
        'success' => $success,
        'error' => $success ? '' : $error,
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}
