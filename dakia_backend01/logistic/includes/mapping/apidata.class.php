<?php 
/**
 * Api Data class
 * @package News Releases
 */
class ApiData extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'type' => 'string',
            'api_request' => 'string',
            'api_response' => 'string',
            'added_by' => 'number',
            'consignment_id' => 'number',
            'date_created' => 'datetime'
        );

        parent::__construct("api_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getApiDataListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfApiDataFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteApiDataDetailsFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
