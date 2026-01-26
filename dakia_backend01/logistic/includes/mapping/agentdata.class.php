<?php
// get settings
//require_once("includes/settings/common.inc.php");

class AgentData extends DbAccess3
{
	
	public function __construct($mixedCreator = null)
	{
		$this->tablename = 'agent_data';
                $this->pkey      = 'id';
		$fieldList = array
                        (
                            'id'      => 'number',
                            'agent_code'      => 'string',
                            'agent_name'      => 'string',
                            'active' => 'number',
                            'contact_name' => 'string',
                            'address_line_1' => 'string',
                            'address_line_2' => 'string',
                            'address_line_3' => 'string',
                            'country_id'=>'number',
                            'county'=>'string',
                            'city'=>'string',
                            'postcode'             => 'string',
                            'telephone'           => 'string',
                            'mobile'      => 'string',
                            'fax'               => 'string',
                            'email'                => 'string',
                            'alternative_contact_1'               => 'string',
                            'alternative1_telephone'               => 'string',
                            'alternative1_mobile'    => 'string',
                            'alternative1_fax'         => 'string',
                            'alternative1_email' => 'string',
                            'alternative_contact_2'         => 'string',
                            'alternative2_telephone'   => 'string',
                            'alternative2_mobile'   => 'string',
                            'alternative2_fax'           => 'string',
                            'alternative2_email'       => 'string',
                            'remarks'       => 'string',
                            'date_created' => 'string',
                            'user_id'      => 'string',
                            'agent_type'      => 'string',
                            'is_deleted'        => 'number',
                            'logo'      => 'string',
                            'contract_name' => 'undefined',
                            'carrier'   => 'undefined',
                            'logo'   => 'string',
                            'status'    => 'undefined',
                            'carrier_id' => 'undefined',
                            'service_id' => 'undefined'
                            
                   
					);
		//
		parent::__construct("agent_data", 'id', $fieldList, $mixedCreator);
	}

	
	


	

	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getAgentListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


	
	public static function getTotalNumberOfAgentFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}
	/***
	 * Check if data held by curernt object is valid
	 */
	public function isValid(&$error_list_array)
	{
		if ($this->getName() == "") $error_list_array[] = "Please enter service name.";
		if ($this->getCode() == "") $error_list_array[] = "Please enter service code.";
		if ($this->getCarrier() == "") $error_list_array[] = "Please enter service carrier.";
		
		t("Error list is " . sizeof($error_list_array), __METHOD__);

		return (sizeof($error_list_array) == 0);
	}
        
        
        public function getLogArray()
        {
            return $this->logArray;
        }




}  // class
