<?php
require_once("../includes/settings/config.inc.php");
require_once ('../includes/autoload/MarketplaceWebServiceOrders/Samples/.config.inc.php'); 
require_once('../includes/autoload/MarketplaceWebServiceOrders/Client.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/MarketplaceIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/GetOrderRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/OrderIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
$amazonOrderId = $_POST["amazonOrderId"];
$status = $_POST["status"];
// United Kingdom
$serviceUrl = "https://mws.amazonservices.co.uk/Orders/2011-01-01";
$config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'MaxErrorRetry' => 5,
 );

 $service = new MarketplaceWebServiceOrders_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

 	//	$data = array();

				$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
				$marketplaceIdList->setId(array(MARKETPLACE_ID));
 
	    	    $request_item = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
				$request_item->setSellerId(MERCHANT_ID);
				$request_item->setAmazonOrderId($amazonOrderId);
			echo '<h2>Order Number: '.$amazonOrderId.' </h2>';
                        echo '<table class="table">';
                        echo '<tr>';
			echo '<th>Order Detail:</th>';
			echo '<td>';	
                        invokeListOrderItems($service, $request_item,$amazonOrderId);// exit;
                        echo '</td>';
                        echo '</tr>';
	 	 $request_getOrder = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
 		 $request_getOrder->setSellerId(MERCHANT_ID);
 		 // Set the list of AmazonOrderIds
 		 $orderIds = new MarketplaceWebServiceOrders_Model_OrderIdList();
 		 $orderIds->setId($amazonOrderId);
		 
		 $request_getOrder->setAmazonOrderId($orderIds);
                echo '<tr>';
		echo '<th>Shipping Detail:</th>';
                echo '<td>';
		invokeGetOrder($service, $request_getOrder,$amazonOrderId);
                echo '</td>';
                echo '</tr>';
		echo '<th style="width:150px;">Oneworld Service</th>';
                echo '<td>';
		serviceWeight($amazonOrderId, $status);
                echo '</td>';
                echo '</tr></table>';
   /****
   This Function will get the Title, ASIN, SKU, Title and other details related to that particular item
   ****/
   $title; $asin; $sku; $quantity; $amount; $name; $addressLine1; $addressLine2; $addressLine3; $city; $country; $district;
    $state; $postalCode;$phone; $currency;
   function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request_item,$index='') 
  {	
    global $title, $asin, $sku, $quantity, $amount, $currency;
      
	  try {
              $response = $service->listOrderItems($request_item);
                if ($response->isSetListOrderItemsResult()) 
				{ 
                    $listOrderItemsResult = $response->getListOrderItemsResult();
                    if ($listOrderItemsResult->isSetNextToken()) 
                    {
                        $listOrderItemsResult->getNextToken();
                    }
  
                        $orderItems = $listOrderItemsResult->getOrderItems();
                        $memberList = $orderItems->getOrderItem();
                        foreach ($memberList as $member) 
						{
                            if ($member->isSetTitle()) 
                            {
                            	echo "<br><br>";
								$title = $member->getTitle();
								echo '<strong>Title:</strong><br>&nbsp;&nbsp;&nbsp;'.$title."<br><br>";
						    }
							if ($member->isSetASIN()) 
                            {
                                 echo "<strong>ASIN:</strong>&nbsp;&nbsp;&nbsp;";
								 $asin = $member->getASIN();
								 echo $asin."<br>";
                            }
                            if ($member->isSetSellerSKU()) 
                            {
                                 echo "<strong>Seller SKU:</strong>&nbsp;&nbsp;&nbsp;";
								 $sku = $member->getSellerSKU();
								 echo $sku."<br>";
                            }                            
                            if ($member->isSetQuantityOrdered()) 
                            {
                         		echo "<strong>Quantity:</strong>&nbsp;&nbsp;&nbsp;";
								$quantity = $member->getQuantityOrdered();
								echo $quantity."<br>";
                            }   
							if ($member->isSetItemPrice()) 
							{ 
                                $itemPrice = $member->getItemPrice();
                               
                                if ($itemPrice->isSetAmount()) 
                                {
                                    $amount = $itemPrice->getAmount();
							 	}	
								if ($itemPrice->isSetCurrencyCode()) 
                                {
                                    $currency = $itemPrice->getCurrencyCode();
                                }							
								
                            }
							
							
							
							
							
							if ($member->isSetShippingPrice()) { 
                                $shippingPrice = $member->getShippingPrice();
                                if ($shippingPrice->isSetAmount()) 
                                {
                                    $shippingCost = $shippingPrice->getAmount();
                                }
                            } 
                            if ($member->isSetGiftWrapPrice()) { 
                                $giftWrapPrice = $member->getGiftWrapPrice();
                                if ($giftWrapPrice->isSetAmount()) 
                                {
                                   $giftWrapCost = $giftWrapPrice->getAmount();
                                }
                            } 
                            if ($member->isSetItemTax()) { 
                                $itemTax = $member->getItemTax();
                                if ($itemTax->isSetAmount()) 
                                {
                                    $itemTaxCost = $itemTax->getAmount();
                                }
                            } 
                            if ($member->isSetShippingTax()) { 
                                $shippingTax = $member->getShippingTax();
                                if ($shippingTax->isSetAmount()) 
                                {
                                    $shippingTaxCost = $shippingTax->getAmount();
                                }
                            } 
                            if ($member->isSetGiftWrapTax()) { 
                                $giftWrapTax = $member->getGiftWrapTax();
                                if ($giftWrapTax->isSetAmount()) 
                                {
                                    $giftWarpTaxCost = $giftWrapTax->getAmount();
                                }
                            } 
                            if ($member->isSetShippingDiscount()) { 
                                $shippingDiscount = $member->getShippingDiscount();
                                if ($shippingDiscount->isSetAmount()) 
                                {
                                    $shippingDiscountCost = $shippingDiscount->getAmount();
                                }
                            } 
                            if ($member->isSetPromotionDiscount()) { 
                                $promotionDiscount = $member->getPromotionDiscount();
                                if ($promotionDiscount->isSetAmount()) 
                                {
                                    $promotionDiscountCost = $promotionDiscount->getAmount();
                                }
                            }
							echo "<strong>Price:</strong>&nbsp;&nbsp;&nbsp;";
							echo ($amount+$shippingCost+$giftWrapCost+$itemTaxCost+$shippingTaxCost+
							      $giftWarpTaxCost+$shippingDiscountCost+$promotionDiscountCost).$currency."<br>"; 						                       
                        }                    
                } 
		//	return $order_item;
     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
     }
	 //echo 'abc<br>';
 }
 
 /*
   Get Orders
   This Function will get the records in chunks of 10
 
  * Get Order Action
  * This operation takes up to 50 order ids and returns the corresponding orders.
 */
  function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request,$amazonOrderId='') 
  {
	  global $name, $addressLine1, $addressLine2, $addressLine3, $city, $country, $district, $state, $postalCode,$phone;
	  try {
              $response = $service->getOrder($request);
    		   //print_r($response);
			   if ($response->isSetGetOrderResult()) 
			   { 
			        $getOrderResult = $response->getGetOrderResult();
                    if ($getOrderResult->isSetOrders()) 
					{ 
			            $orders = $getOrderResult->getOrders();
                        $memberList = $orders->getOrder();
			            foreach ($memberList as $member) 
						{
                            if ($member->isSetLastUpdateDate()) 
                            {
                               // $data[$index]["LastUpdateDate"] = $member->getLastUpdateDate();
                            }
                            if ($member->isSetShippingAddress()) { 
                                $shippingAddress = $member->getShippingAddress();
								echo '<br><br><strong>Address:</strong>&nbsp;&nbsp;&nbsp;';
                                if ($shippingAddress->isSetName()) 
                                {
                                    $name = $shippingAddress->getName();
									echo "<br>".$name."<br>";
                                }
                                if ($shippingAddress->isSetAddressLine1()) 
                                {
                                   $addressLine1 = $shippingAddress->getAddressLine1();
								   echo $addressLine1."<br>";
                                }
																
                                if ($shippingAddress->isSetAddressLine2()) 
                                {
                                   $addressLine2 = $shippingAddress->getAddressLine2();
								   echo $addressLine2."<br>";
                                }
								
								if ($shippingAddress->isSetAddressLine3()) 
                                {
                                    $addressLine3 = $shippingAddress->getAddressLine3();
									echo $addressLine3."<br>";
                                }
								
								if($addressLine1=='')
								{
									$addressLine1 = $addressLine2;
								}
								
                                if ($shippingAddress->isSetCity()) 
                                {
                                    $city = $shippingAddress->getCity();
									echo $city."<br>";
                                }
                                if ($shippingAddress->isSetCounty()) 
                                {
                                    $county = $shippingAddress->getCounty();
									//$country = $shippingAddress->countryCode();
									echo $county."<br>";
                                }

                                if ($shippingAddress->isSetDistrict()) 
                                {
                                    $district = $shippingAddress->getDistrict();
									echo $district."<br>";
                                }
                                if ($shippingAddress->isSetStateOrRegion()) 
                                {
                                    $state = $shippingAddress->getStateOrRegion();
									echo $state."<br>";
                                }
								if($shippingAddress->isSetCountryCode())
								{
									$country = $shippingAddress->getCountryCode();
									echo '<strong>Country:</strong>&nbsp;&nbsp;&nbsp;'.$country."<br>";
								}
                                if ($shippingAddress->isSetPostalCode()) 
                                {
                                    $postalCode = $shippingAddress->getPostalCode();
									echo '<strong>Postcode:</strong>&nbsp;&nbsp;&nbsp;'.$postalCode."<br>";
                                }
								 if ($shippingAddress->isSetPhone()) 
                                {
                                     $phone = $shippingAddress->getPhone();
									 echo '<strong>Telephone:</strong>&nbsp;&nbsp;&nbsp;'.$phone."<br>";
                                }
								
								
								
                			 }
				        }
                    } 
                }             

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
     } 		
 }
 
 function serviceWeight($amazonOrderId, $status)
 {
		global $name, $addressLine1, $addressLine2, $addressLine3, $city, $country, $district, $state, $postalCode,
		$phone,$currency, $title, $asin, $sku, $quantity, $amount;
		//echo $addressLine1; exit;
		$user = SessionManager::getUser();
		$useraccount	= @$user->getUserAccount();
		$username		= @$user->getUserName();
		$userpass		= @$user->getUserPass();
		$serviceType	= $user->getUserServiceType();
		$amazonConsignment	=	checkConsignment($amazonOrderId);
		
		//////////Get Service Name List from Services Table and store it in an array //////////////
			$services = new ServiceFilter();
			$services->addSpecialServicesFilter();
			$services->addAccountNumberFilter($useraccount);
			
			$service_name = $services->getRecordFromServiceAndName();
			$service_list = array();
			if(count($service_name)>0)
			{
				foreach($service_name as $ser_name)
				{
					$service_list[] = $ser_name->getName();
				}
			}
			else
			{
				$service_list	=	array();
			}
			///////////////////////////////////////////////////////////////////////////////////////////
		
		if(count($amazonConsignment)>0)
                {
					echo "<br><strong>Tracking Number:</strong>".$amazonConsignment->getAwb()."<br>";
					$db_status = $amazonConsignment->getConsignmentStatus();
				}
				else
				{
					echo "<br>";
				}
				
				echo "<br>";
				if((trim($status) == 'Unshipped' || trim($status) == 'Shipped') && (count($amazonConsignment)<=0
				   || $amazonConsignment->getDateReceived() == ''))
				{
					echo '<span style="width:75px"><strong>Select a Service:</strong></span>';
					
					if ($serviceType == USER::USER_SERVICE_BOTH)
					{ 
					echo '<input type="checkbox" name="routing[]" id="routing-'.$amazonOrderId.'" 
					value="routing" onchange="$(#service-<?php echo $amazonOrderId;?>).toggle();"/> Routing <br />';
					echo '<select name="service_name[]" id="service-'.$amazonOrderId.'" 
					style="width: 180px; color:#666 !important;">
					<option value="">Please Select a Service</option>';
							foreach($service_list as $ser_list) 
							{
							   echo '<option value="'. $ser_list .'">'. $ser_list .'</option>';
							}
					echo '</select><br>';
					}
					else if($serviceType == USER::USER_SERVICE_ROUTING )
					{ 					
					echo '<input type="checkbox" name="routing[]" id="routing-'.$amazonOrderId.'" 
					value="routing" onchange="$(#service-<?php echo $amazonOrderId;?>).toggle();" 
					checked="checked" readonly="readonly" disabled="disabled"/> 
					Routing <br/>';
					}
					else if($serviceType == USER::USER_SERVICE_CHOICE )
					{
					echo '<select name="service_name[]" id="service-'.$amazonOrderId.'" 
					style="width: 180px; color:#666 !important;">
					<option value="">Please Select a Service</option>';
							foreach($service_list as $ser_list) 
							{
							   echo '<option value="'. $ser_list .'">'. $ser_list .'</option>';
							}
					echo '</select><br>';
					}
				}
				elseif(count($amazonConsignment) > 0 && $amazonConsignment->getDateReceived() != '')
				{
					echo "<b>Service Type:</b>".$amazonConsignment->getServiceType()."<br>";
				}
				else
				{
					echo "Service Type:".''."<br>";
				}
				
				if((trim($status) == 'Unshipped' || trim($status) == 'Shipped')&& (count($amazonConsignment)<=0
				   || $amazonConsignment->getDateReceived() == ''))
				{
					echo '<br>';
					echo '<span style="width:75px"><strong>Weight: </strong></span>';
					echo '<input type="text" name="weight[]" id="weight-'.$amazonOrderId.'" 
					size="5" style="color:#999;"/>KG<br>';	
					
					echo '<br>';
					echo '<span style="width:75px"><strong>Number of Pieces: </strong></span>';
					echo '<input type="text" name="noOfPieces[]" id="noOfPieces-'.$amazonOrderId.'" 
					size="5" style="color:#999;" value="'.$quantity.'"/><br>';				
				}
				elseif( count($amazonConsignment) > 0 && $amazonConsignment->getDateReceived() != '')
				{
					echo "<b>Weight</b>".$amazonConsignment->getWeight().'Kg'."<br>";
					echo "<b>No. of Pieces</b>".$amazonConsignment->getNumberPieces()."<br>";
				}
				else
				{
					echo "Weight".''."<br>";
					echo "Number of Pieces".''."<br>";
				}
				
				echo '<br>';
				///////////Buttons---- View Label, Dispatch and Generate Label
				 //echo $amazonConsignment->getDateReceived().'abxc'; exit;
				echo '<div id="labelLinks-'.$amazonOrderId.'">';
				if((trim($status) == 'Unshipped' || trim($status) == 'Shipped') && (count($amazonConsignment)<=0
				   || $amazonConsignment->getDateReceived() == ''))
				{
                ?>
                <a href="#labelDisplayLink-<?php echo $amazonOrderId;?>" class="ebayButton" 
                onclick="return showlabels('<?php echo $amazonOrderId;?>',
                '<?php echo $amazonOrderId;?>',
                '<?php echo str_replace("'","",$name);?>','<?php echo str_replace("'","",$addressLine1);?>',
                '<?php echo str_replace("'","",$addressLine2);?>','<?php echo str_replace("'","",$addressLine3);?>',
                '<?php echo $city;?>','<?php echo $country;?>','<?php echo $postalCode;?>','<?php echo $phone;?>',              
                '<?php echo $currency;?>','<?php echo $useraccount;?>','<?php echo $username;?>','<?php echo @$userpass;?>',
                '<?php echo $status;?>','service-<?php echo @$amazonOrderId;?>','weight-<?php echo $amazonOrderId;?>',
                'routing-<?php echo $amazonOrderId;?>','<?php echo  str_replace(array("'","\""), "", $title);?>','noOfPieces-<?php echo $amazonOrderId;?>'
                ,'<?php echo $amount;?>' );"
                id="labelDisplayLink-<?php echo $amazonOrderId;?>">Generate Label</a>
                <?php
				}
				else if(count($amazonConsignment) >0 && (trim($status) == 'Unshipped' || trim($db_status) == 'received'))
				{					
				?>	
                    <a href="#" class="ebayButton" onclick="return displayLabel('<?php echo $amazonConsignment->getHawb();?>');
                    " >View Label</a> 
                    <a href="amazon_invoice.php?hawb=<?php echo @$amazonOrderId; ?>&description=<?php echo $title;?>" 
                    target="blank" class="ebayButton">Print Invoice</a>
                    
                    <a href="#" class="ebayButton" onclick="return dispatchLabel('<?php echo $amazonConsignment->getHawb();?>', 
                    '<?php echo $amazonConsignment->getAwb();?>')" >Dispatch</a><br />	
                <?php	
				}
				else if(count($amazonConsignment) >0 && trim($status) == 'Shipped' && (trim($db_status) == 'delivered' || 
				trim($db_status) == 'warehouse received' || trim($db_status == 'booked')))
				{					
				?>	
                    <a href="#" class="ebayButton" 
                    onclick="return displayLabel('<?php echo $amazonConsignment->getHawb();?>');" >View Label</a> <br />
                    <a href="amazon_invoice.php?hawb=<?php echo @$amazonOrderId; ?>&description=
					<?php echo $title;?>&single=single" 
                    target="blank" class="ebayButton">Print Invoice</a>                    
                    <a class="ebayButton">Dispatched</a>	
                <?php	
				}
				else
				{
					echo '';
				}
				echo '<div>';
 }
 
 /***
 This Function checks either the shipment exist in our system or not
 ***/
 function checkConsignment($hawbNumber)
	{
		$congignmentCheck	=	new ConsignmentFilter();
		
		$congignmentCheck->addHawbFilter($hawbNumber);
		if( $congignmentCheck->getCount()>0)
		{
			$rowlist		=	$congignmentCheck->getList();
			$rowlistData	=	$rowlist[0];
		}
		else
		{
			$rowlistData	= array();
		}
		return $rowlistData;
	}
	
	

                

            
