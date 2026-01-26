<?php

class ConsignmentDetails extends DbAccess3
{
	

	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'      => 'number',						
						'consignment_id' => 'number',
						'custom_export_number'	=> 'string',
						'disc_dim_weight' => 'number',
						'ecom_order_no'                 => 'string',
						'ecom_order_date'               => 'string',
						'ecom_platform_reference'       => 'string',
						'ecom_order_status'             => 'number',
						'ecom_platform'                 => 'number',
						'marketplace_id'					 => 'number',
											
					
					);
		//
		parent::__construct("consignment_details", 'id', $fieldList, $mixedCreator);
	}
	
	
	public static function getConsignmentDetailsListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	
	
	

}  // class
