<?php

namespace app\components;

use Yii;
use yii\web\View;
use hail812\adminlte3\assets\PluginAsset;

/**
 * SweetAlert2 come standard di progetto per conferme e avvisi.
 *
 * - Carica il file web/js/dash-swal.js che intercetta qualsiasi elemento
 *   [data-confirm] mostrando la conferma con Swal (al posto del confirm()
 *   nativo di Yii) e mostra i messaggi flash.
 * - I messaggi flash di sessione (success/error/warning/info) vengono passati
 *   alla pagina tramite window.dashSwalFlashes.
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
     * Registra l'asset di SweetAlert2, il gestore conferme e i flash.
     */
    public static function registerAssets(View $view)
    {
        PluginAsset::register($view)->add(['sweetalert2']);

        $view->registerJsFile(
            Yii::$app->request->baseUrl . '/js/dash-swal.js',
            ['depends' => [PluginAsset::class]]
        );

        $flashes = json_encode(self::flashes(),
            JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG);
        $view->registerJs('window.dashSwalFlashes = ' . $flashes . ';', View::POS_HEAD);
    }
}
