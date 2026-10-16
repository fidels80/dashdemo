<?php

namespace app\components;

use Yii;
use yii\web\View;

/**
 * SweetAlert2 come standard di progetto per conferme e avvisi.
 *
 * - Carica il plugin sweetalert2 pubblicandolo direttamente (l'asset bundle
 *   di AdminLTE puo' avere una copia pubblicata obsoleta che non lo include).
 * - Carica web/js/dash-swal.js che intercetta qualsiasi elemento
 *   [data-confirm] e sostituisce yii.confirm, mostrando la conferma con Swal.
 * - Passa i messaggi flash (success/error/warning/info) alla pagina tramite
 *   window.dashSwalFlashes, mostrati come popup Swal.
 *
 * Da chiamare una sola volta per pagina, nei layout (main.php / portale.php).
 */
class Swal
{
    /**
     * Messaggi flash di sessione da mostrare come popup Swal.
     *
     * @return array
     */
    public static function flashes()
    {
        $titoli = [
            'success' => 'Operazione completata',
            'error' => 'Errore',
            'warning' => 'Attenzione',
            'info' => 'Informazione',
        ];
        $out = [];
        foreach (['success', 'error', 'warning', 'info'] as $key) {
            if (Yii::$app->session->hasFlash($key)) {
                $out[] = [
                    'icon' => $key,
                    'title' => $titoli[$key],
                    'text' => (string) Yii::$app->session->getFlash($key),
                ];
            }
        }
        return $out;
    }

    /**
     * Registra SweetAlert2, il gestore conferme e i flash.
     */
    public static function registerAssets(View $view)
    {
        $am = $view->getAssetManager();

        // sweetalert2 pubblicato direttamente dal vendor.
        $dir = $am->publish('@vendor/almasaeed2010/adminlte/plugins/sweetalert2');
        if ($dir !== false) {
            $view->registerJsFile($dir[1] . '/sweetalert2.min.js', ['position' => View::POS_END]);
        }
        $tema = $am->publish('@vendor/almasaeed2010/adminlte/plugins/sweetalert2-theme-bootstrap-4');
        if ($tema !== false) {
            $view->registerCssFile($tema[1] . '/bootstrap-4.min.css');
        }

        // Gestore conferme/flash (dopo sweetalert2).
        $view->registerJsFile(
            Yii::$app->request->baseUrl . '/js/dash-swal.js',
            ['position' => View::POS_END]
        );

        $flashes = json_encode(self::flashes(),
            JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
        $view->registerJs('window.dashSwalFlashes = ' . $flashes . ';', View::POS_HEAD);
    }
}
