<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class ServiceConstantValue extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
		
                    'id'                => 'number',
                    'constant_value'    => 'string',
                    'service_id'        => 'number',
                    'agent_id'          => 'number',
                    'constant_id'       => 'number',
                    'date_created'      => 'datetime',
                    'added_by'          => 'number',
                    'date_update'       => 'datetime',
                    'updated_by'        => 'number',
                    'service_name'      => 'undefined',
                    'agent_name'      => 'undefined',
                    'constant'      => 'undefined',
                    'logo'          => 'undefined',
                    'constant_name'    => 'undefined',
                    
		);

		parent::__construct("service_constant_value", 'id', $fieldList, $mixedCreator);
	}
	 
        public static function getServiceConstantValueListFromSql($sql) {
            return DbAccess3::getListFromSql(__CLASS__, $sql);
        }

        public static function getTotalNumberOfConstantFromSql($sql)
        {
            $rs = DbAccess3::runQuery($sql);
            $data=mysqli_fetch_assoc($rs);
            return $data['total'];
	}
        
        public static function deleteConstant($where)
	{
            if($where != '')
            {
                $sql = "Delete From service_constant_value where " . $where;
                return DbAccess3::runQuery($sql);	
            }
	}

	
	
	
}


