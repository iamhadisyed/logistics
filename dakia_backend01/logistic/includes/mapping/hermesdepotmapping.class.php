<?php
class HermesDepotMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'depot_id' => 'number',
            'sack_depot' => 'number',
            'sack_depot_name' => 'string'
        );
        parent::__construct("hermes_depot_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getHermesDepotMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfHermesDepotMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteHermesDepotMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateHermesDepotMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
