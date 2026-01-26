<?php

/**
 * Provides tab selection and rendering of main menu.
 *
 */
ini_set('default_charset', 'UTF-8');
class Adminmenu {

    const DASHBOARD = 1;
    const ORDERS = 2;
    const CUSTOMERS = 3;
    const ACCOUNTS = 4;
    const COURIERS = 5;
    const COUNTRIES = 6;
    const POSTCODES = 7;
    const PRODUCTS = 8;
    const MARKUPS = 9;
    const INSURANCES = 10;
    const ADMINISTRATORS = 11;
    const COLLECTION_TIMES = 12;
    const AWBS = 13;
    const CITIES = 15;
    const NEWSRELEASES = 16;
    const INVOICES = 17;
    const BOOKINGS = 14;
    const CURRENCY = 18;
    const REMOTEAREAS = 19;

    // Selected menu item
    private $menuItem;

    /**
     * Creates the main menu object and specifies which table will be selected
     */
    public function __construct($selectedMenuItem = "") {
        $this->menuItem = $selectedMenuItem;
    }

   
    /**
     * Renders the main menu
     */
    public function render() {
        $sessionManager = Sessionmanager::getUser();
        //$sessionManager ->getUserAccount() == 'ITTEAM' || $sessionManager ->getUserAccount() == 'AMARJOT'
        if (true) {
	?>
		<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                    <?php
                    $user = SessionManager::getUser();
                    echo Permissions::DisplayMenu($user->getId()); ?>
                </ul>
<?php		
            //$this->renderNew($sessionManager);
        } else {
            ?>
            
            <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">                
                <li class="nav-item start<?php echo (($this->selectedPage == 'index.php') ? ' active' : '');?>">
                    <a href="dashboard"><i class="icon-home"></i><span class="title">Home</span></a>
                </li>
                <li class="start">
                    <a href="../../index.php" target="_blank"><i class="icon-home"></i><span class="title">Labeling System</span></a>
                </li>

                <?php
                $this->renderUserMenu();
                ?>

                <li>
                    <a href="login?logout=true">
                        <i class="icon-logout"></i>
                        <span class="title">Logout</span>
                    </a>
                </li>
            </ul>
            <?php
        }
    }

    /**
     * Renders the main menu
     */

    /**
     * Render Footer
     */
    public function renderNew($sessionManager) {
        $allowMenu 		=	array();
		
        $allowMenu[] 	=	Translation::GetCaption("DASHBOARD");
		
        $allowMenu[] 	=	'Labeling System';
        $menuOption 	=	@$_SESSION['menu-option'];
        /*
			Overwrite session values to view the select menu option and view the user type roles 
		*/
		$menuToShow 	=	$sessionManager->getUserType();
		//$sessionManager->setUserType($menuOption);
		if($menuOption == 'client')
			$sessionManager->setUserType(@$_SESSION['user_type']);
		else
			$sessionManager->setUserType($menuOption);
				
		$isProduct		=	$sessionManager->getIsProduct();
		
		$consignmentNewFilter	=	new ConsignmentFilter();
		$consignmentNewFilter->addFilter(" account = '".$sessionManager->getUserAccount()."'");
		$consignmentNewFilter->addFilter(" consignment_status = 'valid'");
		
		$consignemtValid	=	$consignmentNewFilter->getValidCountCon();
		//die;
		
		$UserMarketPlacesPlatformData = array();
		$UserShippingPlatformData = array();
		
		if($sessionManager->getRetailCustomer() == "1")
		{
			$consignmentFile = "booking_quote.php";
		}
		else
		{
                    $consignmentFile	=	'shipment_edit.php?option=new';
		}
        switch ($menuOption) {
            case User::USER_TYPE_ADMIN :
                $menuToShow = User::USER_TYPE_ADMIN;
                break;
            case User::USER_TYPE_FINANCE :
                $menuToShow = User::USER_TYPE_FINANCE;
                break;
            case User::USER_TYPE_CUSTOMER_SERVICE :
                $menuToShow = User::USER_TYPE_CUSTOMER_SERVICE;
                break;
            case User::USER_TYPE_WAREHOUSE :
                $menuToShow = User::USER_TYPE_WAREHOUSE;
                break;
            case User::USER_TYPE_CORPORATE_CLIENT :
                $menuToShow = User::USER_TYPE_CORPORATE_CLIENT;
                break;
            case User::USER_TYPE_CLIENT :
                $menuToShow = User::USER_TYPE_CLIENT;
                break;
            case User::USER_TYPE_SALES :
                $menuToShow = User::USER_TYPE_SALES;
                break;
        }
        if ($menuToShow == User::USER_TYPE_ADMIN) {
				$allowMenu[] = Translation::GetCaption('CARRIER');
            	$allowMenu[Translation::GetCaption('CARRIER')] = array( Translation::GetCaption('SERVICE_LIST') , Translation::GetCaption('CARRIER'));
			   	$allowMenu[] = Translation::GetCaption("TRANSLATION");
    	    	$allowMenu[Translation::GetCaption("TRANSLATION")] = array(Translation::GetCaption("ADD_LANGUAGE_KEYWORDS"),
		 																			 Translation::GetCaption("KEYWORDS_LIST"),
																					 Translation::GetCaption("ADD_LANGUAGE"),
																					 Translation::GetCaption("LANGUAGE_LIST"));
	    
            
            $allowMenu[] = Translation::GetCaption("DEPARTMENT"); 
            $allowMenu[Translation::GetCaption("DEPARTMENT")] = array( Translation::GetCaption("ADD_NEW_DEPARTMENT"),
																				   Translation::GetCaption("VIEW_DEPARTMENT"));
			
			$allowMenu[] = Translation::GetCaption("MANAGE_MARKET_PLACES"); 
			$allowMenu[Translation::GetCaption("MANAGE_MARKET_PLACES")] = array( Translation::GetCaption("ADD_MARKET_PLACES"),Translation::GetCaption("LIST_MARKET_PLACES"));		
			$allowMenu[] = Translation::GetCaption("MANAGE_SHOPPING_PLATFORM"); 
			$shipping_plarform_arr = array();
			$shipping_plarform_arr[0] = Translation::GetCaption("ADD_SHOPPING_PLATFORM");
			$ShoppingPlatformFilter = new ShoppingPlatformFilter();
			$UserShippingPlatformData = $ShoppingPlatformFilter->getList();
			
			if(is_array($UserShippingPlatformData) && count($UserShippingPlatformData) > 0){
					$count = 1;
					foreach ($UserShippingPlatformData as $shiping_platform) {
						$shipping_plarform_arr[$count] = Translation::GetCaption($shiping_platform->getTranslationKey());
						$count ++;
					} 
			$allowMenu[Translation::GetCaption("SHOPPING_PLATFORM")] = $shipping_plarform_arr;
			}		
		
			$allowMenu[] = Translation::GetCaption("PERMISSIONS"); 
                        $allowMenu[Translation::GetCaption("PERMISSIONS")] = array(Translation::GetCaption("GROUPS_LIST"),Translation::GetCaption("ADD_GROUPS"),Translation::GetCaption("ADD_PERMISSIONS"));
			
        } 
        else if (in_array($menuToShow, array(User::USER_TYPE_ACCOUNT, User::USER_TYPE_FINANCE))) {
            $allowMenu[] = Translation::GetCaption('CARRIER');
            $allowMenu[Translation::GetCaption('CARRIER')] = array( Translation::GetCaption('SERVICE_LIST') , Translation::GetCaption('CARRIER'));
            $allowMenu[] = Translation::GetCaption('accounts');
			//$allowMenu[] = Translation::GetCaption("MULTI_TRACKING_SYSTEM");
          	$allowMenu[] = Translation::GetCaption('MANAGE_CUSTOMERS');
            $allowMenu[Translation::GetCaption('MANAGE_CUSTOMERS')] = array(Translation::GetCaption('customer'), Translation::GetCaption('EMPLOYEES') ,  Translation::GetCaption('admin'),  Translation::GetCaption('REMOTE_AREAS'),  Translation::GetCaption('REMOTE_AREAS_MAPPING'), 'Routing List');
			$allowMenu[] = Translation::GetCaption('MANAGE_TARIFF');
			$allowMenu[Translation::GetCaption('MANAGE_TARIFF')] = array(Translation::GetCaption('CARRIER'),Translation::GetCaption('TARIFF_LIST'));
			
		    $allowMenu[] = Translation::GetCaption('FIND_WAYBILL');
            $allowMenu[Translation::GetCaption('FIND_WAYBILL')] =  array( Translation::GetCaption('MAWB_BOOKING') , Translation::GetCaption('SHIPMENTS'));
            
          //  $allowMenu[] = 'Quotation';
            $allowMenu[] = Translation::GetCaption('INVOICES');
            $allowMenu[Translation::GetCaption('INVOICES')] = array( Translation::GetCaption('INVOICE_LIST') , Translation::GetCaption('ADD_MANUAL_INVOICE'), Translation::GetCaption('MANUAL_INVOICE_LIST'), Translation::GetCaption('CREDIT_NOTE_ADD'),Translation::GetCaption('CREDIT_NOTE_LIST'));
			$allowMenu[] = Translation::GetCaption('AGENT');
           // $allowMenu[] = 'Operation Report';
           //$allowMenu['Operation Report'] = array( 'Batch Scanning Report' , 'Single Scan Report', 'Service Scan Report','Service Scan Report by Account' , 'Operator Scan Report by Country', 'Country Scan Report by Operator','Country Scan Report by Account' , 'Customer Scan Report by Country', 'Poland Scans','Scan Items Not Found' , 'Royal Mail Tracked & Signed', 'Pallet Dispatch Report','Pallet Dispatch Summary','Mawb Collection','Mawb Scan Discrepancy Report','Shipment Scan Discrepancy');
            $allowMenu[] = Translation::GetCaption('REPORTS');
			$allowMenu[ Translation::GetCaption('REPORTS')] = array(  Translation::GetCaption('GP_REPORT') , Translation::GetCaption('DAY_SUMMARY_REPORT') , Translation::GetCaption('ACCOUNT_SUMMARY_REPORT'), Translation::GetCaption('WEIGHT DISCREPANCY_REPORT'), Translation::GetCaption("SERVICE_SCAN_REPORT"), Translation::GetCaption("MANIFEST_REPORT"));
			$allowMenu[] = Translation::GetCaption('SALES');
			$allowMenu[ Translation::GetCaption('SALES')] = array( Translation::GetCaption('SALE_POT_COMMISION'), Translation::GetCaption('SALES_CALLS'));
        }
		else if (in_array($menuToShow, array(User::USER_TYPE_CUSTOMER_SERVICE))) {
			
            $allowMenu[] = Translation::GetCaption('QUOTATION');
			$allowMenu[] = Translation::GetCaption('coservice');
            $allowMenu[] = Translation::GetCaption('FIND_WAYBILL');
            $allowMenu[Translation::GetCaption('FIND_WAYBILL')] =  array( Translation::GetCaption('SHIPMENTS'), Translation::GetCaption('SEARCH_SCANNED_SHIPMENT'));
			$allowMenu[] = Translation::GetCaption('MANAGE_CUSTOMERS');
            $allowMenu[Translation::GetCaption('MANAGE_CUSTOMERS')] = array(Translation::GetCaption('customer'));
            $allowMenu[] = Translation::GetCaption("MULTI_TRACKING_SYSTEM");
			$allowMenu[] = Translation::GetCaption('AGENT');
			$allowMenu[] = Translation::GetCaption('PRE_ALERTS');
			$allowMenu[Translation::GetCaption('PRE_ALERTS')] = array( Translation::GetCaption('CLIENTS_PRE_ALERTS') , Translation::GetCaption('SEARCH_PRE_ALERT'), Translation::GetCaption('MAWB_REPORT'));
			$allowMenu[] = Translation::GetCaption('OPERATION_MANIFEST');
			$allowMenu[] = Translation::GetCaption('REPORTS');
			$allowMenu[ Translation::GetCaption('REPORTS')] = array(  Translation::GetCaption('MAWB_MANIFEST_REPORT') );
			$allowMenu[] = Translation::GetCaption("TOOLBOX");
			$allowMenu[Translation::GetCaption("TOOLBOX")] = array(
					Translation::GetCaption("NOTES"),
					   Translation::GetCaption("BULLETINS")
					  );
        } 
		else if (in_array($menuToShow, array(User::USER_TYPE_WAREHOUSE))) {
			
			//$allowMenu[] = Translation::GetCaption("MULTI_TRACKING_SYSTEM");
			//$allowMenu[] = Translation::GetCaption("operations");
			$allowMenu[] = Translation::GetCaption("BOOKING");
            $allowMenu[ Translation::GetCaption("BOOKING")] = array(  Translation::GetCaption("BOOKING") ,  Translation::GetCaption("LINEHAUL_TRACKING"));
           /* $allowMenu[] = Translation::GetCaption("FIND_WAYBILL");
            $allowMenu[Translation::GetCaption("FIND_WAYBILL")] =  array(Translation::GetCaption("SHIPMENTS"),Translation::GetCaption("SEARCH_SCANNED_SHIPMENT"));*/
			/*$allowMenu[] = Translation::GetCaption("CREATE_SHIPMENT");
            $allowMenu[Translation::GetCaption("CREATE_SHIPMENT")] = array( Translation::GetCaption("ADD_NEW_SHIPMENT") ,  Translation::GetCaption("IMPORT_VIA_CSV"), Translation::GetCaption("LIST_OF_ALL_SHIPMENTS"), 'Generate CSV', 'Ebay Shipment', 'Amazon Shipment');*/
            $allowMenu[] = Translation::GetCaption("AGENT");
            $allowMenu[] = Translation::GetCaption("OPERATION_REPORT");
			/*$allowMenu[] = Translation::GetCaption("END_OF_DAY_SUMMARIES");
            $allowMenu[Translation::GetCaption("END_OF_DAY_SUMMARIES")] = array( Translation::GetCaption("MAWB_REPORT"),
																				 Translation::GetCaption("UNTRACKED_REPORT"),
																				 Translation::GetCaption("MANIFEST_REPORT"));*/
			//'Service Scan Report by Account' , 'Operator Scan Report by Country', 'Country Scan Report by Operator','Country Scan Report by Account' , 'Customer Scan Report by Country', 'Scan Items Not Found' ,'Pallet Dispatch Summary','Shipment Scan Discrepancy'
            $allowMenu[Translation::GetCaption("OPERATION_REPORT")] = array(
																			Translation::GetCaption("MAWB_REPORT"),
																			Translation::GetCaption("UNTRACKED_REPORT"),
																			Translation::GetCaption("MANIFEST_REPORT"),
																			Translation::GetCaption("PALLET_DISPATCH_REPORT"),
																			Translation::GetCaption("BATCH_SCANNING_REPORT")  ,
																			Translation::GetCaption("SERVICE_SCAN_REPORT"), 
																			Translation::GetCaption("ROYAL_MAIL_TRACKED_&_SIGNED"),
																			Translation::GetCaption("MAWB_SCAN_DISCREPENCY_REPORT"),
																			Translation::GetCaption("SCAN_ITEMS_NOT_FOUND"));
																			// Translation::GetCaption("SINGLE_SCAN_REPORT"), 	Translation::GetCaption("POLAND_SCANS") , Translation::GetCaption("MAWB_COLLECTION"),
            $allowMenu[] = Translation::GetCaption("PRE_ALERTS");
            $allowMenu[Translation::GetCaption("PRE_ALERTS")] = array( Translation::GetCaption("CLIENTS_PRE_ALERTS"),
																						Translation::GetCaption("SEARCH_PRE_ALERT"),
																						Translation::GetCaption("GENERATE_MANIFEST"),
																						Translation::GetCaption("MANIFEST_REPORT"),
																						Translation::GetCaption("END_OF_DAY"));
			//$allowMenu[] = Translation::GetCaption("OPERATION_MANIFEST");
			$allowMenu[] = Translation::GetCaption("RETURNS");
            $allowMenu[Translation::GetCaption("RETURNS")] = array( Translation::GetCaption("RETURN_SHIPMENT"),
																			Translation::GetCaption("RYP_SERVICE"),
																			Translation::GetCaption("BULK_RETURN"));
																			
            $allowMenu[] =  Translation::GetCaption('SCANNING_&_REPORTS');
            $allowMenu[ Translation::GetCaption('SCANNING_&_REPORTS')] = array( Translation::GetCaption("SCAN_BAGBOX_SINGLE_ITEM"),
			 																			Translation::GetCaption("RELABEL_SHIPMENT"),
																						Translation::GetCaption("FIND_WAYBILL"),
																						Translation::GetCaption("SEARCH_SCANNED_SHIPMENT"),
																						Translation::GetCaption("CHANGE_SHIPMENT_STATUS"),
																						 Translation::GetCaption("OPERATION_MANIFEST") );
																						//Translation::GetCaption("SEARCH _BAG/ITEM_SCAN"),
			$allowMenu[] =  Translation::GetCaption('FLIGHT_&_SCANNING_STATUS');
             $allowMenu[ Translation::GetCaption('FLIGHT_&_SCANNING_STATUS')] = array(
																						Translation::GetCaption("UPDATE_FLIGHT_INFORMATION"),
																						Translation::GetCaption("MAWB_&_FLIGHT_STATUS_REPORT"),
																						Translation::GetCaption("SCANNING_REPORT_W.R.T_MAWB"));																						
            $allowMenu[] =  Translation::GetCaption("WAREHOUSE_MGMT");
			$allowMenu[Translation::GetCaption("WAREHOUSE_MGMT")] = array( Translation::GetCaption("WAREHOUSE"),
																			Translation::GetCaption("RACKS") ,
																			Translation::GetCaption("STOCK_IN_REPORT"), 
																			Translation::GetCaption("STOCK_OUT_REPORT"),
																			Translation::GetCaption("STOCK_OUT_MULTI_ITEMS"));
			 $allowMenu[] =  Translation::GetCaption("BAGGING");
            $allowMenu[Translation::GetCaption("BAGGING")] = array( Translation::GetCaption("POSTAL_BAGGING"),
																    Translation::GetCaption("FREIGHT_BAGGING"),
																	Translation::GetCaption("DOCUMENT_SEARCH"));
            
            $allowMenu[] = Translation::GetCaption("OPTIMUS_SORTER_MGMT");
            $allowMenu[Translation::GetCaption("OPTIMUS_SORTER_MGMT")] = array(Translation::GetCaption("SEND_DATA_TO_SORTER"),
																				Translation::GetCaption("SORTER_DAILY_SCAN_REPORT") ,
																				Translation::GetCaption("SORTER_SCAN_REPORT") ,
																				Translation::GetCaption("SORTER_ERROR_REPORT") ,
																				Translation::GetCaption("RESOLVE_REJECTED_FILE"),
																				Translation::GetCaption("PALLET_CARTON_BARCODE"),
																				Translation::GetCaption("ADD_INTER_WAREHOUSE_LOCATION") ,
																				Translation::GetCaption("VIEW_INTER_WAREHOUSE_LOCATION"),
																				Translation::GetCaption("INTER_WAREHOUSE_MOVEMENT"),
																				Translation::GetCaption("FIND_PALLET"),
																				Translation::GetCaption("WAREHOUSE_BULK_MOVEMENT") );
			
			
        } 
		else if (in_array($menuToShow, array(User::USER_TYPE_CORPORATE_CLIENT))) {
			//$allowMenu[] = Translation::GetCaption('Corporate');
            $allowMenu[] = Translation::GetCaption('MANAGE_CUSTOMERS');
            $allowMenu[Translation::GetCaption('MANAGE_CUSTOMERS')] = array(Translation::GetCaption('customer'));
			//$allowMenu[] = Translation::GetCaption('Operation Manifest');
            $allowMenu[] = Translation::GetCaption('MANAGE_SCANNING');
			$allowMenu[Translation::GetCaption('MANAGE_SCANNING')] = array(Translation::GetCaption('RETURNS'), Translation::GetCaption('INBOUND_SCAN'));
			
			$allowMenu[] = Translation::GetCaption('REPORTS');
            $allowMenu[Translation::GetCaption('REPORTS')] = array( Translation::GetCaption('HUB_REPORT'), Translation::GetCaption('SCAN_REPORT'),
			Translation::GetCaption('EXPORT_PARTNER_SERVICE'),Translation::GetCaption('MANIFEST_REPORT'),  Translation::GetCaption('ACCOUNT_SUMMARY_REPORT'));
				$allowMenu[] = Translation::GetCaption("TOOLBOX");
			$allowMenu[Translation::GetCaption("TOOLBOX")] = array(
					Translation::GetCaption("OPERATION_MANIFEST"),
					   Translation::GetCaption("RELABEL_SHIPMENT"),
					   Translation::GetCaption("STOP_SHIPMENT")
					  );
        } 
		else if (in_array($menuToShow, array(User::USER_TYPE_CLIENT))) {
			
                    $UserMarketPlacesFilter = new MarketPlacesFilter();
                    $UserMarketPlacesPlatformData = $UserMarketPlacesFilter->getUserPlatformList($sessionManager->getId());
                    $ShoppingPlatformFilter = new ShoppingPlatformFilter();
                    $UserShippingPlatformData = $ShoppingPlatformFilter->getList();
			
		    $allowMenu[] = Translation::GetCaption("MULTI_TRACKING_SYSTEM");
		    $allowMenu[] = 'Client';
                    $allowMenu[] = Translation::GetCaption("CREATE_SHIPMENT");
                    // 'Create Collection','Amazon', 'ebay',
//                    $allowMenu[Translation::GetCaption("CREATE_SHIPMENT")] = array( 
//				Translation::GetCaption("ADD_NEW_SHIPMENT") ,
//				(trim($sessionManager->getCollection()) == '1')? Translation::GetCaption("CREATE_COLLECTION"):'' ,
//				Translation::GetCaption("LIST_OF_ALL_SHIPMENTS"), 
//				Translation::GetCaption("IMPORT_VIA_CSV"), 
//				Translation::GetCaption("EXPORT_SHIPMENTS"), 
//				Translation::GetCaption("SEARCH_DATABASE"), 
//				Translation::GetCaption("STOP_SHIPMENT"), 
//				Translation::GetCaption("MANAGE_ADDRESS_LIST"), 
//				
//				Translation::GetCaption("EBAY_SHIPMENT"), 
//				Translation::GetCaption("AMAZON")
//			); 
			if(is_array($UserMarketPlacesPlatformData) && count($UserMarketPlacesPlatformData) > 0){
                            $MarketPlacesArr = array();
                            foreach ($UserMarketPlacesPlatformData as $MarketPlaces) {
                                    $MarketPlacesArr[] = Translation::GetCaption($MarketPlaces->getTranslationKey());
                            }
                         
				$allowMenu[] = Translation::GetCaption("MARKET_PLACES");
				$allowMenu[Translation::GetCaption("MARKET_PLACES")] = $MarketPlacesArr;
			}
			if(is_array($UserShippingPlatformData) && count($UserShippingPlatformData) > 0){
				$shipping_plarform_arr = array();
				$shipping_plarform_arr[]	=	Translation::GetCaption("ADD_SHOPPING_PLATFORM");
				/*foreach ($UserShippingPlatformData as $shiping_platform) 
				{	$shipping_plarform_arr[] = Translation::GetCaption($shiping_platform->getTranslationKey());	} */
				$allowMenu[] = Translation::GetCaption("SHOPPING_PLATFORM");
	$allowMenu[Translation::GetCaption("SHOPPING_PLATFORM")] = $shipping_plarform_arr;
			}
		
//			$sessionManager = Sessionmanager::getUser();
//			if($sessionManager->getThemeId() != 1)
//			{
//				$allowMenu[] = Translation::GetCaption("CREATE_LABEL");
//				$allowMenu[Translation::GetCaption("CREATE_LABEL")] = array( 
//					Translation::GetCaption("LIST_OF_ALL_LABELS"), 
//					Translation::GetCaption("PRINT_SELECTED"),
//					Translation::GetCaption("RELABEL")
//				);	
//			}
			$allowMenu[] = Translation::GetCaption('PRE_ALERTS');
			$allowMenu[Translation::GetCaption('PRE_ALERTS')] = array( Translation::GetCaption('CLIENTS_PRE_ALERTS') , Translation::GetCaption('SEARCH_PRE_ALERT'), Translation::GetCaption('MAWB_REPORT'));
			
           /* $allowMenu[] = 'Labels';
            $allowMenu['Labels'] = array( 'List' , 'Relabel');*/
            //$allowMenu[] = 'Address';
            $allowMenu[] = Translation::GetCaption("CREATE_MANIFEST");   
//			$allowMenu[Translation::GetCaption("CREATE_MANIFEST")] = 
//				array(Translation::GetCaption("MANIFEST_SELECTED"), Translation::GetCaption("LIST_OF_ALL_MANIFESTS"));//Translation::GetCaption("MANIFEST_ALL")
			
	     $allowMenu[] = Translation::GetCaption("REPORTS");   
//			$allowMenu[Translation::GetCaption("REPORTS")] = 
//				array(Translation::GetCaption("SERVICE_PERFORMANCE_REPORT"));//Translation::GetCaption("MANIFEST_ALL")
			
            //$allowMenu['Create Manifest'] = array( 'Generate Manifest');
			
			if($sessionManager->getThemeId() == 1)
			{
				$allowMenu[] = Translation::GetCaption("COLLECTION");
				//$allowMenu[Translation::GetCaption("COLLECTION")] = array( Translation::GetCaption("REQUEST_COLLECTION") , Translation::GetCaption("LIST_OF_ALL_COLLECTIONS"));
			}
			/*if( Sessionmanager::getUser()->getCreatePreAlert() == "YES")
				$allowMenu[] = 'Send Pre-alert';
            $allowMenu['Send Pre-alert'] = array( 'Customer Pre-Alerts' , 'Search Pre-Alert');*/
			/*$allowMenu[] = Translation::GetCaption("TOOLBOX");
			$allowMenu[Translation::GetCaption("TOOLBOX")] = array(
					   Translation::GetCaption("SEARCH_DATABASE"),
					   Translation::GetCaption("EXPORT_SHIPMENTS"),
					   Translation::GetCaption("STOP_SHIPMENT"),
					   Translation::GetCaption("RELABEL")); ;
			//$allowMenu[] = 'Search';*/
			$allowMenu[] = Translation::GetCaption('HELP');
		    if($sessionManager->getThemeId() == 1)
		    {
          
			$allowMenu[Translation::GetCaption('HELP')] = array(
                                //Translation::GetCaption('ADD_NEW_TICKET'),
                                 Translation::GetCaption('SUPPORT_CENTER'),

                                 Translation::GetCaption("DANGEROUS_GOODS_MENU"),
                                 Translation::GetCaption("PRODUCT_INFORMATION")
                          // Translation::GetCaption("USER_MANUAL"),
                          //Translation::GetCaption("LEGAL_DOCUMENTS"),
                          //Translation::GetCaption("LIST_OF_TARIFFS_&_PRODUCTS")
                         );
			}
           // $allowMenu[] = 'API Interface';g
        }
	
		else if (in_array($menuToShow, array(User::USER_TYPE_SALES))) {
			$allowMenu[] = Translation::GetCaption('SALES');
			$allowMenu[ Translation::GetCaption('SALES')] = array( Translation::GetCaption('SALE_POT_COMMISION'), Translation::GetCaption('SALES_CALLS'));
			
			$allowMenu[] = Translation::GetCaption('REPORTS');
            $allowMenu[Translation::GetCaption('REPORTS')] = array( Translation::GetCaption('ACCOUNT_SUMMARY_REPORT'));
        } 
		
		/*  if($sessionManager->getThemeId() != 1)
		    {
////////////////////HELP DESK //////////////////////////////////		
			  $allowMenu[] = Translation::GetCaption('HELP_DESK');
			  $allowMenu[Translation::GetCaption('HELP_DESK')] = array(  Translation::GetCaption("USER_MANUAL"),
																		Translation::GetCaption("GLOSSARY"),
																		Translation::GetCaption("CONTACT"),
																		Translation::GetCaption("FAQ"));
			 }*/
        $allowMenu[] = Translation::GetCaption('LOGOUT');
        $toolbar = array();
    	 
///////////////////////////// DASHBOARD  ///////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("DASHBOARD");
        $item ["id"] = "sbm-dashboard";
        $item ["class"] = "start active";
        $item ["link"] = "index.php?menu-option=". $menuOption;
        //$item ["link"] = "index.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-home" aria-hidden="true"></i>';
        $item ["arrow"] = '';
        $item ["other_links"] = 'booking_view.php,b.php,c.php';
        $toolbar [] = $item;
		
///////////////////////// SCANNING & REPORT ////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption('SCANNING_&_REPORTS');
        $item ["id"] = "sbm-scannig-n-report";
        $item ["class_li"] = "nav-item";
        $item ["class"] = "nav-link nav-toggle";
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-bar-chart"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $scannignnReportsCount = 0;
		
       
		
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('SCAN_BAGBOX_SINGLE_ITEM');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-scan-bagbox-single-item";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "bagscan.php";
        $scannignnReportsCount++;
		
		$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('RELABEL_SHIPMENT');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-relabel-shipment";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "relabel_shipment.php";
        $scannignnReportsCount++;
		
		$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('FIND_WAYBILL');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-find-waybill";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "bookings.php";
        $scannignnReportsCount++;
		
		$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('SEARCH_SCANNED_SHIPMENT');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-search-scanned-shipment";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "track_shipment.php";
        $scannignnReportsCount++;
		
		if($sessionManager->getOpearationManifest() == 'YES')
		{
			
			$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
      		$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('OPERATION_MANIFEST');
       		$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-operation-manifest";
        	$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        	$item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "generate-manifest_m.php";
        	$scannignnReportsCount++;
		
		}
		
		
      /*  $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('SEARCH_BAG/ITEM_SCAN');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-search-bag-item-scan";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "searchbagging.php";
        $scannignnReportsCount++;*/
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('CLIENTS_PRE_ALERTS');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-clients-pre-alerts";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "pre_alert_files.php";
        $scannignnReportsCount++;
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('CHANGE_SHIPMENT_STATUS');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-change-shipment-status";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "changestatus.php";
//		$toolbar [] = $item;
        $scannignnReportsCount++;
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('POLAND_SHIPMENT_SCAN');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-poland-shipment-scan";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "bagscan_poland.php";
//		$toolbar [] = $item;
        $scannignnReportsCount++;
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount] = array();
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["title"] = Translation::GetCaption('C2Y_YPS_SHIPMENT_SCAN');
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["id"] = "sbm-C2Y-YPS-Shipment-Scan";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["class"] = "";
        $item [Translation::GetCaption('SCANNING_&_REPORTS')][$scannignnReportsCount]["link"] = "bagscan_c2y.php";
        $toolbar [] = $item;
///////////////////////////////////FLIGHT AND SCANNINF STATUS////////////////////////

																					
	    $item = array();
        $item ["title"] = Translation::GetCaption('FLIGHT_&_SCANNING_STATUS');
        $item ["id"] = "sbm-flight-&-scanning-status";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-bar-chart"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $flightCount = 0;
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount] = array();
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["title"] = Translation::GetCaption("UPDATE_FLIGHT_INFORMATION");
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["id"] = "sbm-update-flight-information";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["class"] = "";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["link"] = "pre-alert-warehouse.php";
        $flightCount++;
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount] = array();
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["title"] = Translation::GetCaption("MAWB_&_FLIGHT_STATUS_REPORT");
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["id"] = "sbm-mawb-flight-status-report";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["class"] = "";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["link"] = "pre-alert-report.php";
        $flightCount++;
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount] = array();
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["title"] = Translation::GetCaption("SCANNING_REPORT_W.R.T_MAWB");
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["id"] = "sbm-scanning-report-mawb";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["class"] = "";
        $item [Translation::GetCaption("FLIGHT_&_SCANNING_STATUS")][$flightCount]["link"] = "ajax-scanning-report.php";
        $toolbar [] = $item;
////////////////////////////// RETURN ///////////////////////////////////
        $item = array();
        $item ["title"] =  Translation::GetCaption("RETURNS");
        $item ["id"] = "sbm-return";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-action-undo"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $ReturnCount = 0;
        $item [Translation::GetCaption("RETURNS")][$ReturnCount] = array();
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["title"] = Translation::GetCaption("RETURN_SHIPMENT");
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["id"] = "sbm-Return-Shipment";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["class"] = "";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["link"] = "consigmentreturn.php";
        $ReturnCount++;
        $item [Translation::GetCaption("RETURNS")][$ReturnCount] = array();
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["title"] = Translation::GetCaption("RYP_SERVICE");
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["id"] = "sbm-RYP-Service";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["class"] = "";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["link"] = "consignment_return.php";
        $ReturnCount++;
        $item [Translation::GetCaption("RETURNS")][$ReturnCount] = array();
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["title"] = Translation::GetCaption("BULK_RETURN");
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["id"] = "sbm-Bulk-Return";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["class"] = "";
        $item [Translation::GetCaption("RETURNS")][$ReturnCount]["link"] = "bulkreturn.php";
        $toolbar [] = $item;


		
/////////////////// BOOKING ///////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("BOOKING");
        $item ["id"] = "sbm-Booking";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-book-open"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $BookingCount = 0;
        $item [Translation::GetCaption("BOOKING")][$BookingCount] = array();
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["title"] = Translation::GetCaption("BOOKING");
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["id"] = "sbm-Booking1";
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["class"] = "";
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["link"] = "endofday_summary.php";
        $BookingCount++;
        $item [Translation::GetCaption("BOOKING")][$BookingCount] = array();
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["title"] = Translation::GetCaption("LINEHAUL_TRACKING");
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["id"] = "sbm-LINEHAUL-TRACKING";
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["class"] = "";
        $item [Translation::GetCaption("BOOKING")][$BookingCount]["link"] = "dispatch_status.php";

        $toolbar [] = $item;
	
////////////////////// END OF DAY SUMMARIES //////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("END_OF_DAY_SUMMARIES");
        $item ["id"] = "sbm-return";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-bar-chart"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $EndOfDaySummariesCount = 0;
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount] = array();
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["title"] = Translation::GetCaption("MAWB_REPORT");
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["id"] = "sbm-MAWB-Report";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["class"] = "";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["link"] = "endofday_search.php";
        $EndOfDaySummariesCount++;
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount] = array();
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["title"] = Translation::GetCaption("UNTRACKED_REPORT");
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["id"] = "sbm-Untracked-Report";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["class"] = "";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["link"] = "ops_summary.php";
        $EndOfDaySummariesCount++;
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount] = array();
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["title"] = Translation::GetCaption("CORREOS_UNTRACKED_REPORT");
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["id"] = "sbm-CORREOS-UNTRACKED-Report";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["class"] = "";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["link"] = "generate_correos_report.php";
		$EndOfDaySummariesCount++;
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount] = array();
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["title"] = Translation::GetCaption("SEARCH_SCANNED_SHIPMENT");
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["id"] = "sbm-search-scanned-shipment";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["class"] = "";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["link"] = "track_shipment.php";
		$EndOfDaySummariesCount++;
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount] = array();
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["title"] = Translation::GetCaption("MANIFEST_REPORT");
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["id"] = "sbm-manifest-report";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["class"] = "";
        $item [Translation::GetCaption("END_OF_DAY_SUMMARIES")][$EndOfDaySummariesCount]["link"] = "manifest_search.php";

		
        $toolbar [] = $item;



////////////////////////////////// QUOTATION /////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("QUOTATION");
        $item ["id"] = "sbm-quotation";
        $item ["class"] = "";
        $item ["link"] = "quotationlist.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-printer"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;
		

/////////////////////////// FIND WAY BILL //////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("FIND_WAYBILL");
        $item ["id"] = "sbm-find-waybill";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-drawer"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $findWayBillNumber = 0;
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber] = array();
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["title"] = Translation::GetCaption("MAWB_BOOKING");
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["id"] = "sbm-mawb-booking";
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["class"] = "";
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["link"] = "mawb-report.php";

        $findWayBillNumber++;
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber] = array();
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["title"] = Translation::GetCaption("SHIPMENTS");
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["id"] = "sbm-shipments";
        $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["class"] = "";
		if($menuOption == "customerservice")
       	 $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["link"] = "cs_bookings.php";
		else
		  $item [Translation::GetCaption("FIND_WAYBILL")][$findWayBillNumber]["link"] = "bookings.php";

       
        $toolbar [] = $item;

////////////////////////// Invoices /////////////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("INVOICES");
        $item ["id"] = "sbm-invoices";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-docs"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $invoiceNumber = 0;
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber] = array();
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["title"] = Translation::GetCaption("INVOICE_LIST");
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["id"] = "sbm-invoice-list";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["class"] = "";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["link"] = "invoices.php";

        $invoiceNumber++;
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber] = array();
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["title"] = Translation::GetCaption("ADD_MANUAL_INVOICE");
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["id"] = "sbm-add-manual-invoice";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["class"] = "";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["link"] = "manual_invoices_details.php";

        $invoiceNumber++;
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber] = array();
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["title"] = Translation::GetCaption("MANUAL_INVOICE_LIST");
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["id"] = "sbm-manual-invoice-list";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["class"] = "";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["link"] = "manual_invoices.php";

        $invoiceNumber++;
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber] = array();
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["title"] = Translation::GetCaption("CREDIT_NOTE_ADD");
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["id"] = "sbm-credit-note-add";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["class"] = "";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["link"] = "credit_note_details.php";

        $invoiceNumber++;
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber] = array();
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["title"] = Translation::GetCaption("CREDIT_NOTE_LIST");
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["id"] = "sbm-creditnotelist";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["class"] = "";
        $item [Translation::GetCaption("INVOICES")][$invoiceNumber]["link"] = "credit_note.php";

        $toolbar [] = $item;

/////////////////////// TARIFF LIST //////////////////////////////////////////////
		$item = array();
        $item ["title"] = Translation::GetCaption('MANAGE_TARIFF');
        $item ["id"] = "sbm-manage_tariff";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-credit-card"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $manage_tariff = 0;
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff] = array();
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["title"] = Translation::GetCaption('CARRIER');
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["id"] = "sbm-carrier";
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["class"] = "";
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["link"] = "couriers.php";

        $manage_tariff++;
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff] = array();
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["title"] = Translation::GetCaption('TARIFF_LIST');
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["id"] = "sbm-tariff_list";
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["class"] = "";
        $item [Translation::GetCaption('MANAGE_TARIFF')][$manage_tariff]["link"] = "tariff_list.php";

        $toolbar [] = $item;

///////////////////////////////// MANAGE_CUSTOMERS //////////////////////////////////

        $item = array();
        $item ["title"] = Translation::GetCaption('MANAGE_CUSTOMERS');
        $item ["id"] = "sbm-MANAGE_CUSTOMERS";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-users"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';
		$item ["other_links"] = 'adminusers.php,customers.php,employees.php,remoteareas.php,remoteareas.php,list_remotearea_charges.php?id=DEFAULT,routing.php';

        //$Menu			=	false;
        $MANAGE_CUSTOMERS = 0;
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = Translation::GetCaption('admin');
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-administrators";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "adminusers.php";

        $MANAGE_CUSTOMERS++;
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = Translation::GetCaption('customer');
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-customers";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "customers.php";
        
        $MANAGE_CUSTOMERS++;
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = Translation::GetCaption('EMPLOYEES');
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-customers";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "employees.php";

        $MANAGE_CUSTOMERS++;
		$item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = Translation::GetCaption('REMOTE_AREAS');
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-remoteareas";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "remoteareas.php";
		
		$MANAGE_CUSTOMERS++;
		$item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = Translation::GetCaption('REMOTE_AREAS_MAPPING');
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-remoteareas_default_mapping";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "list_remotearea_charges.php?id=DEFAULT";

        $MANAGE_CUSTOMERS++;
		$item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS] = array();
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["title"] = "Routing List";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["id"] = "sbm-routing-list";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["class"] = "";
        $item [Translation::GetCaption('MANAGE_CUSTOMERS')][$MANAGE_CUSTOMERS]["link"] = "routing.php";
        $toolbar [] = $item;
       
		






 
/////////////////////////// CREATE SHIPMENT /////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("CREATE_SHIPMENT");
        $item ["id"] = "sbm-create-shipments";
        $item ["class"] = "start active";
        $item ["link"] = "client_list.php";
        //$item ["link"] = "index.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-dropbox" aria-hidden="true"></i>';
        $item ["arrow"] = '';
        $item ["other_links"] = 'booking_view.php,b.php,c.php';
        $toolbar [] = $item;
        
//        $item = array();
//        $item ["title"] = Translation::GetCaption("CREATE_SHIPMENT");
//        $item ["id"] = "sbm-create-shipments";
//        if($sessionManager->getThemeId() == 1)
//            $item ["class_li"] = "nav-item menu_margin";
//        else
//            $item ["class_li"] = "nav-item";
//            $item ["class"] = "nav-link nav-toggle";		
//        if($sessionManager->getThemeId() != 1)
//            {
//                    $item ["link"] = "client_list.php";
//            }
//            else
//            {
//                    $item ["link"] = "client_list.php?show=show_all";
//            }
//        $item ["target"] = "";
//        $item ["icon"] = '<i class="fa fa-dropbox" aria-hidden="true"></i>';
//        $item ["arrow"] = '<span class="arrow"></span>';
//        $item ["other_links"] = 'booking_quote.php, import.php, csv.php, client_file_con.php,import_data_csv.php,client_file_con.php,amazon_client_file.php,groupon_client_file.php,ctt_client_file.php';
//		
		if($sessionManager->getThemeId() != 1)
		{
        //$Menu			=	false;
			$createShipmentCount = 0;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("LIST_OF_ALL_SHIPMENTS");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-list_of_all_shipments";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "client_list.php";
			
			 $createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("ADD_NEW_SHIPMENT");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-add-new-shipment";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = $consignmentFile;	//"consignment_edit.php?option=new";
		  
	
			
			$createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("CREATE_COLLECTION");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-create-collection";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "consignment_collection.php?option=new";
	
			$createShipmentCount++;
	
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("IMPORT_VIA_CSV");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-import-via-csv";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "import.php";
			
			  $createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("EXPORT_SHIPMENTS");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-export-via-csv";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "csv.php";
			
			  $createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("SEARCH_DATABASE");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-search_database";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "search.php";
			
			
			  $createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("STOP_SHIPMENT");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-stop_database";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "changestatus.php";
			
			$createShipmentCount++;
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("GENERATE_CSV");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-generate_csv";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "csv.php";
	
	
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount] = array();
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["title"] = Translation::GetCaption("MANAGE_ADDRESS_LIST");
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["id"] = "sbm-manage_address_list";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["class"] = "";
			$item [Translation::GetCaption("CREATE_SHIPMENT")][$createShipmentCount]["link"] = "show_address.php";
			$createShipmentCount++;
			
		}
		//$toolbar [] = $item;
	


///////////////////////// SEARCH //////////////////////////////
       $item = array();
       $item ["title"] = Translation::GetCaption("SEARCH");
       $item ["id"] = "sbm-search.";
       $item ["class"] = "";
       $item ["link"] = "search.php?from=warehouse";
       $item ["target"] = "";
       $item ["icon"] = '<i class="icon-magnifier"></i>';
       $item ["arrow"] = '';
       $toolbar [] = $item;

////////////////////////////////// CREATE SINGLE / BULK LABEL /////////////////  
		if(count($consignemtValid)>0 && $consignemtValid[0]->getId()>0)
		{
			
        $item = array();
        $item ["title"] = Translation::GetCaption("CREATE_LABEL");
        $item ["id"] = "sbm-create-labels";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
			$item ["link"] = "javascript:;";
		
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-pencil" aria-hidden="true"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';
        $item ["other_links"] = 'acv.php,acdv.php';

		
		
        //$Menu			=	false;
		$createLabelsCount = 0;
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount] = array();
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["title"] = Translation::GetCaption("ADD_NEW_LABEL");
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["id"] = "sbm-print_selected";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["class"] = "";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["link"] = "client_list.php?show=show_valid";

        $createLabelsCount++;
	   
	    $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount] = array();
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["title"] = Translation::GetCaption("BATCH_SINGLE_LABEL");
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["id"] = "sbm-batch-single_label";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["class"] = "";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["link"] = "label_generate.php";

        $createLabelsCount++;
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount] = array();
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["title"] = Translation::GetCaption("BATCH_BULK_LABEL");
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["id"] = "sbm-batch-bulk_label";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["class"] = "";
        $item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["link"] = "label_generate_new.php";
		
		$createLabelsCount++;
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount] = array();
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["title"] = Translation::GetCaption("LIST_OF_ALL_LABELS");
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["id"] = "sbm-list_of_all_labels";
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["class"] = "";
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["link"] = "label_list.php";
		
		$createLabelsCount++;
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount] = array();
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["title"] = Translation::GetCaption("RELABEL");
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["id"] = "sbm-relabel";
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["class"] = "";
		$item [Translation::GetCaption("CREATE_LABEL")][$createLabelsCount]["link"] = "label_list_x.php";
		
		
        $toolbar [] = $item;
		}
		
		
///////////////////////////// SERVICES /////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption('CARRIER');
        $item ["id"] = "sbm-carrier";
        $item ["class_li"] = "nav-item";
        $item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "carrier.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-gears"></i>';
//       / $item ["arrow"] = '<span class="arrow"></span>';
        $item ["other_links"] = 'services_list.php, services.php, carrier.php';

        //$Menu			=	false;
       /* 
        $item [Translation::GetCaption('SERVICES')][$servicesCount] = array();
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["title"] = Translation::GetCaption('SERVICE_LIST');
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["id"] = "sbm-service-list";
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["class"] = "";
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["link"] = "services_list.php";
        */


        $servicesCount = 0;
        $item [Translation::GetCaption('SERVICES')][$servicesCount] = array();
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["title"] = Translation::GetCaption('CARRIER');
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["id"] = "sbm-carrier";
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["class"] = "";
        $item [Translation::GetCaption('SERVICES')][$servicesCount]["link"] = "carrier.php";
        $toolbar [] = $item;

///////////////////////////// LABEL ////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption('LABELS');
        $item ["id"] = "sbm-labels";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-doc"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $labelsCount = 0;
        $item [Translation::GetCaption('LABELS')][$labelsCount] = array();
        $item [Translation::GetCaption('LABELS')][$labelsCount]["title"] = Translation::GetCaption('LABEL_LIST');
        $item [Translation::GetCaption('LABELS')][$labelsCount]["id"] = "sbm-label-list";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["class"] = "";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["link"] = "label_list.php";

        $labelsCount++;
        $item [Translation::GetCaption('LABELS')][$labelsCount] = array();
        $item [Translation::GetCaption('LABELS')][$labelsCount]["title"] = Translation::GetCaption('LABEL_SEARCH');
        $item [Translation::GetCaption('LABELS')][$labelsCount]["id"] = "sbm-label-search";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["class"] = "";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["link"] = "search_label_list.php";

        $labelsCount++;
        $item [Translation::GetCaption('LABELS')][$labelsCount] = array();
        $item [Translation::GetCaption('LABELS')][$labelsCount]["title"] = Translation::GetCaption("RELABEL");
        $item [Translation::GetCaption('LABELS')][$labelsCount]["id"] = "sbm-relabel";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["class"] = "";
        $item [Translation::GetCaption('LABELS')][$labelsCount]["link"] = "label_list_x.php";
        $item ["other_links"] = 'a.php,b.php,c.php';
        $toolbar [] = $item;


////////////////////// ADDRESS /////////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption('ADDRESS');
        $item ["id"] = "sbm-address";
        $item ["class"] = "";
        $item ["link"] = "show_address.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-flag"></i>';
        $item ["arrow"] = '';

        $item ["other_links"] = 'show_address.php,save_address.php';
        $toolbar [] = $item;

	
/////////////////////// MANAGE SCANNING///////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption('MANAGE_SCANNING');
        $item ["id"] = "sbm-ManageScanning";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-flag"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $ReceivingHubScanCount = 0;
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount] = array();
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["title"] = Translation::GetCaption('INBOUND_SCAN');
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["id"] = "sbm-scan/-end-of-day/-manifest";
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["class"] = "";
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["link"] = "bagscan.php";
		$ReceivingHubScanCount++;
		
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount] = array();
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["title"] = Translation::GetCaption('RETURNS');
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["id"] = "sbm-address-list";
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["class"] = "";
        $item [Translation::GetCaption('MANAGE_SCANNING')][$ReceivingHubScanCount]["link"] = "supplier_returns.php";
        $item ["other_links"] = 'intransportation_report.php,supplier_returns.php,c.php';
		 $ReceivingHubScanCount++;
      
        $toolbar [] = $item;

		

		
/////////////////////////// OPERATION MANIFEST ///////////////////////////	
		if($sessionManager->getOpearationManifest() == 'YES')
		{
			$item = array();
			$item ["title"] = Translation::GetCaption('OPERATION_MANIFEST');
			$item ["id"] = "sbm-operation-manifest";
			$item ["class"] = "";
			$item ["link"] = "generate-manifest_m.php";
			$item ["target"] = "";
			$item ["icon"] = '<i class="icon-flag"></i>';
			$item ["arrow"] = '';
			$toolbar [] = $item;
		}

/////////////////////// CREATE MANIFEST //////////////////////////////////////
		if($sessionManager->getBagging() != "YES")
		{
			$item = array();
			
			$item ["title"] = Translation::GetCaption("CREATE_MANIFEST");
			$item ["id"] = "sbm-manifest";
                        $item ["class"] = "start active";
			$item ["link"] = "coclient_user_endofday.php";
                        $item ["target"] = "";
                        $item ["icon"] = '<i class="fa fa-clone" aria-hidden="true"></i>';
                        $item ["arrow"] = '';
                        $toolbar [] = $item;
//			 if($sessionManager->getThemeId() != 1)
//			 {
//                            $menifestCount = 0;
//
//                            $menifestCount++;
//
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount] = array();
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["title"] = Translation::GetCaption("MANIFEST_SELECTED");
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["id"] = "sbm-manifest-selected";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["class"] = "";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["link"] = "client_list.php?show=manifest_selected";
//
//                            $menifestCount++;
//                      /*  $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount] = array();
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["title"] = Translation::GetCaption("MANIFEST_ALL");
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["id"] = "sbm-manifest-all";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["class"] = "";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["link"] = "manifest_type.php?manifest=manifestall";	
//
//
//                            $menifestCount++;*/
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount] = array();
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["title"] = Translation::GetCaption("LIST_OF_ALL_MANIFESTS");
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["id"] = "sbm-manifest-all";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["class"] = "";
//                            $item [Translation::GetCaption("CREATE_MANIFEST")][$menifestCount]["link"] = "coclient_user_endofday.php";	
//                               
//			}
                         
			
		}
		
///////////////////// COLLECTION ///////////////////////////////////		
		$item = array();
		$item ["title"] 		= Translation::GetCaption("COLLECTION");
		$item ["id"] 			= "sbm-request_collection";
		$item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";	
		$item ["link"] 			= "client_list.php?show=collection_selected";
		$item ["target"]		= "";
		$item ["icon"] 			= '<i class="fa fa-truck" aria-hidden="true"></i>';
		$item ["arrow"] = '<span class="arrow"></span>';
		$item ["other_links"] = 'find_collection.php';
       	
		/*$requestcollectioncount = 0;
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount] = array();
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["title"] = Translation::GetCaption("REQUEST_COLLECTION");
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["id"] = "sbm-direct_transport_to_hub";
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["class"] = "";
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["link"] = "coclient_user_endofday_collection.php";

        $requestcollectioncount++;
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount] = array();
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["title"] = Translation::GetCaption("LIST_OF_ALL_COLLECTIONS");
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["id"] = "sbm-list_of_all_collection";
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["class"] = "";
        $item [Translation::GetCaption("COLLECTION")][$requestcollectioncount]["link"] = "find_collection.php";*/
		$toolbar [] 			= $item;
		
		

////////////////////////////////Operation Report //////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("OPERATION_REPORT");
        $item ["id"] = "sbm-operation-report";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-docs"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $oprRptNumber = 0;
		$item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("MAWB_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "sbm-MAWB-Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "endofday_search.php";
        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("UNTRACKED_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "sbm-Untracked-Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "ops_summary.php";
		$oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("MANIFEST_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "sbm-manifest-report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "manifest_search.php";
		$oprRptNumber++;
		
		 $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("PALLET_DISPATCH_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Pallet Dispatch Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "pallet_report.php";

        $oprRptNumber++;
		
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("BATCH_SCANNING_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "sbm-batch-scanning-report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("SINGLE_SCAN_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Single Scan Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_single_tracking.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("SERVICE_SCAN_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Service Scan Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_services.php";
        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("SERVICE_SCAN_REPORT_BY_ACCOUNT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Service Scan Report by Account";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_services_account.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("OPERATOR_SCAN_REPORT_BY_COUNTRY");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Operator Scan Report by Country";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_user.php";


        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("COUNTRY_SCAN_REPORT_BY_OPERATOR");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Country Scan Report by Operator";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_country.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("COUNTRY_SCAN_REPORT_BY_ACCOUNT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Country Scan Report by Account";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_country_account.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("CUSTOMER_SCAN_REPORT_BY_COUNTRY");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Customer Scan Report by Country";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_account.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("POLAND_SCANS");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Poland Scans";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_poland.php";
        $oprRptNumber++;
		
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("SCAN_ITEMS_NOT_FOUND");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Scan Items Not Found";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "not_found_report.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("ROYAL_MAIL_TRACKED_&_SIGNED");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Royal Mail Tracked & Signed";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "royal_mail_report.php";


        $oprRptNumber++;
       
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("PALLET_DISPATCH_SUMMARY");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Pallet Dispatch Summary";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "pallet_report_summary_fullscreen.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] =Translation::GetCaption("MAWB_COLLECTION") ;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Mawb Collection";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "mawb_collection.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("MAWB_SCAN_DISCREPENCY_REPORT");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Mawb Scan Discrepancy Report";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "mawb_discrepancy.php";

        $oprRptNumber++;
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber] = array();
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["title"] = Translation::GetCaption("SHIPMENT_SCAN_DISCREPENCY");
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["id"] = "Shipment Scan Discrepancy";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["class"] = "";
        $item [Translation::GetCaption("OPERATION_REPORT")][$oprRptNumber]["link"] = "reporting_discrepancy.php";
        $toolbar [] = $item;

///////////////////// PRE- ALERT //////////////////////////////
		if($sessionManager->getCreatePreAlert() == 'YES')
		{					
			$item = array();
			$item ["title"] = Translation::GetCaption("PRE_ALERTS");
			$item ["id"] = "sbm-manifest-n-pre-alerts";
			$item ["class_li"] = "nav-item";
			$item ["class"] = "nav-link nav-toggle";		
			$item ["link"] = "javascript:;";
			$item ["target"] = "";
			$item ["icon"] = '<i class="icon-docs"></i>';
			$item ["arrow"] = '<span class="arrow"></span>';
	
			//$Menu			=	false;
			$menifestnpreAletCount = 0;
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount] = array();
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["title"] = Translation::GetCaption("CLIENTS_PRE_ALERTS");
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["id"] = "sbm-address-list";
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["class"] = "";
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["link"] = "pre-alert.php";
	
			$menifestnpreAletCount++;
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount] = array();
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["title"] = Translation::GetCaption("SEARCH_PRE_ALERT");
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["id"] = "sbm-address-list";
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["class"] = "";
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["link"] = "pre-alert-search.php";
			
			$menifestnpreAletCount++;
			$item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount] = array();
      	    $item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["title"] = Translation::GetCaption("MAWB_REPORT");
            $item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["id"] = "sbm-mawb-report";
            $item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["class"] = "";
            $item [Translation::GetCaption("PRE_ALERTS")][$menifestnpreAletCount]["link"] = "endofday_search.php";
			
			 $toolbar [] = $item;
		}
		

//////////////////////// MULTI TRACKING ////////////////////////////////////////////		
		$item = array();
       	$item ["title"] 		= Translation::GetCaption("MULTI_TRACKING_SYSTEM");
		$item ["id"] 			= "sbm-multitracking-system";
		$item ["class"] 		= "";
       	$item ["link"] 			= "multitracking.php";
       	$item ["icon"] 			= '<i class="fa fa-road" aria-hidden="true"></i>';
       	$item ["arrow"] 		= '';
       	$toolbar [] 			= $item;





if(count($UserShippingPlatformData) > 0 && $menuToShow!=User::USER_TYPE_ADMIN ){
			
			/////////////////// Shopping Platform for client/////////////	
		$item = array();
			$item ["title"] = Translation::GetCaption("SHOPPING_PLATFORM");
			$item ["id"] = "";        
			if($sessionManager->getThemeId() == 1)
                        {
                            $item ["class"] = "start active";
                            $item ["class_li"] = "menu_margin";
                        }
			else
                        {
                                $item ["class"] = "start active";
                        }
			$item ["class"] = "nav-link nav-toggle";		
			$item ["link"] = "shopping_platform_manage.php";
			$item ["target"] = "";
			$item ["icon"] = '<i class="fa fa-plug"></i>';
			$item ["arrow"] = '';
			$item ["other_links"] = 'shopping_platform_manage.php';
			$shippingIntegration = 0;
			/*$item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration] = array();
			$item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["title"] = Translation::GetCaption('ADD_SHOPPING_PLATFORM');
			$item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["id"] = "sbm-testeasdadasd-platform";
			$item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["class"] = "";
			$item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["link"] = "";
			*/
			/*$shippingIntegration++;
			foreach ($UserShippingPlatformData as $shipping_integration) {
                            $item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration] = array();
                            $item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["title"] = Translation::GetCaption($shipping_integration->getTranslationKey());
                            $item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["id"] = "sbm-".$shipping_integration->getTitle();
                            $item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["class"] = "";
                            $item [Translation::GetCaption("SHOPPING_PLATFORM")][$shippingIntegration]["link"] = "shopping_platform_page.php"."?key=".$shipping_integration->getPageKey();
                            $shippingIntegration++;
			}
			*/
			$toolbar [] = $item;
	}	
	
	
///////////////////////////// MARKET PLACE ////////////////////////////////////
//if($sessionManager->getThemeId() != 1)
{
			if(count($UserMarketPlacesPlatformData) > 0){
					
					/////////////////// MARKET PLACES for client/////////////
					
					$item = array();
					$item ["title"] = Translation::GetCaption("MARKET_PLACES");
					$item ["id"] = "sbm-find-waybill";
                                        $item ["class"] = "start active";
					$item ["link"] = "market_place_manage.php";
					$item ["target"] = "";
					$item ["icon"] = '<i class="fa fa-shopping-cart"></i>';
					$item ["arrow"] = '';
					$shippingPlatform = 0;
					/*foreach ($UserMarketPlacesPlatformData as $shoping_platform) {
							$item [Translation::GetCaption("MARKET_PLACES")][$shippingPlatform] = array();
					$item [Translation::GetCaption("MARKET_PLACES")][$shippingPlatform]["title"] = Translation::GetCaption($shoping_platform->getTranslationKey());
					$item [Translation::GetCaption("MARKET_PLACES")][$shippingPlatform]["id"] = "sbm-".$shoping_platform->getTitle();
					$item [Translation::GetCaption("MARKET_PLACES")][$shippingPlatform]["class"] = "";
					$item [Translation::GetCaption("MARKET_PLACES")][$shippingPlatform]["link"] = $shoping_platform->getPageLink()."?id=".md5($shoping_platform->getId());
					$shippingPlatform++;
					}*/
					
					$toolbar [] = $item;
			}
		}
///////////////////////// REPORTS ////////////////////////////
        if (in_array($menuToShow, array(User::USER_TYPE_CLIENT)))
        {
            $item = array();
            $item ["title"] = Translation::GetCaption("REPORTS");
            $item ["id"] = "sbm-service-performace";
            $item ["class"] = "start active";
            $item ["link"] = "report_service_performance.php";
            $item ["target"] = "";
            $item ["icon"] = '<i class="icon-bar-chart"></i>';
            $item ["arrow"] = '';
            $toolbar [] = $item;
        }
        else
        {
        $item = array();
        $item ["title"] = Translation::GetCaption("REPORTS");
        $item ["id"] = "sbm-reports";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-bar-chart"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        
        $reportsCount = 0;
	
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption('SERVICE_PERFORMANCE_REPORT');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-service-performance_report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "report_service_performance.php";
        $reportsCount++;
        
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption('POLAND_SHIPMENT_SCAN');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-poland-shipment-scan";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "bagscan_poland.php";
        $reportsCount++;
		
		$item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] =  Translation::GetCaption('HUB_REPORT');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-address-list";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "intransportation_report.php";

        $reportsCount++;
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] =  Translation::GetCaption('SCAN_REPORT');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-scan-report-list";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "track_shipment.php";
		
		 $reportsCount++;
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption('EXPORT_PARTNER_SERVICE');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-export_partner/service-list";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "corporate_service_list_report.php";
		
		$reportsCount++;
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] =  Translation::GetCaption('MANIFEST_REPORT');
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-manifest-report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "manifest_search.php";
		$reportsCount++;
		
		$item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("DAY_SUMMARY_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-create-dispatch";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "day-summary-report.php";
		$reportsCount++;
		
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("ACCOUNT_SUMMARY_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-create-dispatch";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "account_summary.php";
		$reportsCount++;
		
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("WEIGHT DISCREPANCY_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-weight-discrepancy-report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "weight_disc_report.php";
		
		$reportsCount++;
		$item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("GP_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-gp-report-report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "gp_report.php";
		
		$reportsCount++;
		$item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("MAWB_MANIFEST_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-mawb-manifest-report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "mawb_manifest_report.php";
		
        $reportsCount++;
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("MANIFEST_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "sbm-manifest-report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "manifest_search.php";
		
		$reportsCount++;
        $item [Translation::GetCaption("REPORTS")][$reportsCount] = array();
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["title"] = Translation::GetCaption("SERVICE_SCAN_REPORT");
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["id"] = "Service Scan Report";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["class"] = "";
        $item [Translation::GetCaption("REPORTS")][$reportsCount]["link"] = "reporting_services.php";
		
		
        $toolbar [] = $item;
        }

///////////////////////// TOOLBOX ////////////////////////////////////////////		
		$item = array();
       	$item ["title"] 		= Translation::GetCaption("TOOLBOX");
		$item ["id"] 			= "sbm-toolbox";
		$item ["class_li"]      = "nav-item";
		$item ["class"]         = "nav-link nav-toggle";		
       	$item ["link"] 			= "javascript:;";
       	$item ["target"]		= "_blanks";
       	$item ["icon"] 			= '<i class="fa fa-wrench" aria-hidden="true"></i>';       	
		$item ["arrow"]         = '<span class="arrow"></span>';
		
		
		$toolBoxCount = 0;
		
		if($sessionManager->getOpearationManifest() == 'YES')
		{
			$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
			$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("OPERATION_MANIFEST");
			$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-operation_manifest";
			$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
			$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "generate-manifest_m.php";
			$toolBoxCount++;
		}
		
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("RELABEL_SHIPMENT");
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-relabel_shipment";
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "relabel_shipment.php";
		$toolBoxCount++;
		
   	  
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("SEARCH_DATABASE");
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-search_shipment";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "search.php";
		$toolBoxCount++;
		
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("EXPORT_SHIPMENTS");
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-export_shipment";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "csv.php";
		$toolBoxCount++;
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("STOP_SHIPMENT");;
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-stop_shipment";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "changestatus.php";
		$toolBoxCount++;
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("RELABEL");
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-relabel";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "label_list_x.php";
		$toolBoxCount++;
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("BULLETINS");
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-bulletins";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "bulletin_list.php";
		$toolBoxCount++;
		
		$item [Translation::GetCaption("TOOLBOX")][$toolBoxCount] = array();
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["title"] = Translation::GetCaption("NOTES");
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["id"] = "sbm-notes";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["class"] = "";
        $item [Translation::GetCaption("TOOLBOX")][$toolBoxCount]["link"] = "notes_list.php";
		$toolBoxCount++;


///////////////////////////////// SERVICES //////////////////////////////////////////////	
                $item = array();
                $item ["title"] = Translation::GetCaption("HELP");
                $item ["id"] = "sbm-service";
                 if($sessionManager->getThemeId() != 1)
                 {
                    $item ["link"] 			= "support_center.php";
                    $item ["class"] = "start active";
                 }
                else
                {
                    $item ["link"] 			= "javascript:;";
                    $item ["class_li"]      = "nav-item";
                    $item ["class"]         = "nav-link nav-toggle";
                }
                //$item ["link"] = "index.php";
                $item ["target"] = "";
                $item ["icon"] = '<i class="fa fa-question" aria-hidden="true"></i>';
                $item ["arrow"] = '';
                $toolbar [] = $item;
                
               if($sessionManager->getThemeId() == 1)
               {
	
		$ServiceCount = 0;
		
		$item [Translation::GetCaption("HELP")][$ServiceCount] = array();
		$item [Translation::GetCaption("HELP")][$ServiceCount]["title"] = Translation::GetCaption("ADD_NEW_TICKET");
		$item [Translation::GetCaption("HELP")][$ServiceCount]["id"] = "sbm-add_new_ticket";
		$item [Translation::GetCaption("HELP")][$ServiceCount]["class"] = "";
		$item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "support_center.php";
		$ServiceCount++;
		
		$item [Translation::GetCaption("HELP")][$ServiceCount] = array();
		$item [Translation::GetCaption("HELP")][$ServiceCount]["title"] = Translation::GetCaption("SUPPORT_CENTER");
		$item [Translation::GetCaption("HELP")][$ServiceCount]["id"] = "sbm-view_tickets";
		$item [Translation::GetCaption("HELP")][$ServiceCount]["class"] = "";
		//if($sessionManager->getThemeId() == "1")
		$item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "support_center.php";
//		else
//		$item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "support_center_list.php";
		$ServiceCount++;
		
	/*	$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("CONTACT_HELPDESK");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-contact_helpdesk";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "contact_us.php";
		$ServiceCount++;*/
		
		$item [Translation::GetCaption("HELP")][$ServiceCount] = array();
        $item [Translation::GetCaption("HELP")][$ServiceCount]["title"] = Translation::GetCaption("DANGEROUS_GOODS_MENU");
        $item [Translation::GetCaption("HELP")][$ServiceCount]["id"] = "sbm-dangerous_goods";
        $item [Translation::GetCaption("HELP")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "dangerous_goods.php";
		$ServiceCount++;
		
		$item [Translation::GetCaption("HELP")][$ServiceCount] = array();
        $item [Translation::GetCaption("HELP")][$ServiceCount]["title"] = Translation::GetCaption("USER_MANUAL");
        $item [Translation::GetCaption("HELP")][$ServiceCount]["id"] = "sbm-user_manual";
        $item [Translation::GetCaption("HELP")][$ServiceCount]["class"] = "";
		$item [Translation::GetCaption("HELP")][$ServiceCount]["target"]		= "_blanks";
        //if($sessionManager->getUserServiceType() ==  "ROUTING")
		if($sessionManager->getUserServiceType() ==  "ROUTING")
		  $item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "../_assets/user_manuals/User Guide One World Tracked (Product User).pdf";
		else
		  $item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = "../_assets/user_manuals/User Guide One World Tracked (Standard_User).pdf";
        		  
		  
		$ServiceCount++;
		
		$item [Translation::GetCaption("HELP")][$ServiceCount] = array();
        $item [Translation::GetCaption("HELP")][$ServiceCount]["title"] = Translation::GetCaption("PRODUCT_INFORMATION");
        $item [Translation::GetCaption("HELP")][$ServiceCount]["id"] = "sbm-product-document";
        $item [Translation::GetCaption("HELP")][$ServiceCount]["class"] = "";
		$item [Translation::GetCaption("HELP")][$ServiceCount]["target"]		= "_blanks";
        $item [Translation::GetCaption("HELP")][$ServiceCount]["link"] = Translation::GetCaption("PRODUCT_DOCUMENT_FILE");//"../_assets/document/product_en.pdf";
		
		$ServiceCount++;
		
		/*$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("FAQ");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-faq";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "faq.php";
		$ServiceCount++;*/
		$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("GLOSSARY");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-glossary";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "glossary.php";
		$ServiceCount++;
		
		
		$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("LEGAL_DOCUMENTS");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-legal_documents";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "legal_documents.php";
		$ServiceCount++;
		
		$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("ABOUT_US");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-about_us";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "";
		$ServiceCount++;
		
		$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = Translation::GetCaption("LIST_OF_TARIFFS_&_PRODUCTS");
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-list_of_tarrifs_&_products";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "tarrif_list.php";
		$ServiceCount++;
		
		/*
		$item [Translation::GetCaption("SERVICE")][$ServiceCount] = array();
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["title"] = "Tarrif Calculator";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["id"] = "sbm-tarrif_calculator";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["class"] = "";
        $item [Translation::GetCaption("SERVICE")][$ServiceCount]["link"] = "#";
		*/
		$toolbar[] = $item;
               }
		
		/*if($sessionManager->getCountry() == 'DE')
		{		
			$item = array();
			$item ["title"] 		= Translation::GetCaption("SERVICE");
			$item ["id"] 			= "sbm-service";
			$item ["class"] 		= "";
			$item ["link"] 			= "#";
			$item ["target"]		= "_blanks";
			$item ["icon"] 			= '<i class="icon-magnifier"></i>';
			$item ["arrow"] 		= '';
			$toolbar [] 			= $item;
		}
*/

//////////////// TRACKING /////////////////////////////////////////////
      /*  $item = array();
        $item ["title"] = Translation::GetCaption("TRACKING");
        $item ["id"] = "sbm-tracking";
       $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-tasks" aria-hidden="true"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $trackingCount = 0;
        $item [Translation::GetCaption("TRACKING")][$trackingCount] = array();
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["title"] = Translation::GetCaption("TRACKING_ESTIMATE_LIST");
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["id"] = "sbm-tracking-estimate-list";
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["class"] = "";
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["link"] = "tracking_estimeted_list.php";
        $trackingCount++;
        $item [Translation::GetCaption("TRACKING")][$trackingCount] = array();
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["title"] = Translation::GetCaption("ADD_TRACKING_ESTIMATE");
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["id"] = "sbm-add-tracking-estimate";
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["class"] = "";
        $item [Translation::GetCaption("TRACKING")][$trackingCount]["link"] = "tracking_estimate_time.php";
        $toolbar [] = $item;
*/




/////////////////////////// BAGGING //////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("BAGGING");
        $item ["id"] = "sbm-bagging";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-social-dropbox "></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu			=	false;
        $baggingCount = 0;
        $item [Translation::GetCaption("BAGGING")][$baggingCount] = array();
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["title"] = Translation::GetCaption("POSTAL_BAGGING");
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["id"] = "sbm-postal-bagging";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["class"] = "";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["link"] = "assign_bag_number_freight.php";
        $baggingCount++;
        $item [Translation::GetCaption("BAGGING")][$baggingCount] = array();
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["title"] = Translation::GetCaption("FREIGHT_BAGGING");
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["id"] = "sbm-freight-bagging";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["class"] = "";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["link"] = "assign_bag_number_freight.php";
        $baggingCount++;
        $item [Translation::GetCaption("BAGGING")][$baggingCount] = array();
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["title"] = Translation::GetCaption("DOCUMENT_SEARCH");
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["id"] = "sbm-document-search";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["class"] = "";
        $item [Translation::GetCaption("BAGGING")][$baggingCount]["link"] = "search_bag.php";
        $toolbar [] = $item;



//////////////////////// API INTERFACE ////////////////////////////////////
        $item = array();
        $item ["title"] = "API Interface";
        $item ["id"] = "sbm-API-Interface";
        $item ["class"] = "";
        $item ["link"] = "api.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-feed"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;

        $toolbar [] = $item;	





/////////////////// REMOTE AREA //////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("REMOTE_AREAS") ;
        $item ["id"] = "sbm-remote-areas";
        $item ["class"] = "";
        $item ["link"] = "remoteareas.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-share"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;

/////////////////// CURRENCY /////////////////////////////////

        $item = array();
        $item ["title"] = Translation::GetCaption("CURRENCY");
        $item ["id"] = "sbm-currency";
        $item ["class"] = "";
        $item ["link"] = "currency.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-target"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;



///////////////////// COUNTRIES ///////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("COUNTRIES");
        $item ["id"] = "sbm-countries";
        $item ["class"] = "";
        $item ["link"] = "countries.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-map"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;

//////////////////// AGENT ///////////////////////////////////

        $item = array();
        $item ["title"] = Translation::GetCaption("AGENT");
        $item ["id"] = "sbm-agent";
        $item ["class"] = "";
        $item ["link"] = "agent.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-user-following"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;

        
///////////// DEPARTMENT        ////////////////////////////////////////
         $item = array();
        $item ["title"] =  Translation::GetCaption("DEPARTMENT");
        $item ["id"] = "sbm-list-department";
        $item ["class_li"] = "nav-item";
        $item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "department_list.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-gg"></i>';
        //$item ["arrow"] = '<span class="arrow"></span>';

        //$Menu	=	false;
        $Department = 0;
//        $item [Translation::GetCaption("DEPARTMENT")][$Department] = array();
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["title"] =Translation::GetCaption("ADD_NEW_DEPARTMENT") ;
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["id"] = "add_new_department";
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["class"] = "";
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["link"] = "department.php";
//
//        $Department++;
//        $item [Translation::GetCaption("DEPARTMENT")][$Department] = array();
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["title"] = Translation::GetCaption("VIEW_DEPARTMENT");
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["id"] = "list_department";
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["class"] = "";
//        $item [Translation::GetCaption("DEPARTMENT")][$Department]["link"] = "department_list.php";
        
        $toolbar [] = $item;
        ////////////////////
        //
///////////// PERMISSIONS        ////////////////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("PERMISSIONS");
        $item ["id"] = "sbm-permissions";
        $item ["class_li"] = "nav-item";
        $item ["link"] = "add_permission.php";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-lock"></i>';
        $item ["arrow"] = '';

        //$Menu			=	false;
        $permissionsCount = 0;
       /* $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount] = array();
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["title"] = Translation::GetCaption("ADD_PERMISSIONS");
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["id"] = "sbm-sub-permissions";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["class"] = "";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["link"] = "";
        $permissionsCount++;
        
        
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount] = array();
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["title"] = Translation::GetCaption("ADD_GROUPS");
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["id"] = "sbm-sub-groups";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["class"] = "";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["link"] = "add_groups.php";        
        $permissionsCount++;
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount] = array();
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["title"] = Translation::GetCaption("GROUPS_LIST");
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["id"] = "sbm-sub-groups-list";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["class"] = "";
        $item [Translation::GetCaption("PERMISSIONS")][$permissionsCount]["link"] = "groups_list.php";        
        * 
        */
        $toolbar [] = $item;
        ////////////////////
        
        
/////////////////////////// WAREHOUSE MANAGEMENT ////////////////////////////////////

        $item = array();
        $item ["title"] = Translation::GetCaption("WAREHOUSE_MGMT");
        $item ["id"] = "Warehouse Mgmt";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="icon-target"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu	=	false;
        $warehouseMgmtNumber = 0;
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber] = array();
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["title"] = Translation::GetCaption("WAREHOUSE");
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["id"] = "Warehouse";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["class"] = "";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["link"] = "warehouse.php";

        $warehouseMgmtNumber++;
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber] = array();
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["title"] = Translation::GetCaption("RACKS");
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["id"] = "Racks";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["class"] = "";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["link"] = "racks.php";

        $warehouseMgmtNumber++;
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber] = array();
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["title"] = Translation::GetCaption("STOCK_IN_REPORT");
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["id"] = "Stock In Report";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["class"] = "";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["link"] = "report_stock_in.php";

        $warehouseMgmtNumber++;
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber] = array();
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["title"] = Translation::GetCaption("STOCK_OUT_REPORT");
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["id"] = "Stock Out Report";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["class"] = "";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["link"] = "report_stock_out.php";
		$warehouseMgmtNumber++;
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber] = array();
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["title"] = Translation::GetCaption("STOCK_OUT_MULTI_ITEMS");
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["id"] = "stock_out_multi_item";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["class"] = "";
        $item [Translation::GetCaption("WAREHOUSE_MGMT")][$warehouseMgmtNumber]["link"] = "stockoutmulti.php";
        $toolbar [] = $item;


////////////////////////////// OPTIMUS SORTER /////////////////////////////////
        if($sessionManager->getWarehouseId() == '9')
		{
			$item = array();
			$item ["title"] = Translation::GetCaption("OPTIMUS_SORTER_MGMT");
			$item ["id"] = "Optimus Sorter Mgmt";
			$item ["class_li"] = "nav-item";
			$item ["class"] = "nav-link nav-toggle";		
			$item ["link"] = "javascript:;";
			$item ["target"] = "";
			$item ["icon"] = '<i class="icon-paper-plane"></i>';
			$item ["arrow"] = '<span class="arrow"></span>';
	
			//$Menu	=	false;
			$optsorterMgmtNumber = 0;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("SEND_DATA_TO_SORTER");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Send Data to Sorter";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "optimus_sorter_senddata.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("SORTER_DAILY_SCAN_REPORT");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Sorter Daily Scan Report";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "birmingham_scan_report.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("SORTER_SCAN_REPORT");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Sorter Scan Report";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "sorter_scan_report.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("SORTER_ERROR_REPORT");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Sorter Error Report";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "sorter_error_report.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("RESOLVE_REJECTED_FILE");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Resolve Rejected File";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "sorter_rejected_file_list.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("PALLET_CARTON_BARCODE");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Pallet/Carton Barcode";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "pallet_numbers.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("Add Inter-Warehouse Location");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Add Warehouse Location";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "addlocation.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("VIEW_INTER_WAREHOUSE_LOCATION");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "View Locations";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "viewlocations.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("INTER_WAREHOUSE_MOVEMENT");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Inter Warehouse Movement";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "movepallet.php";
	
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("FIND_PALLET");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "Find Pallet";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "findpallet.php";
			
			$optsorterMgmtNumber++;
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber] = array();
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["title"] = Translation::GetCaption("WAREHOUSE_BULK_MOVEMENT");
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["id"] = "warehouse_bulk_movement";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["class"] = "";
			$item [Translation::GetCaption("OPTIMUS_SORTER_MGMT")][$optsorterMgmtNumber]["link"] = "movepallet_custom.php";
			$toolbar [] = $item;
		}
		
////////////////////////////// TRANSLATION /////////////////////////////////////////////	
		$item = array();
        $item ["title"] 	= Translation::GetCaption("TRANSLATION");
        $item ["id"] 		= "translation-menu";
        $item ["class_li"] = "nav-item";
		$item ["class"] = "nav-link nav-toggle";		
        $item ["link"] 		= "javascript:;";
        $item ["target"] 	= "";
        $item ["icon"] 		= '<i class="fa fa-language"></i>';
        $item ["arrow"] 	= '<span class="arrow"></span>';

        //$Menu	=	false;
        $translationNumber = 0;
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber] 			= array();
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["title"] 	= Translation::GetCaption("ADD_LANGUAGE_KEYWORDS");
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["id"] 	= "add-language-keywords-sdb";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["class"] 	= "";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["link"] 	= "add_language_key.php";

        $translationNumber++;
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber] 			= array();
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["title"] 	= Translation::GetCaption("KEYWORDS_LIST");
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["id"] 	= "key-list-sdb";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["class"] 	= "";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["link"] 	= "languagekeys_list.php";
		
		$translationNumber++;
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber] 			= array();
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["title"] 	= Translation::GetCaption("ADD_LANGUAGE");
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["id"] 	= "add-language-sdb";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["class"] 	= "";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["link"] 	= "add_language.php";
		
		$translationNumber++;
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber] 			= array();
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["title"] 	= Translation::GetCaption("LANGUAGE_LIST");
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["id"] 	= "list-language-sdb";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["class"] 	= "";
        $item [Translation::GetCaption("TRANSLATION")][$translationNumber]["link"] 	= "language_list.php";
		
        $toolbar [] = $item;
		
	     
        
        
		 $item = array();
        $item ["title"] = Translation::GetCaption("SALES");
        $item ["id"] = "sales-menu";
        $item ["class_li"] = "nav-item";
        $item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] 		= '<i class="fa fa-hourglass-2"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';

        //$Menu	=	false;
        $sales = 0;
        $item [Translation::GetCaption("SALES")][$sales] = array();
        $item [Translation::GetCaption("SALES")][$sales]["title"] = Translation::GetCaption("SALE_POT_COMMISION");
        $item [Translation::GetCaption("SALES")][$sales]["id"] = "sale_pot_commision";
        $item [Translation::GetCaption("SALES")][$sales]["class"] = "";
        $item [Translation::GetCaption("SALES")][$sales]["link"] = "bonus.php";
		
		$sales++;
        $item [Translation::GetCaption("SALES")][$sales] = array();
        $item [Translation::GetCaption("SALES")][$sales]["title"] = Translation::GetCaption("SALES_CALLS");
        $item [Translation::GetCaption("SALES")][$sales]["id"] = "sales_calls";
        $item [Translation::GetCaption("SALES")][$sales]["class"] = "";
        $item [Translation::GetCaption("SALES")][$sales]["link"] = "sales_call_list.php";
	
        $toolbar [] = $item;
        
        	
		       if($sessionManager->getUserType() == User::USER_TYPE_ADMIN){
        ////////////////////////////// Market Places /////////////////////////////////////////////	
		$item = array();
        $item ["title"] 	= Translation::GetCaption("MANAGE_MARKET_PLACES");
        $item ["id"] 		= "MARKET_PLACES-menu";
        $item ["class_li"]      = "nav-item";
	$item ["class"]         = "nav-link nav-toggle";		
        $item ["link"] 		= "javascript:;";
        $item ["target"] 	= "";
        $item ["icon"] 		= '<i class="fa fa-shopping-cart"></i>';
        $item ["arrow"] 	= '<span class="arrow"></span>';

        //$Menu	=	false;

        $MARKET_PLACES_COUNT = 0;
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT] 			= array();
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["title"] 	= Translation::GetCaption("ADD_MARKET_PLACES");
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["id"] 	= "add-Market-Places-keywords-sdb";
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["class"] 	= "";
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["link"] 	= "market_places.php";

        $MARKET_PLACES_COUNT++;
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT] 			= array();
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["title"] 	= Translation::GetCaption("LIST_MARKET_PLACES");
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["id"] 	= "Market-Places-list-sdb";
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["class"] 	= "";
        $item [Translation::GetCaption("MANAGE_MARKET_PLACES")][$MARKET_PLACES_COUNT]["link"] 	= "market_places_list.php";
        
        $toolbar [] = $item;
        ////////////////////////////// SHIPPING INTEGRATION /////////////////////////////////////////////	
		$item = array();
        $item ["title"] 	= Translation::GetCaption("MANAGE_SHOPPING_PLATFORM");
        $item ["id"] 		= "";
        $item ["class_li"]      = "nav-item";
	$item ["class"]         = "nav-link nav-toggle";		
        $item ["link"] 		= "javascript:;";
        $item ["target"] 	= "";
        $item ["icon"] 		= '<i class="fa fa-plug"></i>';
        $item ["arrow"] 	= '<span class="arrow"></span>';
	$item ["other_links"] = 'shopping_platform_page.php';

        //$Menu	=	false;
        $shippingIntegration = 0;
        $item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration] 			= array();
        $item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["title"] 	= Translation::GetCaption("ADD_SHOPPING_PLATFORM");
        $item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["id"] 		= "add-shipping-integration-keywords-sdb";
        $item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["class"] 	= "";
        $item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["link"] 	= "shopping_platform.php";
		$shippingIntegration++;
		
        if(count($UserShippingPlatformData) > 0){
		 /////////////////// Shipping INTEGRATION for Admin/////////////
        $shipping_integration = 1;
        foreach ($UserShippingPlatformData as $shipping_integration) {
            	$item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration] = array();
		$item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["title"] = Translation::GetCaption($shipping_integration->getTranslationKey());
		$item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["id"] = "sbm-".$shipping_integration->getTitle();
		$item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["class"] = "";
		$item [Translation::GetCaption("MANAGE_SHOPPING_PLATFORM")][$shippingIntegration]["link"] = "shopping_platform_page.php"."?key=".$shipping_integration->getPageKey();
		$shipping_integration++;
        }

}
        $toolbar [] = $item;
        }
        
        
        
        
///////////////////// HELP DESK ////////////////////////////////////////
         $item = array();
        $item ["title"] = Translation::GetCaption("HELP_DESK");
        $item ["id"] = "help_desk";
        $item ["class_li"] = "nav-item";
        $item ["class"] = "nav-link nav-toggle";		
        $item ["link"] = "javascript:;";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-comments-o" style="color:white;"></i>';
        $item ["arrow"] = '<span class="arrow"></span>';


        //$Menu	=	false;
        $HelpDesk = 0;
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk] = array();
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["title"] = Translation::GetCaption("USER_MANUAL");
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["id"] = "add_user_manual";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["class"] = "";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["target"]		= "_blanks";
        //if($sessionManager->getUserServiceType() ==  "ROUTING")
		if($sessionManager->getUserServiceType() ==  "ROUTING")
		  $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "../_assets/user_manuals/User Guide One World Tracked (Product User)1.pdf";
		else if($sessionManager->getUserType() ==  User::USER_TYPE_FINANCE )
			$item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "../_assets/user_manuals/accounts_userguide.pdf";
		else
		  $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "../_assets/user_manuals/User Guide One World Tracked (Standard_User).pdf";
        $HelpDesk++;
		
		$item [Translation::GetCaption("HELP_DESK")][$HelpDesk] = array();
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["title"] = Translation::GetCaption("CONTACT");
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["id"] = "sbm-contact";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["class"] = "";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "support_center_list.php";
		$HelpDesk++;
		
		
		$item [Translation::GetCaption("HELP_DESK")][$HelpDesk] = array();
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["title"] = Translation::GetCaption("GLOSSARY");
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["id"] = "sbm-glossary";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["class"] = "";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "glossary.php";
		$HelpDesk++;
		
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk] = array();
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["title"] =Translation::GetCaption("FAQ") ;
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["id"] = "view-faq";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["class"] = "";
        $item [Translation::GetCaption("HELP_DESK")][$HelpDesk]["link"] = "faq.php";
        
        $toolbar [] = $item;
       
///////////////////////// LOGOUT ///////////////////////////////
        $item = array();
        $item ["title"] = Translation::GetCaption("LOGOUT") ;
        $item ["id"] = "Logout";
        $item ["class"] = "start active";
        $item ["link"] = "login.php?logout=true";
        $item ["target"] = "";
        $item ["icon"] = '<i class="fa fa-sign-out" aria-hidden="true"></i>';
        $item ["arrow"] = '';
        $toolbar [] = $item;








        $this->selectedPage = basename($_SERVER['PHP_SELF']);
        $this->selectedItem = $this->menuItem;

        $size = sizeof($toolbar);
        $idx = 0;
        if ($size > 0) {
            $class = "";
            echo '<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">';
            foreach ($toolbar as $item) {
                if ($item["class"] == 'header') {
                    echo '<li class="header">' . $item["icon"] . $item["title"] . '</li>';
                } elseif (in_array($item["title"], $allowMenu)) {
                    $class_li = "";
                    if (isset($item["class_li"]) && trim($item["class_li"]) != '')
                        $class_li = $item["class_li"];
					$class = "";
                    if (isset($item["class"]) && trim($item["class"]) != '')
                        $class = $item["class"];
                    $icon = @$item["icon"];
                    $arrow = @$item["arrow"];

                    $sub_menu_array = array();
                    if (isset($item[@$item["title"]]) && is_array($item[@$item["title"]])) {
                        foreach ($item[@$item["title"]] as $value) {
                            $sub_menu_array[] = $value['link'];
                        }
                    }
                    $other_links_array = array();
                    if (isset($item["other_links"]) && !empty($item["other_links"])) {
                        $other_links_array = explode(",", $item["other_links"]);
                    }
                    $file_name = "";
                    $linkPage = "";
                    $file_name = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
                    
                    $linkArr = explode('?', @$item["link"]);
                    if(is_array($linkArr)){
                        $linkPage = $linkArr[0];
                    }
                    
                    if (isset($_GET['option']) && $_GET['option'] == 'new') {
                        $file_name = $file_name . "?option=new";
                    }
                    
                    
//                    if(Sessionmanager::getUser()->getUserAccount() == 'ITTEAM') {
//                      if ($file_name == $linkPage || (in_array($file_name, $sub_menu_array)) || (in_array($file_name, $other_links_array))) {
//                          echo "open";
//                      }else{
//                          echo "Not Open";
//                      }
//                    }
                    ?>
        
                    <li class="nav-item<?php echo $class_li; if ($file_name == $linkPage || (in_array($file_name, $sub_menu_array)) || (in_array($file_name, $other_links_array))){ echo " active open"; } ?>" id="<?php echo str_replace(' ', '-', strtolower(@$item['name'])).'-menu'; ?>">
                        <a href="<?php echo @$item["link"]; ?>" id="<?php echo @$item["id"]; ?>" target="<?php echo @$item["target"]; ?>" class="<?php echo $class; ?>">
                            <?php echo $icon; ?>
                            <?php echo '<span class="title">' . @$item["title"] . '</span>'; ?>
                            <?php echo $arrow; ?>
                        </a>
                        <?php
                        if (isset($item[@$item["title"]]) && count($item[@$item["title"]]) > 0) {
                            if (in_array($file_name, $sub_menu_array)) {
                                echo '<ul class="sub-menu" style="display: block;">';
                            } else {
                                echo '<ul class="sub-menu">';
                            }
                            
                            foreach ($item[$item["title"]] as $subitem) {

                                $class = "";
                                if (isset($subitem["class"]) && trim($subitem["class"]) != '')
                                    $class = $subitem["class"];
                            
                            if(isset($allowMenu[$item["title"]]) && is_array($allowMenu[$item["title"]]) && in_array($subitem['title'], $allowMenu[$item["title"]]))
                            {
                                ?>
                            <li class="<?php if ($subitem["link"] == $file_name) echo "active"; ?>" ><a href="<?php echo $subitem["link"]; ?>" id="<?php echo $subitem["id"]; ?>" target="<?php echo @$subitem["target"]; ?>" class="<?php echo $class; ?>"><?php echo $subitem["title"]; ?></a>	</li>
                            <?php
                             } 
                             
                        }
                        echo '</ul>';
                    }
                    ?>
                    </li>
                    <?php
                }
            }
            echo '</ul>';
        }
    }

}
?>