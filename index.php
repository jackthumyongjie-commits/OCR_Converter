<?php
declare(strict_types=1);

$supportedLangs = ['en', 'zh'];
$lang = isset($_GET['lang']) && is_string($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, $supportedLangs, true)) {
    $lang = 'en';
}

$htmlLang = $lang === 'zh' ? 'zh-CN' : 'en';
$ocrDefault = $lang === 'zh' ? 'chi_sim' : 'eng';

$t = [
    'en' => [
        'title' => 'OCR Converter',
        'manual' => 'User Manual',
        'langEn' => 'English',
        'langZh' => '中文',
        'tagline' => 'Extract text from images, then copy, print, or export to Word.',
        'heroLead' => 'Drop a photo. Walk away with editable text.',
        'uploadTitle' => 'Upload Image',
        'chooseImage' => 'Choose Image',
        'dropHint' => 'Drag & drop an image here, or browse',
        'noFile' => 'No image selected',
        'ocrLanguage' => 'OCR Language',
        'ocrEng' => 'English',
        'ocrZh' => 'Simplified Chinese',
        'ocrBoth' => 'English + Simplified Chinese',
        'startOcr' => 'Start OCR',
        'clear' => 'Clear',
        'previewAlt' => 'Selected image preview',
        'previewEmpty' => 'Image preview will appear here',
        'progressStarting' => 'Starting',
        'progressUploading' => 'Uploading image',
        'progressRecognizing' => 'Recognizing text',
        'progressComplete' => 'Complete',
        'resultTitle' => 'Recognized Text',
        'resultHint' => 'You can edit the text below before copying, printing, or exporting.',
        'resultPlaceholder' => 'Recognized text will appear here.',
        'copy' => 'Copy Text',
        'print' => 'Print',
        'exportWord' => 'Export to Word',
        'footer' => 'OCR uses Gemini AI. Images are sent to the server for recognition and are not stored.',
        'noImage' => 'Please choose an image before starting OCR.',
        'unsupportedType' => 'This file type is not supported. Please use JPG, JPEG, PNG, GIF, BMP, TIFF, or WEBP.',
        'fileTooLarge' => 'The file is larger than 10 MB. Please choose a smaller image.',
        'ocrFailed' => 'OCR failed. Please check your API settings or try another image.',
        'noText' => 'No text was detected in this image.',
        'ocrSuccess' => 'Text extracted successfully. You can edit it before copying, printing, or exporting.',
        'copied' => 'Text copied to the clipboard.',
        'copyFailed' => 'Could not copy the text. Please select it and copy manually.',
        'nothingToCopy' => 'There is no text to copy.',
        'nothingToPrint' => 'There is no text to print.',
        'nothingToExport' => 'There is no text to export.',
        'exportSuccess' => 'Word document downloaded.',
        'previewUnavailable' => 'Preview is not available for this file, but OCR can still run.',
        'cleared' => 'Image and results have been cleared.',
        'printTitle' => 'OCR Result',
        'exportPrefix' => 'ocr-text',
        'processing' => 'Processing… please wait.',
    ],
    'zh' => [
        'title' => 'OCR 转换器',
        'manual' => '使用手册',
        'langEn' => 'English',
        'langZh' => '中文',
        'tagline' => '从图片中提取文字，然后复制、打印或导出为 Word 文档。',
        'heroLead' => '丢进一张图，带走可编辑的文字。',
        'uploadTitle' => '上传图片',
        'chooseImage' => '选择图片',
        'dropHint' => '将图片拖放到此处，或点击浏览',
        'noFile' => '尚未选择图片',
        'ocrLanguage' => '识别语言',
        'ocrEng' => '英语',
        'ocrZh' => '简体中文',
        'ocrBoth' => '英语 + 简体中文',
        'startOcr' => '开始识别',
        'clear' => '清除',
        'previewAlt' => '所选图片预览',
        'previewEmpty' => '图片预览将显示在这里',
        'progressStarting' => '正在开始',
        'progressUploading' => '正在上传图片',
        'progressRecognizing' => '正在识别文字',
        'progressComplete' => '完成',
        'resultTitle' => '识别结果',
        'resultHint' => '导出、复制或打印前，您可以先编辑下面的文字。',
        'resultPlaceholder' => '识别出的文字将显示在这里。',
        'copy' => '复制文字',
        'print' => '打印',
        'exportWord' => '导出为 Word',
        'footer' => 'OCR 使用 Gemini AI 识别。图片会发送到服务器进行识别，不会永久保存。',
        'noImage' => '请先选择图片，然后再开始识别。',
        'unsupportedType' => '不支持此文件类型。请使用 JPG、JPEG、PNG、GIF、BMP、TIFF 或 WEBP。',
        'fileTooLarge' => '文件大于 10 MB，请选择更小的图片。',
        'ocrFailed' => '识别失败。请检查 API 设置，或更换图片后再试。',
        'noText' => '未在此图片中检测到文字。',
        'ocrSuccess' => '文字提取成功。复制、打印或导出前，您可以先编辑。',
        'copied' => '文字已复制到剪贴板。',
        'copyFailed' => '无法复制文字。请手动选择并复制。',
        'nothingToCopy' => '没有可复制的文字。',
        'nothingToPrint' => '没有可打印的文字。',
        'nothingToExport' => '没有可导出的文字。',
        'exportSuccess' => 'Word 文档已下载。',
        'previewUnavailable' => '此文件无法预览，但仍可进行识别。',
        'cleared' => '图片和识别结果已清除。',
        'printTitle' => 'OCR 识别结果',
        'exportPrefix' => 'ocr-wenben',
        'processing' => '正在处理，请稍候…',
    ],
];

$tr = $t[$lang];

$jsI18n = [
    'noImage' => $tr['noImage'],
    'unsupportedType' => $tr['unsupportedType'],
    'fileTooLarge' => $tr['fileTooLarge'],
    'ocrFailed' => $tr['ocrFailed'],
    'noText' => $tr['noText'],
    'ocrSuccess' => $tr['ocrSuccess'],
    'copied' => $tr['copied'],
    'copyFailed' => $tr['copyFailed'],
    'nothingToCopy' => $tr['nothingToCopy'],
    'nothingToPrint' => $tr['nothingToPrint'],
    'nothingToExport' => $tr['nothingToExport'],
    'exportSuccess' => $tr['exportSuccess'],
    'previewUnavailable' => $tr['previewUnavailable'],
    'cleared' => $tr['cleared'],
    'printTitle' => $tr['printTitle'],
    'exportPrefix' => $tr['exportPrefix'],
    'processing' => $tr['processing'],
    'noFile' => $tr['noFile'],
    'previewEmpty' => $tr['previewEmpty'],
    'progressStarting' => $tr['progressStarting'],
    'progressUploading' => $tr['progressUploading'],
    'progressRecognizing' => $tr['progressRecognizing'],
    'progressComplete' => $tr['progressComplete'],
    'ocrApiUrl' => 'ocr-api.php',
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="<?= e($htmlLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($tr['tagline']) ?>">
    <title><?= e($tr['title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="bg-mesh" aria-hidden="true"></div>

    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="index.php?lang=<?= e($lang) ?>">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 36 36" width="30" height="30" focusable="false">
                        <path d="M6 8h16v20H6z" fill="currentColor" opacity="0.18"></path>
                        <path d="M12 4h16v20H12z" fill="none" stroke="currentColor" stroke-width="2.2"></path>
                        <path d="M16 12h8M16 17h8M16 22h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                </span>
                <span class="brand-text"><?= e($tr['title']) ?></span>
            </a>
            <nav class="header-nav" aria-label="<?= e($tr['title']) ?>">
                <a class="nav-link" href="manual.php?lang=<?= e($lang) ?>"><?= e($tr['manual']) ?></a>
                <span class="lang-switch" role="group" aria-label="Language">
                    <a href="index.php?lang=en" class="<?= $lang === 'en' ? 'is-active' : '' ?>" hreflang="en"><?= e($tr['langEn']) ?></a>
                    <a href="index.php?lang=zh" class="<?= $lang === 'zh' ? 'is-active' : '' ?>" hreflang="zh"><?= e($tr['langZh']) ?></a>
                </span>
            </nav>
        </div>
    </header>

    <main class="page">
        <section class="hero reveal">
            <p class="brand-kicker"><?= e($tr['title']) ?></p>
            <h1 class="hero-title"><?= e($tr['heroLead']) ?></h1>
            <p class="tagline"><?= e($tr['tagline']) ?></p>
        </section>

        <div id="status" class="status reveal" role="status" aria-live="polite" hidden></div>

        <div class="workspace reveal">
            <section class="panel panel-upload" aria-labelledby="upload-title">
                <div class="panel-head">
                    <h2 id="upload-title"><?= e($tr['uploadTitle']) ?></h2>
                    <p id="file-name" class="file-name"><?= e($tr['noFile']) ?></p>
                </div>

                <label class="dropzone" id="dropzone" for="image-input">
                    <input id="image-input" type="file" accept=".jpg,.jpeg,.png,.gif,.bmp,.tif,.tiff,.webp,image/jpeg,image/png,image/gif,image/bmp,image/tiff,image/webp" hidden>
                    <figure class="preview-box" id="preview-box">
                        <img id="preview-image" alt="<?= e($tr['previewAlt']) ?>" hidden>
                        <div class="dropzone-idle" id="dropzone-idle">
                            <span class="dropzone-icon" aria-hidden="true">
                                <svg viewBox="0 0 48 48" width="44" height="44" focusable="false">
                                    <rect x="8" y="12" width="32" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="2.2"></rect>
                                    <path d="M18 28l5-6 5 6 4-4 6 8H14z" fill="currentColor" opacity="0.2"></path>
                                    <circle cx="19" cy="20" r="2.5" fill="currentColor"></circle>
                                    <path d="M24 4v10M19 9l5-5 5 5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </span>
                            <span class="dropzone-text"><?= e($tr['dropHint']) ?></span>
                            <span class="btn btn-primary file-btn"><?= e($tr['chooseImage']) ?></span>
                        </div>
                        <figcaption id="preview-caption" class="sr-only"><?= e($tr['previewEmpty']) ?></figcaption>
                    </figure>
                </label>

                <div class="field">
                    <label for="ocr-lang"><?= e($tr['ocrLanguage']) ?></label>
                    <select id="ocr-lang">
                        <option value="eng" <?= $ocrDefault === 'eng' ? 'selected' : '' ?>><?= e($tr['ocrEng']) ?></option>
                        <option value="chi_sim" <?= $ocrDefault === 'chi_sim' ? 'selected' : '' ?>><?= e($tr['ocrZh']) ?></option>
                        <option value="eng+chi_sim"><?= e($tr['ocrBoth']) ?></option>
                    </select>
                </div>

                <div class="actions">
                    <button type="button" id="start-ocr" class="btn btn-primary"><?= e($tr['startOcr']) ?></button>
                    <button type="button" id="clear-btn" class="btn btn-ghost"><?= e($tr['clear']) ?></button>
                </div>

                <div id="progress" class="progress" hidden>
                    <div class="progress-top">
                        <span id="progress-label"><?= e($tr['progressStarting']) ?></span>
                        <span id="progress-percent">0%</span>
                    </div>
                    <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="progress-bar">
                        <div id="progress-fill" class="progress-fill"></div>
                    </div>
                </div>
            </section>

            <section class="panel panel-result" aria-labelledby="result-title">
                <div class="panel-head">
                    <h2 id="result-title"><?= e($tr['resultTitle']) ?></h2>
                    <p class="hint"><?= e($tr['resultHint']) ?></p>
                </div>
                <label class="sr-only" for="result-text"><?= e($tr['resultTitle']) ?></label>
                <textarea id="result-text" rows="14" placeholder="<?= e($tr['resultPlaceholder']) ?>"></textarea>
                <div class="actions">
                    <button type="button" id="copy-btn" class="btn btn-secondary"><?= e($tr['copy']) ?></button>
                    <button type="button" id="print-btn" class="btn btn-secondary"><?= e($tr['print']) ?></button>
                    <button type="button" id="export-btn" class="btn btn-accent"><?= e($tr['exportWord']) ?></button>
                </div>
            </section>
        </div>
    </main>

    <footer class="site-footer">
        <p><?= e($tr['footer']) ?></p>
    </footer>

    <script id="i18n-data" type="application/json"><?= json_encode($jsI18n, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?></script>
    <script src="app.js"></script>
</body>
</html>
