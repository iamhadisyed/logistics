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
class Rack extends DbAccess3 {
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array('warehouse_id' => 'number',
            'title' => 'string',
            'short_title' => 'string',
            'rack_rows' => 'number',
            'rack_cols' => 'number',
            'shelf_dimension' => 'string',
            'shelf_max_weight' => 'string',
			'is_york' => 'number',
            'is_active' => 'number',
            'is_deleted' => 'number',
            'added_date' => 'date',
            'added_by' => 'number',
            'updated_date' => 'date',
            'updated_by' => 'number'
        );
        parent::__construct('rack', 'id', $fieldList, $mixedCreator);
    }    
    // specific getters
    
    ///setter
    
    public function setWarehouseId($warehouse_id) {
        $this->valArray["warehouse_id"] = $warehouse_id;        
        $this->modifyArray["warehouse_id"] = $warehouse_id;        
    }

    public function setTitle($title) {
        $this->valArray["title"] = $title;
        $this->modifyArray["title"] = $title;
    }
    public function setShortTitle($short_title) {
        $this->valArray["short_title"] = $short_title;
        $this->modifyArray["short_title"] = $short_title;
        
    }

    public function setRackRows($rack_rows) {
        $this->valArray["rack_rows"] = $rack_rows;
        $this->modifyArray["rack_rows"] = $rack_rows;
    }
    public function setRackCols($rack_cols) {
        $this->valArray["rack_cols"] = $rack_cols;
        $this->modifyArray["rack_cols"] = $rack_cols;
    }

    public function setShelfDimension() {
        return $this->valArray["shelf_dimension"];
        return $this->modifyArray["shelf_dimension"];
    }

    public function setShelfMaxWeight($shelf_max_weight) {
        $this->valArray["shelf_max_weight"] = $shelf_max_weight;
        $this->modifyArray["shelf_max_weight"] = $shelf_max_weight;
    }

    public function setPostZipcode($postzipcode) {
        $this->valArray["postzipcode"] = $postzipcode;
        $this->modifyArray["postzipcode"] = $postzipcode;
    }

    public function setIsActive($is_active) {
        $this->valArray["is_active"] = $is_active;
        $this->modifyArray["is_active"] = $is_active;
    }

    public function setIsDeleted($is_deleted) {
        $this->valArray["is_deleted"] = $is_deleted;
        $this->modifyArray["is_deleted"] = $is_deleted;
    }

    public function setAddedDate($added_date) {
        $this->valArray["added_date"] = $added_date;
        $this->modifyArray["added_date"] = $added_date;
    }

    public function setAddedBy($added_by) {
        $this->valArray["added_by"] = $added_by;
        $this->modifyArray["added_by"] = $added_by;
    }

    public function setUpdatedDate($updated_date) {
        $this->valArray["updated_date"] = $updated_date;
        $this->modifyArray["updated_date"] = $updated_date;
    }

    public function setUpdatedBy($updated_by) {
        $this->valArray["updated_by"] = $updated_by;
        $this->modifyArray["updated_by"] = $updated_by;
    }
    
    /// getter
    public function getId() {
        return $this->valArray["id"];
    }

    public function getWarehouseId() {
        return $this->valArray["warehouse_id"];
    }

    public function getTitle() {
        return $this->valArray["title"];;
    }
    public function getShortTitle() {
        return $this->valArray["short_title"];;
    }

    public function getRackRows() {
        return $this->valArray["rack_rows"];
    }
    public function getRackCols() {
        return $this->valArray["rack_cols"];
    }

    public function getShelfDimension() {
        return $this->valArray["shelf_dimension"];
    }

    public function getShelfMaxWeight() {
        return $this->valArray["shelf_max_weight"];
    }

    public function getPostZipcode() {
        return $this->valArray["postzipcode"];
    }

    public function getIsActive() {
        return $this->valArray["is_active"];
    }

    public function getIsDeleted() {
        return $this->valArray["is_deleted"];
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
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getRackListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfRacksFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
?>