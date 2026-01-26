<?php

/**
 * Description of reportingfilter
 *
 * @author Irshad Ali
 */
class reportingfilter {

    private $filter = "";
    private $order_by = "";
    private $join_filter = "";
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $group_by = '';

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    public function setFilter($filter) {
        $this->filter = $filter;
    }
    public function setJoinFilter($filter) {
        $this->join_filter = $filter;
    }
    public function setGroupBy($group) {
        $this->group_by = $group;
    }
    public function setOrderBy($order) {
        $this->order_by = $order;
    }

    public function getConsignmentBagTrackingNo($master_number, $bag_number) {
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . $this->filter;
        }
        $sql = "SELECT c.`awb`, c.`sender_name`, c.`consignment_status`,c.`hawb`,c.`service_type`,c.`weight`,c.`description`,c.`company`,c.`contact`,c.`address_line_1`,c.`address_line_2`,c.`address_line_3`,c.`city`,c.`postcode`,c.`country`,c.`value`,c.`currency`,c.`scanned_by`,c.`date_scanned` FROM  `consignment` c WHERE c.`mawb` = '" . DbAccess3::escape($master_number) . "' AND c.`bag_number` = '" . DbAccess3::escape($bag_number) . "' AND c.`consignment_status` NOT IN ('poland received', 'poland booked')";
        t($sql, __METHOD__);

        return Reporting::getTransactionReportFromSql($sql);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "AND " . $this->filter; //substr($this->filter, 4);
        }
        $sql = "SELECT 
                        COUNT(tracking_numbers) AS total
                     FROM
                        (SELECT count(c.awb) as tracking_numbers
                            FROM consignment c 
                            WHERE c.mawb IS NOT NULL AND c.bag_number IS NOT NULL AND c.mawb <> '' AND c.bag_number <> ''" . $where .
                " GROUP BY c.bag_number) AS consignmentTemp";
        t($sql, __METHOD__);

        return Reporting::getTotalRecordsFromSql($sql);
    }

}
