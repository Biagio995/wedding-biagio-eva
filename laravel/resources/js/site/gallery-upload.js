(function () {
    var form = document.getElementById('gallery-form');
    var input = document.getElementById('photos-input');
    var btn = document.getElementById('submit-btn');
    var progressWrap = document.getElementById('upload-progress');
    var progressBar = progressWrap ? progressWrap.querySelector('.progress-bar-fill') : null;
    var progressTrack = progressWrap ? progressWrap.querySelector('.progress-track') : null;
    var progressPct = document.getElementById('progress-pct');
    var errBox = document.getElementById('gallery-upload-errors');
    var i18n = document.getElementById('gallery-upload-i18n');
    var previewRoot = document.getElementById('gallery-preview');
    var previewGrid = document.getElementById('gallery-preview-grid');
    var previewSummary = document.getElementById('gallery-preview-summary');
    if (!form || !input) return;

    var objectUrls = [];

    function msg(key) {
        return (i18n && i18n.dataset && i18n.dataset[key]) || '';
    }

    function revokeAllObjectUrls() {
        objectUrls.forEach(function (u) {
            try {
                URL.revokeObjectURL(u);
            } catch (e) {}
        });
        objectUrls = [];
    }

    function removeFileAt(index) {
        var files = input.files;
        if (!files || !files.length || index < 0 || index >= files.length) return;
        var dt = new DataTransfer();
        for (var j = 0; j < files.length; j++) {
            if (j !== index) {
                dt.items.add(files[j]);
            }
        }
        input.files = dt.files;
        renderPreview();
    }

    function previewLabel(n) {
        if (n <= 0) return '';
        if (n === 1) return msg('msgPreviewOne');
        var t = msg('msgPreviewOther');
        return t ? t.replace(':count', String(n)) : String(n);
    }

    function addRemoveButton(item, index) {
        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'gallery-preview__remove';
        removeBtn.setAttribute('aria-label', msg('msgRemovePhoto') || 'Remove');
        removeBtn.appendChild(document.createTextNode('\u00D7'));
        removeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            removeFileAt(index);
        });
        item.appendChild(removeBtn);
    }

    function renderPreview() {
        revokeAllObjectUrls();
        if (!previewGrid || !previewRoot) return;
        previewGrid.innerHTML = '';
        var files = input.files;
        if (!files || !files.length) {
            previewRoot.hidden = true;
            if (previewSummary) previewSummary.textContent = '';
            return;
        }
        previewRoot.hidden = false;
        if (previewSummary) previewSummary.textContent = previewLabel(files.length);

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var item = document.createElement('div');
            item.className = 'gallery-preview__item';
            item.setAttribute('role', 'listitem');
            var url = URL.createObjectURL(file);
            objectUrls.push(url);
            var img = document.createElement('img');
            img.alt = file.name;
            img.loading = 'lazy';
            img.decoding = 'async';
            img.onerror = function () {
                try {
                    URL.revokeObjectURL(url);
                } catch (e) {}
                var idx = objectUrls.indexOf(url);
                if (idx !== -1) objectUrls.splice(idx, 1);
                img.remove();
                var ph = document.createElement('div');
                ph.className = 'gallery-preview__placeholder';
                ph.textContent = file.name;
                item.insertBefore(ph, item.querySelector('.gallery-preview__remove'));
            };
            img.src = url;
            item.appendChild(img);
            addRemoveButton(item, i);
            previewGrid.appendChild(item);
        }
    }

    input.addEventListener('change', renderPreview);

    var uploading = false;

    function showErrors(payload) {
        if (!errBox) return;
        var errors = payload.errors || {};
        var parts = [];
        Object.keys(errors).forEach(function (k) {
            (errors[k] || []).forEach(function (m) {
                parts.push(m);
            });
        });
        if (payload.message && parts.length === 0) parts.push(payload.message);
        errBox.textContent = parts.join(' ');
        errBox.hidden = parts.length === 0;
    }

    function setProgress(pct) {
        if (!progressBar || !progressTrack) return;
        progressBar.style.width = pct + '%';
        progressTrack.setAttribute('aria-valuenow', String(Math.round(pct)));
        var span = progressPct && progressPct.querySelector('span');
        if (span) span.textContent = Math.round(pct) + '%';
    }

    function uploadWithProgress() {
        if (uploading) return;
        if (!input.files || !input.files.length) return;
        uploading = true;
        if (errBox) {
            errBox.hidden = true;
            errBox.textContent = '';
        }
        if (btn) btn.disabled = true;
        if (progressWrap) progressWrap.hidden = false;
        setProgress(0);

        var xhr = new XMLHttpRequest();
        var data = new FormData(form);
        var token = form.querySelector('[name="_token"]');
        xhr.open('POST', form.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token.value);
        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable && e.total > 0) {
                setProgress((e.loaded / e.total) * 100);
            }
        };
        xhr.onload = function () {
            uploading = false;
            if (btn) btn.disabled = false;
            if (progressWrap) progressWrap.hidden = true;
            setProgress(0);
            if (xhr.status === 200) {
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json.redirect) {
                        window.location.href = json.redirect;
                        return;
                    }
                } catch (ignore) {}
                window.location.reload();
                return;
            }
            if (xhr.status === 422) {
                try {
                    showErrors(JSON.parse(xhr.responseText));
                } catch (e2) {
                    showErrors({ message: msg('msgFailed') });
                }
                return;
            }
            if (xhr.status === 429) {
                showErrors({ message: msg('msgRateLimit') });
                return;
            }
            showErrors({ message: msg('msgFailed') });
        };
        xhr.onerror = function () {
            uploading = false;
            if (btn) btn.disabled = false;
            if (progressWrap) progressWrap.hidden = true;
            setProgress(0);
            showErrors({ message: msg('msgNetwork') });
        };
        xhr.send(data);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!input.files || !input.files.length) {
            input.click();
            return;
        }
        uploadWithProgress();
    });

    btn.addEventListener('click', function (e) {
        if (!input.files || !input.files.length) {
            e.preventDefault();
            input.click();
        }
    });
})();
