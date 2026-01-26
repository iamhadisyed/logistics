<?php
/*
 * Consignment Filter
 *
 */

class CurrencyFilter
{
	private $filter = "";
	private $order_by = "";
	private $groupBy = "";
	private $join = "";

	public function getList($debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") {
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

		$groupBy = "";
		if ($this->groupBy != "")
			$groupBy = "" . $this->groupBy;

		$checkJoin = "";
		if ($this->join != "")
			$checkJoin = $this->join;

		$sql = "SELECT *
				FROM currency c
				$checkJoin
				$where
				$groupBy
				$sort
				";
		if ($debug) {
			echo $sql;
			die;
		}
		return Currency::getCurrencyListFromSql($sql);

	}

	public function getColumnList($fields)
	{
		$fields = rtrim($fields, ",");
		// has filter been configured?
		$where = "";
		if ($this->filter != "") {
			$where = "WHERE " . substr($this->filter, 4);
		}
		$groupBy = "";
		if ($this->groupBy != "")
			$groupBy = $this->groupBy;

		$checkJoin = "";
		if ($this->join != "")
			$checkJoin = $this->join;

		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT currencyid, " . $fields . "
				FROM currency c
				$checkJoin
				$where
				$groupBy
				$sort
				LIMIT 5000
				";
		return Currency::getCurrencyListFromSql($sql);
	}

	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}

	public function getPagingList($fields, $debug = false)
	{


		if ($this->filter != "") {
			$where = "WHERE " . substr($this->filter, 4);
		}
		$groupBy = $this->groupBy;
		if ($this->order_by != '')
			$sort = " order by " . $this->order_by;

		$groupBy = "";
		if ($this->groupBy != "")
			$groupBy = $this->groupBy;

		$checkJoin = "";
		if ($this->join != "")
			$checkJoin = $this->join;

		$sql = "SELECT " . $fields . " , c.id from 
                    currency c
                    $this->join
                    $where 
                    $groupBy
                    $sort LIMIT $this->pageOffset , $this->rowsPerPage";

		if ($debug) {
			echo $sql;
			exit;
		}
		t($sql, __METHOD__);

		return Currency::getCurrencyListFromSql($sql);
	}

	public function getPagingCount($debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") {
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "")
			$sort = "ORDER BY " . $this->order_by;

		$groupBy = "";
		if ($this->groupBy != "")
			$groupBy = $this->groupBy;

		$checkJoin = "";
		if ($this->join != "")
			$checkJoin = $this->join;

		$sql = "SELECT COUNT(c.id) as total from 
               currency c
               $this->join
               $where
               $groupBy";

		if ($debug) {
			echo $sql;
			exit;
		}
		t($sql, __METHOD__);

		return Currency::getTotalNumberOfCurrencyFromSql($sql);
	}

	public function setRowsPerPage($rowsPP)
	{
		$this->rowsPerPage = $rowsPP;
	}

	public function setOffset($offset)
	{
		$this->pageOffset = $offset;
	}

	public function addFieldFilter($colm, $value)
	{
		if ($this->filter != "")
			$this->filter .= " AND ";
		$this->filter .= "		" . $colm . " = '" . $value . "'";
	}

	public function addFieldLikeFilter($colm, $value){
		if ($this->filter != "")
			$this->filter .= " AND ";
		$this->filter .= "		" . $colm . " LIKE '%" . $value . "%'";
	}

	public function addIsactiveFilter($isActive){
		if ($this->filter != "")
			$this->filter .= " AND ";

		$this->filter .= "		isactive = '" . $isActive . "'";
	}

	public function addFilter($account_number){
		//echo $account_number;
		if ($this->filter != "")
			$this->filter .= " AND ";
		$this->filter .= $account_number;
		//echo $this->filter;
		//exit;
	}

	public function AddOrderBy($name, $ascending = true)
	{
		//if ($this->order_by != "") $this->order_by = " ";
		//
		if (trim($name) != '')
			$this->order_by = $name . " " . ($ascending ? "ASC" : " DESC");
	}

	public function addGroupBy($field)
	{
		$this->groupBy .= " GROUP BY $field";
	}

	public function addJoin($table, $where, $type = "INNER")
	{
		$this->join .= $type . " JOIN " . $table . "  ON " . $where . " ";
	}
}
