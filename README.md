# OCR Converter

Extract text from images with Gemini AI, then edit, copy, print, or export to Word.

A lightweight PHP web app for XAMPP and shared hosting — **no database required**.

## Quick start (XAMPP)

1. Place the project in `C:\xampp\htdocs\project\OCR_Converter`
2. Copy `config.example.php` to `config.php` and add your Gemini API key
3. Start **Apache** in XAMPP (MySQL is not needed)
4. Open http://localhost/project/OCR_Converter/
5. Choose an image → pick OCR language → **Start OCR**
6. Edit the result, then **Copy**, **Print**, or **Export to Word**

Get an API key from [Google AI Studio](https://aistudio.google.com/apikey).

## Features

- Drag & drop or browse image upload with live preview
- OCR via Google Gemini AI (`gemini-3.5-flash`)
- English / Simplified Chinese UI switcher
- OCR language: English, Simplified Chinese, or both
- Edit recognized text before export
- Copy to clipboard, print, or download `.docx`
- Built-in user manual
- Responsive layout for desktop and mobile
- No database, no framework

## Folder structure

```
OCR_Converter/
├── index.php              Main converter page
├── manual.php             User manual (EN / 中文)
├── ocr-api.php            Gemini OCR backend
├── app.js                 Frontend logic
├── styles.css             UI styles
├── config.example.php     Config template
├── config.php             API key (private, gitignored)
├── .htaccess              Blocks public access to config.php
└── LICENSE                MIT License
```

## 1. XAMPP setup

1. Copy this folder to `C:\xampp\htdocs\project\OCR_Converter`
2. Start **Apache** in XAMPP
3. Copy the config template:

```bash
copy config.example.php config.php
```

4. Edit `config.php`:

```php
<?php
return [
    'api_key' => 'YOUR_GEMINI_API_KEY',
    'api_url' => 'https://generativelanguage.googleapis.com/v1/models/gemini-3.5-flash:generateContent',
];
```

5. Make sure PHP **curl** is enabled (`extension=curl` in `php.ini`)
6. Open: http://localhost/project/OCR_Converter/index.php?lang=en

Language URLs:

- English: `index.php?lang=en`
- Chinese: `index.php?lang=zh`
- Manual: `manual.php?lang=en`

## 2. How to use

1. Click **Choose Image** or drag a file into the drop zone
2. Select **OCR Language**
3. Click **Start OCR** and wait for the progress bar
4. Edit the text in **Recognized Text** if needed
5. Use **Copy Text**, **Print**, or **Export to Word**

Supported formats: JPG, JPEG, PNG, GIF, BMP, TIFF, WEBP  
Max file size: **10 MB**

Tips for better results:

- Use a clear, well-lit photo
- Keep the text upright and fill most of the frame
- Choose the OCR language that matches the image
- For mixed English + Chinese, select both

## 3. Deploy to cPanel (no database)

You do **not** need MySQL or phpMyAdmin.

1. Upload all project files to `public_html/` (or a subfolder such as `public_html/ocr/`)
2. Create `config.php` on the server with your API key
3. In cPanel, set PHP **8.0+** and enable **curl**
4. Set `upload_max_filesize` ≥ **10M** and `post_max_size` ≥ **12M**
5. Visit `https://yourdomain.com/index.php?lang=en`

## 4. System flow

1. User uploads an image in the browser
2. Frontend validates type and size (max 10 MB)
3. Image is posted to `ocr-api.php`
4. Server sends the image to Gemini with an OCR prompt
5. Recognized text is returned to the page
6. User edits, copies, prints, or exports to Word

Images are processed for recognition and are **not stored permanently** on the server.

## Security notes

- `config.php` is blocked by `.htaccess` and listed in `.gitignore`
- The Gemini API key stays on the server (never sent to the browser)
- Do not commit `config.php` or paste keys into public repos / chats
- Rotate your API key if it was exposed
- Keep `upload_max_filesize` reasonable to limit abuse

## Requirements

- PHP 8.0+
- PHP extensions: `curl`, `fileinfo`, `json`
- Apache (XAMPP or cPanel)
- Gemini API key

## License

This project is licensed under the [MIT License](LICENSE).

## Author

[weihern08](https://github.com/weihern08)
