<?php

/**
 * ManifestEntityMapping Object
 *
 */
class ManifestEntityMapping extends DbAccess3
{

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'entity_id' => 'number',
            'manifest_id' => 'string',
            'manifest_entity_type' => 'string',
            'consignment_id' => 'undefined'
        );
        parent::__construct("manifest_entity_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of manifest_entity_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getManifestEntityMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfManifestEntityMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId()
    {
        return $this->valArray["id"];
    }

    public function deleteById($id)
    {
        if ($id != "" && $id > 0)
            self::runQuery("DELETE FROM manifest_entity_mapping WHERE id = '" . DbAccess3::escape($id) . "'");
    }

    public function deleteByManifestId($manifestId)
    {
        if (!empty($manifestId))
            self::runQuery("DELETE FROM manifest_entity_mapping WHERE manifest_id = '" . DbAccess3::escape($manifestId) . "'");
    }


    public function bulkDataInsertCustomerMenifest($manifestId, $menifestType = 'p', $consignmentArray = array(), $debug = false)
    {
        if (count($consignmentArray)>0) {
            $consignmentArrayString =   implode("','", $consignmentArray);
            $sqlQuery = "INSERT INTO manifest_entity_mapping (id, entity_id,manifest_id, manifest_entity_type )  
                          SELECT NULL , p.id, $manifestId, '".$menifestType."' FROM parcel p WHERE p.consignment_id in ('" .$consignmentArrayString  . "' ) AND p.consignment_id  <> ''";
        
            if ($debug) {
                echo $sqlQuery;
                die;
            }
            return DbAccess3::runQuery($sqlQuery);
        }
    }

}
