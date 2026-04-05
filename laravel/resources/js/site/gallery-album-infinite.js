(function () {
    var root = document.getElementById('album-root');
    var grid = document.getElementById('album-grid');
    var sentinel = document.getElementById('album-sentinel');
    var loadingEl = document.getElementById('album-loading');
    var endEl = document.getElementById('album-end');
    if (!root || !grid || !sentinel) return;

    var nextUrl = root.getAttribute('data-next-url') || '';
    var loading = false;
    var downloadLabel = root.getAttribute('data-download-label') || '';
    var saveLabel = root.getAttribute('data-save-label') || '';

    function appendItems(items) {
        items.forEach(function (item) {
            var fig = document.createElement('figure');
            fig.className = 'album-item';
            fig.setAttribute('role', 'listitem');
            var wrap = document.createElement('div');
            wrap.className = 'album-item-wrap';
            var img = document.createElement('img');
            img.src = item.url;
            img.alt = item.alt || '';
            img.loading = 'lazy';
            img.decoding = 'async';
            img.width = 512;
            img.height = 512;
            wrap.appendChild(img);
            if (item.download_url) {
                var dl = document.createElement('a');
                dl.className = 'album-download';
                dl.href = item.download_url;
                dl.setAttribute('download', '');
                dl.setAttribute('aria-label', downloadLabel);
                dl.textContent = saveLabel;
                wrap.appendChild(dl);
            }
            fig.appendChild(wrap);
            grid.appendChild(fig);
        });
    }

    var io = null;

    function loadMore() {
        if (!nextUrl || loading) return;
        loading = true;
        if (loadingEl) loadingEl.hidden = false;
        fetch(nextUrl, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        })
            .then(function (r) {
                if (!r.ok) throw new Error('feed');
                return r.json();
            })
            .then(function (payload) {
                appendItems(payload.data || []);
                nextUrl = payload.next_page_url || '';
                root.setAttribute('data-next-url', nextUrl || '');
                if (!nextUrl) {
                    if (endEl) endEl.hidden = false;
                    if (io) io.disconnect();
                }
            })
            .catch(function () {})
            .finally(function () {
                loading = false;
                if (loadingEl) loadingEl.hidden = true;
            });
    }

    io = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) loadMore();
            });
        },
        { rootMargin: '400px' },
    );
    if (nextUrl) {
        io.observe(sentinel);
    } else if (endEl && grid.querySelectorAll('.album-item').length > 0) {
        endEl.hidden = false;
    }
})();
