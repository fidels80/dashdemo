<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;
/**
 * Asset bundle per centralizzare DataTables e le sue estensioni.
 */
class DataTablesAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
        'https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css',
        'https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css',
        // --- SELECT2 ---
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        'https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css',

        // --- TIPPY.JS (TOOLTIPS) ---
        'https://unpkg.com/tippy.js@6/dist/tippy.css',
        ];

    public $js = [
        'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
        'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
        'https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js',
        'https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js',
        'https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js',
        'https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js',
   // --- FULLCALENDAR 6 ---
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js',
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/it.global.min.js',
        //'https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/it.global.min.js',
        // --- POPPER & TIPPY ---
        'https://unpkg.com/@popperjs/core@2',
        'https://unpkg.com/tippy.js@6',

        // --- SELECT2 ---
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        //sweetalert2
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
    // Forza il caricamento di alcuni script nell'HEAD se necessario (come FullCalendar)
    public $jsOptions = [
        'position' => View::POS_END, // POS_END è meglio per la velocità, ma FullCalendar è globale quindi va bene ovunque
    ];
}
