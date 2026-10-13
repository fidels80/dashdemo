<?php

/*return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
];*/

return [
    'adminEmail' => 'supporto@programma2000.com',
     'senderEmail' =>
     'marco.cardinale@ilvbc.it',
    // 'pubblicazioni@vivenda.it',
    'senderName' => 'DASHBOARD DashDemo',
    'supportEmail'=> 'marco.cardinale@ilvbc.it',
    'hail812/yii2-adminlte3' => [
        'pluginMap' => [
            'sweetalert2' => [
                'css' => 'sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
                'js' => 'sweetalert2/sweetalert2.min.js'
            ],
            'toastr' => [
                'css' => ['toastr/toastr.min.css'],
                'js' => ['toastr/toastr.min.js']
            ],
        ]
        ],
        //'bsDependencyEnabled' => false,
      'user.passwordResetTokenExpire' => 3600,
        'bsVersion' => '4.x', 
        'smartsupp' => 'de3e18ac23d0b2ca7ac25aaf8ea058bfb6d85025',
     //   'icon-framework' => \kartik\icons\Icon::FAS,

    // Utenti con accesso completo (bypass ACL e visibilita' totale del menu).
    // Non sono modificabili dalla gestione permessi.
    'superEmails' => [
        'cardinale.marco@gmail.com',
        'marco.cardinale@ilvbc.it',
    ],

    // ------------------------------------------------------------------
    // Sistema di licenza e aggiornamenti (license-server).
    // ------------------------------------------------------------------
    // URL base del license-server (senza index.php: usa URL pulite /web/api/...).
    'licenseServerUrl' => 'https://fidels.synology.me/license-server/web',
    // Prodotto licenziato richiesto a questo server.
    'licenseProduct' => 'dashdemo',
    // Segreto HMAC condiviso col license-server (params.licenseSecret del server).
    'licenseSecret' => 'CHANGE_ME_in_production_use_random_64_chars',
    // Versione corrente dell'applicazione (confrontata con gli artifact).
    'appVersion' => '0.0.1',
    // Giorni di tolleranza senza server raggiungibile/licenza attiva.
    'licenseGraceDays' => 14,
    // Se false disattiva completamente il blocco moduli (utile in sviluppo).
    'licenseEnforce' => true,
    ];
