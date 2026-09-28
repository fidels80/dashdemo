<?php

use yii\web\View;

/* @var $this yii\web\View */
/* @var $endpoints array */

$this->title = 'Prova servizi REST';
$this->params['breadcrumbs'][] = ['label' => 'Token API', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$samples = [
    'tipi-documento' => [
        'entity' => 'tipi-documento',
        'records' => [['codice' => 'API-TEST', 'descrizione' => 'Tipo di prova API']],
    ],
    'anagrafica' => [
        'entity' => 'anagrafica',
        'records' => [['codice' => 'API-TEST', 'ragione_sociale' => 'Soggetto di prova API', 'is_cliente' => 1, 'attivo' => 1]],
    ],
    'articoli' => [
        'entity' => 'articoli',
        'records' => [['codice' => 'API-TEST', 'descrizione' => 'Articolo di prova API', 'um' => 'PZ', 'prezzo' => 1, 'iva' => 22, 'attivo' => 1]],
    ],
    'documenti' => [
        'entity' => 'documenti',
        'records' => [['id_tipo' => 1, 'anno' => (int) date('Y'), 'numero' => 0, 'suffisso' => '', 'data' => date('Y-m-d'), 'descrizione' => 'Documento di prova API']],
    ],
    'righe' => [
        'entity' => 'righe',
        'records' => [['id_documento' => 0, 'ordine' => 1, 'descrizione' => 'Riga di prova API', 'qta' => 1, 'prezzo' => 1, 'iva' => 22]],
    ],
];

$config = [
    'endpoints' => $endpoints,
    'samples' => $samples,
];

$this->registerJs('var ApiTestConfig = ' . json_encode($config, JSON_UNESCAPED_SLASHES) . ';', View::POS_BEGIN);
?>
<div class="apitoken-test">
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key"></i> Token Bearer</h3>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="password" id="api-token" class="form-control" autocomplete="off"
                               placeholder="Incolla il token (64 caratteri esadecimali)">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="btn-toggle-token" title="Mostra/nascondi">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btn-forget-token" title="Dimentica token">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input" id="remember-token" checked>
                        <label class="custom-control-label" for="remember-token">Ricorda il token in questo browser</label>
                    </div>
                    <small class="text-muted">
                        Il token viene inviato nell'header <code>Authorization: Bearer</code>
                        (in alternativa è accettato il parametro <code>token</code>).
                    </small>
                </div>
            </div>

            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-paper-plane"></i> Richiesta</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-4">
                            <label>Metodo</label>
                            <select id="req-method" class="form-control">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="OPTIONS">OPTIONS</option>
                            </select>
                        </div>
                        <div class="form-group col-8">
                            <label>Endpoint</label>
                            <select id="req-endpoint" class="form-control">
                                <option value="index">api/index — stato e conteggi</option>
                                <option value="export">api/export — esporta entità</option>
                                <option value="view">api/view — singolo record</option>
                                <option value="import">api/import — importa (upsert)</option>
                                <option value="delete">api/delete — elimina record</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Parametri query</label>
                        <input type="text" id="req-params" class="form-control" spellcheck="false"
                               placeholder="entity=documenti&id=1&with_righe=1">
                    </div>
                    <div class="form-group" id="req-body-group">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0">Body JSON (POST)</label>
                            <select id="sample-select" class="form-control form-control-sm" style="width:auto">
                                <option value="">Carica esempio…</option>
                                <option value="tipi-documento">tipi-documento</option>
                                <option value="anagrafica">anagrafica</option>
                                <option value="articoli">articoli</option>
                                <option value="documenti">documenti</option>
                                <option value="righe">righe</option>
                            </select>
                        </div>
                        <textarea id="req-body" class="form-control" rows="7" spellcheck="false"
                                  placeholder='{"entity":"articoli","records":[{...}]}'></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <button type="button" id="btn-send" class="btn btn-primary">
                            <i class="fas fa-play"></i> Invia richiesta
                        </button>
                        <button type="button" id="btn-send-notoken" class="btn btn-outline-secondary">Senza token</button>
                        <button type="button" id="btn-send-badtoken" class="btn btn-outline-warning">Token errato</button>
                        <button type="button" id="btn-copy-curl" class="btn btn-outline-info float-right">
                            <i class="fas fa-copy"></i> Copia cURL
                        </button>
                    </div>
                    <pre id="curl-preview" class="bg-light border rounded p-2 mb-0"
                         style="white-space:pre-wrap; word-break:break-all; font-size:.8rem; min-height:3.5rem;"></pre>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-reply"></i> Risposta</h3>
                    <div class="card-tools">
                        <span id="resp-status" class="badge badge-secondary">—</span>
                        <span id="resp-time" class="badge badge-light"></span>
                        <span id="resp-size" class="badge badge-light"></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <pre id="resp-body" class="p-3 mb-0"
                         style="min-height:420px; max-height:75vh; overflow:auto; font-size:.85rem; background:#f8f9fa;"></pre>
                </div>
                <div class="card-footer">
                    <button type="button" id="btn-copy-response" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-copy"></i> Copia risposta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-secondary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-book-open"></i> Riferimenti rapidi</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-7">
                    <table class="table table-sm table-striped mb-3">
                        <thead>
                        <tr>
                            <th>Endpoint</th>
                            <th>Metodo</th>
                            <th>Parametri</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr><td><code>api/index</code></td><td>GET</td><td>—</td></tr>
                        <tr><td><code>api/export</code></td><td>GET</td><td><code>entity</code>, <code>filters</code> (JSON), <code>with_righe</code></td></tr>
                        <tr><td><code>api/view</code></td><td>GET</td><td><code>entity</code>, <code>id</code></td></tr>
                        <tr><td><code>api/import</code></td><td>POST</td><td>body: <code>entity</code>, <code>records[]</code></td></tr>
                        <tr><td><code>api/delete</code></td><td>POST</td><td>body: <code>entity</code>, <code>ids[]</code></td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5">
                    <p class="mb-1">
                        <strong>Entità:</strong>
                        <code>tipi-documento</code>, <code>anagrafica</code>, <code>articoli</code>,
                        <code>documenti</code>, <code>righe</code>
                    </p>
                    <p class="mb-1"><strong>Filtri export:</strong> <code>filters={"anno":2026}</code></p>
                    <p class="mb-1"><strong>Documenti:</strong> <code>with_righe=1</code> include le righe</p>
                    <p class="mb-0"><strong>Errori:</strong> 401 token mancante/non valido, 404 record non trovato</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<'JS'
(function () {
    var LS_KEY = 'dashdemo_api_token';
    var DEFAULTS = {
        index: { method: 'GET', params: '', body: '' },
        export: { method: 'GET', params: 'entity=documenti&with_righe=1', body: '' },
        view: { method: 'GET', params: 'entity=documenti&id=1', body: '' },
        import: { method: 'POST', params: '', body: '' },
        delete: { method: 'POST', params: '', body: '{\n  "entity": "righe",\n  "ids": []\n}' }
    };

    var $token = $('#api-token');
    var $method = $('#req-method');
    var $endpoint = $('#req-endpoint');
    var $params = $('#req-params');
    var $body = $('#req-body');

    function now() {
        return (window.performance && performance.now) ? performance.now() : Date.now();
    }

    function tokenValue() {
        return ($token.val() || '').trim();
    }

    function loadStoredToken() {
        try {
            var saved = localStorage.getItem(LS_KEY);
            if (saved) {
                $token.val(saved);
                $('#remember-token').prop('checked', true);
            }
        } catch (e) {}
    }

    function storeToken() {
        try {
            if ($('#remember-token').is(':checked')) {
                localStorage.setItem(LS_KEY, tokenValue());
            } else {
                localStorage.removeItem(LS_KEY);
            }
        } catch (e) {}
    }

    function buildUrl() {
        var base = ApiTestConfig.endpoints[$endpoint.val()] || '';
        var params = ($params.val() || '').trim().replace(/^[?&]+/, '');
        return params ? base + '&' + params : base;
    }

    function buildBody() {
        if ($method.val() !== 'POST') {
            return '';
        }
        return ($body.val() || '').trim();
    }

    function updatePreview() {
        var method = $method.val();
        var url = buildUrl();
        var body = buildBody();
        var token = tokenValue() || '<TOKEN>';
        var curl = 'curl -X ' + method + ' "' + url + '"';
        if (method !== 'OPTIONS') {
            curl += ' -H "Authorization: Bearer ' + token + '"';
        }
        if (body) {
            curl += ' -H "Content-Type: application/json" -d \'' + body.replace(/'/g, "'\\''") + '\'';
        }
        $('#curl-preview').text(curl);
        $('#req-body-group').toggle(method === 'POST');
    }

    function showResponse(status, statusText, text, elapsed) {
        var $badge = $('#resp-status');
        $badge.removeClass('badge-secondary badge-success badge-warning badge-danger badge-info');
        if (status >= 200 && status < 300) {
            $badge.addClass('badge-success');
        } else if (status >= 400 && status < 500) {
            $badge.addClass('badge-warning');
        } else if (status === 0 || status >= 500) {
            $badge.addClass('badge-danger');
        } else {
            $badge.addClass('badge-info');
        }
        $badge.text(status > 0 ? status + ' ' + statusText : statusText);
        $('#resp-time').text(elapsed + ' ms');
        $('#resp-size').text((text ? new Blob([text]).size : 0) + ' byte');

        var pretty = text;
        try {
            pretty = JSON.stringify(JSON.parse(text), null, 2);
        } catch (e) {}
        $('#resp-body').text(pretty || '');
    }

    function sendRequest(mode) {
        var headers = {};
        var body = buildBody();
        var started = now();

        if (mode === 'badtoken') {
            headers.Authorization = 'Bearer token-non-valido';
        } else if (mode !== 'notoken' && tokenValue() !== '') {
            headers.Authorization = 'Bearer ' + tokenValue();
        }

        if (body) {
            headers['Content-Type'] = 'application/json';
        }

        var options = { method: $method.val(), headers: headers };
        if (body) {
            options.body = body;
        }

        $('#resp-body').text('Richiesta in corso...');
        $('#resp-status').removeClass('badge-success badge-warning badge-danger badge-info')
            .addClass('badge-secondary').text('...');
        $('#resp-time, #resp-size').text('');
        $('#btn-send, #btn-send-notoken, #btn-send-badtoken').prop('disabled', true);

        fetch(buildUrl(), options).then(function (response) {
            return response.text().then(function (text) {
                showResponse(response.status, response.statusText, text, Math.round(now() - started));
            });
        }).catch(function (error) {
            showResponse(0, 'Errore di rete', String(error), Math.round(now() - started));
        }).then(function () {
            $('#btn-send, #btn-send-notoken, #btn-send-badtoken').prop('disabled', false);
            if (mode !== 'notoken' && mode !== 'badtoken') {
                storeToken();
            }
        });
    }

    function copyText(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
            return;
        }
        var $ta = $('<textarea>').val(text).appendTo('body');
        $ta[0].select();
        document.execCommand('copy');
        $ta.remove();
    }

    function applyDefaults() {
        var preset = DEFAULTS[$endpoint.val()];
        if (!preset) {
            return;
        }
        $method.val(preset.method);
        $params.val(preset.params);
        $body.val(preset.body);
        updatePreview();
    }

    $(function () {
        loadStoredToken();
        applyDefaults();

        $endpoint.on('change', applyDefaults);
        $method.on('change', updatePreview);
        $params.on('input', updatePreview);
        $body.on('input', updatePreview);
        $token.on('input', updatePreview);

        $('#btn-toggle-token').on('click', function () {
            var hidden = $token.attr('type') === 'password';
            $token.attr('type', hidden ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye', !hidden).toggleClass('fa-eye-slash', hidden);
        });

        $('#btn-forget-token').on('click', function () {
            $token.val('');
            try { localStorage.removeItem(LS_KEY); } catch (e) {}
            updatePreview();
        });

        $('#remember-token').on('change', storeToken);

        $('#btn-send').on('click', function () { sendRequest(); });
        $('#btn-send-notoken').on('click', function () { sendRequest('notoken'); });
        $('#btn-send-badtoken').on('click', function () { sendRequest('badtoken'); });

        $('#btn-copy-curl').on('click', function () { copyText($('#curl-preview').text()); });
        $('#btn-copy-response').on('click', function () { copyText($('#resp-body').text()); });

        $('#sample-select').on('change', function () {
            var key = $(this).val();
            if (!key || !ApiTestConfig.samples[key]) {
                return;
            }
            $method.val('POST');
            $endpoint.val('import');
            $params.val('');
            $body.val(JSON.stringify(ApiTestConfig.samples[key], null, 2));
            updatePreview();
            $(this).val('');
        });
    });
})();
JS
, View::POS_END);
