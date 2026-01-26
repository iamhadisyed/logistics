<?php
class ServiceAgentMapping extends DbAccess3
{
	public function __construct($mixedCreator = null)
	{
		$fieldList = array
                (
                    'id'                    => 'number',
                    'serviceid'             => 'number',						
                    'agentid'               => 'number',
                    'linehaul_agent'        => 'number',
                    'account_number'        => 'string',
                    'api_url'               => 'string',
                    'api_username'          => 'string',
                    'api_password'          => 'string',
                    'ftp_host'              => 'string',
                    'ftp_username'          => 'string',
                    'ftp_password'          => 'string',
                    'integration_type'      => 'string',
                    'class_file_name'       => 'string',
                    'insurance_charges'     => 'number',
                    'insurance_cover'       => 'number',
                    'reroute_charges'       => 'number',
                    'oversize_charges'      => 'number',
                    'address_change_charges' => 'number',
                    'other_surcharges'      => 'number',
                    'return_charges'        => 'number',
                    'relabel_charges'       => 'number',
                    'wrong_address_charges' => 'number',
                    'from_weight'           => 'number',
                    'to_weight'             => 'number',
                    'email'                 => 'text',
                    'email_finance'         => 'text'
                );
		//
		parent::__construct("service_agent_mapping", 'id', $fieldList, $mixedCreator);
	}
	
	
	public static function getServiceAgentListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	public function bulkDataInsert($rows)
	{
            $sql = array(); 
            foreach($rows as $row) 
            {
                $sql[] = "(".DbAccess3::escape($row['serviceid']).",'".DbAccess3::escape($row['agentid'])."')";
            }
            $sqlQuery =     'INSERT INTO service_agent_mapping ( serviceid, agentid) VALUES '.implode(',', $sql);
            return DbAccess3::runQuery($sqlQuery);				 
	}
	public function deleteByAgentId($agent_id)
	{
            $sql = "Delete From service_agent_mapping where agentid = '".DbAccess3::escape($agent_id)."'";
            return DbAccess3::runQuery($sql);	
	}
        public static function deleteById($id)
	{
            $sql = "Delete From service_agent_mapping where id = '".DbAccess3::escape($id)."'";
            return DbAccess3::runQuery($sql);	
	}
	public static function getTotalNumberOfServiceAgentListFromSql($sql)
        {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}
        
        public function getLogArray()
        {
            return $this->logArray;
        }
}  // class
