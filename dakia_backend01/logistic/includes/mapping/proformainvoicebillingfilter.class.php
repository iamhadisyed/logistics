<?php
/*
 * Parcel Filter
 *
 */
class ProformaInvoiceBillingFilter
{
	private $filter = "";

	/**
	 * Get list of parcels based on filter conditions
	 *
	 * @return array[parcel]
	 */
	public function getList()
	{
		// has filter been configured
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;

		 $sql = "SELECT *
				FROM proforma_invoice_biiling p
				$where";

		return ProformaInvoiceBilling::getProformaInvoiceBiilingListFromSql($sql);
	}


	/**
	 * Get list of parcels based on filter conditions
	 *
	 * @return array[parcel]
	 */
	public function getColumnList($fields)
	{
		// has filter been configured
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;

		 $sql = "SELECT id, ".$fields."
				FROM proforma_invoice_biiling p
				$where";
	//mail("mruga@oneworldexpress.com", "", $sql);
		return ProformaInvoiceBilling::getProformaInvoiceBiilingListFromSql($sql);
	}

 public function getCount()
    {
        $result = $this->getList();
        return sizeof($result);
    }
	/**
	 * Filter on a consignment Id
	 *
	 * @param int - consignment Id
	 */
	public function addConsignmentIdFilter ($id)
	{
		// List of service types
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "consignment_id=".DbAccess3::escape($id);
	}
	
	public function addIdArrayFilter($idArray)
	{
		if(is_array($idArray))
		{
			$generatelabelstr	=	implode("','",$idArray);
			if ($this->filter != "") $this->filter .= " AND ";
			$this->filter .= "consignment_id IN ('" . $generatelabelstr . "')";	
		}
		
	}
	
	
}