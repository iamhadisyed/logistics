<?php

class SalesCallLog extends DbAccess3
{
	

	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'      => 'number',						
						'date_call' => 'datetime',
						'meeting_date' => 'datetime',
						'customer_code'	=> 'string',
						'company'	=> 'string',
						'contact'	=> 'string',
						'address'	=> 'string',
						'telephone'	=> 'string',
						'email'	=> 'string',
						'detail_discussed'	=> 'string',
						'document_link'	=> 'string',
						'follow_meeting_date'	=> 'datetime',
						'userid'	=> 'number',
						'email_send' => 'string'
					);
		//
		parent::__construct("sales_call_log", 'id', $fieldList, $mixedCreator);
	}
	
	
	public static function getSalesCallLogListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	
	
	
	

}  // class
