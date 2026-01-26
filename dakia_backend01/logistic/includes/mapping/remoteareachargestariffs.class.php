<?php

////////////////////////////////////////////////////
//
// Class for RemoteAreaChargesTariffs Object
//
////////////////////////////////////////////////////

 
class RemoteAreaChargesTariffs extends DbAccess3
{
    
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

          $fieldList = array(
            'id' => 'number',
            'remotearea_group_id' => 'number',
            'remotearea_charges' => 'number',
            'tariff_id' => 'number',
            'from_weight' => 'number',
            'to_weight' => 'number',
            'formulla' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'string'
        );

        parent::__construct("remotearea_charges_tariffs", 'id', $fieldList, $mixedCreator);
    }
  
      /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getRemoteareaChargesTariffListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRemoteareaChargesTariffFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
  
    public static function deleteByTariffId($tariffId) {
        if($tariffId != "" && $tariffId > 0)
            self::runQuery("DELETE FROM remotearea_charges_tariffs WHERE tariff_id = '".DbAccess3::escape($tariffId)."'");
    }
 
}
?>
