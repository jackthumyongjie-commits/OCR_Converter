<?php
declare(strict_types=1);

$supportedLangs = ['en', 'zh'];
$lang = isset($_GET['lang']) && is_string($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, $supportedLangs, true)) {
    $lang = 'en';
}

$htmlLang = $lang === 'zh' ? 'zh-CN' : 'en';

$t = [
    'en' => [
        'title' => 'User Manual',
        'appName' => 'OCR Converter',
        'manual' => 'User Manual',
        'langEn' => 'English',
        'langZh' => '中文',
        'home' => 'Back to Converter',
        'intro' => 'This page explains how to extract text from an image, edit the result, and copy, print, or export it.',
        's1' => '1. How to select an image',
        's1p1' => 'On the main page, click Choose Image.',
        's1p2' => 'Pick one image from your computer, phone, or tablet.',
        's1p3' => 'The file name will appear next to the button, and a preview will show below it.',
        's2' => '2. Supported image formats',
        's2p' => 'You can use these formats:',
        's3' => '3. Maximum file size',
        's3p' => 'The maximum file size is 10 MB. If the image is larger than 10 MB, the application will show an error and will not start OCR.',
        's4' => '4. How to start OCR',
        's4p1' => 'Choose the OCR language: English, Simplified Chinese, or both.',
        's4p2' => 'Click Start OCR. Do not reload the page while OCR is running.',
        's4p3' => 'A progress bar will show the current step, such as Starting, Loading OCR, Recognizing text, and Complete.',
        's4p4' => 'Recognition is powered by Gemini AI through your server.',
        's5' => '5. How to edit the recognized text',
        's5p' => 'The recognized text appears in the text box. You can change spelling, punctuation, or layout before you copy, print, or export.',
        's6' => '6. How to copy the text',
        's6p' => 'Click Copy Text. The current contents of the text box, including your edits, will be copied to the clipboard.',
        's7' => '7. How to print the text',
        's7p' => 'Click Print. A print window will open with the current text. Confirm the print dialog in your browser.',
        's8' => '8. How to export to Word',
        's8p1' => 'Click Export to Word. The application downloads a .docx file with a safe filename.',
        's8p2' => 'Line breaks are kept. The text is escaped so it cannot inject HTML or JavaScript into the document.',
        's9' => '9. Tips for better OCR results',
        'tip1' => 'Use a clear, well-lit photo. Avoid blur, shadows, and strong glare.',
        'tip2' => 'Keep the text upright and fill most of the image.',
        'tip3' => 'High-contrast images work better, such as dark text on a light background.',
        'tip4' => 'Choose the OCR language that matches the text in the image.',
        'tip5' => 'For mixed English and Chinese, choose English + Simplified Chinese.',
        'tip6' => 'If the result is incomplete, crop the image closer to the text and try again.',
        'errors' => 'Common error messages',
        'e1' => 'No image selected: click Choose Image first, then Start OCR.',
        'e2' => 'Unsupported file type: use JPG, JPEG, PNG, GIF, BMP, TIFF, or WEBP.',
        'e3' => 'File larger than 10 MB: compress or resize the image.',
        'e4' => 'OCR failed: try another image, or check the Gemini API settings in config.php.',
        'e5' => 'No text detected: the image may be too blurry, or it may not contain readable text.',
        'privacy' => 'Privacy',
        'privacyP' => 'OCR uses Gemini AI. Images are sent to your server for recognition and are not stored permanently.',
    ],
    'zh' => [
        'title' => '使用手册',
        'appName' => 'OCR 转换器',
        'manual' => '使用手册',
        'langEn' => 'English',
        'langZh' => '中文',
        'home' => '返回转换器',
        'intro' => '本页说明如何从图片提取文字、编辑识别结果，以及复制、打印或导出。',
        's1' => '1. 如何选择图片',
        's1p1' => '在主页点击“选择图片”。',
        's1p2' => '从电脑、手机或平板中选择一张图片。',
        's1p3' => '按钮旁边会显示文件名，下方会显示图片预览。',
        's2' => '2. 支持的图片格式',
        's2p' => '可以使用以下格式：',
        's3' => '3. 最大文件大小',
        's3p' => '最大文件大小为 10 MB。如果图片超过 10 MB，系统会显示错误，并且不会开始识别。',
        's4' => '4. 如何开始识别',
        's4p1' => '先选择识别语言：英语、简体中文，或两者。',
        's4p2' => '点击“开始识别”。识别过程中请不要刷新页面。',
        's4p3' => '进度条会显示当前步骤，例如：正在开始、正在加载 OCR、正在识别文字、完成。',
        's4p4' => '识别由服务器上的 Gemini AI 完成。',
        's5' => '5. 如何编辑识别结果',
        's5p' => '识别出的文字会出现在文本框中。复制、打印或导出前，您可以先修改错字、标点或排版。',
        's6' => '6. 如何复制文字',
        's6p' => '点击“复制文字”。文本框里的当前内容（包括您的修改）会复制到剪贴板。',
        's7' => '7. 如何打印文字',
        's7p' => '点击“打印”。浏览器会打开打印窗口，显示当前文字。请在打印对话框中确认。',
        's8' => '8. 如何导出为 Word',
        's8p1' => '点击“导出为 Word”。系统会下载一个文件名安全的 .docx 文件。',
        's8p2' => '换行会被保留。文字会经过转义，因此无法向文档注入 HTML 或 JavaScript。',
        's9' => '9. 提高识别效果的提示',
        'tip1' => '使用清晰、光线充足的照片，避免模糊、阴影和强烈反光。',
        'tip2' => '让文字摆正，并尽量占满画面。',
        'tip3' => '高对比度图片效果更好，例如浅色背景上的深色文字。',
        'tip4' => '请选择与图片文字相符的识别语言。',
        'tip5' => '如果图片同时包含英文和中文，请选择“英语 + 简体中文”。',
        'tip6' => '如果结果不完整，可以把图片裁切得更靠近文字后再试一次。',
        'errors' => '常见错误提示',
        'e1' => '尚未选择图片：请先点击“选择图片”，再开始识别。',
        'e2' => '不支持的文件类型：请使用 JPG、JPEG、PNG、GIF、BMP、TIFF 或 WEBP。',
        'e3' => '文件大于 10 MB：请压缩或缩小图片。',
        'e4' => '识别失败：请更换图片，或检查 config.php 中的 Gemini API 设置。',
        'e5' => '未检测到文字：图片可能太模糊，或没有可读文字。',
        'privacy' => '隐私说明',
        'privacyP' => 'OCR 使用 Gemini AI 识别。图片会发送到您的服务器进行识别，不会永久保存。',
    ],
];

$tr = $t[$lang];

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
    <title><?= e($tr['title']) ?> — <?= e($tr['appName']) ?></title>
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
                <span class="brand-text"><?= e($tr['appName']) ?></span>
            </a>
            <nav class="header-nav" aria-label="<?= e($tr['appName']) ?>">
                <a class="nav-link" href="index.php?lang=<?= e($lang) ?>"><?= e($tr['home']) ?></a>
                <span class="lang-switch" role="group" aria-label="Language">
                    <a href="manual.php?lang=en" class="<?= $lang === 'en' ? 'is-active' : '' ?>" hreflang="en"><?= e($tr['langEn']) ?></a>
                    <a href="manual.php?lang=zh" class="<?= $lang === 'zh' ? 'is-active' : '' ?>" hreflang="zh"><?= e($tr['langZh']) ?></a>
                </span>
            </nav>
        </div>
    </header>

    <main class="page manual">
        <section class="hero reveal">
            <p class="brand-kicker"><?= e($tr['appName']) ?></p>
            <h1 class="hero-title"><?= e($tr['title']) ?></h1>
            <p class="tagline"><?= e($tr['intro']) ?></p>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s1']) ?></h2>
            <ol>
                <li><?= e($tr['s1p1']) ?></li>
                <li><?= e($tr['s1p2']) ?></li>
                <li><?= e($tr['s1p3']) ?></li>
            </ol>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s2']) ?></h2>
            <p><?= e($tr['s2p']) ?></p>
            <ul>
                <li>JPG / JPEG</li>
                <li>PNG</li>
                <li>GIF</li>
                <li>BMP</li>
                <li>TIFF</li>
                <li>WEBP</li>
            </ul>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s3']) ?></h2>
            <p><?= e($tr['s3p']) ?></p>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s4']) ?></h2>
            <ol>
                <li><?= e($tr['s4p1']) ?></li>
                <li><?= e($tr['s4p2']) ?></li>
                <li><?= e($tr['s4p3']) ?></li>
                <li><?= e($tr['s4p4']) ?></li>
            </ol>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s5']) ?></h2>
            <p><?= e($tr['s5p']) ?></p>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s6']) ?></h2>
            <p><?= e($tr['s6p']) ?></p>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s7']) ?></h2>
            <p><?= e($tr['s7p']) ?></p>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s8']) ?></h2>
            <ol>
                <li><?= e($tr['s8p1']) ?></li>
                <li><?= e($tr['s8p2']) ?></li>
            </ol>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['s9']) ?></h2>
            <ul>
                <li><?= e($tr['tip1']) ?></li>
                <li><?= e($tr['tip2']) ?></li>
                <li><?= e($tr['tip3']) ?></li>
                <li><?= e($tr['tip4']) ?></li>
                <li><?= e($tr['tip5']) ?></li>
                <li><?= e($tr['tip6']) ?></li>
            </ul>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['errors']) ?></h2>
            <ul>
                <li><?= e($tr['e1']) ?></li>
                <li><?= e($tr['e2']) ?></li>
                <li><?= e($tr['e3']) ?></li>
                <li><?= e($tr['e4']) ?></li>
                <li><?= e($tr['e5']) ?></li>
            </ul>
        </section>

        <section class="panel reveal">
            <h2><?= e($tr['privacy']) ?></h2>
            <p><?= e($tr['privacyP']) ?></p>
        </section>
    </main>
</body>
</html>
