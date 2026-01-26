<?php

////////////////////////////////////////////////////
//
// Class for dealing with Orders
//
////////////////////////////////////////////////////

/**
 * Order - Order class
 * @package Ecommerce
 */
class Order extends DbAccess
{
	const STATUS_READY = 1;
	const STATUS_COMPLETE = 1;
	const STATUS_PAYMENT_FAILED = 2;
	const STATUS_CANCELLED = 3;
	//
	const ALERT_MISSED_COLLECTION = 10;
	//
	private $basket = null;
	private $admin_notes = array();

	//***
	protected $order_date;

	protected $total;
	protected $shipping;
	protected $tax;
	protected $discount;

	protected $order_status;
	protected $guid;
	protected $personal_message;
	protected $order_email_sentq;
	protected $payment_sentq;
	protected $payment_reference;
	protected $transaction_code;
	protected $client_transaction_code;
	protected $alert_level;

	protected $added_on;
	protected $added_by;
	protected $changed_on;
	protected $changed_by;
    protected $account_id;
    protected $payment_type;

	/**
	* Constructor. Returns the object.
	* @param int $id
	* @return bool
	*/
	public function Order($id=0)
	{

		$this->tablename = 'orders';
		$this->pkey    = 'id';
		$this->fields    =
			array( 	'order_date'     => 'order_date',
					'guid'		     => 'guid',

					'total'		     => 'total',
					'shipping'       => 'shipping',
					'tax'            => 'tax',
					'discount'       => 'discount',

					'order_status'		 => 'order_status',
					'alert_level'        => 'alert_level',

					'personal_message'   => 'personal_message',
					'order_email_sentq'  => 'order_email_sentq',
					'payment_sentq'	     => 'payment_sentq',
					'payment_reference'  => 'payment_reference',
					'transaction_code'   => 'transaction_code',
					'client_transaction_code' => 'client_transaction_code',

					'added_on'           => 'added_on',
					'added_by'           => 'added_by',
					'changed_on'		 => 'changed_on',
					'changed_by'		 => 'changed_by',
                    'account_id'         => 'account_id',
                    'payment_type'       => 'payment_type'
					);


		$this->_construct($id);

		// Can only get items if we have a valid order id!
		if ($this->id > 0)
		{
			if ($this->getBasket() != null)
			{
				$this->delivery_address = new ParcelGroupAddress($this->getBasket()->getDeliveryAddressId());
			}
		}

		return $this;
	}

	////////////////////////////////////////////////////
	// Access and update methods
	////////////////////////////////////////////////////

	/**
	* getAnyOrder. Returns the object array of Order.
	* @param string $where
	* @param string $activeOnly
	* @param string $orderBy
	* @return string
	*/
	public static function getAnyOrder($where = "", $activeOnly = true, $orderBy = "order_date desc")
	{
		$order = new Order();
		$ret = array();
		$ids = $order->getAnyOrderIds($where, $activeOnly, $orderBy);

		if (count($ids)>0)
		{
			foreach ($ids as $i)
			{
				$ret[] = new Order($i);
			}
			return $ret;
		}
		else return $ret;
	}

	/**
	* getAnyOrderIds. Returns the object array of Order ids.
	* @param string $where
	* @param string $activeOnly
	* @param string $orderBy
	* @return string
	*/
	public function getAnyOrderIds($where = "", $activeOnly = true, $orderBy = "order_date desc")
	{
		$ret    = array();

		$whereSql = "";
		if ($where)	$whereSql = "WHERE  $where";

		$sql = "SELECT {$this->pkey}
					FROM {$this->tablename}
				$whereSql
				ORDER BY $orderBy";

		$result = Db::query($sql);
		while (list($pId) = mysqli_fetch_row($result))
		{
			$ret[] = $pId;
		}

		return $ret;
	}

    public function setAccountId($account_id)
    {
        $this->account_id=$account_id;
    }

     public function setPaymentType($type)
    {
        $this->payment_type=$type;
    }

	/**
	* save. Updates table.
	* @return void
	*/
	public function save()
	{
		if (isset($_SESSION['admin']['id']))
		{
			$by = $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'];
		}
		else
		{
			$by = "Website";
		}

		$this->changed_on = date("Y-m-d H:i:s");
		$this->changed_by = $by;

		if ($this->id < 1)
		{
			$this->added_on = date("Y-m-d H:i:s");
			$this->added_by = $by;
		}

		// save
		parent::save();

		// get new id
		if ($this->id < 1)
		{
			$this->id = Db::getLastInsertId();
		}

		// save any admin notes
		foreach ($this->admin_notes as $noteArray)
		{
			$note = new Note();
			$note->setUpdatedBy ($by);
			$note->setUpdateTime ($noteArray["time"]);
			$note->setNote ($noteArray["note"]);
			$note->saveOrderNote($this->getId());
		}

		return;
	}

	public static function getOrderIdFromOrderStr($order_ref)
	{
		// strip the order prefix off
		$order_ref = str_replace(CONFIG_ORDER_PREFIX, "", $order_ref);
		$order_ref = str_replace("-", "", $order_ref);
		$order_ref = str_replace(" ", "", $order_ref);

		// convert to number
		$orderId = intval($order_ref);

		return $orderId;
	}

	public static function getOrderFromOrderNumber ($order_ref)
	{
		// convert to number
		$orderId = self::getOrderIdFromOrderStr($order_ref);

		if ($orderId == 0) return null;

		// order ref
		$where = "id=$orderId";
		$orderList = Order::getAnyOrder($where);

		// was the order found
		if (sizeof($orderList) == 0) return null;

		return $orderList[0];
	}


	/**
	 * Get required order
	 *
	 * @param int $parcelGroupId
	 * @return Order
	 */
	public static function getOrderFromParcelGroupId ($parcelGroupId)
	{
		// order ref
		$sql = "SELECT o.* FROM orders o
				INNER JOIN baskets b on o.id=b.order_id
				INNER JOIN basket_items i on b.id=i.basket_id
				WHERE i.parcel_group_id=$parcelGroupId ";

//		echo "<p>$sql</p>"; exit;
		$orderList = self::getObjectList(__CLASS__, $sql);

		// was the order found
		if (sizeof($orderList) == 0) return null;

		return $orderList[0];
	}

	////////////////////////////////////////////////////
	// Special methods
	////////////////////////////////////////////////////

	/**
	* createOrder. create an order
	* @return void
	*/
	
	function createOrder($BskObj, $paymentType)
	{
		// base order information
		$this->setOrderDate(date("Y-m-d H:i:s"));

		// totals
		$this->setTotal($BskObj->getBasketAmount());
		$this->setShipping($BskObj->getShippingCost());
		$this->setTax($BskObj->getBasketVAT());
		$this->setDiscount(0);
		$this->setGuid($BskObj->getId() . md5(time()));

		// order status
		$this->setStatus(self::STATUS_COMPLETE);
		
		$this->setPaymentType($paymentType);
		

		// notes
		$this->setPersonalMessage("");

		// switches
		$this->setOrderEmailSent(false);
		$this->setPaymentSent("Y");

		// save order
		$this->save();
	}
	
	/*function createOrder($BskObj)
	{
		// base order information
		$this->setOrderDate(date("Y-m-d H:i:s"));

		// totals
		$this->setTotal($BskObj->getBasketAmount());
		$this->setShipping($BskObj->getShippingCost());
		$this->setTax($BskObj->getBasketVAT());
		$this->setDiscount(0);
		$this->setGuid($BskObj->getId() . md5(time()));

		// order status
		$this->setStatus(self::STATUS_COMPLETE);	

		// notes
		$this->setPersonalMessage("");

		// switches
		$this->setOrderEmailSent(false);
		$this->setPaymentSent("Y");

		// save order
		$this->save();
	}*/

	/**
	* createPdf. create a PDF of the order
	* @return void
	*/
	function createPdf()
	{
		return;
	}
    
    public function deleteDHLOrder($val)
    {
        $sql= "delete from dhl_bookings where parcel_group_id='".$val."'";
        Db::query($sql);
    }
    
    public function updateParcelRecord($val)
    {
        $sql = "UPDATE parcel_groups SET booking_status=1 WHERE id= '" .$val."'";
        Db::query($sql);
    }
    
    public function updateParcelStartTime($val, $val2)
    {
        $sql = "UPDATE parcel_groups SET earliest_collection_time='".$val."' WHERE id= '" .$val2."'";
        Db::query($sql);
    }
    
    public function updateParcelEndTime($val, $val2)
    {
        $sql = "UPDATE parcel_groups SET latest_collection_time='".$val."' WHERE id= '" .$val2."'";
        Db::query($sql);
    }
    
    public function updateDate($val, $val2)
    {
        $sql = "UPDATE parcel_groups SET collection_date='".$val."' WHERE id= '" .$val2."'";
        Db::query($sql);
    }

	public static function getOrdersDueEmails ()
	{
		// Order that are not complete
		$sql = "SELECT o.id
				FROM orders o
				LEFT JOIN baskets b on o.id=b.order_id
				LEFT JOIN basket_items bi on b.id=bi.basket_id
				LEFT JOIN parcel_groups pg on bi.parcel_group_id=pg.id
				WHERE (pg.booking_status = " . ParcelGroup::BOOKING_STATUS_READY .")
				";

		// So get a list of order to send
		$sql = "SELECT o.*
				FROM orders o
				WHERE ((o.order_email_sentq is null) or (o.order_email_sentq != 'Y'))
				AND o.id not in ($sql)
				";
		//echo "<p>$sql</p>";
		//
		$orderArray = array();
		//
		$result = Db::query($sql);
		while ($row = mysqli_fetch_array($result))
		{
			// create and populate the orders
			$order = new Order();
			$order->populate($row);
			//
			$orderArray[] = $order;
		}
		return $orderArray;
	}

	/**
	* Gets list of orders for the given customer.
	*
	* @param int $customerId
	*/
	public static function getOrdersForCustomerId ($customerId)
	{
		$sql = "SELECT o.*
				FROM orders o
				LEFT JOIN baskets b on o.id=b.order_id
				WHERE b.customer_id=$customerId
				ORDER BY order_date desc
				";

		//echo "<p>$sql</p>";

		$orderArray = array();
		//
		$result = Db::query($sql);
		while ($row = mysqli_fetch_array($result))
		{
			// create and populate the orders
			$order = new Order();
			$order->populate($row);
			//
			$orderArray[] = $order;
		}
		return $orderArray;
	}

	/**
	* Get the full order number (string) from the order number value (number) passed
	*
	* @param int Order number value
	* @return void
	*/
	public static function formatOrderNumber ($order_number_val)
	{
		return CONFIG_ORDER_PREFIX . str_pad($order_number_val, 6, "0", STR_PAD_LEFT);
	}

	/**
	* Get the basket assocated with this order
	*
	* @return Basket
	*/
	public function getBasket ()
	{
		if ($this->basket == null) {
			// Look up the basket if not already got
			$where = "order_id=" . $this->getId() . "";
			$basket_array = Basket::getAnyBasket($where);
			if (sizeof($basket_array) > 0)
			{
				$this->basket = $basket_array[0];
			}
			else
			{
				$this->basket = new Basket();
				$this->basket->setOrderId($this->getId());
			}
		}
		return $this->basket;
	}

	/**
	* A global unique identify.
	* Used in Urls shown to users, so they cannot guess other peoples orders.
	*
	*/
	public function getGuid() {return $this->guid; }
	private function setGuid($guid) {$this->guid =$guid; }

	/**
	 * Order Number (read only)
	 *
	 * @return string
	 */
	public function getOrderNumber()
	{
		// order number is based on the order id
		return self::formatOrderNumber($this->getId());
	}


	/**
	 * Get Order date,
	 * Use getOrderDateAsDateTime
	 *
	 * @return string!
	 */
	public function getOrderDate()
	{
		return $this->order_date;
	}
	public function getOrderDateAsDateTime()
	{
		return strToTime($this->order_date);
	}


	/**
	 * Payment reference - also know as authorisation code.
	 * Only available on a successful transaction.
	 *
	 * @return string
	 */
	public function getPaymentReference()	{	return $this->payment_reference; }
	public function setPaymentReference($payment_reference)	{	$this->payment_reference = $payment_reference; }

	/**
	 * Transaction code generated by the payment server.
	 *
	 */
	public function getTransactionCode()
	{
		return $this->transaction_code;
	}
	public function setTransactionCode ($val)
	{
		$this->transaction_code = $val;
	}

	/**
	 * Transaction code generated by this site
	 *
	 */
	public function setLocalTransactionCode($code)
	{
		$this->client_transaction_code = $code;
	}

	/**
	 * Order Status
	 *
	 * @return Order Status const
	 */
	public function setStatus($status)
	{
		switch ($status)
		{
			case Order::STATUS_CANCELLED:
			case Order::STATUS_COMPLETE:
			case Order::STATUS_PAYMENT_FAILED:
				break;
			default:
				$status = Order::STATUS_READY;
		}
		$this->order_status = $status;
	}
	public function getStatus()
	{
		return $this->order_status;
	}
	/**
	 * Description of order status
	 *
	 */
	public function getStatusDescription()
	{
		$status = "Unknown";
		switch ($this->getStatus())
		{
			case self::STATUS_CANCELLED:
				$status = "Cancelled";
				break;
			case self::STATUS_COMPLETE:
				$status = "Complete";
				break;
			case self::STATUS_PAYMENT_FAILED:
				$status = "Payment Failed";
		}
		return $status;
	}

	/**
	 * Admin notes
	 *
	 * @param string $admin_notes
	 */
	public function addAdminNote($admin_note)
	{
		$this->admin_notes[] = array ("note" => $admin_note, "time" => time());
	}

	/**
	 * Set or clear the alert level
	 *
	 * @param alert value $alert
	 */
	public function addAlert($alert)
	{
		$this->alert_level += $alert;
	}
	public function clearAlert ()
	{
		$this->alert_level = 0;
	}

	////////////////////////////////////////////////////
	// Getters
	////////////////////////////////////////////////////


	// specific getters
	public function getId()						{	return $this->id; }

	public function getTotal()					{	return $this->total; }
	public function getShippingCost()				{	return $this->shipping; }
	public function getTax($formatted = false)
	{
		return ($formatted ? util_money($this->tax) : $this->tax);
	}
	public function getDiscount()				{	return $this->discount; }

	public function getGrandTotal($formatted = false)
	{
		$total = $this->getTotal() + $this->getShippingCost();

		return ($formatted ? util_money($total) : $total);
	}
	public function getGrandTotalPlusVat($formatted = false)
	{
		$total = $this->getGrandTotal() + $this->getTax();

		return ($formatted ? util_money($total) : $total);
	}


	public function getPersonalMessage()		{	return $this->personal_message; }
	public function getIsOrderEmailSent()		{	return ($this->order_email_sentq == "Y"); }
	public function getIsPaymentSent()			{	return $this->payment_sentq; }
	public function getPaymentProvider()		{	return "eDPQ"; /* */ }
    public function getAccountId()             {    return $this->account_id; }

	// general and audit getters
	public function getAddedOn()				{	return $this->added_on; }
	public function getAddedBy()				{	return $this->added_by; }
	public function getChangedOn()				{	return $this->changed_on; }
	public function getChangedBy()				{	return $this->changed_by; }

	// get special getters
	public function getMaxId()					{
														$sql = "SELECT MAX(id)
																FROM {$this->tablename}";
														$result = Db::query($sql);
														$max_id = mysqli_fetch_row($result);

														return $max_id[0];
												}



	////////////////////////////////////////////////////
	// Setters
	////////////////////////////////////////////////////

	// specific setters
	public function setOrderDate($order_date)						{	$this->order_date = $order_date; }

	public function setTotal($total)								{	$this->total = $total; }
	public function setShipping($shipping)							{	$this->shipping = $shipping; }
	public function setTax($tax)									{	$this->tax = $tax; }
	public function setDiscount($discount)							{	$this->discount = $discount; }

	public function setPersonalMessage($personal_message)			{	$this->personal_message = $personal_message; }
	public function setOrderEmailSent($order_email_sentq)			{	$this->order_email_sentq = ($order_email_sentq ? 'Y':'N'); }
	public function setPaymentSent($payment_sentq)					{	$this->payment_sentq = $payment_sentq; }

	// general and audit setters
	public function setRowOrder($order)								{	$this->orderq = $order; }
	public function setAddedOn($added_on)							{	$this->added_on = added_on; }
	public function setAddedBy($added_by)							{	$this->added_by = added_by; }
	public function setChangedOn($changed_on)						{	$this->changed_on = changed_on; }
	public function setChangedBy($changed_by)						{	$this->changed_by = changed_by; }

    
}
?>
