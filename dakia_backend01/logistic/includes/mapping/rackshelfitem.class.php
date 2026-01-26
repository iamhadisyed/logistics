<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * RackShelfItem - rack_shelf class
 * @package Ecommerce
 */
class RackShelfItem extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        
        
        $this->primaryKey = 'id';

        $fieldList = array('goods_name' => 'string',
            'description' => 'string',
            'weight' => 'number',
            'dimension' => 'string',
            'added_date' => 'date',
            'added_by' => 'number',
            'updated_date' => 'date',
            'updated_by' => 'number',
			'tracking_number' => 'string'
        );
        parent::__construct('rack_shelf_item', 'id', $fieldList, $mixedCreator);
    }
    
    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////
    // specific getters
    public function getId() {
        return $this->valArray["id"];
    }

    public function getGoodsName() {
        return $this->valArray["goods_name"];
    }
	
	 public function getTrackingNumber() {
        return $this->valArray["tracking_number"];
    }

    public function getDescription() {
        return $this->valArray["description"];
    }

    public function getWeight() {
        return $this->valArray["weight"];
    }
    
    public function getDimension() {
        return $this->valArray["dimension"];
    }

     public function getAddedDate() {
        return $this->valArray["added_date"];
    }

    public function getAddedBy() {
        return $this->valArray["added_by"];
    }
    
    public function getUpdatedDate() {
        return $this->valArray["updated_date"];
    }

    public function getUpdatedBy() {
        return $this->valArray["updated_by"];
    }

    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////
    // specific setters
    public function setGoodsName($goods_name) {
        $this->valArray["goods_name"] = $goods_name;
    }
	
	public function setTrackingNumber($tracking_number) {
        $this->valArray["tracking_number"] = $tracking_number;
    }

    public function setDescription($description) {
        $this->valArray["description"] = $description;
    }

    public function setWeight($weight) {
        $this->valArray["weight"] = $weight;
    }
    
    public function setDimension($dimension) {
        $this->valArray["dimension"] = $dimension;
    }

    public function setAddedDate($added_date) {
        $this->valArray["added_date"] = $added_date;
    }

    public function setAddedBy($added_by) {
        $this->valArray["added_by"] = $added_by;
    }
    
    public function setUpdatedDate($updated_date) {
        $this->valArray["updated_date"] = $updated_date;
    }

    public function setUpdatedBy($updated_by) {
        $this->valArray["updated_by"] = $updated_by;
    }
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getRackShelfItemListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}

?>