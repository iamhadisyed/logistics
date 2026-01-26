<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class CustomizedServicesRouting extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
	
	 
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
		
                    'id'                        => 'number',
                    'country_iso'               => 'undefined',
                    'from_weight'               => 'number',
                    'to_weight'                 => 'number',
                    'status'                    => 'number',
                    'customize_service_id'           => 'number',
                    'service_id'                => 'number',
                    'country_id'                => 'number',
                    
                    'logo'                      => 'undefined',
                    'carrier'                   => 'undefined',
                    'service_name'              => 'undefined',
                    'country_name'              => 'undefined',
                    'service_from_weight'       => 'undefined',
                    'service_to_weight'         => 'undefined',
                    'carrier_country'           => 'undefined',
                    'product_name'              => 'undefined',
                    'product_id'                => 'undefined',
                    'proforma_invoice'          => 'undefined',
                    'account_owner'             => 'undefined',
                    'type'                      => 'undefined',
                    'volumetric_denominator'    => 'undefined',
                    'is_agreed'     => 'undefined',
                    'service_code'              => 'undefined'
                    
                    
                
		);

		//
		parent::__construct("customized_services_routing", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	
	/**
	 * Get list of Consignmnet Piece objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getCustomizedServicesListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
        public static function deleteRoutine($where)
        {
            $updateQuery	=  " DELETE FROM customized_services_routing where $where"; 
            DbAccess3::runQuery($updateQuery);
            return true;
        }
        
        public static function updateCustomizedService($country_iso, $from_weight, $to_weight, $product_id, $service_id,$tTimeCheeck = true)
	{
            $updateQuery	=  " UPDATE customized_services_routing SET service_id = '".$service_id."' where country_id='".$country_iso."' 
                                AND (from_weight >= '".$from_weight."' AND to_weight <= '".$to_weight."') AND customize_service_id = '".$product_id."'";
            DbAccess3::runQuery($updateQuery);
            
            $serviceCountryTimeFilter = new ServiceCountryTimeFilter();
            $serviceCountryTimeFilter->addFieldFilter('id_country', $country_iso);
            $serviceCountryTimeFilter->addFieldFilter('id_service', $service_id);
            $serviceTTimeList = $serviceCountryTimeFilter->getList();
            if (count($serviceTTimeList) > 0) {
                //do nothing
            }else if($tTimeCheeck){
                //Insert new record
                    $insertQuery2 = "INSERT INTO `service_country_ttime`
                            (
                            `id_country`,
                            `id_service`)
                            VALUES
                            (
                            '".$country_iso."',
                            ".$product_id.")";
                    DbAccess3::runQuery($insertQuery2);
            }
            
            DbAccess3::runQuery($updateQuery);
            
            return true;
        }
        
        public static function insertCustomizedService($countryIso, $fromWeight, $toWeight, $customizeServiceId, $serviceId,$tTimeCheeck = true)
	{
            $tempCountryId = "";
            $tempServiceId = "";
            $insertQuery = "INSERT INTO `customized_services_routing`
                               (
                               `country_id`,
                               `from_weight`,
                               `to_weight`,
                               `status`,
                               `customize_service_id`,
                               `service_id`)
                               VALUES
                               (
                               '".$countryIso."',
                               ".$fromWeight.",
                               ".$toWeight.",
                               '1',
                               ".$customizeServiceId.",
                               ".$serviceId.")
                               ";
            DbAccess3::runQuery($insertQuery);
            if($tTimeCheeck){
                $insertQuery2 = "INSERT INTO `service_country_ttime`
                                   (
                                   `id_country`,
                                   `id_service`)
                                   VALUES
                                   (
                                   '".$countryIso."',
                                   ".$customizeServiceId.")";
                DbAccess3::runQuery($insertQuery2);
            }
            return true;
        }
        public static function getTotalNumberOfCustomizedServiceFromSql($sql) {
            $rs = DbAccess3::runQuery($sql);
            $data=mysqli_fetch_assoc($rs);
            return $data['total'];
	}
	
	
	
}


