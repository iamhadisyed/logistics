<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * LogRackShelf - rack_shelf class
 * @package Ecommerce
 */
class LogRackShelf extends DbAccess3 {
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        
        $fieldList = array('rack_shelf_id' => 'number',
            'rack_shelf_item_id' => 'number',
            'customer_id' => 'number',
            'remarks' => 'string',
            'in_date' => 'datetime',
            'in_by' => 'number',
            'out_date' => 'datetime',
            'out_by' => 'number'
        );
        parent::__construct('log_rack_shelf', 'id', $fieldList, $mixedCreator);
    }

    
    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////
    // specific getters
    public function getId() {
        return $this->valArray["id"];
    }

    public function getRackShelfId() {
        return $this->valArray["rack_shelf_id"];
    }
    public function getCustomerId() {
        return $this->valArray["customer_id"];
    }

    public function getRackShelfItemId() {
        return $this->valArray["rack_shelf_item_id"];
    }
    
    public function getRemarks() {
        return $this->valArray["remarks"];
    }

    public function getInDate() {
        return $this->valArray["in_date"];
    }
    
    public function getInBy() {
        return $this->valArray["in_by"];
    }

    public function getOutDate() {
        return $this->valArray["out_date"];
    }

    public function getOutdBy() {
        return $this->valArray["out_by"];
    }

    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////
    // specific setters
    
    public function setRackShelfId($rack_shelf_id) {
        $this->valArray["rack_shelf_id"] = $rack_shelf_id;
    }

    public function setRackShelfItemId($rack_shelf_item_id) {
        $this->valArray["rack_shelf_item_id"] = $rack_shelf_item_id;
    }
    
    public function setCustomerId($customer_id) {
        $this->valArray["customer_id"] = $customer_id;
    }
    
    public function setRemarks($remarks) {
        $this->valArray["remarks"] = $remarks;
    }

    public function setInDate($in_date) {
        $this->valArray["in_date"] = $in_date;
    }
    
    public function setInBy($in_by) {
        $this->valArray["in_by"] = $in_by;
    }

    public function setOutDate($out_date) {
        $this->valArray["out_date"] = $out_date;
    }

    public function setOutdBy($out_by) {
        $this->valArray["out_by"] = $out_by;
    }
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getLogRackShelfListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getRecordsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data;
    }
}
?>