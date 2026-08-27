(function () {
    "use strict";

    var MAX_BYTES = 10 * 1024 * 1024;
    var ALLOWED_EXTENSIONS = ["jpg", "jpeg", "png", "gif", "bmp", "tif", "tiff", "webp"];
    var ALLOWED_TYPES = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/bmp",
        "image/x-ms-bmp",
        "image/tiff",
        "image/webp"
    ];

    var i18n = readI18n();
    var imageInput = document.getElementById("image-input");
    var fileNameEl = document.getElementById("file-name");
    var dropzone = document.getElementById("dropzone");
    var previewBox = document.getElementById("preview-box");
    var previewImage = document.getElementById("preview-image");
    var previewCaption = document.getElementById("preview-caption");
    var startBtn = document.getElementById("start-ocr");
    var clearBtn = document.getElementById("clear-btn");
    var ocrLang = document.getElementById("ocr-lang");
    var progressWrap = document.getElementById("progress");
    var progressLabel = document.getElementById("progress-label");
    var progressPercent = document.getElementById("progress-percent");
    var progressBar = document.getElementById("progress-bar");
    var progressFill = document.getElementById("progress-fill");
    var resultText = document.getElementById("result-text");
    var copyBtn = document.getElementById("copy-btn");
    var printBtn = document.getElementById("print-btn");
    var exportBtn = document.getElementById("export-btn");
    var statusEl = document.getElementById("status");

    var selectedFile = null;
    var previewUrl = "";
    var ocrBusy = false;
    var ocrAbortController = null;
    var progressTimer = null;
    var emptyCaption = i18n.previewEmpty || (previewCaption ? previewCaption.textContent : "");
    var statusSeq = 0;
    var ocrApiUrl = i18n.ocrApiUrl || "ocr-api.php";

    imageInput.addEventListener("change", onFileChange);
    startBtn.addEventListener("click", startOcr);
    clearBtn.addEventListener("click", function () {
        clearAll(true);
    });
    copyBtn.addEventListener("click", copyText);
    printBtn.addEventListener("click", printText);
    exportBtn.addEventListener("click", exportWord);

    if (dropzone) {
        ["dragenter", "dragover"].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                if (!ocrBusy) {
                    dropzone.classList.add("is-dragover");
                }
            });
        });
        ["dragleave", "drop"].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropzone.classList.remove("is-dragover");
            });
        });
        dropzone.addEventListener("drop", function (event) {
            if (ocrBusy) {
                return;
            }
            var files = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files : null;
            if (!files || !files.length) {
                return;
            }
            applySelectedFile(files[0]);
        });
    }

    function readI18n() {
        var node = document.getElementById("i18n-data");
        try {
            return JSON.parse(node.textContent);
        } catch (err) {
            return {};
        }
    }

    function onFileChange() {
        var file = imageInput.files && imageInput.files[0] ? imageInput.files[0] : null;
        if (!file) {
            selectedFile = null;
            fileNameEl.textContent = i18n.noFile || "";
            return;
        }
        applySelectedFile(file);
    }

    function applySelectedFile(file) {
        hideStatus();
        if (!file) {
            selectedFile = null;
            fileNameEl.textContent = i18n.noFile || "";
            return;
        }

        var error = validateFile(file);
        if (error) {
            clearAll(false);
            imageInput.value = "";
            showStatus(error, "error");
            return;
        }

        selectedFile = file;
        fileNameEl.textContent = file.name;
        try {
            var transfer = new DataTransfer();
            transfer.items.add(file);
            imageInput.files = transfer.files;
        } catch (err) {
            // Some browsers may block assigning FileList; OCR still uses selectedFile.
        }
        showPreview(file);
    }

    function validateFile(file) {
        var ext = extensionOf(file.name);
        if (!ext || ALLOWED_EXTENSIONS.indexOf(ext) === -1) {
            return i18n.unsupportedType;
        }
        if (file.type && ALLOWED_TYPES.indexOf(file.type.toLowerCase()) === -1) {
            return i18n.unsupportedType;
        }
        if (file.size > MAX_BYTES) {
            return i18n.fileTooLarge;
        }
        return "";
    }

    function extensionOf(name) {
        var parts = String(name || "").toLowerCase().split(".");
        return parts.length > 1 ? parts.pop() : "";
    }

    function showPreview(file) {
        revokePreview();
        previewUrl = URL.createObjectURL(file);
        previewImage.onload = function () {
            previewImage.hidden = false;
            previewCaption.hidden = true;
            previewBox.classList.add("has-image");
        };
        previewImage.onerror = function () {
            previewImage.hidden = true;
            previewCaption.hidden = false;
            previewCaption.textContent = i18n.previewUnavailable;
            previewBox.classList.remove("has-image");
        };
        previewImage.src = previewUrl;
    }

    function revokePreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = "";
        }
    }

    async function startOcr() {
        hideStatus();
        if (ocrBusy) {
            return;
        }
        if (!selectedFile) {
            showStatus(i18n.noImage, "error");
            return;
        }

        var error = validateFile(selectedFile);
        if (error) {
            showStatus(error, "error");
            return;
        }

        ocrBusy = true;
        setBusy(true);
        showProgress(i18n.progressStarting, 5);
        startProgressAnimation();

        ocrAbortController = new AbortController();

        try {
            var formData = new FormData();
            formData.append("image", selectedFile);
            formData.append("ocr_lang", ocrLang.value);

            showProgress(i18n.progressUploading, 25);

            var response = await fetch(ocrApiUrl, {
                method: "POST",
                body: formData,
                signal: ocrAbortController.signal
            });

            showProgress(i18n.progressRecognizing, 70);

            var data = await response.json();
            if (!response.ok || !data || !data.success) {
                throw new Error((data && data.error) ? data.error : "ocr-failed");
            }

            var text = typeof data.text === "string" ? data.text.trim() : "";

            showProgress(i18n.progressComplete, 100);
            resultText.value = text;

            if (!text) {
                showStatus(i18n.noText, "warning");
            } else {
                showStatus(i18n.ocrSuccess, "success");
            }
        } catch (err) {
            if (err && err.name === "AbortError") {
                return;
            }
            var message = i18n.ocrFailed;
            if (err && err.message && err.message !== "ocr-failed") {
                message = err.message;
            }
            showStatus(message, "error");
            resetProgress();
        } finally {
            stopProgressAnimation();
            ocrAbortController = null;
            ocrBusy = false;
            setBusy(false);
        }
    }

    function startProgressAnimation() {
        stopProgressAnimation();
        var step = 30;
        progressTimer = window.setInterval(function () {
            step = Math.min(step + 4, 90);
            showProgress(i18n.progressRecognizing, step);
        }, 700);
    }

    function stopProgressAnimation() {
        if (progressTimer !== null) {
            window.clearInterval(progressTimer);
            progressTimer = null;
        }
    }

    function showProgress(label, percent) {
        progressWrap.hidden = false;
        progressLabel.textContent = label;
        progressPercent.textContent = percent + "%";
        progressFill.style.width = percent + "%";
        progressBar.setAttribute("aria-valuenow", String(percent));
        progressBar.setAttribute("aria-valuetext", label + " " + percent + "%");
    }

    function resetProgress() {
        stopProgressAnimation();
        progressWrap.hidden = true;
        progressLabel.textContent = i18n.progressStarting || "";
        progressPercent.textContent = "0%";
        progressFill.style.width = "0%";
        progressBar.setAttribute("aria-valuenow", "0");
    }

    function setBusy(isBusy) {
        startBtn.disabled = isBusy;
        imageInput.disabled = isBusy;
        ocrLang.disabled = isBusy;
        copyBtn.disabled = isBusy;
        printBtn.disabled = isBusy;
        exportBtn.disabled = isBusy;
        if (isBusy) {
            showStatus(i18n.processing, "warning");
        }
    }

    async function clearAll(showMessage) {
        var wasBusy = ocrBusy;
        ocrBusy = false;
        stopProgressAnimation();
        if (ocrAbortController) {
            ocrAbortController.abort();
            ocrAbortController = null;
        }
        selectedFile = null;
        imageInput.value = "";
        imageInput.disabled = false;
        ocrLang.disabled = false;
        startBtn.disabled = false;
        copyBtn.disabled = false;
        printBtn.disabled = false;
        exportBtn.disabled = false;
        fileNameEl.textContent = i18n.noFile || "";
        previewImage.hidden = true;
        previewImage.removeAttribute("src");
        previewCaption.hidden = false;
        previewCaption.textContent = emptyCaption;
        previewBox.classList.remove("has-image");
        revokePreview();
        resultText.value = "";
        resetProgress();
        if (showMessage || wasBusy) {
            nextStatus();
            showStatus(i18n.cleared, "success");
        } else {
            hideStatus();
        }
    }

    function copyText() {
        var text = resultText.value;
        if (!text.trim()) {
            showStatus(i18n.nothingToCopy, "error");
            return;
        }

        var seq = nextStatus();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                showStatus(i18n.copied, "success", seq);
            }).catch(function () {
                fallbackCopy(text, seq);
            });
            return;
        }
        fallbackCopy(text, seq);
    }

    function fallbackCopy(text, seq) {
        var ok = false;
        try {
            var helper = document.createElement("textarea");
            helper.value = text;
            helper.setAttribute("readonly", "readonly");
            helper.style.position = "fixed";
            helper.style.left = "-9999px";
            document.body.appendChild(helper);
            helper.select();
            ok = document.execCommand("copy");
            helper.remove();
        } catch (err) {
            ok = false;
        }
        showStatus(ok ? i18n.copied : i18n.copyFailed, ok ? "success" : "error", seq);
    }

    function printText() {
        var text = resultText.value;
        if (!text.trim()) {
            showStatus(i18n.nothingToPrint, "error");
            return;
        }

        var popup = window.open("", "_blank", "noopener,noreferrer,width=800,height=900");
        if (!popup) {
            window.print();
            return;
        }

        popup.document.open();
        popup.document.write(
            "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>" +
            escapeHtml(i18n.printTitle || "OCR") +
            "</title><style>body{font-family:Segoe UI,Noto Sans SC,Microsoft YaHei,sans-serif;white-space:pre-wrap;line-height:1.6;padding:24px;}</style></head><body>" +
            escapeHtml(text) +
            "</body></html>"
        );
        popup.document.close();
        popup.focus();
        popup.print();
    }

    function exportWord() {
        var text = resultText.value;
        if (!text.trim()) {
            showStatus(i18n.nothingToExport, "error");
            return;
        }

        var bytes = buildDocx(text);
        var blob = new Blob([bytes], {
            type: "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        });
        var link = document.createElement("a");
        var objectUrl = URL.createObjectURL(blob);
        link.href = objectUrl;
        link.download = safeFilename(i18n.exportPrefix || "ocr-text") + ".docx";
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(objectUrl);
        showStatus(i18n.exportSuccess, "success");
    }

    function safeFilename(prefix) {
        var clean = String(prefix).replace(/[^a-zA-Z0-9_-]/g, "") || "ocr-text";
        var now = new Date();
        return clean + "-" +
            now.getFullYear() +
            pad(now.getMonth() + 1) +
            pad(now.getDate()) +
            "-" +
            pad(now.getHours()) +
            pad(now.getMinutes()) +
            pad(now.getSeconds());
    }

    function pad(value) {
        return String(value).padStart(2, "0");
    }

    function nextStatus() {
        statusSeq += 1;
        return statusSeq;
    }

    function showStatus(message, type, seq) {
        if (seq !== undefined && seq !== statusSeq) {
            return;
        }
        statusEl.hidden = false;
        statusEl.className = "status " + type;
        statusEl.textContent = message;
    }

    function hideStatus() {
        nextStatus();
        statusEl.hidden = true;
        statusEl.textContent = "";
        statusEl.className = "status";
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    function escapeXml(value) {
        return sanitizeXmlText(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function sanitizeXmlText(value) {
        return String(value).replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, "");
    }

    function buildDocx(text) {
        var lines = String(text).replace(/\r\n/g, "\n").replace(/\r/g, "\n").split("\n");
        var paragraphs = lines.map(function (line) {
            if (line === "") {
                return "<w:p/>";
            }
            return "<w:p><w:r><w:t xml:space=\"preserve\">" + escapeXml(line) + "</w:t></w:r></w:p>";
        }).join("");

        var documentXml =
            "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" +
            "<w:document xmlns:w=\"http://schemas.openxmlformats.org/wordprocessingml/2006/main\">" +
            "<w:body>" + paragraphs +
            "<w:sectPr><w:pgSz w:w=\"11906\" w:h=\"16838\"/>" +
            "<w:pgMar w:top=\"1440\" w:right=\"1440\" w:bottom=\"1440\" w:left=\"1440\"/></w:sectPr>" +
            "</w:body></w:document>";

        var contentTypes =
            "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" +
            "<Types xmlns=\"http://schemas.openxmlformats.org/package/2006/content-types\">" +
            "<Default Extension=\"rels\" ContentType=\"application/vnd.openxmlformats-package.relationships+xml\"/>" +
            "<Default Extension=\"xml\" ContentType=\"application/xml\"/>" +
            "<Override PartName=\"/word/document.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml\"/>" +
            "</Types>";

        var rels =
            "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" +
            "<Relationships xmlns=\"http://schemas.openxmlformats.org/package/2006/relationships\">" +
            "<Relationship Id=\"rId1\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument\" Target=\"word/document.xml\"/>" +
            "</Relationships>";

        var documentRels =
            "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" +
            "<Relationships xmlns=\"http://schemas.openxmlformats.org/package/2006/relationships\"></Relationships>";

        return zipStore([
            { name: "[Content_Types].xml", data: utf8Bytes(contentTypes) },
            { name: "_rels/.rels", data: utf8Bytes(rels) },
            { name: "word/document.xml", data: utf8Bytes(documentXml) },
            { name: "word/_rels/document.xml.rels", data: utf8Bytes(documentRels) }
        ]);
    }

    function utf8Bytes(text) {
        return new TextEncoder().encode(text);
    }

    function zipStore(files) {
        var localParts = [];
        var centralParts = [];
        var offset = 0;
        var now = dosDateTime(new Date());

        files.forEach(function (file) {
            var nameBytes = utf8Bytes(file.name);
            var crc = crc32(file.data);
            var local = concatBytes([
                u16(0x4b50), u16(0x0403),
                u16(20), u16(0), u16(0),
                u16(now.time), u16(now.date),
                u32(crc), u32(file.data.length), u32(file.data.length),
                u16(nameBytes.length), u16(0),
                nameBytes,
                file.data
            ]);
            var central = concatBytes([
                u16(0x4b50), u16(0x0201),
                u16(20), u16(20), u16(0), u16(0),
                u16(now.time), u16(now.date),
                u32(crc), u32(file.data.length), u32(file.data.length),
                u16(nameBytes.length), u16(0), u16(0), u16(0), u16(0),
                u32(0), u32(offset),
                nameBytes
            ]);
            localParts.push(local);
            centralParts.push(central);
            offset += local.length;
        });

        var centralDir = concatBytes(centralParts);
        var end = concatBytes([
            u16(0x4b50), u16(0x0605),
            u16(0), u16(0),
            u16(files.length), u16(files.length),
            u32(centralDir.length), u32(offset),
            u16(0)
        ]);

        return concatBytes(localParts.concat([centralDir, end]));
    }

    function dosDateTime(date) {
        return {
            time: (date.getHours() << 11) | (date.getMinutes() << 5) | (date.getSeconds() >> 1),
            date: ((date.getFullYear() - 1980) << 9) | ((date.getMonth() + 1) << 5) | date.getDate()
        };
    }

    function u16(value) {
        var out = new Uint8Array(2);
        out[0] = value & 255;
        out[1] = (value >> 8) & 255;
        return out;
    }

    function u32(value) {
        var out = new Uint8Array(4);
        out[0] = value & 255;
        out[1] = (value >> 8) & 255;
        out[2] = (value >> 16) & 255;
        out[3] = (value >> 24) & 255;
        return out;
    }

    function concatBytes(chunks) {
        var total = 0;
        chunks.forEach(function (chunk) {
            total += chunk.length;
        });
        var out = new Uint8Array(total);
        var offset = 0;
        chunks.forEach(function (chunk) {
            out.set(chunk, offset);
            offset += chunk.length;
        });
        return out;
    }

    var CRC_TABLE = (function () {
        var table = new Uint32Array(256);
        for (var i = 0; i < 256; i += 1) {
            var c = i;
            for (var k = 0; k < 8; k += 1) {
                c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
            }
            table[i] = c >>> 0;
        }
        return table;
    }());

    function crc32(data) {
        var crc = 0xFFFFFFFF;
        for (var i = 0; i < data.length; i += 1) {
            crc = CRC_TABLE[(crc ^ data[i]) & 255] ^ (crc >>> 8);
        }
        return (crc ^ 0xFFFFFFFF) >>> 0;
    }
}());
