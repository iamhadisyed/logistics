<?php

// get settings
//require_once("includes/settings/common.inc.php");
class PalletLocationFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT *
				FROM pallet_location l
				$where
				 $sort ";

		return PalletLocation::getLocationListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT  id, ".$fields."  FROM pallet_location l  $where  $sort "; 
		
		return PalletLocation::getLocationListFromSql($sql);
	}
	
	public function getPagingCount($palletid, $conid, $locationid, $dateFrom, $dateTo)
			{
				// has filter been configured?
				/*$where = "";
				if ($this->filter != "")
				{
					$where = "WHERE " . $this->filter;//substr($this->filter, 4);
				}
				$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;*/

				//$sql = "SELECT count(*) as total FROM pallet_location l $where $sort ";
				
				if($palletid > 0)
				{
		
				$sql = "select count(*) 'total' from
						(select palletno 'palletid', l.name 'locationid', w.warehouse_name 'warehouseid',
						pl.date_created, u.user_name 'createdby' 
						from pallet_location pl, 			                
						pallet p , location l, user u, warehouse w
						where pl.locationid = l.id and pl.palletid = p.id and 
						pl.createdby = u.id and w.id = l.warehouseid and
						palletid = ".DbAccess3::escape($palletid);
				
				if($locationid > 0)
					$sql.= " and locationid = ".DbAccess3::escape($locationid);
					
				if($dateFrom != '')
					$sql.= " and date_format(pl.date_created, '%Y-%m-%d') >= '$dateFrom' ";					
					
				if($dateTo != '')
					$sql.= " and date_format(pl.date_created, '%Y-%m-%d') <= '$dateTo' ";		
							                 
				$sql.=	"union
						select palletno, 'Dispatched', p.date_dispatch, u.user_name 'createdby' 
						from pallet p, user u where p.dispatch_userid = u.id and p.id = ".DbAccess3::escape($palletid).") t
						";
				}
				elseif($conid > 0)
				{
					$sql = "select count(*) 'total' from (
							select awb 'palletid', l.name 'locationid', w.warehouse_name 'warehouseid', pl.date_created,                        u.user_name 'createdby' from
							pallet_location pl, 			                
							location l, user u, consignment c, warehouse w
							where pl.locationid = l.id and pl.createdby = u.id and
							c.id = pl.consignmentid and w.id = l.warehouseid"; 
					
					if($conid > 0)						
						$sql .=	" and consignmentid = ".DbAccess3::escape($conid);
					
					if($locationid > 0)
						$sql.= " and locationid = ".DbAccess3::escape($locationid);
						
					if($dateFrom != '')
						$sql.= " and date_format(pl.date_created, '%Y-%m-%d') >= '$dateFrom' ";					
					
					if($dateTo != '')
						$sql.= " and date_format(pl.date_created, '%Y-%m-%d') <= '$dateTo' ";		
												 
					$sql.=	"union
							select tracking_number, track_point, tdh.description 'warehouseid', date_created, tdh.account                        'createdby' from tracking_data_history tdh  
							where consignment_id = ".DbAccess3::escape($conid)."
							) t					
							"; 
						
				}
				elseif($locationid >0 || $dateFrom != '' || $dateTo != '')
				{
					$sql = "
							select count(*) 'total' from
							pallet_location pl, 			                
							location l, user u, consignment c, warehouse w
							where pl.locationid = l.id and pl.createdby = u.id and
							c.id = pl.consignmentid and w.id = l.warehouseid"; 
							
					if($dateFrom != '')
						$sql.= " and date_format(pl.date_created, '%Y-%m-%d') >= '$dateFrom' ";					
					
					if($dateTo != '')
						$sql.= " and date_format(pl.date_created, '%Y-%m-%d') <= '$dateTo' ";		
							
					if($locationid > 0)
						$sql.= " and locationid = ".DbAccess3::escape($locationid);				
					
					
				}
				
				
				//echo $sql;
				

				t($sql, __METHOD__);

				return PalletLocation::getTotalNumberOfLocationsFromSql($sql);

			}

			
			public function getPagingColumnList ( $fields)
			{
					// has filter been configured?
					$where = "";
					if ($this->filter != "")
					{
						$where = "WHERE " . substr($this->filter, 4);
					}
					$sort = "";
					if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;


					$sql = "SELECT ".$fields." FROM pallet_location l $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					
					

					t($sql, __METHOD__);

					return PalletLocation::getLocationListFromSql($sql);
			}


			public function setOffset($offset)
			{
				$this->pageOffset = $offset;
			}

			public function setRowsPerPage($rowsPP)
			{
				$this->rowsPerPage = $rowsPP;
			}
	/**
	 * Get count of user items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}
	
	public function AddOrderById($ascending = true)
	{
		if ($this->order_by != "") $this->order_by .= ", ";
		//
		$this->order_by .= "id" . ($ascending ? "" : " DESC");
	}


	
}  // class
?>