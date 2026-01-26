<?php
/**
 * MAWB CLASS
 *
 */
class Mawb extends DbAccess3
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
                                    'mawb_number' => 'string',
                                    'mawb_source_country_id' => 'number',					
                                    'mawb_source_warehouse_id' => 'number',					
                                    'mawb_destination_country_id' => 'number',					
                                    'mawb_destination_warehouse_id' => 'number',					
                                    'is_active' => ['enum' => ['y','n'],'default' => 'y'],
                                    'added_by' => 'string',
                                    'added_date' => 'string',
                                    'updated_by' => 'datetime',
                                    'updated_date' => 'string',					
                                    'mawb_status' => ['enum' => ['o','d','pd'],'default' => 'o'],
                                    'manifest_label' => 'string',
                                     'mawb_lv_manifest' => 'string',
                                     'mawb_hv_manifest' => 'string',
                                    'mawb_mv_manifest' => 'string',
                                     'mawb_class' => 'string',
                                     'gross_weight' => 'number',
                                     'charge_weight' => 'number',
                                    'source_warehouse' => 'undefined',
                                    'destination_warehouse' => 'undefined',
                                    'source_country' => 'undefined',
                                    'destination_country' => 'undefined'
                                    );
		parent::__construct("mawb", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	
	public static function getMawbListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	public static function getConsignmentListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	public static function getTotalNumberOfRecordsFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}


}
