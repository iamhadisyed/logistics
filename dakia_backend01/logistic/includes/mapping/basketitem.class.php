<?php

////////////////////////////////////////////////////
//
// Class for dealing with Basket items
//
// Notes: As orders will relate directly to a basket
//        the order items are stored in this class
//
////////////////////////////////////////////////////

/**
 * Basketitem - Basket item class
 * @package Ecommerce
 */
class Basketitem extends DbAccess3
{
	//*** use getParcelGroup method to access
	private $parcel_group; 	// A basket item may be a physical item or a parcel group

	//***
    protected $basket_id;
    protected $parcel_group_id;
    protected $quantity;
    protected $discount;

    protected $orderq;
    protected $active;
    protected $deletedq;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    protected $basket_items           = array();

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($id=0)
    {

        $this->tablename = 'basket_items';
        $this->pkey      = 'id';
        $this->fields    =
            array(  'basket_id'           => 'basket_id',
                    'parcel_group_id'     => 'parcel_group_id',
                    'quantity'            => 'quantity',
                    'discount'            => 'discount',

                    'orderq'              => 'orderq',
                    'active'              => 'active',
                    'deletedq'            => 'deletedq',

                    'added_on'            => 'added_on',
                    'added_by'            => 'added_by',
                    'changed_on'          => 'changed_on',
                    'changed_by'          => 'changed_by'
                    );

        // construct object
        $this->_construct($id);

        // get parcel group
		$this->parcel_group = new Parcelgroup(@$this->getParcelGroupId());

        return $this;
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * getAnyBasketitem. Returns the object array of Basket items.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public static function getAnyBasketitem($where = "", $activeOnly = true, $orderBy = "id")
    {
    	$lookupObj = new Basketitem();
        $ret = array();
        $ids = $lookupObj->getAnyBasketitemIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Basketitem($i);
            }

            return $ret;
        }
        else return $ret;
    }

    /**
     * getAnyBasketitemIds. Returns the object array of Basket items ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyBasketitemIds($where = "", $activeOnly = true, $orderBy = "id")
    {
        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " AND $where";
        if ($activeOnly) $whereSql .= " AND active=1";
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

    /**
     * Return the parcel group associated with this basket item.
     *
     * @return parcel group object
     */
    public function getParcelGroup()
    {
    	return $this->parcel_group;
    }

 
    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['deletedq']} = 'Y'
                 WHERE {$this->pkey} = '{$this->id}'";
        $result = Db::query($sql);
        return;

    }

    /**
     * expunge. Real delete
     * @return void
     */
    public function expunge()
    {

        $sql = "DELETE FROM {$this->tablename}
                 WHERE {$this->pkey} = '{$this->id}'";
        $result = Db::query($sql);
        return;

    }

    /**
     * Move items between baskets.
     *
     * @param unknown_type $from_basket_id
     * @param unknown_type $to_basket_id
     */
    public function moveItems ($from_basket_id, $to_basket_id)
    {
        $sql = "UPDATE {$this->tablename}
                   SET basket_id=$to_basket_id,
        				changed_on =" . date("Y-m-d H:i:s") . "
        				changed_by ='Website'
                 WHERE baskey_id=$from_basket_id ";

        $result = Db::query($sql);
    }

    /**
     * Update basket quantities
     *
     * @param array quantities
     */
    public function updateQuantities ($quantities)
    {

		foreach ($quantities as $key => $val)
		{
			$sql = "UPDATE basket_items
					   SET quantity = " . $val . "
					 WHERE id 		= " . $key;

			$result = Db::query($sql);
		}
    }

    /**
     * delete basket item
     *
     * @param integer item
     */
    public function deleteItem ($item)
    {
		// get basket item
		$BimObj = new Basketitem($item);

		// get parcel group
		$PgpObj = new ParcelGroup($BimObj->getParcelGroupId());

		// if booking, delete parcel group child data
		if ($PgpObj->getProductId() == 0)
		{
			// delete parcels
			$ParObj = new Parcel;
			$sql = "DELETE FROM parcels
					 WHERE parcel_group_id = " . $BimObj->getParcelGroupId();
			$result = Db::query($sql);

			// delete collection address
			$CadObj = new Parcelgroupaddress($PgpObj->getCollectionAddressId());
			$CadObj->expunge();

			// delete destination address
			$DadObj = new Parcelgroupaddress($PgpObj->getDestinationAddressId());
			$DadObj->expunge();
		}

		// delete parcel group
		$PgpObj->expunge();

		// delete basket item
        $BimObj->expunge();

        return;

    }


    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                 {     return $this->id; }
    public function getBasketId()           {     return $this->basket_id; }
    public function getParcelGroupId()      {     return $this->parcel_group_id; }
    public function getQuantity()           {     return $this->quantity; }
    public function getDiscount()           {     return $this->discount; }

    // special getters
    public function getMiniBasketDescription()
	{
		// for booking parcel groups
		if ($this->getParcelGroup()->getProductId() == 0)
		{
			$quote		  	= $this->getQuoteNumber();

			$no_of_parcels  = $this->getParcelGroup()->getTotalParcels();
			$collection		= $this->getParcelGroup()->getCollectionPostcode();
			$destination	= new Country($this->getParcelGroup()->getCollectionCountryId());

			$parcel_text	 = " Parcel ";
			if ($no_of_parcels > 1)
			{
				$parcel_text = " Parcels ";
			}

			$description  = "";
			$description .= "<a href='booking_summary.php?basket_item_id=" . $this->getId() . "'>Shipment " . $quote . "</a> ";
			$description .= $no_of_parcels . $parcel_text . "<br/>";
			$description .= $collection . "&rarr;" . $destination->getName();
		}

		// for product parcel groups
		if ($this->getParcelGroup()->getProductId() > 0)
		{
			$PrdObj = new Product($this->getParcelGroup()->getProductId());

			$description  = "";
			$description .= "<a href='product_details.php?id=" . $PrdObj->getId() . "'>" . $PrdObj->getName() . "</a> at &pound;" . $PrdObj->getPrice() . "<br/>";
			$description .= "Quantity: " . $this->getQuantity();
		}

	    return $description;
	}

	public function getQuoteNumber()
	{
		//return str_pad($this->getId(), 6, "0", STR_PAD_LEFT);
		$pg = $this->getParcelGroup();
		if ($pg != null)
		{
			return $pg->getParcelGroupRef();
		}
		return "";
	}


    public function getMainBasketDescription()
	{

		// for booking parcel groups
		if ($this->getParcelGroup()->getProductId() == 0)
		{
			$quote		  	= $this->getQuoteNumber();

			$no_of_parcels  = $this->getParcelGroup()->getTotalParcels();
			$collection		= $this->getParcelGroup()->getCollectionPostcode();
			$destination	= new Country($this->getParcelGroup()->getCollectionCountryId());

			$parcel_text	 = " Parcel ";
			if ($no_of_parcels > 1)
			{
				$parcel_text = " Parcels ";
			}

			$description  = "";
			$description .= "<a href='booking_summary.php?basket_item_id=" . $this->getId() . "'>Shipment " . $quote . "</a> for ";
			$description .= $no_of_parcels . $parcel_text . "<br/>";
			$description .= $collection . "&rarr;" . $destination->getName();
		}

		// for product parcel groups
		if ($this->getParcelGroup()->getProductId() > 0)
		{
			$PrdObj = new Product($this->getParcelGroup()->getProductId());

			$description  = "";
			$description .= "<a href='product_details.php?id=" . $PrdObj->getId() . "'>" . $PrdObj->getName() . "</a><br/>";
			$description .= "&nbsp;";  // put 1st summary line here
		}

	    return $description;
	}

    public function getMainBasketDescriptionPDF()
	{

		// for booking parcel groups
		if ($this->getParcelGroup()->getProductId() == 0)
		{
			$no_of_parcels  = $this->getParcelGroup()->getTotalParcels();
			$collection		= $this->getParcelGroup()->getCollectionPostcode();
			$destination	= new Country($this->getParcelGroup()->getCollectionCountryId());

			$parcel_text	 = " Parcel ";
			if ($no_of_parcels > 1)
			{
				$parcel_text = " Parcels ";
			}

			$description  = "";
			$description .= $no_of_parcels . $parcel_text;
			$description .= " from " . $collection . " to " . $destination->getName();
		}

		// for product parcel groups
		if ($this->getParcelGroup()->getProductId() > 0)
		{
			$PrdObj = new Product($this->getParcelGroup()->getProductId());

			$description  = "";
			$description .= "<a href='product_details.php?id=" . $PrdObj->getId() . "'>" . $PrdObj->getName() . "</a><br/>";
			$description .= "&nbsp;";  // put 1st summary line here
		}

	    return $description;
	}


    public function getMainBasketPrice($include_vat = true, $formattted = false)
	{
		$price = 0;
		// for booking parcel groups
		if ($this->getParcelGroup()->getProductId() == 0)
		{
			$price = $this->getParcelGroup()->getTotalPrice();
			if ($include_vat) $price += $this->getParcelGroup()->getVAT();
		}
		// for product parcel groups
		else if ($this->getParcelGroup()->getProductId() > 0)
		{
			$PrdObj = new Product($this->getParcelGroup()->getProductId());

			$price = $PrdObj->getPrice();
		}
		//
		return ($formattted ? util_money($price) : $price);
	}

    public function getMainBasketAmount($include_vat = true, $formatted = false)
	{
		$val = $this->getMainBasketPrice($include_vat) * $this->getQuantity();

		return ($formatted ? util_money($val) : $val);
	}

    // general and audit getters
    public function getRowOrder()           {     return $this->orderq; }
    public function getActive()             {     return $this->active; }
    public function getDeleted()            {     return $this->deletedq; }
    public function getAddedOn()            {     return $this->added_on; }
    public function getAddedBy()            {     return $this->added_by; }
    public function getChangedOn()          {     return $this->changed_on; }
    public function getChangedBy()          {     return $this->changed_by; }


    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setId($id)                           {     $this->id = $id; }
    public function setBasketId($basket_id)              {     $this->basket_id = $basket_id; }
    public function setParcelGroupId($parcel_group_id)   {     $this->parcel_group_id = $parcel_group_id; }
    public function setQuantity($quantity)               {     $this->quantity = $quantity; }
    public function setDiscount($discount)               {     $this->discount = $discount; }

    // general and audit setters
    public function setRowOrder($order)                  {     $this->orderq = $order; }
    public function setActive($active)                   {     $this->active = $active; }
    public function setDeleted($deleted)                 {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)                {     $this->added_on = added_on; }
    public function setAddedBy($added_by)                {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)            {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)            {     $this->changed_by = changed_by; }

}
?>
