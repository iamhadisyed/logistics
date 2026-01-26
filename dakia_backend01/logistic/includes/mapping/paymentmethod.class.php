<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * Rack - rack class
 * @package Ecommerce
 */
class PaymentMethod extends DbAccess3 {
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array('title' => 'string',
            'description' => 'string',
            'isactive' => 'number',            
            'added_by' => 'number',
            'added_date' => 'date',
            'updated_by' => 'number',
            'updated_date' => 'date'
        );
        parent::__construct('paymentmethod', 'id', $fieldList, $mixedCreator);
    }    
    // specific getters
    
    ///setter
    
    public function setTitle($title) {
        $this->valArray["title"] = $title;        
        $this->modifyArray["title"] = $title;
    }

    public function setDescription($description) {
        $this->valArray["description"] = $description;
        $this->modifyArray["description"] = $description;
    }
    public function setIsactive($isactive) {
        $this->valArray["isactive"] = $isactive;
        $this->modifyArray["isactive"] = $isactive;        
    }

    public function setAddedBy($added_by) {
        $this->valArray["added_by"] = $added_by;
        $this->modifyArray["added_by"] = $added_by;
    }

    public function setAddedDate($added_date) {
        $this->valArray["added_date"] = $added_date;
        $this->modifyArray["added_date"] = $added_date;
    }
    
    public function setUpdatedBy($updated_by) {
        $this->valArray["updated_by"] = $updated_by;
        $this->modifyArray["updated_by"] = $updated_by;
    }

    public function setUpdatedDate($updated_date) {
        $this->valArray["updated_date"] = $updated_date;
        $this->modifyArray["updated_date"] = $updated_date;
    }
    // getter   
    public function getId() {
        return $this->valArray["id"];
    }
    public function getTitle() {
        return $this->valArray["title"];
    }
    
    public function getDescription() {
        return $this->valArray["description"];
    }
    public function getIsactive() {
        return $this->valArray["isactive"];
    }
    
    public function getAddedBy() {
        return $this->valArray["added_by"];
    }

    public function getAddedDate() {
        return $this->valArray["added_date"];
    }
    
    public function getUpdatedBy() {
        return $this->valArray["updated_by"];
    }

    public function getUpdatedDate() {
        return $this->valArray["updated_date"];
    }
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getPaymentMethodListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>