// ============================================================
// SweetAlert2 - standard di progetto per conferme e avvisi.
// - Intercetta qualsiasi elemento [data-confirm] (GET o data-method=post).
// - Mostra i messaggi flash passati in window.dashSwalFlashes.
// Richiede il global Swal (AdminLTE sweetalert2).
// ============================================================
(function () {
    'use strict';

    function getCsrf() {
        var p = document.querySelector('meta[name="csrf-param"]');
        var t = document.querySelector('meta[name="csrf-token"]');
        if (!t) { return null; }
        return { name: (p ? p.getAttribute('content') : '_csrf'), value: t.getAttribute('content') };
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

    document.addEventListener('click', function (e) {
        var el = e.target && e.target.closest ? e.target.closest('[data-confirm]') : null;
        if (!el) { return; }
        if (typeof Swal === 'undefined') { return; } // fallback al comportamento nativo

        e.preventDefault();
        e.stopPropagation();

        var href = el.getAttribute('href') || el.getAttribute('formaction');
        var method = (el.getAttribute('data-method') || 'get').toLowerCase();
        var msg = el.getAttribute('data-confirm') || "Confermi l'operazione?";

        Swal.fire({
            title: 'Confermi?',
            text: msg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sì, procedi',
            cancelButtonText: 'Annulla',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d'
        }).then(function (res) {
            if (!res.isConfirmed || !href) { return; }
            if (method === 'post') {
                postAndGo(el, href);
            } else {
                window.location.href = href;
            }
        });
    }, true);

    function mostraFlash() {
        if (typeof Swal === 'undefined') { return; }
        var flashes = window.dashSwalFlashes || [];
        flashes.forEach(function (f) {
            Swal.fire({ icon: f.icon, title: f.title, text: f.text });
        });
        window.dashSwalFlashes = [];
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mostraFlash);
    } else {
        mostraFlash();
    }
})();
