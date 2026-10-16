// ============================================================
// SweetAlert2 - standard di progetto per conferme e avvisi.
// - Sostituisce yii.confirm (usato da yii.js per data-confirm/data-method).
// - Intercetta anche qualsiasi elemento [data-confirm] non gestito da yii.
// - Mostra i messaggi flash passati in window.dashSwalFlashes.
// ============================================================
(function () {
    'use strict';

    window.__dashSwalLoaded = true;

    function swal() {
        return window.Swal || window.Sweetalert2 || null;
    }

    function getCsrf() {
        var p = document.querySelector('meta[name="csrf-param"]');
        var t = document.querySelector('meta[name="csrf-token"]');
        if (!t) { return null; }
        return { name: (p ? p.getAttribute('content') : '_csrf'), value: t.getAttribute('content') };
    }

    function conferma(message, ok, cancel) {
        var S = swal();
        if (!S) {
            if (window.confirm(message)) { ok && ok(); } else { cancel && cancel(); }
            return;
        }
        S.fire({
            title: 'Confermi?',
            text: message || "Confermi l'operazione?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sì, procedi',
            cancelButtonText: 'Annulla',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d'
        }).then(function (res) {
            if (res.isConfirmed) { ok && ok(); } else { cancel && cancel(); }
        });
    }

    // Sostituzione di yii.confirm (punto di estensione ufficiale di yii.js).
    function installYiiConfirm() {
        if (window.yii && typeof window.yii.confirm === 'function') {
            window.yii.confirm = conferma;
        }
    }
    installYiiConfirm();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', installYiiConfirm);
    }

    function postAndGo(el, href) {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = href;
        form.style.display = 'none';

        var c = getCsrf();
        if (c) {
            var i = document.createElement('input');
            i.type = 'hidden';
            i.name = c.name;
            i.value = c.value;
            form.appendChild(i);
        }

        var params = el.getAttribute('data-params');
        if (params) {
            try {
                var obj = JSON.parse(params);
                for (var k in obj) {
                    if (!obj.hasOwnProperty(k)) { continue; }
                    var h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = k;
                    h.value = obj[k];
                    form.appendChild(h);
                }
            } catch (e) { }
        }

        document.body.appendChild(form);
        form.submit();
    }

    // Fallback per elementi con [data-confirm] non gestiti da yii.js.
    document.addEventListener('click', function (e) {
        var el = e.target && e.target.closest ? e.target.closest('[data-confirm]') : null;
        if (!el || !swal()) { return; }
        // Se yii.js gestisce l'elemento, lascia fare a yii.confirm (evita doppioni).
        if (el.getAttribute('data-method') !== null && window.yii) { return; }

        e.preventDefault();
        e.stopPropagation();

        var href = el.getAttribute('href') || el.getAttribute('formaction');
        var method = (el.getAttribute('data-method') || 'get').toLowerCase();

        conferma(el.getAttribute('data-confirm'), function () {
            if (!href) { return; }
            if (method === 'post') { postAndGo(el, href); } else { window.location.href = href; }
        });
    }, true);

    function mostraFlash() {
        var S = swal();
        if (!S) { return; }
        var flashes = window.dashSwalFlashes || [];
        flashes.forEach(function (f) {
            S.fire({ icon: f.icon, title: f.title, text: f.text });
        });
        window.dashSwalFlashes = [];
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mostraFlash);
    } else {
        mostraFlash();
    }
})();
