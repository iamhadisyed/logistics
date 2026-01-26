<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class ServiceConstant extends DbAccess3
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
                    'constant'           => 'string',
                    'carrier_id'         => 'number',
                    'caption'            => 'string',
                    'description'        => 'string',
                    'design_control'     => 'string',
                    'mandatory'          => 'number',
                    'sort_order'         => 'number',
                    'integration_type'   => 'string',
                    'default_values'      => 'string',
                'field_size'            => 'number',
                    'constant_value'     => 'undefined',

		);

		parent::__construct("service_constant", 'id', $fieldList, $mixedCreator);
	}

	public static function getServiceConstantListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	
	
}


