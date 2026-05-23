@once
@push('scripts')
<script>
(function (window, document) {
    if (window._AjaxListInstalled) return;
    window._AjaxListInstalled = true;

    function createSpinnerOverlay(container) {
        let overlay = container.querySelector('.ajax-list-spinner-overlay');
        if (overlay) return overlay;
        overlay = document.createElement('div');
        overlay.className = 'ajax-list-spinner-overlay d-none';
        overlay.style.cssText = 'position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.6); z-index:9999;';
        overlay.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        const computed = window.getComputedStyle(container);
        if (computed.position === 'static') container.style.position = 'relative';
        container.appendChild(overlay);
        return overlay;
    }

    function disablePagination(container, disabled) {
        const anchors = container.querySelectorAll('a[href*="page="]');
        anchors.forEach(a => {
            if (disabled) {
                a.dataset._ajaxListPointerEvents = a.style.pointerEvents || '';
                a.style.pointerEvents = 'none';
                a.classList.add('disabled');
                a.setAttribute('aria-disabled', 'true');
            } else {
                if (a.dataset._ajaxListPointerEvents !== undefined) a.style.pointerEvents = a.dataset._ajaxListPointerEvents;
                a.classList.remove('disabled');
                a.removeAttribute('aria-disabled');
            }
        });
    }

    function setFormDisabled(form, disabled) {
        if (!form) return;
        Array.from(form.elements || []).forEach(el => {
            if (disabled) {
                el.dataset._prevDisabled = el.disabled ? '1' : '';
                el.disabled = true;
            } else {
                if (el.dataset._prevDisabled === '') el.disabled = false;
                delete el.dataset._prevDisabled;
            }
        });
    }

    async function fetchAndReplace(url, container, overlay, options) {
        try {
            overlay.classList.remove('d-none');
            disablePagination(container, true);
            setFormDisabled(options.formEl, true);

            const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Network response was not ok');
            const html = await resp.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById(container.id);
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
            window.history.replaceState({}, '', url);
        } finally {
            overlay.classList.add('d-none');
            disablePagination(container, false);
            setFormDisabled(options.formEl, false);
        }
    }

    window.initAjaxList = function initAjaxList(config) {
        const container = document.getElementById(config.containerId);
        if (!container) return null;
        const searchEl = config.searchSelector ? document.querySelector(config.searchSelector) : null;
        const formEl = config.formSelector ? document.querySelector(config.formSelector) : null;
        const searchParam = config.searchParam || 'q';
        const debounceMs = config.debounceMs || 300;

        const overlay = createSpinnerOverlay(container);
        let timer = null;
        const options = { formEl };

        async function doFetch(urlOrQ) {
            let url;
            if (typeof urlOrQ === 'string' && (urlOrQ.startsWith('http') || urlOrQ.startsWith('/'))) {
                url = new URL(urlOrQ, window.location.origin);
            } else {
                url = new URL(window.location.href);
                const q = String(urlOrQ || (searchEl ? searchEl.value : '') || '').trim();
                if (q) url.searchParams.set(searchParam, q);
                else url.searchParams.delete(searchParam);
                if (formEl) {
                    const formData = new FormData(formEl);
                    for (const [k, v] of formData.entries()) {
                        if (v === null || v === undefined || v === '') { url.searchParams.delete(k); continue; }
                        url.searchParams.set(k, v);
                    }
                }
            }
            await fetchAndReplace(url, container, overlay, options);
        }

        if (searchEl) {
            searchEl.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => doFetch(searchEl.value.trim()), debounceMs);
            });
        }

        if (formEl) {
            formEl.addEventListener('submit', function (e) {
                e.preventDefault();
                doFetch();
            });
        }

        container.addEventListener('click', function (e) {
            const anchor = e.target.closest('a');
            if (!anchor) return;
            const href = anchor.getAttribute('href') || '';
            if (href.includes('page=')) {
                e.preventDefault();
                doFetch(href);
            }
        });

        return {
            fetch: doFetch,
            container,
            overlay
        };
    };

})(window, document);
</script>
@endpush
@endonce