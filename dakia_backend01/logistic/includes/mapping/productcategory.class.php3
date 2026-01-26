<?php

////////////////////////////////////////////////////
//
// Class for dealing with product categories
//
////////////////////////////////////////////////////

/**
 * Productcategory - Product category class
 * @package Ecommerce
 */
class Productcategory extends DbAccess
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
    public function __construct($id = 0)
    {

        $this->tablename = 'product_categories';
        $this->pkey      = 'id';
        $this->fields      =
            array(  'name'                       => 'name',

                    'orderq'                     => 'orderq',

                    'active'                     => 'active',
                    'deletedq'                   => 'deletedq',

                    'added_on'                   => 'added_on',
                    'added_by'                   => 'added_by',
                    'changed_on'                 => 'changed_on',
                    'changed_by'                 => 'changed_by'
                    );

        $this->_construct($id);

        return $this;
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * getAnyProductcategory. Returns the object array of Product categories.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyProductcategory($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret = array();
        $ids = $this->getAnyProductcategoryIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Productcategory($i);
            }

            return $ret;
        }
        else return $ret;
    }

    /**
     * getAnyProductcategoryIds. Returns the object array of Product category ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyProductcategoryIds($where = "", $activeOnly = true, $orderBy = "orderq")
    {

        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " and $where";
        if ($activeOnly) $whereSql .= " and active=1";
           $sql = "select {$this->pkey} from {$this->tablename} $whereSql order by $orderBy";

        $this->sql = $sql;

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
        $this->changed_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');

        if ($this->id < 1)
        {
            $this->added_on = date("Y-m-d H:i:s");
            $this->added_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');
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
    function delete()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['deletedq']} = 'Y'
                 WHERE {$this->pkey} = '{$this->id}'
                 ";

        $result = Db::query($sql);
        return;

    }


    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                 {     return $this->id; }
    public function getName()               {     return $this->name; }

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
    public function setName($name)                          {     $this->name = $name; }

    // general and audit setters
    public function setRowOrder($order)                     {     $this->orderq = $order; }
    public function setActive($active)                      {     $this->active = $active; }
    public function setDeleted($deleted)                    {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)                   {     $this->added_on = added_on; }
    public function setAddedBy($added_by)                   {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)               {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)               {     $this->changed_by = changed_by; }


}
?>
