<?php
// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class ConsignmentRelabel extends DbAccess3
{
	

	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'      => 'number',	
						'consignment_id'   => 'number',					
						'old_tracking_no'   => 'string',
						'new_tracking_no'   => 'string',
						'date_created' => 'datetime',
						'userid' => 'number',					
						'old_consignment_data'   => 'string',
						'old_parcel_tracking_no'   => 'string',
						'old_new_tracking_mapping'   => 'string'
						
											
					
					);
		//
		parent::__construct("consignment_relabel", 'id', $fieldList, $mixedCreator);
	}
	
	
	public static function getManifestListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
        public static function getTotalNumberOfConsignmentRelabelFromSql($sql) {
            $rs = DbAccess3::runQuery($sql);
            $data = mysqli_fetch_assoc($rs);
            return $data['total'];
        }
	
	
	
	
	

}  // class
