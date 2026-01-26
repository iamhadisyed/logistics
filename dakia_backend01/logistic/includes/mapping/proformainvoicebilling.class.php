<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class ProformaInvoiceBilling extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'consignment_id' => 'number',
					'billing_company' => 'string',
					'billing_contact' => 'string',
					'billing_address_line_1' => 'string',
					'billing_address_line_2' => 'string',
					'billing_address_line_3' => 'string',
                    'billing_city'=>'string',
					'billing_country'=>'string',
					'billing_postcode'=>'string',
					'billing_telephone'=>'string',
					'payment_terms'=>'string',
					'export_type'=>'string',
					'comments'=>'string',
					'delivery_terms'=>'string',
					'link_file'=>'string',
					'payer_vat'=>'string',
					'harm_comm_code'=>'string',
					'export' => 'string',
					'invoice_type'=>'string',
					
					);

		//
		parent::__construct("proforma_invoice_biiling", 'id', $fieldList, $mixedCreator);
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
	public static function getProformaInvoiceBiilingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


}
