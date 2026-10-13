<?php

namespace app\components;

use Yii;
use app\models\DashLicenza;
use app\models\DashLicenzaLog;

/**
 * Client del license-server: attivazione, heartbeat, controllo aggiornamenti
 * e verifica delle feature (moduli) abilitate dalla licenza.
 *
 * Lo stato e' memorizzato in `dash_licenza` (riga singola). Se il server non
 * e' raggiungibile la licenza resta valida per `params['licenseGraceDays']`
 * giorni dall'ultimo controllo andato a buon fine; oltre il periodo di
 * tolleranza i moduli non presenti in licenza vengono bloccati.
 */
class Licensing
{
    /** Mappa controller -> codice feature (licenza). */
    protected static $controllerFeature = [
        'mglotto' => 'lotti',
        'mgmatricola' => 'matricole',
        'mgdocumento' => 'documenti',
        'agenti' => 'agenti',
    ];

    public static function serverUrl()
    {
        return rtrim((string) (Yii::$app->params['licenseServerUrl'] ?? ''), '?&');
    }

    public static function product()
    {
        return (string) (Yii::$app->params['licenseProduct'] ?? 'dashdemo');
    }

    public static function appVersion()
    {
        return (string) (Yii::$app->params['appVersion'] ?? '0.0.1');
    }

    public static function graceDays()
    {
        return (int) (Yii::$app->params['licenseGraceDays'] ?? 14);
    }

    public static function enforced()
    {
        return (bool) (Yii::$app->params['licenseEnforce'] ?? true);
    }

    /**
     * Funzione di blocco da usare nel beforeAction globale.
     */
    public static function isBlocked($controllerId, $actionId)
    {
        $feature = self::featureForRoute($controllerId, $actionId);
        if ($feature === null) {
            return false;
        }
        return !self::hasFeature($feature);
    }

    /**
     * Codice feature associato a una rotta, oppure null se non licenziata.
     */
    public static function featureForRoute($controllerId, $actionId = null, $params = [])
    {
        $controllerId = strtolower((string) $controllerId);
        $actionId = strtolower((string) $actionId);

        if ($controllerId === 'mgdocumento') {
            if ($actionId === 'crea-lotto') {
                return 'lotti';
            }
            if ($actionId === 'crea-matricola') {
                return 'matricole';
            }
        }

        if (isset(self::$controllerFeature[$controllerId])) {
            return self::$controllerFeature[$controllerId];
        }

        if ($controllerId === 'mgattributo') {
            $tipo = strtolower((string) ($params['tipo'] ?? ''));
            if ($tipo === '') {
                $req = (Yii::$app instanceof \yii\web\Application) ? Yii::$app->request : null;
                $tipo = strtolower((string) ($req ? $req->get('tipo', '') : ''));
            }
            if ($tipo === 'taglia') {
                return 'taglie';
            }
            if ($tipo === 'colore') {
                return 'colori';
            }
            if ($tipo === 'tessuto') {
                return 'tessuti';
            }
        }

        return null;
    }

    /**
     * Codice feature a partire da un url di menu (es. mgattributo/index?tipo=taglia).
     */
    public static function featureForMenuUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $raw = $url;
        $params = [];
        $pos = strpos($raw, '?');
        if ($pos !== false) {
            parse_str(substr($raw, $pos + 1), $params);
            $raw = substr($raw, 0, $pos);
        }

        $parts = explode('/', ltrim($raw, '/'));
        return self::featureForRoute($parts[0] ?? '', $parts[1] ?? null, $params);
    }

    /**
     * True se la feature e' abilitata dalla licenza (o in periodo di tolleranza).
     */
    public static function hasFeature($code)
    {
        if (!self::enforced()) {
            return true;
        }

        self::resolve();

        if (self::$readOnly) {
            return true;
        }

        if (!self::$withinGrace) {
            return false;
        }

        if (empty(self::$featuresCache)) {
            return true;
        }

        return in_array($code, self::$featuresCache, true);
    }

    /**
     * True se la licenza e' scaduta o revocata: applicazione in sola lettura.
     */
    public static function isReadOnly()
    {
        if (!self::enforced()) {
            return false;
        }

        self::resolve();
        return self::$readOnly;
    }

    /**
     * Rotte consentite anche in sola lettura (riattivazione licenza e accesso).
     */
    public static function isReadOnlyAllowed($controllerId, $actionId)
    {
        $controllerId = strtolower((string) $controllerId);
        $actionId = strtolower((string) $actionId);

        if ($controllerId === 'licenza') {
            return true;
        }

        if ($controllerId === 'site') {
            if (in_array($actionId, ['login', 'logout', 'error'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True se la rotta scrive dati: azione create/update/delete o metodo non-GET.
     */
    public static function isWriteRoute($controllerId, $actionId)
    {
        $actionId = strtolower((string) $actionId);
        if (in_array($actionId, ['create', 'update', 'delete'], true)) {
            return true;
        }

        $request = (Yii::$app instanceof \yii\web\Application) ? Yii::$app->request : null;
        if ($request === null) {
            return false;
        }

        if (!$request->isGet && !$request->isHead) {
            return true;
        }

        return false;
    }

    /** Cache dello stato licenza nella singola richiesta. */
    private static $resolved = false;
    private static $withinGrace = false;
    private static $readOnly = false;
    private static $featuresCache = [];

    private static function resolve()
    {
        if (self::$resolved) {
            return;
        }
        self::$resolved = true;

        $state = DashLicenza::current();
        $base = $state->last_check_ok ?: $state->first_run_at;
        self::$withinGrace = $base && (time() - strtotime($base)) < (self::graceDays() * 86400);
        self::$featuresCache = $state->getFeatureArray();

        self::$readOnly = false;
        if ($state->status === 'error') {
            self::$readOnly = true;
        }
        if (!empty($state->expires_at)) {
            $expTs = strtotime($state->expires_at);
            if ($expTs !== false && $expTs < time()) {
                self::$readOnly = true;
            }
        }
    }

    public static function isConfigured()
    {
        $state = DashLicenza::current();
        return !empty($state->license_key);
    }

    /**
     * Aggiorna la licenza in modo trasparente se l'ultimo controllo e' piu'
     * vecchio di $hours ore. Best-effort: non solleva eccezioni.
     */
    public static function autoRefresh($hours = 12)
    {
        try {
            if (!self::isConfigured()) {
                return;
            }
            $state = DashLicenza::current();
            if (!empty($state->last_check_at)
                && (time() - strtotime($state->last_check_at)) < ($hours * 3600)) {
                return;
            }
            self::heartbeat();
            self::checkUpdate();
        } catch (\Throwable $e) {
            Yii::warning('autoRefresh licenza: ' . $e->getMessage(), 'licensing');
        }
    }

    public static function daysLeft()
    {
        $state = DashLicenza::current();
        if (empty($state->expires_at)) {
            return null;
        }
        $diff = strtotime($state->expires_at) - time();
        return (int) floor($diff / 86400);
    }

    /**
     * Riepilogo per la pagina /licenza.
     */
    public static function summary()
    {
        $state = DashLicenza::current();
        return [
            'license_key' => $state->license_key,
            'machine_id' => $state->machine_id,
            'product' => $state->product,
            'tier' => $state->tier,
            'status' => $state->status,
            'features' => $state->getFeatureArray(),
            'expires_at' => $state->expires_at,
            'days_left' => self::daysLeft(),
            'last_check_ok' => $state->last_check_ok,
            'last_check_at' => $state->last_check_at,
            'last_error' => $state->last_error,
            'update_version' => $state->update_version,
            'configured' => !empty($state->license_key),
            'enforced' => self::enforced(),
        ];
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    //  Machine ID
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public static function machineId()
    {
        $state = DashLicenza::current();
        if (!empty($state->machine_id)) {
            return $state->machine_id;
        }

        $host = php_uname('n') ?: (getenv('COMPUTERNAME') ?: 'unknown');
        $mac = self::firstMacAddress();
        $id = strtoupper(md5($host . '|' . $mac . '|' . php_uname('s') . '|' . php_uname('m')));

        $state->machine_id = substr($id, 0, 32);
        $state->updated_at = date('Ymd H:i:s');
        $state->save(false);

        return $state->machine_id;
    }

    protected static function firstMacAddress()
    {
        try {
            if (stripos(PHP_OS, 'WIN') === 0) {
                $out = @shell_exec('getmac');
                if ($out && preg_match('/([0-9A-F]{2}-){5}[0-9A-F]{2}/i', $out, $m)) {
                    return $m[0];
                }
            } else {
                foreach ((array) @glob('/sys/class/net/*/address') as $file) {
                    $mac = trim((string) @file_get_contents($file));
                    if ($mac && $mac !== '00:00:00:00:00:00') {
                        return $mac;
                    }
                }
            }
        } catch (\Exception $e) {
        }
        return 'no-mac';
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    //  API license-server
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    /**
     * Normalizza una data per SQL Server (formato ISO compatto YYYYMMDD:
     * non ambiguo rispetto al DATEFORMAT italiano).
     */
    public static function toSqlDate($value)
    {
        if (empty($value)) {
            return null;
        }
        $ts = strtotime($value);
        return $ts ? date('Ymd H:i:s', $ts) : null;
    }

    public static function apiUrl($route, $params = [])
    {
        // Usa URL pulite (es. .../web/api/license-api/activate).
        $base = preg_replace('#/index\.php$#i', '', rtrim(self::serverUrl(), '/'));
        $url = $base . '/' . ltrim($route, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    public static function activate($key)
    {
        $key = trim((string) $key);
        if ($key === '') {
            return ['ok' => false, 'message' => 'Chiave vuota'];
        }

        $resp = self::http(self::apiUrl('api/license-api/activate'), 'POST', [
            'license_key' => $key,
            'machine_id' => self::machineId(),
            'product' => self::product(),
            'hostname' => php_uname('n'),
            'version' => self::appVersion(),
            'php_version' => substr(PHP_VERSION, 0, 20),
            'os_info' => php_uname('s') . ' ' . php_uname('r'),
        ]);

        if (!$resp['ok']) {
            self::recordError($key, 'Server non raggiungibile: ' . $resp['error']);
            return ['ok' => false, 'message' => 'Server non raggiungibile: ' . $resp['error']];
        }

        $data = $resp['data'];
        if (empty($data['ok'])) {
            $msg = $data['error'] ?? 'Attivazione rifiutata';
            self::recordError($key, $msg);
            return ['ok' => false, 'message' => $msg];
        }

        $now = date('Ymd H:i:s');
        $state = DashLicenza::current();
        $state->license_key = $data['license_key'] ?? $key;
        $state->product = $data['product'] ?? self::product();
        $state->tier = $data['tier'] ?? null;
        $state->features = json_encode($data['features'] ?? [], JSON_UNESCAPED_UNICODE);
        $state->expires_at = self::toSqlDate($data['expires_at'] ?? null);
        $state->status = 'active';
        $state->signature = $data['signature'] ?? null;
        $state->activated_at = $now;
        $state->last_check_at = $now;
        $state->last_check_ok = $now;
        $state->last_error = null;
        $state->update_version = null;
        $state->updated_at = $now;
        $state->save(false);

        DashLicenzaLog::log('activate_ok', ['tier' => $state->tier, 'product' => $state->product]);

        return ['ok' => true, 'message' => 'Licenza attivata', 'data' => $data];
    }

    public static function heartbeat()
    {
        $state = DashLicenza::current();
        if (empty($state->license_key)) {
            return ['ok' => false, 'message' => 'Nessuna licenza configurata'];
        }

        $resp = self::http(self::apiUrl('api/license-api/heartbeat'), 'POST', [
            'license_key' => $state->license_key,
            'machine_id' => self::machineId(),
            'product' => self::product(),
            'version' => self::appVersion(),
            'hostname' => php_uname('n'),
            'php_version' => substr(PHP_VERSION, 0, 20),
            'os_info' => php_uname('s') . ' ' . php_uname('r'),
        ]);

        $now = date('Ymd H:i:s');
        $state->last_check_at = $now;

        if (!$resp['ok']) {
            $state->last_error = 'Server non raggiungibile: ' . $resp['error'];
            $state->updated_at = $now;
            $state->save(false);
            DashLicenzaLog::log('heartbeat_failed', $state->last_error);
            return ['ok' => false, 'message' => $state->last_error];
        }

        $data = $resp['data'];
        if (empty($data['ok'])) {
            $state->status = 'error';
            $state->last_error = $data['error'] ?? 'Heartbeat rifiutato';
            if (isset($data['features']) && is_array($data['features'])) {
                $state->features = json_encode($data['features'], JSON_UNESCAPED_UNICODE);
            }
            $state->updated_at = $now;
            $state->save(false);
            DashLicenzaLog::log('heartbeat_failed', $state->last_error);
            return ['ok' => false, 'message' => $state->last_error];
        }

        $state->status = 'active';
        $state->tier = $data['tier'] ?? $state->tier;
        if (isset($data['features'])) {
            $state->features = json_encode($data['features'], JSON_UNESCAPED_UNICODE);
        }
        $state->expires_at = self::toSqlDate($data['expires_at'] ?? null) ?? $state->expires_at;
        $state->last_check_ok = $now;
        $state->last_error = null;
        $state->updated_at = $now;
        $state->save(false);

        DashLicenzaLog::log('heartbeat_ok', ['tier' => $state->tier]);
        return ['ok' => true, 'message' => 'Licenza aggiornata'];
    }

    public static function checkUpdate()
    {
        $state = DashLicenza::current();
        $resp = self::http(self::apiUrl('api/license-api/check-update', [
            'version' => self::appVersion(),
            'tier' => $state->tier ?: 'standard',
            'product' => self::product(),
            'machine_id' => self::machineId(),
        ]));

        if (!$resp['ok']) {
            return ['ok' => false, 'message' => 'Server non raggiungibile: ' . $resp['error']];
        }

        $data = $resp['data'];
        if (empty($data['ok'])) {
            return ['ok' => false, 'message' => $data['error'] ?? 'Errore controllo aggiornamenti'];
        }

        if (!empty($data['update_available'])) {
            $state->update_version = $data['latest_version'] ?? null;
            $state->updated_at = date('Ymd H:i:s');
            $state->save(false);
            DashLicenzaLog::log('update_available', ['version' => $data['latest_version'] ?? null]);
        } else {
            $state->update_version = null;
            $state->updated_at = date('Ymd H:i:s');
            $state->save(false);
        }

        return ['ok' => true, 'data' => $data];
    }

    /**
     * Scarica l'aggiornamento in @runtime/updates (applicazione manuale).
     *
     * @return array ['ok'=>bool,'message'=>string,'path'=>string|null]
     */
    public static function downloadUpdate($version, $tier = null)
    {
        $version = (string) $version;
        if ($version === '') {
            return ['ok' => false, 'message' => 'Versione mancante'];
        }

        $dir = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'updates';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $target = $dir . DIRECTORY_SEPARATOR . 'v' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $version) . '.zip';
        $url = self::apiUrl('api/license-api/download', [
            'version' => $version,
            'tier' => $tier ?: (DashLicenza::current()->tier ?: 'standard'),
            'product' => self::product(),
        ]);

        if (!self::httpDownload($url, $target)) {
            return ['ok' => false, 'message' => 'Download non riuscito'];
        }

        DashLicenzaLog::log('update_downloaded', ['version' => $version, 'path' => $target]);
        return ['ok' => true, 'message' => 'Aggiornamento scaricato', 'path' => $target];
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    //  HTTP internals
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private static function http($url, $method = 'GET', $data = null, $timeout = 15)
    {
        if (!function_exists('curl_init')) {
            return self::httpStream($url, $method, $data, $timeout);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $body = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['ok' => false, 'error' => $err ?: 'connessione fallita', 'data' => null];
        }

        return ['ok' => true, 'error' => null, 'data' => json_decode($body, true), 'raw' => $body];
    }

    private static function httpStream($url, $method, $data, $timeout)
    {
        $opts = [
            'http' => [
                'method' => $method,
                'timeout' => $timeout,
                'ignore_errors' => true,
                'header' => "Content-Type: application/json\r\n",
            ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ];
        if ($method === 'POST' && $data !== null) {
            $opts['http']['content'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        }

        $body = @file_get_contents($url, false, stream_context_create($opts));
        if ($body === false) {
            return ['ok' => false, 'error' => 'connessione fallita', 'data' => null];
        }
        return ['ok' => true, 'error' => null, 'data' => json_decode($body, true), 'raw' => $body];
    }

    private static function httpDownload($url, $target)
    {
        if (function_exists('curl_init')) {
            $fp = @fopen($target, 'w');
            if (!$fp) {
                return false;
            }
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_FILE => $fp,
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $ok = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);
            if (!$ok || $code >= 400) {
                @unlink($target);
                return false;
            }
            return true;
        }

        $content = @file_get_contents($url);
        if ($content === false) {
            return false;
        }
        return @file_put_contents($target, $content) !== false;
    }

    private static function recordError($key, $message)
    {
        $state = DashLicenza::current();
        $state->license_key = $key ?: $state->license_key;
        $state->status = 'error';
        $state->last_check_at = date('Ymd H:i:s');
        $state->last_error = substr($message, 0, 500);
        $state->updated_at = date('Ymd H:i:s');
        $state->save(false);

        DashLicenzaLog::log('activate_failed', $message);
    }
}
