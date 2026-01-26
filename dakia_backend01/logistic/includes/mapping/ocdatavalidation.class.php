<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class OcDataValidation extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
                                'id'    => 'number',
                                'order_reference' => 'string',
								'tracking_number' => 'string',
                                'oc_response' => 'string',
                                'oc_request' => 'string',
								'host_url' => 'string',
                                'date_created' => 'string',
                                'date_updated' => 'datetime',
                                'added_by' => 'number'

                                
                                );

		//
		parent::__construct("oc_data_validation", 'id', $fieldList, $mixedCreator);
	}

    public static function getOcDataValidationListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}	


	
}

