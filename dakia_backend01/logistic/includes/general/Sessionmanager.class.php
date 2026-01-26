<?php
////////////////////////////////////////////////////
//
//	Session Management
//
//	The current user could be identified by a session variable or a cookie.
//	The current user may be a customer or an anonymous user. We could have the
//	scenario where we know the customer, but they may not have logged in yet.
//	A basket can be linked to a customer or just to the current user.
//	And when a user logs in the contents of the basket has to be dealt with appropriately.
//	-	The purpose of this class is to encapsulate all this functionality. We should
//		be able to say give me basket, or give me customer, or is this user logged in,
//		with out any consideration of session variables, cookies, if the user has just logged in, etc.
//
////////////////////////////////////////////////////

/**
 * Session Manager
 * @package Ecommerce
 */
class Sessionmanager
{
	// store state for current page for this session
	private static $customer = null;
	private static $basket = null;
	private static $login_msg = "";
	private static $parcelGroupLookup = null;
	private static $parcelGroup = null;
	private static $parcelArray = null;
	private static $parcelCount = 0;
	private static $adminUser = null;
	private static $parcelGroupFilter = null;
    private static $additionalChargesFilter  = null;
	private static $user = null;
	private static $label = null;

	/**
	* Constructor.
	*/
	private function __construct() // make private to force use of factory method.
	{                                                                   
		// determine if user is logged in
		if (isset($_SESSION["logged_in"])) $this->logged_in = ($_SESSION["logged_in"] == "yes");
	}

	/**
	* Login a customer
	*
	* @param emailaddress
	* @param password
	* @return bool - login successful
	*/
	function login($emailaddress, $password, $page = "")
	{
		$customers = array();
		$errorMsg = "";

		$password_md5 = md5(trim(strip_tags($password)));
		$emailaddress = strip_tags($emailaddress);

		//$where = " email_address = '$emailaddress' AND password = '$password_md5'";
		$where = " email_address = '$emailaddress'";

		$CusObj = new Customer;
		$customers = $CusObj->getAnyCustomer($where, false);

		if(count($customers) > 0)
		{
			// emails address should be unique
			if (count($customers) > 1)
			{
				EmailSend::EmailError("Customer repeated email: $emailaddress");
			}
			$customer = $customers[0];

			// Check if this user is active
			if (!$customer->getActive())
			{
				$errorMsg = "Sorry this email address has not be activated yet.";
			}
			// Check the password
			if (($errorMsg == "") && ($customer->getPassword() != $password_md5))
			{
				$errorMsg = "Password is not correct.";
			}

			// Save the user to session
			if ($errorMsg == "")
			{
				Sessionmanager::setCustomerId($customer->getId());

				// if they have a basket - update the customer id
				if (Sessionmanager::getBasketId() > 0)
				{
					$BasObj = new Basket(Sessionmanager::getBasketId());
					$BasObj->setCustomerId($customer->getId());
					$BasObj->updateCustomerId();
				}
			}
		}
		else
		{
			$errorMsg = "This email address is not registered on the system.";
		}

		// Save the error message
		$_SESSION['login_message'] = $errorMsg;

		return ($errorMsg == "");
	}

	/**
	 * Logs user out
	 *
	 */
	public static function logout()
	{
		self::setBasketId(0);
		self::setCustomerId(0);
	}

	/**
	* Indicates if a customer is logged in.
	* Returns boolean.
	*
	*/
	public static function isLoggedIn ()
	{
		if (Sessionmanager::getCustomerId() > 0) return true;
		return false;
	}
    
    public function setDelCountryCode($val)
    {
        $_SESSION['delCountryCode']=$val;
    }
    
    public function getDelCountryCode()
    {
        return $_SESSION['delCountryCode'];
    }

	/**
	* Indicates if the current user is a business account holder
	*
	*/
	public static function isBusinessAccount()
	{
		$accountHolder = false;
		//
		if (self::isLoggedIn())
		{
			// If current customer has an account id - then is business account.
			$accountHolder = (self::getCustomer()->getAccountId() > 0);
		}
		return $accountHolder;
	}

	/**
	* Gets the current customer - may or may not be logged in.
	* Returns a customer object.
	*
	*/
	public static function getCustomer ()
	{
		// get customer if not already retrieved
		if (self::$customer == null)
		{
			$customerId = self::getCustomerId();
			// Get the customer (could be new if customer id is 0).
			self::$customer = new Customer($customerId);
		}
		return self::$customer;
	}

	/**
	* Add a parcel group to existing basket
	*
	* @param unknown_type $parcelGroupId
	*/
	public static function addParcelGroupToCurrentBasket($parcelGroupId)
	{
		$basket = Sessionmanager::getBasket();
		//
		$basket->addToBasket($parcelGroupId);
	}


	public static function saveParcelGroupFilter($parcelGroupFilter)
	{
		$filterStr = "";
		if ($parcelGroupFilter != null)
		{
			$filterStr = serialize($parcelGroupFilter);
		}
		$_SESSION["parcelgroupfilter"] = $filterStr;
		self::$parcelGroupFilter = $parcelGroupFilter;
	}

	public static function getParcelGroupFilter()
	{
		if (self::$parcelGroupFilter == null)
		{
			$filterStr = @$_SESSION["parcelgroupfilter"];
			if ($filterStr != "")
			{
				self::$parcelGroupFilter = unserialize($filterStr);
			}
			else
			{
				self::$parcelGroupFilter = new ParcelGroupFilter();
				self::$parcelGroupFilter->setLimit(30);
			}
		}
		return self::$parcelGroupFilter;
	}

	public static function AddAdminUser($adminUser)
	{
		self::$adminUser = $adminUser;
		$str = ($adminUser == null) ? "" : self::$adminUser->serialize();
		$_SESSION["admin_user"] = $str;
	}
	public static function getAdminUser()
	{
		if (self::$adminUser == null)
		{
			$adminStr = @$_SESSION["admin_user"];
			if ($adminStr != "")
			{
				self::$adminUser = AdminUser::unserialize($adminStr);
			}
			else
			{
				self::$adminUser = new AdminUser();
			}
		}
		return self::$adminUser;
	}

	/**
	* Get the basket id either from session or from a cookie
	*
	* @return unknown
	*/
	public static function getBasketId()
	{
		$basketId = intval(@$_SESSION['basket']['id']);

		// not set in the sesion check in the cookie
		if ($basketId <= 0)
		{
			$basket_id = intval(@$_COOKIE['basket']);
		}
		return $basketId;
	}

	/**
	* Set the current basket id
	*
	* @param int $id
	*/
	public static function setBasketId($id) {
		// Set the session
		$_SESSION['basket']['id'] = $id;

		// save to cookie for 7 days
		setcookie("basket_id", $id, time()+60*60*24*7, "/");

		// Clear the basket object - forces retrieve if the basket has changed.
		self::$basket == null;
	}

	/**
	* Get the current basket
	*
	* @return Basket
	*
	*/
	public static function getBasket()
	{
		if (self::$basket == null)
		{
			// get basket id from session
			$basket_id = Sessionmanager::getBasketId();

			// create basket, if it does not exist
			if (!$basket_id)
			{

				$basket = Basket::createBasket();
				$basket_id = $basket->getId();
			}

			// set basket in the session
			Sessionmanager::setBasketId($basket_id);

			// Set the basket for this instance
			self::$basket = new Basket($basket_id);
		}
		// return basket object
		return self::$basket;
	}


	/**
	* Get the current parcel group
	*
	* @return Parcel Group Object
	*
	*/
	public static function getParcelGroup()
	{
		if (self::$parcelGroupLookup == null)
		{
			self::$parcelGroupLookup = new ParcelGroup();
			// now populate with database values
			$new = true;
			if (self::getParcelGroupId() > 0)
			{
				$new = !self::$parcelGroupLookup->fillFromDatabase(self::getParcelGroupId());
			}
			// this is a new parcel group - set default insurance
			if ($new)
			{
				$insuranceList = Insurance::getAnyInsurance("", true, "cover_amount");
				if (sizeof($insuranceList) > 0)
				{
					self::$parcelGroupLookup->setInsuranceCover ($insuranceList[0]->getCoverAmount());
					self::$parcelGroupLookup->setInsurancePremium ($insuranceList[0]->getPremiumAmount());
				}
			}
		}
//echo "<br>Parcel Group " . self::getParcelGroupId(); die;
		return self::$parcelGroupLookup;
	}

	/**
	* Allow full parcel group object to be stored in session
	*
	*/
	public static function setQuoteParcelGroup ($parcelGroup)
	{
		self::$parcelGroup = $parcelGroup;
		$serializeStr = $parcelGroup->serialize();
		$_SESSION['parcelgroup'] = $serializeStr;

		// Save the parcels
		self::clearQuoteParcels();

		foreach ($parcelGroup->getParcels() as $parcel)
		{
			self::addQuoteParcel($parcel);
		}
	}
	/**
	* Get the parcel group that has been stored in a session
	*
	*/
	public static function getQuoteParcelGroup()
	{
		if (self::$parcelGroup == null)
		{
			$parcelGroupSerialized = @$_SESSION['parcelgroup'];
			if ($parcelGroupSerialized != "")
			{
				self::$parcelGroup = ParcelGroup::unserialize($parcelGroupSerialized);

				foreach (self::getQuoteParcels() as $parcel)
				{
					self::$parcelGroup->addParcel($parcel);
				}
			}
		}
		return self::$parcelGroup;
	}
	/**
	* Clear the parcel group from session
	*
	*/
	public static function clearQuoteParcelGroup ()
	{
		self::$parcelGroup = null;
		$_SESSION['parcelgroup'] = "";

		// clear the parcels
		self::clearQuoteParcels();
	}
	/**
	* Clear all parcels that have been stored in this session
	*
	*/
	private static function clearQuoteParcels()
	{
		self::$parcelArray = null;
		self::$parcelCount = 0;
		$_SESSION['parcelcount'] = 0;
	}
	/**
	* Add a parcel to session
	*/
	private function addQuoteParcel($parcel)
	{
		// ensure have got parcels
		self::getQuoteParcels();

		// add parcel to array
		self::$parcelArray[] = $parcel;
		// update count
		self::$parcelCount = sizeof(self::$parcelArray);
		$_SESSION['parcelcount'] = self::$parcelCount;
		// save parcel to session
		$i = self::$parcelCount - 1;
		$_SESSION['parcel'.$i] = $parcel->serialize();
	}
	/**
	* Get a parcel that has been stored in session
	*/
	private static function getQuoteParcels()
	{
		if (self::$parcelArray == null)
		{
			self::$parcelCount = intval(@$_SESSION['parcelcount']);

//echo "count : " . self::$parcelCount . "<br>";
			//
			self::$parcelArray = array();
			//
			for ($i=0; $i< self::$parcelCount; $i++)
			{
				$serializeStr = @$_SESSION['parcel'.$i];
				if ($serializeStr != "")
				{
					self::$parcelArray[] = Parcel::unserialize($serializeStr);
//echo  "<br>";
//echo self::$parcelArray[sizeof(self::$parcelArray) - 1]->getWeight();
//echo  "<br>";
//echo self::$parcelArray[sizeof(self::$parcelArray) - 1]->getIsDocuments() ? "Yes":"No";
				}
			}
//die;
		}
		return self::$parcelArray;
	}

	public static function clearBooking()
	{
		@$_SESSION["booking"] = null;
	}


	////////////////////////////////////////////////
	//
	// getters
	//
	////////////////////////////////////////////////

	public static function getBookingStage()            { return intval(@$_SESSION['booking']['stage']);	}
	public static function getParcelGroupId()            { return @$_SESSION['booking']['parcelgroup']['id'];	}
	public static function getTempParcelGroupId()        { return @$_SESSION['booking']['tempparcelgroup']['id'];	}

	public static function getSessionId()                { return @$_SESSION['session']['id'];	}
	public static function getSessionPage()            { return @$_SESSION['session']['page'];	}
	public static function getSessionPrevPage()        { return @$_SESSION['session']['prev_page'];	}
	public static function getEmailAddress()
	{
		if (Sessionmanager::isLoggedIn())
		{
			return Sessionmanager::getCustomer()->getEmailAddress();
		}

		return @$_SESSION['session']['email_address'];
	}

	public static function getCustomerId()                { return @$_SESSION['customer']['id'];	}
	public static function getOrderId()                { return @$_SESSION['order']['id'];	}

	public static function isLoginPage()                {

													if (Sessionmanager::getSessionPage() == "my_account_login.php")
													{
														return true;
													}

													return false;
												}
	// retrieve address info for display
	public static function getAddressesStored()         { return (@$_SESSION['booking']['address_stored'] == "Y");	}
	public static function getCollFullName()            { return @$_SESSION['booking']['coll_fullname'];	}
	public static function getCollCompany()             { return @$_SESSION['booking']['coll_company'];	}
	public static function getCollAddr1()               { return @$_SESSION['booking']['coll_address_line_1'];	}
	public static function getCollAddr2()               { return @$_SESSION['booking']['coll_address_line_2'];	}
	public static function getCollAddr3()               { return @$_SESSION['booking']['coll_address_line_3'];	}
	public static function getCollTown()                { return @$_SESSION['booking']['coll_town'];	}
	public static function getCollRegion()              { return @$_SESSION['booking']['coll_region'];	}
	public static function getCollUsaState()            { return @$_SESSION['booking']['coll_usa_state'];	}
	public static function getCollPostcode()            { return @$_SESSION['booking']['coll_postcode'];	}
	public static function getCollTelephone()           { return @$_SESSION['booking']['coll_telephone'];	}
	public static function getDestFullName()            { return @$_SESSION['booking']['dest_fullname'];	}
	public static function getDestCompany()             { return @$_SESSION['booking']['dest_company'];	}
	public static function getDestAddr1()               { return @$_SESSION['booking']['dest_address_line_1'];	}
	public static function getDestAddr2()               { return @$_SESSION['booking']['dest_address_line_2'];	}
	public static function getDestAddr3()               { return @$_SESSION['booking']['dest_address_line_3'];	}
	public static function getDestTown()                { return @$_SESSION['booking']['dest_town'];	}
	public static function getDestRegion()              { return @$_SESSION['booking']['dest_region'];	}
	public static function getDestUsaState()            { return @$_SESSION['booking']['dest_usa_state'];	}
	public static function getDestPostcode()            { return @$_SESSION['booking']['dest_postcode'];	}
	public static function getDestTelephone()           { return @$_SESSION['booking']['dest_telephone'];	}
	public static function getLoginMessage()            { return @$_SESSION['login_message'];	}
	public static function getCollPoint()               { return @$_SESSION['booking']['coll_point']; }

	// delivery address
	public static function getDelFullName()            { return @$_SESSION['booking']['del_address_line_1'];	}
	public static function getDelAddr1()                { return @$_SESSION['booking']['del_address_line_1'];	}
	public static function getDelAddr2()                { return @$_SESSION['booking']['del_address_line_2'];	}
	public static function getDelAddr3()                { return @$_SESSION['booking']['del_address_line_3'];	}
	public static function getDelTown()                { return @$_SESSION['booking']['del_town'];	}
	public static function getDelRegion()                { return @$_SESSION['booking']['del_region'];	}
	public static function getDelPostcode()            { return @$_SESSION['booking']['del_postcode'];	}

	////////////////////////////////////////////////
	//
	// setters
	//
	////////////////////////////////////////////////

	public static function setBookingStage()            { $_SESSION['booking']['stage'] = 0;	}
	public static function setParcelGroupId($id)
	{
		self::$parcelGroup = null; // ensure parcel group is clear if id is set.
		$_SESSION['booking']['parcelgroup']['id'] = $id;
	}
	public static function setTempParcelGroupId($id)      { $_SESSION['booking']['tempparcelgroup']['id'] = $id;	}

	public static function setSessionId($id)               { $_SESSION['session']['id'] = $id;	}
	public static function setSessionPage($page)           { $_SESSION['session']['page'] = $page;	}
	public static function setSessionPrevPage($page)       { $_SESSION['session']['prev_page'] = $page;	}
	public static function setEmailAddress($email_address) { $_SESSION['session']['email_address'] = $email_address;	}

	public static function setCustomerId($id)
	{
		// Ensure customer object is clear.
		self::$customer = null;

		$_SESSION['customer']['id'] = $id;
	}

	public static function setOrderId($id)                 { $_SESSION['order']['id'] = $id;	}

	// store address info for redisplay
	public static function setAddressesStored($stored_flag)     { $_SESSION['booking']['address_stored'] = ($stored_flag ? "Y":"NO");	}
	public static function setCollFullName($coll_fullname)      { $_SESSION['booking']['coll_fullname'] = $coll_fullname;	}
	public static function setCollCompany($company)             { $_SESSION['booking']['coll_company'] = $company;	}
	public static function setCollAddr1($coll_address_line_1)   { $_SESSION['booking']['coll_address_line_1'] = $coll_address_line_1;	}
	public static function setCollAddr2($coll_address_line_2)   { $_SESSION['booking']['coll_address_line_2'] = $coll_address_line_2;	}
	public static function setCollAddr3($coll_address_line_3)   { $_SESSION['booking']['coll_address_line_3'] = $coll_address_line_3;	}
	public static function setCollTown($coll_town)              { $_SESSION['booking']['coll_town'] = $coll_town;	}
	public static function setCollRegion($coll_region)          { $_SESSION['booking']['coll_region'] = $coll_region;	}
	public static function setCollUsaState($state)              { $_SESSION['booking']['coll_usa_state'] = $state;	}
	public static function setCollPostcode($coll_postcode)      { $_SESSION['booking']['coll_postcode'] = $coll_postcode;	}
	public static function setCollTelephone($coll_telephone)    { $_SESSION['booking']['coll_telephone'] = $coll_telephone;	}
	public static function setDestFullName($dest_fullname)      { $_SESSION['booking']['dest_fullname'] = $dest_fullname;	}
	public static function setDestCompany($company)             { $_SESSION['booking']['dest_company'] = $company;	}
	public static function setDestAddr1($dest_address_line_1)   { $_SESSION['booking']['dest_address_line_1'] = $dest_address_line_1;	}
	public static function setDestAddr2($dest_address_line_2)   { $_SESSION['booking']['dest_address_line_2'] = $dest_address_line_2;	}
	public static function setDestAddr3($dest_address_line_3)   { $_SESSION['booking']['dest_address_line_3'] = $dest_address_line_3;	}
	public static function setDestTown($dest_town)              { $_SESSION['booking']['dest_town'] = $dest_town;	}
	public static function setDestRegion($dest_region)          { $_SESSION['booking']['dest_region'] = $dest_region;	}
	public static function setDestUsaState($state)              { $_SESSION['booking']['dest_usa_state'] = $state;	}
	public static function setDestPostcode($dest_postcode)      { $_SESSION['booking']['dest_postcode'] = $dest_postcode;	}
	public static function setDestTelephone($dest_telephone)    { $_SESSION['booking']['dest_telephone'] = $dest_telephone;	}
	public static function setCollPoint($value)                 { $_SESSION['booking']['coll_point'] = $value;	}

	// store delivery address
	public static function setDelFullName($del_fullname)        { $_SESSION['booking']['del_fullname'] = $del_fullname;	}
	public static function setDelAddr1($del_address_line_1)     { $_SESSION['booking']['del_address_line_1'] = $del_address_line_1;	}
	public static function setDelAddr2($del_address_line_2)     { $_SESSION['booking']['del_address_line_2'] = $del_address_line_2;	}
	public static function setDelAddr3($del_address_line_3)     { $_SESSION['booking']['del_address_line_3'] = $del_address_line_3;	}
	public static function setDelTown($del_town)                { $_SESSION['booking']['del_town'] = $del_town;	}
	public static function setDelRegion($del_region)            { $_SESSION['booking']['del_region'] = $del_region;	}
	public static function setDelPostcode($del_postcode)        { $_SESSION['booking']['del_postcode'] = $del_postcode;	}


	/***
	 * Factory method to get the consignment filter.
	 * A user is always restricted to consignments in their account,
	 * if they have an account number assigned to them.
	 *
	 * @return ConsignmentFilter
	 */
	public static function getConsignmentFilter()
	{
		$filter = new ConsignmentFilter();
		
		$user = SessionManager::getUser();
		//print_r($user);
		//echo $user->getUserAccount();
		//exit;
		if ($user->getUserAccount() != "")
		{
			$filter->addAccountFilter($user->getId());
			//exit;
		}
		return $filter;
	}
    
    public static function getAddressFilter()
    {
        $filter = new AddressFilter();
        //
        $user = SessionManager::getUser();
       
        $filter->addUserFilter($user->getId());
        return $filter;
    }

	/**
	 * Gets currently logged in user.
	 *
	 * @return User
	 */
	public static function getUser($debug = '')
	{
        if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0)//$_SESSION['user_id']if (isset($_SESSION['admin']["user_name"]))
        {
            if(!isset($_SESSION['session_user'])) {
               $list = new User((int)$_SESSION['user_id']);
                $_SESSION['session_user'] = $list;
                if (!empty($list)) {
                    self::$user = $list;
                }
            } else {
                self::$user = $_SESSION['session_user'];
            }
        }
		if (self::$user == null) {
            self::$user = new User();
        }
        return self::$user;
	}
	
	/**
	 * Gets currently logged in user.
	 *
	 * @return User
	 */
	public static function getLabel()
	{
		if (self::$label == null)
		{
			if (isset($_SESSION['BULKLABEL']['labelFile']))
			{
				$labelFilter = new LabelFileFilter();
				$labelFilter->addIdFilter($_SESSION['BULKLABEL']['labelFile']);
				$list = $labelFilter->getList();

				if (sizeof($list) > 0) self::$label = $list[0];
			}
			if (self::$label == null) self::$label = new LabelFile();
		}
		return self::$label;
	}

	public static function checkUserAccess ($privilege, $ajaxrequest=false)
	{
		if(isset($_GET['data']))
			{
				$strUrl	=	'?data='.$_GET['data'];
			}
			else
			{
				$strUrl	=	'';
			}
		$strUrl;

		$user = self::getUser();
		if (!$user->hasPrivilege($privilege))
		{
			if($ajaxrequest)
			    echo "login-failed";
			else
			    util_redirect ("login.php".$strUrl);
		}
	}

    public static function loadPermission()
    {
        /*
         * Get all permissions for a login user
         * Check if session is already exsist
         * return session array
         */

        if(empty($_SESSION['allPermissions'])&& isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0){

            $_SESSION['allPermissions'] = Permissions::getAllPermissions($_SESSION['user_id']);

        }
    }
    public static function saveAdditionalChargesFilter($additionalChargesFilter)
    {
        $filterStr = "";
        if ($additionalChargesFilter != null)
        {
            $filterStr = serialize($additionalChargesFilter);
        }
        $_SESSION["additionalchargesfilter"] = $filterStr;
        self::$additionalChargesFilter = $additionalChargesFilter;
    }

    public static function getAdditionalChargesFilter()
    {
        if (self::$additionalChargesFilter == null)
        {
            $filterStr = @$_SESSION["additionalchargesfilter"];
            if ($filterStr != "")
            {
                self::$additionalChargesFilter = unserialize($filterStr);
            }
            else
            {
                self::$additionalChargesFilter = new ParcelGroupFilter();
                self::$additionalChargesFilter->setLimit(30);
            }
        }
        return self::$additionalChargesFilter;
    }
}
?>