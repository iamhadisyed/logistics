<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Pallet entity
 *
 */
class PalletEntityMapping extends DbAccess3 { /**
 * Construct
 *
 * @param id/array
 */

    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'pallet_id' => 'number',
            'entity_id' => 'number',
            'pallet_entity_type' => 'string',
            'pre_sort' => 'string'
        );
        //
        parent::__construct("pallet_entity_mapping", 'id', $fieldList, $mixedCreator);
    }
    
    public static function getPalletEntityMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function bulkDataInsert($rows) {
        $sql = array();

        foreach ($rows as $row) {
            $sql[] = "(" . DbAccess3::escape($row['pallet_id']) . ",'"
                    . DbAccess3::escape($row['entity_id']) . "')";
        }
        $sqlQuery = 'INSERT INTO pallet_entity_mapping (
						 pallet_id, 
						 entity_id,
						 pallet_entity_type
						 ) 
						 VALUES ' . implode(',', @$sql);
        t($sql, __METHOD__);
        return DbAccess3::runQuery($sqlQuery);
    }

    public static function getPalletListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

   
    public function DeleteAllPalletFromPallet($entityId,$palletEntityType = 'b') {
        if ($entityId != '') {
            $sql = "DELETE from pallet_entity_mapping where entity_id = '" . DbAccess3::escape($entityId) . "' AND pallet_entity_type = '" . DbAccess3::escape($palletEntityType) . "' ";
            return DbAccess3::runQuery($sql);
        }
    }
    public function DeleteByIdFromPallet($entityMappingId) {
        if ($entityMappingId != '') {
            $sql = "DELETE from pallet_entity_mapping where id = '" . DbAccess3::escape($entityMappingId) . "' ";
            return DbAccess3::runQuery($sql);
        }
    }

}
