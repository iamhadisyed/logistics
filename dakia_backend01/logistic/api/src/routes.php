<?php
// Routes
$apiVersion = "";
$app->get('/', App\Action\HomeAction::class);
$app->get('/ping', App\Action\PingAction::class);
// Routes that need a valid user token
$app->group('', function () use ($app) {
	$app->post('/authorise', Auth\Action\AuthoriseAction::class);

    eval('$namespaceGetLabelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetLabelAction::class;');
	$app->post('/'.API_VERSION.'get-label', $namespaceGetLabelAction);

    eval('$namespaceAddShipment = Smarttrack'.API_VESION_NAMESPACE.'Action\AddShipmentAction::class;');
	$app->post('/'.API_VERSION.'add-shipment',$namespaceAddShipment);

    eval('$namespaceMultiShipments = Smarttrack'.API_VESION_NAMESPACE.'Action\AddMultiShipmentsAction::class;');
    $app->post('/'.API_VERSION.'add-multi-shipments',$namespaceMultiShipments);

    eval('$namespaceGetDriverParcelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetDriverParcelAction::class;');
    $app->post('/'.API_VERSION.'get-driver-parcel',$namespaceGetDriverParcelAction);

    eval('$namespaceAssignMawb = Smarttrack'.API_VESION_NAMESPACE.'Action\AssignMawbAction::class;');
    $app->post('/'.API_VERSION.'assign-mawb',$namespaceAssignMawb);

    eval('$namespaceVoidMasterBag = Smarttrack'.API_VESION_NAMESPACE.'Action\VoidMasterBagAction::class;');
    $app->post('/'.API_VERSION.'void-master-bag',$namespaceVoidMasterBag);


	$app->post('/validate-multi-shipments',Smarttrack\Action\ValidateMultiShipmentsAction::class);
    $app->post('/validate-service-dimensions',Smarttrack\Action\ValidateServiceDimensionAction::class);

    eval('$namespaceGenerateLabel = Smarttrack'.API_VESION_NAMESPACE.'Action\GenerateLabelAction::class;');
	$app->post('/'.API_VERSION.'generate-label', $namespaceGenerateLabel);// done

    eval('$namespaceUserServices = Smarttrack'.API_VESION_NAMESPACE.'Action\UserServicesAction::class;');
	$app->post('/'.API_VERSION.'get-services',$namespaceUserServices);

	eval('$namespaceUserServices = Smarttrack'.API_VESION_NAMESPACE.'Action\UserServicesAction::class;');
	$app->post('/'.API_VERSION.'services',$namespaceUserServices);

    eval('$namespaceQuotesAction = Smarttrack'.API_VESION_NAMESPACE.'Action\UserQuotesAction::class;');
	$app->post('/'.API_VERSION.'get-quotes',$namespaceQuotesAction);// done

    eval('$namespaceCreateAccountAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CreateAccountAction::class;');
	$app->post('/'.API_VERSION.'create-account',$namespaceCreateAccountAction);// done

    eval('$namespaceUserAccountsAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserAccountsAction::class;');
	$app->get('/'.API_VERSION.'get-account',$namespaceUserAccountsAction);

    eval('$namespaceUsersAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetUsersAction::class;');
    $app->get('/'.API_VERSION.'get-users',$namespaceUsersAction);

	$app->get('/get-accessabilities',Smarttrack\Action\GetUserAccessabilitiesAction::class);
	$app->get('/get-warehouse-list',Smarttrack\Action\GetWarehouseListAction::class);

    eval('$namespaceCreateUserAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CreateUserAction::class;');
	$app->post('/'.API_VERSION.'add-user',$namespaceCreateUserAction);

//	$app->post('/get-shipments',Smarttrack\Action\GetUserShipmentAction::class);
    eval('$namespaceGetUserShipmentAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserShipmentAction::class;');
    $app->post('/'.API_VERSION.'get-shipments', $namespaceGetUserShipmentAction);

    eval('$namespaceGetMarketplaceOrdersAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetMarketplaceOrdersAction::class;');
    $app->post('/'.API_VERSION.'get-marketplace-orders', $namespaceGetMarketplaceOrdersAction);

	$app->post('/get-user-parcel',Smarttrack\Action\GetUserParcelAction::class);

    eval('$namespaceGetSingleUserAccountAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserAccountsAction::class;');
	$app->get('/'.API_VERSION.'get-account-info/{account_number}',$namespaceGetSingleUserAccountAction);

    eval('$namespaceGetSingleUserAccountAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetUserAccountsAction::class;');
	$app->get('/'.API_VERSION.'get-account-info',$namespaceGetSingleUserAccountAction);
    // Scanning API
    eval('$namespaceGetAddScanningAction = Smarttrack'.API_VESION_NAMESPACE.'Action\AddScanningAction::class;');
    $app->post('/'.API_VERSION.'scan-parcel', $namespaceGetAddScanningAction);
    // Create Bagging API
    eval('$namespaceGetAddBaggingActionAction = Smarttrack'.API_VESION_NAMESPACE.'Action\AddBaggingAction::class;');
    $app->post('/'.API_VERSION.'create-bag', $namespaceGetAddBaggingActionAction);

    // Create Sku API
    eval('$namespaceCreateSkuAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CreateSkuAction::class;');
    $app->post('/'.API_VERSION.'create-sku', $namespaceCreateSkuAction);// done

    // Create InboudSkuBagOrder API
    eval('$namespaceCreateInboundSkuBagOrderAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CreateInboundSkuBagOrderAction::class;');
    $app->post('/'.API_VERSION.'create-sku-bag', $namespaceCreateInboundSkuBagOrderAction);// done

    // get shipment API
    eval('$namespaceGetShipmentAction = Smarttrack'.API_VESION_NAMESPACE.'Action\GetShipmentAction::class;');
    $app->post('/'.API_VERSION.'get-shipment', $namespaceGetShipmentAction);

    // remove parel from bag
    eval('$namespaceVoidBagParcelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\VoidBagParcelAction::class;');
    $app->post('/'.API_VERSION.'void-bag-parcel', $namespaceVoidBagParcelAction);


    // New routes for ship Station
    $app->post('/GetRates',Smarttrack\Action\UserQuotesAction::class);
    $app->post('/CreateLabel',Smarttrack\Action\GenerateLabelAction::class);
    $app->post('/Register',Smarttrack\Action\CreateAccountAction::class);
    $app->post('/Track', Smarttrack\Action\GetSTTrackingAction::class);

    eval('$namespaceLabelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\VoidLabelAction::class;');
    $app->post('/'.API_VERSION.'VoidLabels', $namespaceLabelAction);

    eval('$namespaceLabelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\VoidLabelAction::class;');
    $app->post('/'.API_VERSION.'void-labels', $namespaceLabelAction);

    $app->post('/CreateManifest', Smarttrack\Action\CreateManifestAction::class);
    
    
    
    eval('$namespaceCreateManifestAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CreateManifestAction::class;');
    $app->post('/'.API_VERSION.'create-manifest', $namespaceCreateManifestAction);
    
    
    //check driver assign to shipnment
    $app->post('/check-driver-assign',Smarttrack\Action\CheckDriverAssignAction::class);
        
    //New API
    //$app->post('/Track', create-bagSmarttrack\Action\TrackingAction::class);
        
    $app->post('/get-drop-off-locations',Smarttrack\Action\GetDropOffLocationAction::class);

    eval('$namespaceImportBoxAction = Smarttrack'.API_VESION_NAMESPACE.'Action\ImportBoxAction::class;');
    $app->post('/'.API_VERSION.'import-box', $namespaceImportBoxAction);// done


    eval('$namespaceRestoreLabelAction = Smarttrack'.API_VESION_NAMESPACE.'Action\RestoreLabelAction::class;');
    $app->post('/'.API_VERSION.'restore-label', $namespaceRestoreLabelAction);// done

    eval('$namespaceConsignmentStatusUpdateAction = Smarttrack'.API_VESION_NAMESPACE.'Action\ConsignmentStatusUpdateAction::class;');
    $app->post('/'.API_VERSION.'update-status', $namespaceConsignmentStatusUpdateAction);// done

    eval('$namespaceParcelWeightUpdateAction = Smarttrack'.API_VESION_NAMESPACE.'Action\ParcelWeightUpdateAction::class;');
    $app->post('/'.API_VERSION.'update-weight', $namespaceParcelWeightUpdateAction);// done

    eval('$namespaceCurrencyConverterAction = Smarttrack'.API_VESION_NAMESPACE.'Action\CurrencyConverterAction::class;');
    $app->post('/'.API_VERSION.'currency-converter', $namespaceCurrencyConverterAction);// done

    eval('$namespaceDispatchMarketplaceOrderAction = Smarttrack'.API_VESION_NAMESPACE.'Action\DispatchMarketplaceOrderAction::class;');
    $app->post('/'.API_VERSION.'dispatch-marketplace-order', $namespaceDispatchMarketplaceOrderAction);// done
        
})->add(Auth\GuardMiddleware::class);


eval('$namespaceJDGenerateLabel = Smarttrack'.API_VESION_NAMESPACE.'Action\JDGenerateLabelAction::class;');
$app->post('/'.API_VERSION.'jd-generate-label', $namespaceJDGenerateLabel);// done

eval('$namespaceTracking = Smarttrack'.API_VESION_NAMESPACE.'Action\GetTrackingAction::class;');
$app->get('/'.API_VERSION.'get-tracking/{tracking_id}', $namespaceTracking);// done

eval('$namespaceStTracking = Smarttrack'.API_VESION_NAMESPACE.'Action\GetTrackingAction::class;');
$app->get('/'.API_VERSION.'get-tracking', $namespaceStTracking);// done

eval('$namespaceMultiTracking = Smarttrack'.API_VESION_NAMESPACE.'Action\MultiTrackingAction::class;');
$app->post('/'.API_VERSION.'multitracking', $namespaceMultiTracking);// done

$app->post('/get-tracking-ali-baba', Smarttrack\Action\GetTrackingAliBabaAction::class);// done
$app->get('/get-tracking-code',Smarttrack\Action\GetTrackinCodeAction::class);
// Auth routes
$app->post('/'.API_VERSION.'token', Auth\Action\TokenAction::class);


