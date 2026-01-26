<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * RackShelf - rack_shelf class
 * @package Ecommerce
 */
class RackShelf extends DbAccess3 {
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array('rack_id' => 'number',
            'shelf_no' => 'number',
            'is_filled' => 'number',   
            'updated_date' => 'date',
            'updated_by' => 'number'
        );        
        parent::__construct('rack_shelf', 'id', $fieldList, $mixedCreator);
        
    }

    
    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////
    // specific getters
    public function getId() {
        return $this->valArray["id"];
    }

    public function getRackId() {
        return $this->valArray["rack_id"];
    }

    public function getShelfNo() {
        return $this->valArray["shelf_no"];
    }

    public function getIsFilled() {
        return $this->valArray["is_filled"];
    }

    public function getUpdatedDate() {
        return $this->valArray["updated_date"];
    }

    public function getUpdatedBy() {
        return $this->valArray["updated_by"];
    }

  
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getRackShelfListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>