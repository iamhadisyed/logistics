<?php
// DIC configuration
// Register AuthServer services
$container->register(new Auth\OAuth2ServerProvider());
// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    if (!empty($settings['path'])) {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(0, Monolog\Logger::DEBUG, true, true));
    }
    return $logger;
};

// HAL renderer
$container['renderer'] = function ($c) {
    return new RKA\ContentTypeRenderer\HalRenderer();
};

// Database adapter
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];

    $pdo = new PDO($db['dsn'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if (strpos($db['dsn'], 'sqlite') === 0) {
        $pdo->exec('PRAGMA foreign_keys = ON');
    }

    return $pdo;
};

// Error handlers
$container['notFoundHandler'] = function () {
    return new Error\Handler\NotFound();
};
$container['notAllowedHandler'] = function () {
    return new Error\Handler\NotAllowed();
};
$container['errorHandler'] = function () {
    return new Error\Handler\Error();
};
$container['phpErrorHandler'] = function () {
    return new Error\Handler\Error();
};

// Mappers
// Actions
$container[App\Action\HomeAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new App\Action\HomeAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\AddShipmentAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\AddShipmentAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\AddMultiShipmentsAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\AddMultiShipmentsAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\VoidBagParcelAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\VoidBagParcelAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack\Action\GetDriverParcelAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetDriverParcelAction($logger, $renderer);
};
$container[Smarttrack\Action\ValidateMultiShipmentsAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\ValidateMultiShipmentsAction($logger, $renderer);
};

$container[Smarttrack.API_VESION_NAMESPACE.Action\GenerateLabelAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GenerateLabelAction($logger, $renderer);');
    return $call;
};

$container[Smarttrack.API_VESION_NAMESPACE.Action\JDGenerateLabelAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\JDGenerateLabelAction($logger, $renderer);');
    return $call;
};


$container[Smarttrack.API_VESION_NAMESPACE.Action\MultiTrackingAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\MultiTrackingAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetTrackingAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetTrackingAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack\Action\GetTrackingAliBabaAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetTrackingAliBabaAction($logger, $renderer);
};
$container[Smarttrack\Action\GetSTTrackingAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetSTTrackingAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetLabelAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetLabelAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetShipmentAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetShipmentAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\UserServicesAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\UserServicesAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\UserQuotesAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\UserQuotesAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\CreateAccountAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CreateAccountAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\CreateUserAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CreateUserAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\CurrencyConverterAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CurrencyConverterAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetUserShipmentAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserShipmentAction($logger, $renderer);');
    return $call;
//	return new Smarttrack\Action\GetUserShipmentAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\AddScanningAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\AddScanningAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\AddBaggingAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\AddBaggingAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\CreateInboundSkuBagOrderAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CreateInboundSkuBagOrderAction($logger, $renderer);');
    return $call;
};

$container[Smarttrack.API_VESION_NAMESPACE.Action\CreateSkuAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CreateSkuAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\CreateManifestAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\CreateManifestAction($logger, $renderer);');
    return $call;
};
/*
$container[Smarttrack\Action\CreateManifestAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\CreateManifestAction($logger, $renderer);
}; */
$container[Smarttrack.API_VESION_NAMESPACE.Action\VoidLabelAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\VoidLabelAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack\Action\ValidateServiceDimensionAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\ValidateServiceDimensionAction($logger, $renderer);
};
$container[Smarttrack\Action\GetDropOffLocationAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetDropOffLocationAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetUserAccountsAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserAccountsAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetUsersAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetUsersAction($logger, $renderer);');
	return $call;
};
$container[Smarttrack\Action\GetUserAccessabilitiesAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetUserAccessabilitiesAction($logger, $renderer);
};
$container[Smarttrack\Action\GetWarehouseListAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetWarehouseListAction($logger, $renderer);
};
$container[Smarttrack\Action\GetTrackinCodeAction::class] = function ($c) {
	$logger = $c->get('logger');
	$renderer = $c->get('renderer');
	return new Smarttrack\Action\GetTrackinCodeAction($logger, $renderer);
};
$container[App\Action\PingAction::class] = function ($c) {
    $logger = $c->get('logger');
    return new App\Action\PingAction($logger);
};
$container[Smarttrack\Action\GetUserParcelAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new Smarttrack\Action\GetUserParcelAction($logger, $renderer);
};
$container[Smarttrack\Action\CheckDriverAssignAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new Smarttrack\Action\CheckDriverAssignAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\AssignMawbAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\AssignMawbAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\GetMarketplaceOrdersAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\GetMarketplaceOrdersAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\VoidMasterBagAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\VoidMasterBagAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack\Action\CheckDriverAssignAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    return new Smarttrack\Action\CheckDriverAssignAction($logger, $renderer);
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\RestoreLabelAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\RestoreLabelAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\ConsignmentStatusUpdateAction::class] = function ($c) {
    $logger = $c->get('loggeaur');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\ConsignmentStatusUpdateAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\ParcelWeightUpdateAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\ParcelWeightUpdateAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\ImportBoxAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\ImportBoxAction($logger, $renderer);');
    return $call;
};
$container[Smarttrack.API_VESION_NAMESPACE.Action\DispatchMarketplaceOrderAction::class] = function ($c) {
    $logger = $c->get('logger');
    $renderer = $c->get('renderer');
    eval('$call = new Smarttrack'.API_VESION_NAMESPACE.'Action\DispatchMarketplaceOrderAction($logger, $renderer);');
    return $call;
};
//----------------------Smart Track Dependency Start-----------------------------//
// @codingStandardsIgnoreEnd
