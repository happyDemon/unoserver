<?php

return [
    'default_server' => 'default',

    'servers' => [
        'default' => [
            'command' => base_path('bin/unoconvert'),
        ],
        'remote' => [
            // Defaults should be ok when running the server on the same machine.
            'interface' => env('UNOSERVER_HOST', '127.0.0.1'),
            'port' => env('UNOSERVER_PORT', 2002)
        ]
    ],

    'executables' => [
        // Only required for generating the unoserver wrapper script
        'libreoffice' => env('UNSORSERVER_EXEC_LIBRE'),

        // These 2 don't have to be set
        'unoserver' => env('UNSORSERVER_EXEC_SERVER'),
        'unoconvert' => env('UNSORSERVER_EXEC_CONVERT'),
    ],
];
