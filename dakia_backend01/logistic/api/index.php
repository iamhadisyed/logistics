<?php
require __DIR__ . '/vendor/autoload.php';
if(strstr($_SERVER['REQUEST_URI'],"api/v2")){
    define('API_VERSION', 'v2/');
    define('API_VESION_NAMESPACE', '\\V2\\');
}else{
    define('API_VERSION', '');
    define('API_VESION_NAMESPACE', '\\');
}
// Instantiate the app
$settings = require __DIR__ . '/src/settings.php';
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies
$container = $app->getContainer();
require __DIR__ . '/src/dependencies.php';

// Register middleware
require __DIR__ . '/src/middleware.php';

// Register routes
require __DIR__ . '/src/routes.php';
// SmartTransformer
//require __DIR__ . '/src/Smarttrack/routes.php';
// Run app
$app->run();
