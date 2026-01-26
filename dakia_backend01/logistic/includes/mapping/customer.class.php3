<?php

////////////////////////////////////////////////////
//
// Class for dealing with Customer
//
////////////////////////////////////////////////////

/**
 * Customer - Customer class
 * @package Ecommerce
 */
class Customer extends DbAccess3
{
	const PAGE_SIZE = 20;
	//
	protected $account_id;
    protected $title;
    protected $firstname;
    protected $surname;
    protected $email_address;
    protected $password;
    protected $last_logged_in;
    protected $login_attempts;
    protected $active_hash;
    protected $telephone;
    protected $vat_number;

    protected $orderq;
    protected $active;
    protected $deletedq;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($id = 0)
    {

        $this->tablename = 'customers';
        $this->pkey      = 'id';
        $this->fields      =
            array(  'account_id'               => 'account_id',
            		'title'                    => 'title',
                    'firstname'                => 'firstname',
                    'surname'                  => 'surname',
                    'email_address'            => 'email_address',
                    'password'                 => 'password',
                    'last_logged_in'           => 'last_logged_in',
                    'login_attempts'           => 'login_attempts',
                    'active_hash'              => 'active_hash',
            		'telephone'                => 'telephone',
            		'vat_number'               => 'vat_number',
                    'orderq'                   => 'orderq',

                    'active'                   => 'active',
                    'deletedq'                 => 'deletedq',

                    'added_by'                 => 'added_by',
                    'added_on'                 => 'added_on',
                    'changed_by'               => 'changed_by',
                    'changed_on'               => 'changed_on'
                    );

        $this->_construct($id);
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * getAnyCustomer. Returns the object array of customers.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyCustomer($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret = array();
        $ids = $this->getAnyCustomerIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Customer($i);
            }

            return $ret;
        }
        else return $ret;
    }


    /**
     * getAnyCustomerIds. Returns the object array of customer ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyCustomerIds($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " and $where";
        if ($activeOnly) $whereSql .= " and active=1";
        //
		$sql = "SELECT {$this->pkey}
                  FROM {$this->tablename}
                  $whereSql
              ORDER BY $orderBy";
        //
        //echo "<p>$sql</p>";

        $result = Db::query($sql);
        while (list($pId) = @mysqli_fetch_row($result))
        {
            $ret[] = $pId;
        }
        return $ret;
    }

    /**
     * save. Updates table.
     * @return void
     */
    public function save()
    {
        $this->changed_on = date("Y-m-d H:i:s");
        $this->changed_by = @$_SESSION['admin']['firstname'] . " " . @$_SESSION['admin']['surname'];

        if ($this->id < 1)
        {
            $this->added_on = date("Y-m-d H:i:s");
            $this->added_by = @$_SESSION['admin']['firstname'] . " " . @$_SESSION['admin']['surname'];
        }

        // save
        parent::save();

        // get new id
        if ($this->id < 1)
        {
             $this->id = Db::getLastInsertId();
        }

        return;
    }

    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['deletedq']} = 'Y'
                 WHERE {$this->pkey} = '{$this->id}'
                 ";

        $result = Db::query($sql);
        return;

    }


    /**
     * activate. activate the account
     * @return void
     */
    function activate($id)
	{

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['active']} = 1
                 WHERE {$this->pkey} = '{$id}'
                 ";

        $result = Db::query($sql);
        return;

    }

    /**
     * Indicates if this user is active
     *
     * @return integer 0-inactive.
     */
    public function getActive() { return $this->active; }
    public function setActive($active) { $this->active = $active; }

    /**
     * A customer can store their VAT number to save have to keep
     * re-entering it when are making exports
     *
     */
    public function getVatNumber() { return $this->vat_number; }
    public function setVatNumber($vat_number) { $this->vat_number = $vat_number; }

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                        {     return $this->id; }
	public function getAccountId()                 {     return $this->account_id; }
    public function getTitle()                     {     return $this->title; }
    public function getFirstname()                 {     return $this->firstname; }
    public function getSurname()                   {     return $this->surname; }
    public function getFullname()                  {     return $this->firstname . " " . $this->surname; }
    public function getEmailAddress()              {     return $this->email_address; }
    public function getPassword()                  {     return $this->password; }
    public function getPhoneNumber()                {     return $this->telephone; }

    // general and audit getters
    public function getRowOrder()                  {     return $this->orderq; }
    public function getDeleted()                   {     return $this->deletedq; }
    public function getAddedOn()                   {     return $this->added_on; }
    public function getAddedBy()                   {     return $this->added_by; }
    public function getChangedOn()                 {     return $this->changed_on; }
    public function getChangedBy()                 {     return $this->changed_by; }


    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
	public function setAccountId($account_id)           {     $this->account_id = $account_id; }
    public function setTitle($title)                    {     $this->title = $title; }
    public function setFirstname($firstname)            {     $this->firstname = $firstname; }
    public function setSurname($surname)                {     $this->surname = $surname; }
    public function setEmailAddress($email_address)     {     $this->email_address = $email_address; }
    public function setPassword($password)              {     $this->password = md5($password); }
  
    public function setPhoneNumber($telephone)			{     $this->telephone = $telephone; }

    // general and audit setters
    public function setRowOrder($order)                 {     $this->orderq = $order; }
    public function setDeleted($deleted)                {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)               {     $this->added_on = added_on; }
    public function setAddedBy($added_by)               {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)           {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)           {     $this->changed_by = changed_by; }

}
?>