<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class ServiceRangeMapping extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
	
	 
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
		
                    'id'                 => 'number',
                    'service_id'         => 'number',
                    'agent_id'           => 'number',
                    'licence_plate_id'   => 'number',
                    'service_name'       => 'undefined',
                    'agent_name'         => 'undefined',
                    'logo'               => 'undefined',
                    'agent_name'         => 'undefined',
                    'name'               => 'undefined'
		);

		//
		parent::__construct("service_range_mapping", 'id', $fieldList, $mixedCreator);
	}

	public static function getServiceRangeMappingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
        public static function deleteRange($where)
	{
            if($where != '')
            {
                $sql = "Delete From service_range_mapping where " . $where;
                return DbAccess3::runQuery($sql);	
            }
	}
	
	
	
}


