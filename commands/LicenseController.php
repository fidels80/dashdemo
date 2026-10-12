<?php

namespace app\commands;

use Yii;
use app\components\Licensing;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Gestione licenza e aggiornamenti dell'applicazione (license-server).
 *
 * Uso tipico:
 *   php yii license/status                stato della licenza
 *   php yii license/activate XXXX-XXXX-XXXX-XXXX
 *   php yii license/heartbeat             aggiorna la licenza (heartbeat)
 *   php yii license/check-update          verifica aggiornamenti disponibili
 *   php yii license/download [versione]   scarica l'aggiornamento in runtime/updates
 */
class LicenseController extends Controller
{
    public function actionStatus()
    {
        $s = Licensing::summary();

        Console::output('Licenza: ' . ($s['configured'] ? $s['license_key'] : '(non configurata)'));
        Console::output('Prodotto: ' . ($s['product'] ?: '-') . '   Tier: ' . ($s['tier'] ?: '-'));
        Console::output('Stato: ' . ($s['status'] ?: '-'));
        Console::output('Machine ID: ' . ($s['machine_id'] ?: '-'));
        Console::output('Scadenza: ' . ($s['expires_at'] ?: 'permanente')
            . ($s['days_left'] !== null ? ' (' . $s['days_left'] . ' giorni)' : ''));
        Console::output('Feature: ' . (empty($s['features']) ? '(nessuna)' : implode(', ', $s['features'])));
        Console::output('Ultimo controllo OK: ' . ($s['last_check_ok'] ?: 'mai'));
        if (!empty($s['last_error'])) {
            Console::output(Console::ansiFormat('Ultimo errore: ' . $s['last_error'], [Console::FG_YELLOW]));
        }
        if (!empty($s['update_version'])) {
            Console::output(Console::ansiFormat('Aggiornamento disponibile: v' . $s['update_version'], [Console::FG_GREEN]));
        }

        return ExitCode::OK;
    }

    public function actionActivate($key = null)
    {
        if (empty($key)) {
            Console::output(Console::ansiFormat('Specificare la chiave: php yii license/activate XXXX-XXXX-XXXX-XXXX', [Console::FG_RED]));
            return ExitCode::USAGE;
        }

        $res = Licensing::activate($key);
        $color = $res['ok'] ? Console::FG_GREEN : Console::FG_RED;
        Console::output(Console::ansiFormat($res['message'], [$color]));

        return $res['ok'] ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionHeartbeat()
    {
        $res = Licensing::heartbeat();
        $color = $res['ok'] ? Console::FG_GREEN : Console::FG_YELLOW;
        Console::output(Console::ansiFormat($res['message'], [$color]));

        return $res['ok'] ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionCheckUpdate()
    {
        $res = Licensing::checkUpdate();
        if (!$res['ok']) {
            Console::output(Console::ansiFormat($res['message'], [Console::FG_YELLOW]));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $data = $res['data'];
        if (!empty($data['update_available'])) {
            Console::output(Console::ansiFormat('Aggiornamento disponibile: v' . $data['latest_version'], [Console::FG_GREEN]));
            if (!empty($data['notes'])) {
                Console::output('Note: ' . $data['notes']);
            }
        } else {
            Console::output('Nessun aggiornamento disponibile (versione corrente ' . Licensing::appVersion() . ').');
        }

        return ExitCode::OK;
    }

    public function actionDownload($version = null)
    {
        if (empty($version)) {
            $res = Licensing::checkUpdate();
            if ($res['ok'] && !empty($res['data']['update_available'])) {
                $version = $res['data']['latest_version'];
            }
        }

        if (empty($version)) {
            Console::output(Console::ansiFormat('Nessuna versione da scaricare.', [Console::FG_YELLOW]));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $res = Licensing::downloadUpdate($version);
        $color = $res['ok'] ? Console::FG_GREEN : Console::FG_RED;
        Console::output(Console::ansiFormat($res['message'], [$color]));
        if (!empty($res['path'])) {
            Console::output('File: ' . $res['path']);
            Console::output('Applicare manualmente dopo aver verificato il contenuto.');
        }

        return $res['ok'] ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }
}
