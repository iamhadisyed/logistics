<?php

////////////////////////////////////////////////////
//
// Class for dealing with Postcode groups
//
////////////////////////////////////////////////////

/**
 * Postcodegroup - Postcode groups class
 * @package Postcodes
 */
class Postcodegroup extends DbAccess
{

    protected $name;

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
    public function __construct($id=0)
    {

        $this->tablename = 'postcode_groups';
        $this->pkey      = 'id';
        $this->fields      =
            array(  'name'              	  => 'name',

                    'orderq'                  => 'orderq',
                    'active'                  => 'active',
                    'deletedq'                => 'deletedq',

                    'added_on'                => 'added_on',
                    'added_by'                => 'added_by',
                    'changed_on'              => 'changed_on',
                    'changed_by'              => 'changed_by'
                    );

        $this->_construct($id);

        return $this;
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * getAnyPostcodegroup. Returns the object array of Courier postcodes.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyPostcodegroup($where = "", $activeOnly = true, $orderBy = "id")
    {
        $ret = array();
        $ids = $this->getAnyPostcodegroupIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Postcodegroup($i);
            }

            return $ret;
        }
        else return $ret;
    }

    /**
     * getAnyPostcodegroupIds. Returns the object array of Postcodegroup ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyPostcodegroupIds($where = "", $activeOnly = true, $orderBy = "id")
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
     * save. Updates table.
     * @return void
     */
    public function save()
    {
        $this->changed_on = date("Y-m-d H:i:s");
        $this->changed_by = "Website";

        if ($this->id < 1)
        {
            $this->added_on = date("Y-m-d H:i:s");
            $this->added_by = "Website";
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
     * updateAdmin. Updates row from admin area
     * @return void
     */
    public function updateAdmin()
    {
		// if no id then do a straight save
		if ($this->id == 0)
		{
			$this->save();
			return;
		}

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['name']}       = '$this->name'
                 WHERE {$this->pkey} = '{$this->id}'";
        $result = Db::query($sql);
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
                 WHERE {$this->pkey} = '{$this->id}'";
        $result = Db::query($sql);
        return;

    }

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                      {     return $this->id; }
    public function getName()               	 {     return $this->name; }

    // general and audit getters
    public function getRowOrder()             {     return $this->orderq; }
    public function getActive()               {     return $this->active; }
    public function getDeleted()              {     return $this->deletedq; }
    public function getAddedOn()              {     return $this->added_on; }
    public function getAddedBy()              {     return $this->added_by; }
    public function getChangedOn()            {     return $this->changed_on; }
    public function getChangedBy()            {     return $this->changed_by; }


    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setName($name)                    			 {     $this->name = $name; }

    // general and audit setters
    public function setRowOrder($order)                          {     $this->orderq = $order; }
    public function setActive($active)                           {     $this->active = $active; }
    public function setDeleted($deleted)                         {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)                        {     $this->added_on = added_on; }
    public function setAddedBy($added_by)                        {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)                    {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)                    {     $this->changed_by = changed_by; }

}
?>
