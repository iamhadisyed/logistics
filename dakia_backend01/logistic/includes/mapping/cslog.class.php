<?php
// get settings
//require_once("includes/settings/common.inc.php");

class CsLog extends DbAccess3
{
	
	public function __construct($mixedCreator = null)
	{
		$this->tablename = 'cs_log';
        $this->pkey      = 'id';
		$fieldList = array
					(
					'id'      => 'number',
					'consignment_id'      => 'number',
					'internal_message'      => 'string',
					'customer_message'   => 'string',
					'cust_mail' 		=> 'string',
					'agent_mail' => 'string',
					'date_created' => 'datetime',
					'reminder'	=> 'string',
					'reminder_expiry' => 'datetime',
					'userid'	=> 'number'
					);
		//
		parent::__construct("cs_log", 'id', $fieldList, $mixedCreator);
	}

	
	


	

	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getCsLogListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


	
	public static function getTotalNumberOfCsLogFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}


}  // class
