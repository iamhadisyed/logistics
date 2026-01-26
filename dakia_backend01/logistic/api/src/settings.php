<?php
require_once __DIR__ . '/../../includes/settings/config.inc.php';
//require_once __DIR__ . '/../../includes/general/carrierservice.class.php';
require_once __DIR__ . '/../../includes/3rdparty/tcpdf/tcpdf.php';
require_once __DIR__ . '/../../includes/mapping/iaddress.class.php';
foreach (glob(__DIR__ . '/../../includes/mapping/*.php') as $filename){
    include_once $filename;
}
foreach (glob(__DIR__ . '/../../includes/labels/*.php') as $labelFilename){
    include_once $labelFilename;
}

return [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false,

    // OAuth 2 configuration
    'oauth2' => [
        'use_jwt_bearer_tokens' => false,
    ],

    // Database adapter
    'db' => [
        'dsn' => getenv('DB_DSN') ?: 'mysql:host='.SETTING_DB_SERVER.';dbname='.SETTING_DB_DATABASE.';charset=utf8',
        'user' => SETTING_DB_USER,
        'pass' => SETTING_DB_PASSWORD,
    ],

    // Monolog
    'logger' => [
        'name' => 'slim-smarttrack-api',
        // uncomment 'path' setting to log to file rather than the error log
        // 'path' => __DIR__ . '/../var/app.log',
    ],
];
