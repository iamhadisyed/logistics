<?php

/*
 * Consignment Filter
 *
 */

class ConsignmentFilter {


    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;


    private $filter  = "";
    private $consignmentfilter = "";
    private $userfilter = "";
    private $countyfilter = "";
    private $servicefilter = "";
    private $manifestfilter = "";
    private $userAccountFilter = "";
    private $invoicedetailfilter = "";
    private $parcelJoinFilter = "";
    private $parcelfilter = "";

    private $groupBy               = "";
    private $order_by               = "";
    private $order_by_consignment   = "";
    private $order_by_user          = "";
    private $order_by_country       = "";
    private $order_by_service       = "";
    private $order_by_manifest      = "";
    private $order_by_invoicedetail      = "";
    private $order_by_user_account = "";
    private $join                   = "";
    private $consignmentChargesJoinFilter = "";
    private $consignmentHoldJoinFilter = "";
    private $invoiceJoinFilter = "";
    private $baggingMappingFilter = "";
    private $vechileParcelJoinFilter = "";


    const PAGE_SIZE = 20;
    public function __construct() {
        $this->user = SessionManager::getUser();
    }
    /**
     * Get list of consignment items based on filter conditions
     *
     * @return array[Consignment]
     */
    public function getList($debug=false, $fields = "*", $limit = "5000" ) {
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
            $groupBy = "GROUP BY " . ltrim ($this->groupBy, ",");
        
        $sql = "SELECT " . $fields . " FROM consignment c  
                INNER JOIN  parcel pc  
                    ON pc.consignment_id = c.id  
                INNER JOIN user u 
                    ON c.user_id = u.id
                LEFT JOIN services s
                    ON c.service_id = s.id
                LEFT JOIN country con
                    ON c.country_id = con.id
                	$where $groupBy $sort LIMIT ".$limit;
        if($debug) {
            echo $sql;
            die;
        }
        return Consignment::getConsignmentListFromSql($sql);
    }
    
    public function getConList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;

        $sql = "SELECT * FROM consignment c  $where $groupBy $sort LIMIT 5000";
        t($sql, __METHOD__);




        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getCountList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as id FROM consignment c 	$where $sort LIMIT 5000";

        return Consignment::getConsignmentListFromSql($sql);
    }

    public static function getAgentId($handlingCode, $default = 1, $type = 'D') {

        if ($default == '')
            $default = 1;
        if ($type == 'C')
            $collection = 1;
        else
            $collection = 0;
        $sql = "SELECT sam.agentid as agentid FROM service_agent_mapping as sam
				inner join services as s ON sam.serviceid = s.id 
				where sam.agentid in (select id from agent_data where active = 1 and default_agent = $default and collection_agent = $collection) and  s.code = '$handlingCode' ";
        //$sql = "SELECT sam.agentid as agentid FROM service_agent_mapping as sam inner join services as s ON sam.serviceid = s.id where s.code = '" . DbAccess3::escape($handlingCode) . "' order by id desc limit 1";
        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDistinctColumnList($fields, $recordLimit = 5000) {
        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . " 
                FROM consignment c
                INNER JOIN user u 
                    ON c.user_id = u.id
                LEFT JOIN services s
                    ON c.service_id = s.id
                LEFT JOIN country con
                    ON c.country_iso_code = con.iso
                $where
                $sort
				
				";
//        echo $sql;
//        die;
        return Consignment::getConsignmentListFromSql($sql);
    }

  
public function getColumnList($fields, $recordLimit = 5000, $debug = false,$includeParcelGroupby=true) {
        // has filter been configured?
        $where = "";
        $whereconsignment = "";
        $whereuser = "";
        $whereservice = "";
        $wherecountry = "";
        $wheremanifest = "";
        $whereuseraccount = "";
        $whereparcel = "";
        $consignmentChargesJoin = "";
        $parcelJoin = "";
        $consignmentHoldJoin = "";
        $invoiceJoin = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        if ($this->consignmentfilter != "") {
		    $whereconsignment = " AND  " . substr($this->consignmentfilter, 4);
		}
        if ($this->userfilter != "") {
	    	$whereuser = " AND " . substr($this->userfilter, 4);
		}
        if ($this->userAccountFilter != "") {
            $whereuseraccount = " AND " . substr($this->userAccountFilter, 4);
        }
        if ($this->countyfilter != "") {
			$wherecountry = " AND " . substr($this->countyfilter, 4);
		}
        if ($this->servicefilter != "") {
			$whereservice = " AND " . substr($this->servicefilter, 4);
		}
        if ($this->manifestfilter != "") {
		    $wheremanifest = " AND " . substr($this->manifestfilter, 4);
		}
         if ($this->parcelfilter != "") {
		    $whereparcel = " AND " . substr($this->parcelfilter, 4);
		}
        if ($this->consignmentChargesJoinFilter != "") {
            $consignmentChargesJoin = $this->consignmentChargesJoinFilter;
        }
        if ($this->parcelJoinFilter != "") {
            $parcelJoin = $this->parcelJoinFilter;
        }
        
        if ($this->consignmentHoldJoinFilter != "") {
            $consignmentHoldJoin = $this->consignmentHoldJoinFilter;
        }
        
        if ($this->invoiceJoinFilter != "") {
            $invoiceJoin = $this->invoiceJoinFilter;
        }
        $manifestjoin = "";
        if ($this->join != "") {
            $manifestjoin = $this->join;
        }
        /*if ($this->invoicedetailfilter != "") {
            $whereinvoicedetail = "WHERE " . substr($this->invoicedetailfilter, 4);
        }*/
        
	$sort = "";
	$sortConsignment = "";
	$sortUser = "";
        $sortUserAccount = "";
	$sortService = "";
	$sortCountry = "";
	$sortManifest = "";
	$sortInvoicedetail = "";
        $groupBy = "";

        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
	if ($this->order_by != "")
	    $sort = "ORDER BY " . $this->order_by;
	if ($this->order_by_consignment != "")
	    $sortConsignment = "ORDER BY " . $this->order_by_consignment;
	if ($this->order_by_user != "")
	    $sortUser = "ORDER BY " . $this->order_by_user;
	if ($this->order_by_country != "")
	    $sortCountry = "ORDER BY " . $this->order_by_country;
	if ($this->order_by_service != "")
	    $sortService = "ORDER BY " . $this->order_by_service;
	if ($this->order_by_manifest != "")
	    $sortManifest = "ORDER BY " . $this->order_by_manifest;
    if ($this->order_by_user_account != "")
        $sortUserAccount = "ORDER BY " . $this->order_by_user_account;
    if ($this->order_by_invoicedetail != "")
            $sortInvoicedetail = "ORDER BY " . $this->order_by_manifest;

    $invoiceDetailData  =   '';
    /*if ($this->invoicedetailfilter != "")
    {
        $invoiceDetailData  =   " INNER JOIN ( select invd.* from invoice_detail invd $whereinvoicedetail $sortInvoicedetail ) invd ON c.id=invd.consignment_id " ;
    }*/
    if(stripos($fields,"DISTINCT") === false) {
        $fields = $fields.", c.id";
    }
    $invoiceIdCol = '0 AS invoice_id';
        if($consignmentChargesJoin != '') {
            $invoiceIdCol = " cc.invoice_id  AS charges_invoice_id";
        }
        

    if($includeParcelGroupby)
        $parcelGroupBy =   'GROUP BY consignment_id ';
    else
        $parcelGroupBy =   '';

    $addLimit = '';
    if($recordLimit) {
        $addLimit = " LIMIT " . $recordLimit;
    }

	$sql = "SELECT " . $fields . "," . $invoiceIdCol . " 
                FROM consignment c
                INNER JOIN  parcel pc  
                    ON pc.consignment_id = c.id  $whereconsignment  $parcelJoin $whereparcel
                INNER JOIN   user u
                    ON c.user_id = u.id $whereuser  
                INNER JOIN customer_account ua 
                    ON u.user_account_id = ua.id $whereuseraccount
                LEFT JOIN services s 
                    ON c.service_id = s.id $whereservice 
                LEFT JOIN services p
                    ON c.customized_service_id = p.id 
                LEFT JOIN country  con 
                    ON c.country_id = con.id $wherecountry  
                $consignmentChargesJoin
                $consignmentHoldJoin
                $invoiceJoin
                $manifestjoin $wheremanifest "
                . " LEFT JOIN mawb_parcel_mapping mawbpm
                ON mawbpm.`parcel_id` = pc.`id`
              LEFT JOIN mawb
                ON mawb.`id` = mawbpm.`mawb_id`"
                            . ""." "
                    . "$where
                    
		". (($groupBy != "") ? $groupBy:"")."
                $sort
                $addLimit
                ";
        
        if($debug){
            echo $sql;
            die;
        }
        return Consignment::getConsignmentListFromSql($sql);
    }

    
public function getColumnListOptimized($fields, $recordLimit = 5000, $debug = false,$includeParcelGroupby=true) {
        // has filter been configured?
        $where = "";
        $whereconsignment = "";
        $whereuser = "";
        $whereservice = "";
        $wherecountry = "";
        $wheremanifest = "";
        $whereuseraccount = "";
        $whereparcel = "";
        $consignmentChargesJoin = "";
        $parcelJoin = "";
        $consignmentHoldJoin = "";
        $invoiceJoin = "";
        
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        if ($this->consignmentfilter != "") {
		    $whereconsignment = "WHERE " . substr($this->consignmentfilter, 4);
		}
        if ($this->userfilter != "") {
	    	$whereuser = "WHERE " . substr($this->userfilter, 4);
		}
        if ($this->userAccountFilter != "") {
            $whereuseraccount = "WHERE " . substr($this->userAccountFilter, 4);
        }
        if ($this->countyfilter != "") {
			$wherecountry = "WHERE " . substr($this->countyfilter, 4);
		}
        if ($this->servicefilter != "") {
			$whereservice = "WHERE " . substr($this->servicefilter, 4);
		}
        if ($this->manifestfilter != "") {
		    $wheremanifest = "WHERE " . substr($this->manifestfilter, 4);
		}
         if ($this->parcelfilter != "") {
		    $whereparcel = "WHERE " . substr($this->parcelfilter, 4);
		}
        if ($this->consignmentChargesJoinFilter != "") {
            $consignmentChargesJoin = $this->consignmentChargesJoinFilter;
        }
        if ($this->parcelJoinFilter != "") {
            $parcelJoin = $this->parcelJoinFilter;
        }
        
        if ($this->consignmentHoldJoinFilter != "") {
            $consignmentHoldJoin = $this->consignmentHoldJoinFilter;
        }
        
        if ($this->invoiceJoinFilter != "") {
            $invoiceJoin = $this->invoiceJoinFilter;
        }
        
        
	$sort = "";
	$sortConsignment = "";
	$sortUser = "";
        $sortUserAccount = "";
	$sortService = "";
	$sortCountry = "";
	$sortManifest = "";
	$sortInvoicedetail = "";
        $groupBy = "";

        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
	if ($this->order_by != "")
	    $sort = "ORDER BY " . $this->order_by;
	if ($this->order_by_consignment != "")
	    $sortConsignment = "ORDER BY " . $this->order_by_consignment;
	if ($this->order_by_user != "")
	    $sortUser = "ORDER BY " . $this->order_by_user;
	if ($this->order_by_country != "")
	    $sortCountry = "ORDER BY " . $this->order_by_country;
	if ($this->order_by_service != "")
	    $sortService = "ORDER BY " . $this->order_by_service;
	if ($this->order_by_manifest != "")
	    $sortManifest = "ORDER BY " . $this->order_by_manifest;
    if ($this->order_by_user_account != "")
        $sortUserAccount = "ORDER BY " . $this->order_by_user_account;
    if ($this->order_by_invoicedetail != "")
            $sortInvoicedetail = "ORDER BY " . $this->order_by_manifest;

    $invoiceDetailData  =   '';
    if ($this->invoicedetailfilter != "")
    {
        $invoiceDetailData  =   " INNER JOIN ( select invd.* from invoice_detail invd $whereinvoicedetail $sortInvoicedetail ) invd ON c.id=invd.consignment_id " ;
    }
    if(stripos($fields,"DISTINCT") === false) {
        $fields = $fields.", c.id";
    }
    $invoiceIdCol = '0 AS invoice_id';
    if($consignmentChargesJoin != ''){
		$invoiceIdCol = "(SELECT invoice_id from consignment_charges WHERE c.id = consignment_id AND cost_type = 'customer' AND account_id IN (SELECT id FROM `user_account`WHERE `parentid` = '".$this->user->getUserAccountId()."' ) limit 1) AS charges_invoice_id";
        //$invoiceIdCol = 'cc.invoice_id AS invoice_id';
	}

    if($includeParcelGroupby)
        $parcelGroupBy =   'GROUP BY consignment_id ';
    else
        $parcelGroupBy =   '';

    $addLimit = "";
    if($recordLimit) {
        $addLimit = " LIMIT " . $recordLimit;
    }

	$sql = "SELECT " . $fields . "," . $invoiceIdCol . " 
                FROM (select * from consignment c $whereconsignment $sortConsignment ) c 
                INNER JOIN  (select * from parcel pc  $whereparcel $parcelGroupBy ) pc  
                    ON pc.consignment_id = c.id  $parcelJoin
                INNER JOIN (select id, user_account_id from user u $whereuser $sortUser ) u
                     ON c.user_id = u.id
                INNER JOIN
                    (select * from customer_account ua $whereuseraccount $sortUserAccount )  ua 
                    ON ua.id = u.user_account_id 
                 $invoiceDetailData                 
  
                $consignmentChargesJoin 
                $consignmentHoldJoin
                $invoiceJoin        
                     LEFT JOIN mawb_parcel_mapping mawbpm
    ON mawbpm.`parcel_id` = pc.`id`
  LEFT JOIN mawb
    ON mawb.`id` = mawbpm.`mawb_id`
                $where
                    
		". (($groupBy != "") ? $groupBy:"")."
                $sort
                $addLimit
                ";
        
        if($debug){
            echo $sql;
            die;
        }
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getValidCountCon() {

        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT count( id ) as id FROM consignment c $where
				$sort";

        return Consignment::getConsignmentListFromSql($sql);
    }

    
    public function getDashboardTopCoutry($count = 5, $debug =false) {
        // has filter been configured?
        $whereconsignment = "";

        if ($this->consignmentfilter != "") {
            $whereconsignment = "AND " . substr($this->consignmentfilter, 4);
        }

        $sql = "SELECT 
                COUNT(c.id) AS id,
                (select iso from country ct where ct.id = c.country_id) as country_iso_code
              FROM
                consignment c              
              WHERE c.country_id != '' AND date_label_created IS NOT NULL AND  date_label_created > 0 " . $whereconsignment . "
              GROUP BY c.country_id 
              ORDER BY id DESC
              LIMIT " . $count;
        if($debug)
            echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardTopServices($count = 5) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . substr($this->filter, 4);
        }
        $sql = "SELECT 
                COUNT(c.id) AS id,
                c.service_type
              FROM
                consignment c
              WHERE c.service_type != '' " . $where . "
              GROUP BY c.service_type 
              ORDER BY id DESC
              LIMIT " . $count;

        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardTotalHoldConsignment() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . substr($this->filter, 4);
        }
        $sql = "SELECT 
                COUNT(c.id) AS id,
                c.consignment_status
              FROM
                consignment c
              WHERE c.consignment_status = 'hold' " . $where . "
              GROUP BY c.consignment_status";

        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardTotalShippedConsignment() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . substr($this->filter, 4);
        }
        $sql = "SELECT 
                COUNT(c.id) AS id,
                c.consignment_status
              FROM
                consignment c
              WHERE c.consignment_status = 'booked' " . $where . "
              GROUP BY c.consignment_status";

        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardTotalDeliveredConsignment() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . substr($this->filter, 4);
        }
        $sql = "SELECT 
                COUNT(c.id) AS id,
                c.consignment_status
              FROM
                consignment c
              WHERE c.consignment_status = 'delivered' " . $where . "
              GROUP BY c.consignment_status";

        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardTotalConsignmentInfo($debug=false) {
        // has filter been configured?
        $where = "";
        if ($this->consignmentfilter != "") {
            $whereconsignment = "WHERE " . substr($this->consignmentfilter, 4);
        }

        /*$sql = "SELECT 
                    COUNT(c.id) AS id, c.shipment_status
                  FROM
                     
        consignment c
        INNER JOIN
    
    (SELECT 
        id, user_account_id
    FROM
        user u) u ON c.user_id = u.id
        INNER JOIN
    (SELECT 
        id, user_account
    FROM
        customer_account ua) ua ON u.user_account_id = ua.id
        LEFT JOIN
    (SELECT 
        id, code, name
    FROM
        services s) s ON c.service_id = s.id
        LEFT JOIN
    services p ON c.customized_service_id = p.id
        LEFT JOIN
    (SELECT 
        id, iso, name
    FROM
        country con) con ON c.country_id = con.id
                   " . $whereconsignment . " GROUP BY c.shipment_status";*/
//                   SUM(c.`weight`) AS weight
        
        $sql = "SELECT 
                    COUNT(c.id) AS id,
                    c.shipment_status 
                  FROM
                    consignment c 
                    JOIN `user` u 
                      ON c.user_id = u.id 
                  ".$whereconsignment." 
                  GROUP BY c.shipment_status";
        if($debug)
            echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardHighestConsignmentWeight() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = " AND " . substr($this->filter, 4);
        }
        $sql = "SELECT                     
                    MAX(c.`weight`) AS weight  
                  FROM
                    consignment c
                  WHERE 1 = 1 " . $where;

        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getShipmentStatusReport($days=15, $debug=false) {
        $where = "";
        if ($this->consignmentfilter != "") {
            $where = "AND " . substr($this->consignmentfilter, 4);
        }

        $days = $days - 1;
        $sql = "SELECT 
                cal.cal_date AS date_label_created,
                COUNT(c.`id`) AS id,
                IFNULL(SUM(weight),0) AS weight,
                COUNT(DISTINCT c.`service_id`) AS service_id,
                GROUP_CONCAT(DISTINCT (s.`name`) SEPARATOR '|') AS service   
              FROM (
                SELECT ADDDATE(DATE_SUB(CURRENT_DATE(), INTERVAL ".$days." DAY), INTERVAL @i:=@i+1 DAY) AS cal_date
                FROM (
                  SELECT a.a
                  FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                  CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                  CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS cacesa_routine
                ) a
              JOIN (SELECT @i := -1) r1
              WHERE 
                @i < DATEDIFF(CURRENT_DATE(), DATE_SUB(CURRENT_DATE(), INTERVAL ".$days." DAY))
              ) AS cal
              LEFT JOIN `consignment` c 
                ON DATE(FROM_UNIXTIME(c.`date_label_created`)) = cal.cal_date AND DATE(FROM_UNIXTIME(c.`date_label_created`)) >= DATE_SUB(CURRENT_DATE(), INTERVAL ".$days." DAY) ".$where."
              LEFT JOIN services s 
                ON s.`id` = c.`service_id`
              GROUP BY cal.cal_date;";
        if($debug)
            echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }
    public function getServicePerformanceReport($debug = false) {
        $where = "";
        if ($this->consignmentfilter != "") {
            $where =  substr($this->consignmentfilter, 4);
        }
        $days = $days - 1;

        $sql = "SELECT 
                    COUNT(c.`id`) AS id,                    
                    c.`shipment_status`,
                    s.`name`AS service_id
                  FROM
                    `consignment` c 
                    JOIN `services` s 
                      ON s.`id` = (CASE WHEN s.`is_customized` = 1 THEN c.`customized_service_id` ELSE c.`service_id`END) 
                    WHERE  $where
                  GROUP BY c.`service_id`,
                    c.`shipment_status`";
        if($debug) {
            echo $sql;
        }
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardReport_A() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "  1 AND " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

//        $sql = "SELECT 	count(c.consignment_status) as id, consignment_status FROM
//					consignment c
//				WHERE
//					$where
//					GROUP BY consignment_status
//					ORDER BY FIELD(consignment_status, 'valid', 'received', 'data ready', 'dataready supplier', 'booked', 'poland booked', 'hold', 'delivered', 'returned', 'supplier returned', 'closed', 'rtntransfer') ASC;
//				";
        $sql = "SELECT 
                COUNT(consignment_status) AS id,
                (
                  case
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END
                ) AS consignment_status 
              FROM
                consignment c 
              WHERE " . $where . " 
              GROUP BY (
                  CASE
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END
                ) 
              ORDER BY FIELD(
                   CASE
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END,
                  'valid',
                  'received',
                  'intransit',
                  'hold',
                  'delivered',
                  'returned',
                  'closed'
                ) ASC";
        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getDashboardReport_Daily() {


        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "  date_created = '" . date('Y-m-d', time()) . "' AND " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

//        $sql = "SELECT 	count(c.consignment_status) as id, consignment_status FROM
//					consignment c
//				WHERE
//					$where
//					group by consignment_status
//					order by consignment_status ASC
//				";
        $sql = "SELECT 
                COUNT(consignment_status) AS id,
                (
                  case
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END
                ) AS consignment_status 
              FROM
                consignment c 
              WHERE " . $where . " 
              GROUP BY (
                  CASE
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END
                ) 
              ORDER BY FIELD(
                   CASE
                    consignment_status 
                    WHEN 'printed' THEN 'received' 
                    WHEN 'printing' THEN 'received'       
                    WHEN 'return hold' THEN 'hold' 
                    WHEN 'held' THEN 'hold'       
                    WHEN 'awaiting claim' THEN 'intransit' 
                    WHEN 'warehouse received' THEN 'intransit' 
                    WHEN 'booked' THEN 'intransit'
                    WHEN 'Book' THEN 'intransit' 
                    WHEN 'exporting' THEN 'intransit' 
                    WHEN 'exported' THEN 'intransit' 
                    WHEN 'relabel' THEN 'intransit' 
                    WHEN 'parcel departure' THEN 'intransit' 
                    WHEN 'parcel leaving port' THEN 'intransit' 
                    WHEN 'discrepancy' THEN 'intransit'
                    WHEN 'data ready' THEN 'intransit'        
                    WHEN 'poland received' THEN 'intransit' 
                    WHEN 'poland booked' THEN 'intransit' 
                    WHEN 'dataready supplier' THEN 'intransit' 
                    WHEN 'tracking data' THEN 'intransit'            
                    WHEN 'supplier returned' THEN 'returned' 
                    WHEN 'rtntransfer' THEN 'returned' 
                    ELSE consignment_status 
                  END,
                  'valid',
                  'received',
                  'intransit',
                  'hold',
                  'delivered',
                  'returned',
                  'closed'
                ) ASC";
        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

   
    public function updateFunction($setParam, $whereClouse) {

        //echo $whereClouse;
        //print_r($hawbNumber);
        if (trim($whereClouse) != '') {
           $sql = "UPDATE consignment c SET " . $setParam . "  WHERE " . $whereClouse . " ";
            return Consignment::runQuery($sql);
        }
    }

    public function getColumnListLimit($fields, $debug = false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT  " . $fields . ", id
				FROM consignment c
				$where
				$sort
				LIMIT 40000
				";

//	if ($debug)
        //mail("mruga@oneworldexpress.com",'',$sql);
        return Consignment::getConsignmentListFromSql($sql);
    }

    function getConsignmentTotalWeight() {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE consignment_status not in ('recycled', 'invalid') and (awb is not NULL or awb <> '0') and " . $this->filter;
        } else {
            $where = "WHERE consignment_status not in ('recycled', 'invalid' ) and (awb is not NULL or awb <> '0') ";
        }
        $sql = "SELECT SUM(IF(weight > vol_weight, weight, vol_weight)) AS weight FROM consignment c " . $where;
        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getRowCountSumPiecesSumWeight($recycled = "") {
        $where = "";
        $whereconsignment = "";
        $whereuser = "";
        $whereservice = "";
        $wherecountry = "";
        $wheremanifest = "";
        $whereuserAccount = "";
        $parcelJoin = "";
        $consignmentChargesJoin = "";

        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        if ($this->consignmentfilter != "") {
            $whereconsignment = "WHERE " . substr($this->consignmentfilter, 4);
        }
        if ($this->userfilter != "") {
            $whereuser = "WHERE " . substr($this->userfilter, 4);
        }
        if ($this->userAccountFilter != "") {
            $whereuserAccount = "WHERE " . substr($this->userAccountFilter, 4);
        }

        if ($this->countyfilter != "") {
            $wherecountry = "WHERE " . substr($this->countyfilter, 4);
        }
        if ($this->servicefilter != "") {
            $whereservice = "WHERE " . substr($this->servicefilter, 4);
        }
        if ($this->manifestfilter != "") {
            $wheremanifest = "WHERE " . substr($this->manifestfilter, 4);
        }
        if ($this->parcelJoinFilter != "") {
            $parcelJoin = $this->parcelJoinFilter;
        }
        if ($this->consignmentChargesJoinFilter != "") {
            $consignmentChargesJoin = $this->consignmentChargesJoinFilter;
        }

        $sort               = "";
        $sortConsignment    = "";
        $sortUser           = "";
        $sortUserAccount    = "";
        $sortService        = "";
        $sortCountry        = "";
        $sortManifest       = "";
        $groupBy            = "";

        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        if ($this->order_by_consignment != "")
            $sortConsignment = "ORDER BY " . $this->order_by_consignment;
        if ($this->order_by_user != "")
            $sortUser = "ORDER BY " . $this->order_by_user;
        if ($this->order_by_user_account != "")
            $sortUserAccount = "ORDER BY " . $this->order_by_user_account;
        if ($this->order_by_country != "")
            $sortCountry = "ORDER BY " . $this->order_by_country;
        if ($this->order_by_service != "")
            $sortService = "ORDER BY " . $this->order_by_service;
        if ($this->order_by_manifest != "")
            $sortManifest = "ORDER BY " . $this->order_by_manifest;
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
            
        if($sort == "")
            $sort = "ORDER BY c.id";
        $manifestjoin = "";
        if ($this->join != "")
            $manifestjoin = $this->join;
        $sql = "SELECT 
                    SUM(number_pieces) AS number_pieces,
                    SUM(weight) AS weight
                  FROM
                    (SELECT c.number_pieces,c.weight 
                        FROM (select * from consignment c $whereconsignment $sortConsignment) c
                        INNER JOIN  parcel pc  
                            ON pc.consignment_id = c.id  $parcelJoin
                        INNER JOIN (select id, user_account_id from user u $whereuser $sortUser ) u
                            ON c.user_id = u.id 
                        INNER JOIN (select id, user_account from customer_account ua $whereuserAccount $sortUserAccount  ) ua
                            ON u.user_account_id = ua.id
                        LEFT JOIN (select id, code,name from services s $whereservice $sortService) s
                            ON c.service_id = s.id 
                        LEFT JOIN services p
                            ON c.customized_service_id = p.id 
                        LEFT JOIN (select id, iso, name from country  con $wherecountry $sortCountry)con
                            ON c.country_id = con.id 
                        $consignmentChargesJoin    
                    $manifestjoin $wheremanifest "
                    . " ". $where." ". (($groupBy != "") ? $groupBy." " :" ") .$sort.") AS asd";
        return Consignment::getConsignmentListFromSql($sql);
    }

    /*  Scanning Function */

    public function getTodayMawb() {
        $todaysDay = date('Y-m-d 00:00:00', time());
        $sql = "SELECT distinct(mawb) FROM consignment c WHERE date_scanned > '" . $todaysDay . "' AND  mawb != '' AND consignment_status not in ('recycled','valid','invalid')";
        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getTodayMawbWithSession($sessionArray) {
        $todaysDay = date('Y-m-d  00:00:00', time());

        $sql = "SELECT distinct(mawb) FROM consignment c WHERE date_scanned > '" . $todaysDay . "' AND  mawb != '' AND  mawb NOT IN ('" . implode("','", $sessionArray) . "') AND consignment_status not in ('recycled','valid','invalid') LIMIT 1";
        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }

    /*
     * 	scanning function
     */

    public function accountUpdateQuery($fieldName, $fieldValue) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            //$where = "WHERE " . substr($this->filter, 4);
            $where = "WHERE " . $this->filter;
        }
        $sql = "UPDATE consignment c SET " . $fieldName . " = '" . $fieldValue . "' $where ";

        t($sql, __METHOD__);
        return Consignment::runQuery($sql);
    }

    public function getListFieldsFilter($selectedFields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            //$where = "WHERE " . substr($this->filter, 4);
            $where = "WHERE c.consignment_status not in ('recycled', 'invalid') and " . $this->filter;
        } else {
            $where = "WHERE c.consignment_status not in ('recycled', 'invalid')";
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        //if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
        $sql = "SELECT
                        c.id, " . $selectedFields . "
                        FROM 
                       consignment c
                    LEFT JOIN `invoice_detail` invd ON  invd.`consignment_id` = c.id
                        $where 
                        $sort 
                        ";

        return Consignment::getConsignmentListFromSql($sql);
    }

    public function GetAllBagsManifestByFlight($flight, $tagnumber = '') {
        $date = date("Y/m/d");

        //$date = "2015/10/27";

        $sql = "SELECT c.hawb, c.awb, tag_number 'mawb', company,
			    address_line_1, address_line_2, city, b.country, date_booked, c.date_printed, c.service, c.weight,
				c.number_pieces, description, c.value, currency, reference FROM consignment c, bagnumbers b 
				WHERE c.id = b.consignment_id
				AND b.status = 'close' AND b.flight_id = '$flight'";

        if ($tagnumber != '') {
            $sql .= " AND b.tag_number = '$tagnumber' ";
        }


        $sql .= " group by c.hawb";

        ///echo $sql;
        //die;

        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            //$where = "WHERE " . substr($this->filter, 4);
            //$where = "WHERE consignment_status not in ('recycled', 'valid', 'invalid', 'received') and " . $this->filter;
            $where = "WHERE consignment_status not in ('recycled', 'invalid') and (awb is not NULL or awb <> '0') and " . $this->filter;
        } else {
            //$where = "WHERE consignment_status not in ('recycled', 'valid', 'invalid', 'received')";
            $where = "WHERE consignment_status not in ('recycled', 'invalid' ) and (awb is not NULL or awb <> '0') ";
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(id) as id FROM consignment c $where $sort ";

        //t($sql, __METHOD__);

        return Consignment::getConsignmentListFromSql($sql);

        //return Consignment::getTotalNumberOfConsignmentsFromSql($sql);
    }

    public function getZeroPricedCount($account_id , $debug=false) {
        // has filter been configured?
        if(is_array($account_id)){
            $userAccountPrice = " AND cc.account_id in ('".implode(',', $account_id)."') AND cc.account_id > 0 " ;
        }else{
            $userAccountPrice = " AND cc.account_id in (select id from user_account where parentid = $account_id) AND cc.account_id > 0 " ;
        }
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE c.consignment_status not in ('recycled', 'invalid', 'valid' )  and (cc.`cost` <= 0 or cc.`cost` is null) and (cc.`charge_type_id` = 1)   ".$userAccountPrice." AND" . $this->filter;
        } else {
            //$where = "WHERE consignment_status not in ('recycled', 'valid', 'invalid', 'received')";
            $where = "WHERE c.consignment_status not in ('recycled', 'invalid', 'valid' )   and (cc.`cost` <= 0 or cc.`cost` is null) and (cc.`charge_type_id` = 1)  ".$userAccountPrice." ";
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        
        $sql = "SELECT 
                   count(c.id ) AS id 
                  FROM
                    consignment c
                    LEFT JOIN (select * from `consignment_charges` WHERE charge_type_id = 1 and cost_type = 'customer' )  cc ON  cc.`consignment_id` = c.id
                  " . $where . " " . $sort . "";
        if($debug){
        echo $sql; die;}
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getPagingList($recycled = "") {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            if (trim($recycled) != "" && $recycled == "recycled") {
                $where = "WHERE c.consignment_status = 'recycled'  and " . $this->filter;
            } else {
                $where = "WHERE c.consignment_status not in ('recycled', 'invalid', 'valid')  and " . $this->filter;
            }
        } else {

            if (trim($recycled) != "" && $recycled == "recycled") {
                $where = "WHERE c.consignment_status = 'recycled'  ";
            } else {
                $where = "WHERE c.consignment_status not in ('recycled', 'invalid', 'valid' ) ";
            }
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT 
                    c.id,
                    c.consignment_status,
                    c.account,
                    c.awb,
                    c.hawb,
                    c.reference,
                    c.service,
                    c.handling,
                    c.date_created,
                    c.number_pieces,
                    c.weight,
                    c.date_booked,
                    c.date_printed,
                    c.company,
                    c.contact,
                    c.address_line_1,
                    c.address_line_2,
                    c.address_line_3,
                    c.postcode,
                    c.city,
                    c.country,
                    c.single_label,
                    c.service_type,
                    c.mawb,
                    c.bag_number,
                    c.InvoiceId,
                    c.credit_id,
                    c.remote_charges,
                    c.sender_checked,
                    c.billing_hold,
                    c.reinvoices,
                    c.type,
                    c.sender_city,
                    c.sender_country,
                    c.vol_weight,
                    c.hv_lv,
                    invd.`amount` AS or_link ,
                    invd.`basic_charges` AS basic_charges,
                    invd.`fuel_charges` AS fuel_charges,
                    invd.`additional_charges` AS additional_charges,
                    invd.`remote_area_charge` AS remote_area_charge,
                    invd.`extra` AS extra,
                    invd.`ancillary_charges` AS ancillary_charges,
                    c.invoice_type
                  FROM
                    consignment c
                    LEFT JOIN `invoice_detail` invd ON  invd.`consignment_id` = c.id
                 " . $where . " GROUP BY c.id " . $sort . " 
                  LIMIT " . $this->pageOffset . ", " . $this->rowsPerPage;
//        echo $sql;
//        die;
        //	else
        //	$sql = "SELECT * FROM consignment c $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        /* if( $_SERVER['REMOTE_ADDR'] == '188.66.86.88')
          echo $sql; */
        // echo $sql;
        //t($sql, __METHOD__);
//mail("mruga@oneworldexpress.com","",$sql);
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getShipmentPagingCountNew($debug=false) {
        $sql = "SELECT FOUND_ROWS() as total";
        return Consignment::getTotalNumberOfConsignmentsFromSql($sql);
//        // has filter been configured?
//
//        // has filter been configured?
//        $where = "";
//        $whereconsignment = "";
//        $whereuser = "";
//        $whereservice = "";
//        $wherecountry = "";
//        $wheremanifest = "";
//        $whereuserAccount = "";
//
//        if ($this->filter != "") {
//            $where = "WHERE " . substr($this->filter, 4);
//        }
//        if ($this->userAccountFilter != "") {
//            $whereuserAccount = "WHERE " . substr($this->userAccountFilter, 4);
//        }
//        if ($this->consignmentfilter != "") {
//            $whereconsignment = "WHERE  " . substr($this->consignmentfilter, 4);
//        }
//        if ($this->userfilter != "") {
//            $whereuser = "WHERE " . substr($this->userfilter, 4);
//        }
//
//        if ($this->countyfilter != "") {
//            $wherecountry = "WHERE " . substr($this->countyfilter, 4);
//        }
//
//        if ($this->servicefilter != "") {
//            $whereservice = "WHERE " . substr($this->servicefilter, 4);
//        }
//
//        if ($this->manifestfilter != "") {
//            $wheremanifest = "WHERE " . substr($this->manifestfilter, 4);
//        }
//
//
//        $sort = "";
//        $sortConsignment = "";
//        $sortUserAccount = "";
//        $sortUser = "";
//        $sortService = "";
//        $sortCountry = "";
//        $sortManifest = "";
//
//        if ($this->order_by != "")
//            $sort = "ORDER BY " . $this->order_by;
//        if ($this->order_by_user_account != "")
//            $sortUserAccount = "ORDER BY " . $this->order_by_user_account;
//        if ($this->order_by_consignment != "")
//            $sortConsignment = "ORDER BY " . $this->order_by_consignment;
//        if ($this->order_by_user != "")
//            $sortUser = "ORDER BY " . $this->order_by_user;
//        if ($this->order_by_country != "")
//            $sortCountry = "ORDER BY " . $this->order_by_country;
//        if ($this->order_by_service != "")
//            $sortService = "ORDER BY " . $this->order_by_service;
//        if ($this->order_by_manifest != "")
//            $sortManifest = "ORDER BY " . $this->order_by_manifest;
//
//
//        $manifestjoin = "";
//        if ($this->join != "")
//            $manifestjoin = $this->join;
//
//        /*  $sql = "SELECT  count(c.id) as total
//               FROM (select * from consignment c $whereconsignment $sortConsignment ) c
//               INNER JOIN (select id from user u $whereuser $sortUser ) u
//                   ON c.user_id = u.id
//          "
//              . " ". $where." ".$sort;
//          */
//
//
//        $sql = "SELECT count(c.id) as total FROM (select * from consignment c $whereconsignment $sortConsignment ) c 
//                  INNER JOIN parcel pc  ON pc.consignment_id = c.id 
//                INNER JOIN (select id, user_account_id from user u $whereuser $sortUser ) u
//                     ON c.user_id = u.id 
//                INNER JOIN (select id, user_account from customer_account ua $whereuserAccount $sortUserAccount  ) ua
//                    ON u.user_account_id = ua.id
//                LEFT JOIN (select id, code,name from services s $whereservice $sortService) s
//                     ON c.service_id = s.id 
//                LEFT JOIN services p
//                     ON c.customized_service_id = p.id
//                 LEFT JOIN (select id, iso, name from country  con $wherecountry $sortCountry)con
//                     ON c.country_id = con.id 
//
//            $manifestjoin $wheremanifest "
//            . " ". $where." ".$sort;
//
//        if($debug) {
//            echo $sql;
//            die;
//        }
//        return Consignment::getTotalNumberOfConsignmentsFromSql($sql);


    }

    public function getShipmentPagingListOpt($debug = false, $parcel = false, $addLimit = true,$countData = false) {
       //
       ////  $this->user = SessionManager::getUser();
// has filter been configured?
        $where = "";
        $whereconsignment = "";
        $whereuser = "";
        $whereservice = "";
        $wherecountry = "";
        $wheremanifest = "";
        $whereuserAccount = "";
        $parcelJoin = "";
        $consignmentChargesJoin = "";
        $consignmentHoldJoin = "";
        $invoiceJoin = "";


        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        if ($this->consignmentfilter != "") {
            $whereconsignment = " AND  " . substr($this->consignmentfilter, 4);
        }
        if ($this->userfilter != "") {
            $whereuser = " AND  " . substr($this->userfilter, 4);
        }

        if ($this->userAccountFilter != "") {
            $whereuserAccount = " AND  " . substr($this->userAccountFilter, 4);
        }

        if ($this->countyfilter != "") {
            $wherecountry = " AND  " . substr($this->countyfilter, 4);
        }

        if ($this->servicefilter != "") {
            $whereservice = " AND " . substr($this->servicefilter, 4);
        }

        if ($this->manifestfilter != "") {
            $wheremanifest = " AND  " . substr($this->manifestfilter, 4);
        }
        
        if ($this->parcelJoinFilter != "") {
            $parcelJoin = $this->parcelJoinFilter;
        }
        
        if ($this->consignmentChargesJoinFilter != "") {
           $consignmentChargesJoin = $this->consignmentChargesJoinFilter. " AND cc.cost_type = 'customer'";
        }
        
        if ($this->consignmentHoldJoinFilter != "") {
            $consignmentHoldJoin = $this->consignmentHoldJoinFilter;
        }
        
        if ($this->invoiceJoinFilter != "") {
            $invoiceJoin = $this->invoiceJoinFilter;
        }

        $sort               = "";
        $sortConsignment    = "";
        $sortUser           = "";
        $sortUserAccount    = "";
        $sortService        = "";
        $sortCountry        = "";
        $sortManifest       = "";
        $groupBy       = "";

        
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        if ($this->order_by_consignment != "")
            $sortConsignment = "ORDER BY " . $this->order_by_consignment;
        if ($this->order_by_user != "")
            $sortUser = "ORDER BY " . $this->order_by_user;
        if ($this->order_by_user_account != "")
            $sortUserAccount = "ORDER BY " . $this->order_by_user_account;
        if ($this->order_by_country != "")
            $sortCountry = "ORDER BY " . $this->order_by_country;
        if ($this->order_by_service != "")
            $sortService = "ORDER BY " . $this->order_by_service;
        if ($this->order_by_manifest != "")
            $sortManifest = "ORDER BY " . $this->order_by_manifest;
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
            
        if($sort == "")
            $sort = "ORDER BY c.id";
        $manifestjoin = "";
        if ($this->join != "") {
            $manifestjoin = $this->join;
        }
        $invoiceIdCol = '0 AS charges_invoice_id';
        if($consignmentChargesJoin != '') {
            $invoiceIdCol = " cc.invoice_id  AS charges_invoice_id";
            //$invoiceIdCol = 'cc.invoice_id AS charges_invoice_id';
        }
        $consignmentHoldIdCol = '';
        if($consignmentHoldJoin != '') {
            $consignmentHoldIdCol = ',cbh.id as consignment_billing_hold_id';
        }
        $select = "";
        if($parcel) {
            $select = "c.id, c.awb, c.sorter_image, c.shipment_status, c.service_id as service_display_id, pc.id as parcel_id,pc.tracking_number,"
                    . "pc.length,pc.width,pc.height,pc.weight,c.service_id,s.name as service_name,is_white_label, s.is_reschedulable, " 
                    . "c.is_dead_weight_chargable, c.is_doc,c.ioss_number, c.eori_number, c.vat_number, con.iso as country_iso_code, con.vat_rate as vat_rate, con.region as country_region, c.date_scanned, c.value,"
                    . " c.is_customer_billable, " 
                    . " c.customized_service_id, c.sender_name, c.sender_address_line_1,c.sender_address_line_2,c.sender_city,
                        c.charge_weight,  c.service_id as service_using, c.consignment_type, c.description , c.created_from, (SELECT 
                bagging.bagnumber
            FROM
                `parcel_bagging_mapping` INNER JOIN bagging ON parcel_bagging_mapping.bag_id = bagging.id WHERE 
              parcel_bagging_mapping.parcel_id = pc.id ORDER BY parcel_bagging_mapping.id DESC LIMIT 1) as bag_id  ";
        } else {
            $select = " c.id, c.consignment_status, c.service_id as service_display_id, c.currency, c.shipment_status,  con.name as country_name, "
                    . " pc.length,pc.width,pc.height, c.weight, c.is_doc, c.user_id, c.awb, c.hawb, c.reference,c.is_dead_weight_chargable, "
                    . " IF(c.customized_service_id =  0 OR c.customized_service_id IS NULL , c.service_id, c.customized_service_id  )  service_id,"
                    . " c.customized_service_id,  c.charge_weight, c.date_created, c.date_label_created, c.company, c.contact, c.address_line_1, "
                    . " c.is_customer_billable, c.sender_name, c.sender_address_line_1,c.sender_address_line_2,c.sender_city," 
                    . "c.address_line_2, c.address_line_3, c.postcode, c.city, c.country_id, c.label_file, c.message, c.sender_checked,  "
                    . "c.shipment_type, c.return_awb, s.name 'service_name', s.code 'service_code', p.name 'product_name', s.is_reschedulable, "
                    . "p.code 'product_code', c.date_booked, c.date_delivered, c.number_pieces, c.weight, c.vol_weight, c.hv_lv, c.created_from, c.ioss_number, c.eori_number, c.vat_number, con.vat_rate as vat_rate, con.region as country_region ,"
                    . "  GROUP_CONCAT(DISTINCT mawb.mawb_number ORDER BY mawb.id ) as mawb,".$invoiceIdCol.", c.remote_charges, c.consignment_type, c.shipment_type, c.country_id, c.is_customer_manifested, c.billing_hold".$consignmentHoldIdCol.",ua.user_account as user_account,ua.id as user_account_id,is_white_label, con.iso as country_iso_code,c.date_scanned, c.value, c.service_id as service_using, c.consignment_type, c.description , (SELECT 
                bagging.bagnumber
            FROM
                `parcel_bagging_mapping` INNER JOIN bagging ON parcel_bagging_mapping.bag_id = bagging.id WHERE 
              parcel_bagging_mapping.parcel_id = pc.id ORDER BY parcel_bagging_mapping.id DESC LIMIT 1) as bag_id ";
        }
        
        if($countData){
            $select = " c.id "; 
        }
        $limit = "";
        if($addLimit) {
            $limit = "LIMIT $this->pageOffset , $this->rowsPerPage";
        }
//SQL_CALC_FOUND_ROWS
        $sql = "SELECT  $select
                FROM  consignment c
                INNER JOIN  parcel pc  
                    ON pc.consignment_id = c.id  $whereconsignment  $parcelJoin
                INNER JOIN   user u
                    ON c.user_id = u.id $whereuser  
                INNER JOIN customer_account ua 
                    ON u.user_account_id = ua.id $whereuserAccount
                LEFT JOIN services s 
                    ON c.service_id = s.id $whereservice 
                LEFT JOIN services p
                    ON c.customized_service_id = p.id 
                LEFT JOIN country  con 
                    ON c.country_id = con.id $wherecountry  
                $consignmentChargesJoin
                $consignmentHoldJoin
                $invoiceJoin
            $manifestjoin $wheremanifest "
                . " LEFT JOIN mawb_parcel_mapping mawbpm
                ON mawbpm.`parcel_id` = pc.`id`
              LEFT JOIN mawb
                ON mawb.`id` = mawbpm.`mawb_id`"
                . ""
            . " ". $where." ". (($groupBy != "") ? $groupBy." " :" ") . "".$sort." ".$limit;
   
        if($debug){
            echo $sql;
            die;            
        }
   
        
        if($countData){
            $sql = "SELECT count(*) as total FROM (".$sql.") AS consignment_count ";
            return Consignment::getTotalNumberOfConsignmentsFromSql($sql);
        }
        return Consignment::getConsignmentListFromSql($sql);
    }
  
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function addawbFilterList($awbList) {
        $this->filter .= " AND ";
        $this->filter .= "c.awb IN(" . $awbList . ")";

        //mail("mkazim4u@gmail.com","AWB Filter List",$this->filter);
    }

    /**
     * Get count of consignment items based on filter conditions
     *
     * @return int
     */
    public function getCount($debug=false) {
        $result = $this->getList($debug);
        return sizeof($result);
    }

    public function addDateFilter($date_value1, $date_value2, $filterDate,$filter='consignmentfilter') {
        if ($filterDate == "submitted") {
            $this->filter .= " AND ";
            $this->filter .= "(c.date_created>='" . date('Y-m-d 00:00:00',strtotime($date_value1)) . "'";

            $this->filter .= " AND ";
            $this->filter .= "c.date_created<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
        } elseif ($filterDate == "shipped") {
            $this->filter .= " AND ";
            //$this->filter .= "c.date_booked>='" . Date("Y-m-d", $date_value1) . "'";
            $this->filter .= "c.date_booked>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "c.date_booked<='" . strtotime($date_value2) . "'";
        } elseif ($filterDate == "delivered") {
            $this->filter .= " AND ";
            $this->filter .= "(c.date_delivered>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "c.date_delivered<='" . strtotime($date_value2) . "')";
        } elseif ($filterDate == "printed") {
            $this->filter .= " AND ";
            $this->filter .= "(c.date_label_created>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "c.date_label_created<='" . strtotime($date_value2) . "')";
        } else {
            $this->filter .= " AND ";
            $this->filter .= "(c.date_printed>='" . strtotime($date_value1) . "'";

            $this->filter .= " AND ";
            $this->filter .= "c.date_printed<='" . strtotime($date_value2) . "')";
        }
    }

    public function addServiceCodeArrayFilter($carrierArrayString) {
        $this->filter .= " AND c.service_id IN (" . $carrierArrayString . ")";
    }

    public function addAccountFilter($Accoutn,$filter='consignmentfilter') {
        $this->filter .= " AND c.user_id IN (" . $Accoutn . ") ";
    }

    public function AddAccountFilterArray($Accoutn,$filter= 'consignmentfilter') {
        $this->filter .= " AND c.user_id IN (" . $Accoutn . ") ";
    }

    public function addCountryFilter($country) {
        $this->filter .= " AND c.country_id = '" . DbAccess3::escape($country) . "'";
    }

    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $fieldName . " = '" . DbAccess3::escape($fieldValue) . "'";
    }
    
    public function addFieldNotNullFilter($fieldName) {
        if ($this->filter != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= "  " . $fieldName . " IS NOT NULL";
    }
    
    public function addFieldNullFilter($colm) {
        if ($this->filter != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= " " . $colm . " IS NULL";
    }

    public function addFieldNotFilter($fieldName, $fieldValue,$filter = 'consignmentfilter') {
        if (trim($this->filter) != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= "  c." . $fieldName . " != '" . DbAccess3::escape($fieldValue) . "'";
    }

    public function addFilter($fieldName, $filter = 'consignmentfilter') {
        if (trim($this->$filter) != "") {
            $this->$filter .= " AND ";
        }
        $this->$filter .= $fieldName . "";
    }
    public function addFilterNew($fieldName) {
        if (trim($this->filter) != "") {
            $this->filter .= " AND ";
        }
        $this->filter .= $fieldName . "";
    }
    public function addOrFilter($fieldName, $filter = 'consignmentfilter') {
        if (trim($this->$filter) != "") {
            $this->$filter .= " OR ";
        }
        $this->$filter .= $fieldName . "";
    }
 
    public function addConsignmentFilter($fieldName, $filter = 'consignmentfilter') {
        if (trim($this->consignmentfilter) != "") {
            $this->consignmentfilter .= " AND ";
        }
        $this->consignmentfilter .=  $fieldName . "";
    }
   

    /**
     * Limit list to consignments with given states.
     *
     */

    public function addStatusFilter($mixed_status, $filter = 'consignmentfilter') {
        //print_r($mixed_status); exit;
        // list of states?
        if (is_array($mixed_status)) {
            $where = "";
            foreach ($mixed_status as $status) {
                if ($where != "")
                    $where .= " OR ";
                $where .= "c.shipment_status='" . DbAccess3::escape($status) . "'";
            }
            if ($where != "") {

                $this->filter .= " AND ($where)";
            }
        }

        // specific status
        else {

            $this->filter .= "AND c.shipment_status='" . DbAccess3::escape($mixed_status) . "'";
        }
    }

    public function addStatusFilterNotIn($mixed_status,  $filter = 'consignmentfilter') {
        //print_r($mixed_status); exit;
        // list of states?
        if (is_array($mixed_status)) {
            $where = "";
            $in = array();
            foreach ($mixed_status as $status) {
                $in[] = "'" . $status . "'";
//                if ($where != "")
//                    $where .= " OR ";
//                $where .= "c.consignment_status not in ('" . $status . "')";
            }
            $where .= "      c.shipment_status NOT IN (" . implode(",", $in) . ")";
            if ($where != "") {
                $this->$filter .= " AND " . $where;
            }
        }

        // specific status
        else {
            $this->$filter .= "AND c.shipment_status NOT IN ('" . $mixed_status . "')";
        }

        //echo $this->filter;
    }

    public function addStatusFilterIn($mixed_status,$filter='consignmentfilter') {
        //print_r($mixed_status); exit;
        // list of states?
        if (is_array($mixed_status)) {
            $where = "";
            $in = array();
            foreach ($mixed_status as $status) {
                $in[] = "'" . $status . "'";
//                if ($where != "")
//                    $where .= " OR ";
//                $where .= "c.consignment_status not in ('" . $status . "')";
            }
            $where .= "c.shipment_status IN (" . implode(",", $in) . ")";
            if ($where != "") {
                $this->$filter .= " AND " . $where;
            }
        }

        // specific status
        else {
            $this->$filter .= "AND c.shipment_status IN ('" . $mixed_status . "')";
        }

        //echo $this->filter;
    }

    public function addWeightRangeFilter($from_weight, $to_weight) {
        $this->filter .= " AND c.weight>='" . DbAccess3::escape($from_weight) . "' AND c.weight<='" . DbAccess3::escape($to_weight) . "'";
    }

    /**
     * Limit list to consigments with given Postcode value
     * @param $postcode_value
     */
    public function addIdFilter($id_value) {
        if (trim($id_value) != "") {
            $this->filter .= " AND ";
            $this->filter .= "c.id ='" . DbAccess3::escape($id_value) . "'";
        }
    }

    public function addAwbAndHawbOrFilter($awbORhawb) {
        $this->filter .= " AND ";
        $this->filter .= " ( c.awb = '" . DbAccess3::escape($awbORhawb) . "' OR c.hawb = '" . DbAccess3::escape($awbORhawb) . "' )";
    }

    public function addAwbAndHawbOrFilterNotRecycled($awbORhawb) {
        $this->filter .= " AND ";
        $this->filter .= " ( c.awb = '" . DbAccess3::escape($awbORhawb) . "' OR c.hawb = '" . DbAccess3::escape($awbORhawb) . "' ) AND shipment_status != " . Consignment::STATUS_RECYCLED;
    }

    public function addBagNumberFilter($bag_number) {
        $this->filter .= " AND ";
        $this->filter .= " c.bag_number = '" . DbAccess3::escape($bag_number) . "'  ";
    }

    /**
     * Limit list to consigments with given city value
     * @param $postcode_value
     */
    public function addCityFilter($city_value) {
        $this->filter .= " AND ";
        $this->filter .= "c.city Like '%" . DbAccess3::escape($city_value) . "%'";
    }


    public function addManifestTableJoin() {
        $this->join = " LEFT JOIN manifest_entity_mapping mcm ON pc.id = mcm.entity_id";
    }
    
    public function addJoin($table,$where, $type = "INNER") {
        $this->join .= $type . " JOIN ".$table."  ON " . $where . " ";
    }

    /**
     * Limit list to consigments with given HAWB value
     * @param $hawb_value
     */
    public function addHawbFilter($hawb_value) {
        $this->filter .= " AND ";
        $this->filter .= "c.hawb = '" . DbAccess3::escape($hawb_value) . "'";
    }

    public function addStatusEbayArr($status,$filter = 'consignmentfilter') {
        $status = implode("','", $status);

        $this->filter  .= " AND ";
        $this->filter  .= "c.consignment_status IN ('" . $status . "')";
    }

    /**
     * Limit list to consigments with given HAWB value
     * @param $hawb_value
     */
    public function addHawbFilter_bag($hawb_value,$filter = 'consignmentfilter') {
        //if($this->filter!=null)
        //{
        $this->filter .= " AND ";
        //}
        $this->filter .= "c.hawb = '" . DbAccess3::escape($hawb_value) . "'";
    }

    /**
     * Limit list to consigments with given HAWB value
     * @param $hawb_value
     */
    public function addIdArrayFilter($idArray,$filter = 'consignmentfilter') {
        if (is_array($idArray)) {
            $generatelabelstr = implode("','", $idArray);
            if(!empty($this->filter))
                $this->filter .= " AND ";
            $this->filter .= "      c.id IN ('" . $generatelabelstr . "')";
        } else {
            $generatelabelstr = implode("','", $generateLabel);
            if (trim($generatelabelstr)) {
                if(!empty($this->filter))
                    $this->filter .= " AND ";
                $this->filter .= "c.id = " . DbAccess3::escape($generatelabelstr);
            }
        }
    }

    public function addHawbExactFilter($hawb_value,$filter = 'consignmentfilter') {
        $this->filter .= " AND ";
        $this->filter .= "c.hawb = '" . DbAccess3::escape($hawb_value) . "'";
    }

    /**
     * Limit list to consigments with given HAWB value
     * @param $hawb_value
     */
    public function addAwbArrayFilter($awb_value,$filter = 'consignmentfilter') {
        $this->filter .= " AND ";
        $this->filter .= "c.awb IN ('" . $awb_value . "')";
    }

    /**
     * Limit list to consigments with given HAWB value
     * @param $hawb_value
     */
    public function addDeletedHawbFilter($hawb_value,$filter = 'consignmentfilter') {
        $this->filter .= " AND ";
        //$this->filter .= "c.hawb Like '" . $hawb_value . "%' and consignment_status <> 'recycled'"; (commented by Kiran because of repeated hawb)
        $this->filter .= "c.hawb ='" . DbAccess3::escape($hawb_value) . "' and c.shipment_status <> '".Consignment::STATUS_INVALID."'";
    }

    public function addawbFilter($awb_value) {
        $this->filter .= " AND ";
        $this->filter .= "c.awb Like '" . DbAccess3::escape($awb_value) . "%'";
    }

    public function addDateBookedRangeFilter($date_value1, $date_value_2) {
        $this->filter .= " AND ";
        $this->filter .= "c.date_booked>='" . Date("Y-m-d", $date_value1) . "'";

        $this->filter .= " AND ";
        $this->filter .= "c.date_booked<='" . Date("Y-m-d", $date_value_2) . "'";
    }

    public function addDateScannedRangeFilter($date_value1, $date_value_2) {
        $this->filter .= " AND ";
        $this->filter .= "(c.date_scanned>='" . Date("Y-m-d 00:00:00", $date_value1) . "'";

        $this->filter .= " AND ";
        $this->filter .= "c.date_scanned<='" . Date("Y-m-d 23:59:59", $date_value_2) . "')";
    }

    public function addDateBookedNotSet() {
        $this->filter .= " AND ";
        $this->filter .= " (c.date_booked is null or c.date_booked = '' or c.booked_file_id = 0 or c.booked_file_id = '') ";

        //echo $this->filter;
    }

    public function addFieldEqualFilter($columnName, $operator, $columnValue,$filter = 'consignmentfilter') {
        if ($this->filter != "")
                $this->filter .= " AND ";
        $this->filter .= "c." . $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }

    public function AddPalletNumberFilter($palletno) {
        $this->filter .= " AND ";
        $this->filter .= " c.pallet_no = '" . DbAccess3::escape($palletno) . "' ";
    }

    public function AddPalletNumberArrayFilter($palletnoArr) {
        $palletno = implode("','", $palletnoArr);
        $this->filter .= " AND ";
        $this->filter .= " c.pallet_no IN ('" . $palletno . "')";
    }

    /*     * *
     * Limit number of rows returned
     */

    public function setLimit($lim) {
        $this->limit = $lim;
    }

    public function addGroupByClause($field) {
        $this->filter .= " group by $field";
    }


    public function addGroupBy($field) {
        if(trim($this->groupBy) != '')
            $this->groupBy .= ", $field";
        else 
            $this->groupBy .= " GROUP BY $field";
    }


    public function addWarehouseIdFilter($warehouse_id) {
        $this->filter .= " AND c.warehouse_id = '" . DbAccess3::escape($warehouse_id) . "'";
    }

    public function GetBagList($mawb) {

        $sql = "select bag_number, date_scanned from consignment where mawb = '" . DbAccess3::escape($mawb) . "' order by date_scanned desc ";

        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function IsBagScanned($bagNumber) {
        $sql = "select bag_number from consignment where 
				bag_number = '" . DbAccess3::escape($bagNumber) . "' and (date_scanned = '0000-00-00 00:00')";

        return Consignment::getConsignmentListFromSql($sql);
    }

   
    public static function getTodaysMawb() {

        //$date = date("Y-m-d");
        $date = date("Y-m-d H:i:s", strtotime("-1 month"));
        $sql = "select count(c.id) as id, c.mawb from pre_alert p, consignment c 
					where p.mawb = c.mawb  COLLATE utf8_unicode_ci
					and date_format(p.eta,'%Y-%m-%d') > '" . $date . "' 
					and p.current_status = 'SCANNING IN PROGRESS'
					and (c.date_scanned = '' or c.date_scanned = '0000-00-00 00:00:00') group by c.mawb order by p.eta desc";
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getServicesSummary($mawb) {
        $sql = "SELECT service_type, COUNT(number_pieces) as number_pieces, Sum(weight) as weight FROM consignment where mawb IN ('" . $mawb .
            "') GROUP BY service_type";
        t($sql, __METHOD__);
        //	echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }
   public function getScannedBagCount($mawb) {
        $mawb = str_replace('-', '', $mawb);
        $sql = "SELECT count(distinct bag_number) 'bag_number' FROM consignment c WHERE replace(mawb, '-', '') = '" . DbAccess3::escape($mawb) . "' AND (date_scanned <> '0000-00-00 00:00:00' AND date_scanned <> '' AND date_scanned is not null)";
        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getRemainingBagCount($mawb) {
        $mawb = str_replace('-', '', $mawb);
        $sql = "SELECT count(distinct bag_number) 'bag_number' FROM consignment c WHERE replace(mawb, '-', '') = '" . DbAccess3::escape($mawb) . "' AND (date_scanned = '0000-00-00 00:00:00' OR date_scanned = '' or date_scanned is null)";
        t($sql, __METHOD__);
        return Consignment::getConsignmentListFromSql($sql);
    }


    public function addIsCustomerManifested($filter = 'consignmentfilter') {
        $this->filter .= " AND is_customer_manifested  = 0 ";
    }

    public function GetDistinctMawbScanneddate($useraccount) {
        $date = date("Y-m-d");
        $sql = "select distinct mawb from consignment where date_format(date_scanned, '%Y-%m-%d') = '" . $date . "' and account in ($useraccount)";
        //echo $sql;
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function getUntrackedMawbByCountry($mawb, $servicearray) {
        $service_array = "'" . implode("','", $servicearray) . "'";
        $sql = "SELECT sum(c.weight) as weight,sum(c.number_pieces) as number_pieces, c.country,  c.service_type
				FROM consignment c, services s
				where c.handling = s.code and s.is_untrack = 'YES' and s.code in (" . $service_array . ") and  c.mawb = '" . $mawb . "' group by c.service_type, c.country order by c.id desc";
        return Consignment::getConsignmentListFromSql($sql);
    }

    public function updateShipmentWithoutAgent($handling, $agentid) {
        $sql = "update 
				consignment
				set agentid = '" . $agentid . "'
			WHERE
				isInvoiced <> 'Y'
					AND date_created > '2016-04-30'
					AND consignment_status NOT IN ('invalid' , 'recycled')
					AND handling <> ''
					AND agentid = ''
					AND handling NOT LIKE 'RTN%'
					AND handling = '" . $handling . "'";

        return Consignment::runQuery($sql);
    }

    public function getShipmentWithoutAgent($handling) {
        $sql = "select count(id) as id from consignment
			WHERE
				isInvoiced <> 'Y'
					AND date_created > '2016-04-30'
					AND consignment_status NOT IN ('invalid' , 'recycled')
					AND handling <> ''
					AND agentid = ''
					AND handling NOT LIKE 'RTN%'
					AND handling = '" . $handling . "'";

        return Consignment::getConsignmentListFromSql($sql);
    }

    public function addFieldLikeFilter($colm, $value, $filteTable='consignmentfilter') {
        $this->$filteTable .= " AND ";
        $this->$filteTable .= " " . $colm . " LIKE '%" . DbAccess3::escape($value) . "%'";
    }


    public function AddOrderBy($fieldName, $orderTable, $ascending = true) {
        //if ($this->order_by != "") $this->order_by .= ", ";
        //
        $this->order_by = " ".$fieldName. ($ascending ? "" : " DESC");
    }

    
    public function addFilterIn($field, $values, $filter='consignmentfilter') {
        $finalArr = [];
        if (trim($this->$filter) != "") {
                $this->$filter .= " AND ";
        }
        if (is_array($values)) {
            foreach ($values as $trackinNumber) {
                $finalArr[] = ParseTrackingNumber::Parse($trackinNumber);
            }
            $this->$filter .= $field ." IN ('" . implode("','", $finalArr) . "')";
        } else {
            $this->$filter .= $field . " IN (" . $values . ")";
        }
    }
    
    public function addFilterLikeIn($field, $values, $filter='consignmentfilter') {
        if (trim($this->$filter) != "") {
                $this->$filter .= " AND ";
        }
        if (is_array($values)) { 
            $this->$filter .= $field ." REGEXP  (" . implode(",", $values) . ")";
        } else {
            $this->$filter .= $field . " REGEXP  (" . $values . ")";
        }
    }
    
    public function addFilterNotIn($field, $values, $filter='consignmentfilter') {
        if (trim($this->$filter) != "") {
                $this->$filter .= " AND ";
        }
        if (is_array($values)) { 
            $this->$filter .= $field ." NOT IN (" . implode(",", $values) . ")";
        } else {
            $this->$filter .= $field . " NOT IN (" . $values . ")";
        }
    }
    public function getListNew($col = "*",$addLimit=false, $debug=false) {
        // has filter been configured
        //echo $this->filter;
        $where = "";
        
        if ($this->filter != "")
            $where = "WHERE " . ltrim($this->filter, ' AND');
       
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
        
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $checkJoin = "";
        if ($this->join != "") {
            $checkJoin = $this->join;
        }
        
        $limit = "";
        if($addLimit) {
            $limit = "LIMIT $this->pageOffset , $this->rowsPerPage";
        }
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS $col 
                FROM consignment c
                $checkJoin
                $where $groupBy $sort $limit";
        if($debug){
            echo $sql."<br />"; die;
        }
        return Consignment::getConsignmentListFromSql($sql);
    }
 
    public function addConsignmentChargesJoin($accountId,$userType ) {
        $where = "";
        if($userType == User::USER_TYPE_ADMIN) {
            $where = " OR `id`=" . $accountId;
        }
            $this->consignmentChargesJoinFilter .= " LEFT JOIN `consignment_charges` cc  ON cc.consignment_id = c.id AND cc.account_id IN (SELECT id FROM `user_account` WHERE `parentid` = '".$accountId."' " . $where . ")";
 
        }
    
    public function addInvoiceJoin() {
        $this->invoiceJoinFilter .= " LEFT JOIN `invoices` inv  ON inv.id = cc.invoice_id AND cc.cost_type = 'customer' ";
    }
    
    public function addBaggingMappingJoin() {
        $this->baggingMappingFilter .= " LEFT JOIN `parcel_bagging_mapping` pbm  ON  pbm.parcel_id = pc.id ";
    }
    
    public function addVechileJoin() {
        $this->vechileParcelJoinFilter .= "  JOIN `vehicle` veh  ON veh.id = vpm.vehicle_id";
    }
    
    public function addDriverJoin() {
        $this->vechileParcelJoinFilter .= "  JOIN `vehicle_driver` vd  ON vd.id = vpm.driver_id";
    }
    
    public function addConsignmentBillingHoldJoin($accountId,$userType) {
        $where = "";
        if($userType == User::USER_TYPE_ADMIN) {
            $where = " OR `id`=" . $accountId;
        }
        $this->consignmentHoldJoinFilter .= " LEFT JOIN `consignment_billing_hold` cbh  ON cbh.consignment_id = c.id AND cbh.user_account_id_from='" . $accountId . "' AND cbh.user_account_id_to IN (SELECT id FROM `user_account` WHERE `parentid` = '".$accountId."' " . $where . ")";
    }
    public function getLabelCreatedReport($startDate, $endDate,$allouedAcccounts,$carrierId = '',$serviceId = array(),$userAccountId = '',$dateType='',$fieldName = '') {
        $where = '';
        //".$startDate."
        //".$endDate."
        if(count($allouedAcccounts) > 0){
            $where .=" AND u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }
        
        if(!empty($carrierId)){
            $where .="  AND ser.carrier_id = '".$carrierId."'";
        }
        if(!empty($serviceId) && count($serviceId) > 0){
            $where .=" AND c.`service_id` IN (".implode(',',$serviceId).")";
        }
        if(!empty($userAccountId)){
             //$where .=" AND u.`user_account_id` = '".$userAccountId."'";
        }        
        $dateConsignment = '';
        $groupBy = '';
        /*
         * dateScanned => datetime
         * dateCreated => timestamp
         * label created => integer
         * dateBooked => integer
         * dateDelivered => integer
        */
        $selectClause = "";
        if(!empty($dateType) && $dateType == 'dateLabelCreated'){
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_label_created),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_label_created` != '' AND c.`date_label_created` > 0 AND c.`label_file` != ''
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_label_created),
                '%Y-%m-%d'
              ) ";
            
        }else if(!empty($dateType) && $dateType == 'dateDelivered'){
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_delivered),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_delivered` != '' AND c.`date_delivered` > 0
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_delivered),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_delivered),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_delivered),
                '%Y-%m-%d'
              ) ";
        }else if(!empty($dateType) && $dateType == 'dateBooked'){
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_booked),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_booked` != '' AND c.`date_booked` > 0
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_booked),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_booked),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_booked),
                '%Y-%m-%d'
              ) ";
        }else if(!empty($dateType) && $dateType == 'dateScanned'){
           $selectClause =  "DATE(c.date_scanned)"; 
           $dateConsignment = "
                   DATE(c.date_scanned)  >= '".$startDate."'  AND DATE(c.date_scanned) <= '".$endDate."'"; 
           $groupBy = " GROUP BY DATE(c.date_scanned) ";
        }else if(!empty($dateType) && $dateType == 'dateCreated'){
            $selectClause =  "DATE(c.date_created)"; 
           $dateConsignment = " DATE(c.date_created)  >= '".$startDate."'  AND DATE(c.date_created) <= '".$endDate."' "; 
           $groupBy = " GROUP BY DATE(c.date_created) ";
           
        }
     
        $sql = "SELECT 
                ".$selectClause." AS date_label_created,
                ser.`name` AS service_name,
                ser.`code` AS `code`,
                $fieldName
                SUM(c.weight) AS weight,
                COUNT(DISTINCT c.id) AS id 
              FROM
                `consignment` c 
                INNER JOIN `services` ser 
                  ON ser.`id` = c.`service_id`                 
                INNER JOIN `user` u 
                    ON u.`id` = c.`user_id` 
                INNER JOIN `user_account` a 
                    ON a.id = u.`user_account_id`                   
              WHERE  
                $dateConsignment
                    $where   AND c.`shipment_status` NOT IN ('11','22')
              $groupBy ,c.`service_id` "; //exit;
        //echo $sql; exit;
        return Consignment::getConsignmentListFromSql($sql); 
    }
    
    public function getAccountWiseLabelCreatedReport($startDate, $endDate,$allouedAcccounts,$carrierId = '',$serviceId = array(),$userAccountId = '',$dateType='',$fieldName = '') {
        $where = '';
        //".$startDate."
        //".$endDate."
        if(count($allouedAcccounts) > 0){
            $where .=" AND u.`user_account_id` IN ('".implode("','", $allouedAcccounts)."')";
        }
        
        if(!empty($carrierId)){
            $where .="  AND ser.carrier_id = '".$carrierId."'";
        }
        if(!empty($serviceId) && count($serviceId) > 0){
            $where .=" AND c.`service_id` IN (".implode(',',$serviceId).")";
        }
        if(!empty($userAccountId)){
             //$where .=" AND u.`user_account_id` = '".$userAccountId."'";
        }        
        $dateConsignment = '';
        $groupBy = '';
        /*
         * dateScanned => datetime
         * dateCreated => timestamp
         * label created => integer
         * dateBooked => integer
         * dateDelivered => integer
        */
        $selectClause = "";
        if(!empty($dateType) && $dateType == 'dateLabelCreated'){
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_label_created),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_label_created` != '' AND c.`date_label_created` > 0 AND c.`label_file` != ''
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_label_created),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_label_created),
                '%Y-%m-%d'
              ) ";
            
        } else if(!empty($dateType) && $dateType == 'dateDelivered') {
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_delivered),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_delivered` != '' AND c.`date_delivered` > 0
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_delivered),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_delivered),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_delivered),
                '%Y-%m-%d'
              ) ";
        } else if(!empty($dateType) && $dateType == 'dateBooked') {
            $selectClause =  "DATE_FORMAT(
                                FROM_UNIXTIME(c.date_booked),
                                '%Y-%m-%d'
                              )";
            $dateConsignment = "
                c.`date_booked` != '' AND c.`date_booked` > 0
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_booked),
                  '%Y-%m-%d'
                ) >= '".$startDate."'
                AND DATE_FORMAT(
                  FROM_UNIXTIME(c.date_booked),
                  '%Y-%m-%d'
                ) <= '".$endDate."' ";
            $groupBy = " GROUP BY DATE_FORMAT(
                FROM_UNIXTIME(c.date_booked),
                '%Y-%m-%d'
              ) ";
        } else if(!empty($dateType) && $dateType == 'dateScanned') {
           $selectClause =  "DATE(c.date_scanned)"; 
           $dateConsignment = "
                   DATE(c.date_scanned)  >= '".$startDate."'  AND DATE(c.date_scanned) <= '".$endDate."'"; 
           $groupBy = " GROUP BY DATE(c.date_scanned) ";
        } else if(!empty($dateType) && $dateType == 'dateCreated') {
            $selectClause =  "DATE(c.date_created)"; 
           $dateConsignment = " DATE(c.date_created)  >= '".$startDate."'  AND DATE(c.date_created) <= '".$endDate."' "; 
           $groupBy = " GROUP BY DATE(c.date_created) ";
           
        }
     
        $sql = "SELECT 
                ".$selectClause." AS date_label_created,
                ua.`user_account` AS service_name,
                u.`user_name` AS code,
                $fieldName
                SUM(c.weight) AS weight,
                COUNT(DISTINCT c.id) AS id 
              FROM
                `consignment` c 
                INNER JOIN `services` ser 
                  ON ser.`id` = c.`service_id`                 
                INNER JOIN `user` u 
                    ON u.`id` = c.`user_id` 
                INNER JOIN customer_account ua 
                    ON ua.id = u.`user_account_id`                   
              WHERE  
                $dateConsignment
                    $where   AND c.`shipment_status` NOT IN ('11','22')
              $groupBy ,u.`user_account_id` "; //exit;
        //echo $sql; exit;
        return Consignment::getConsignmentListFromSql($sql); 
    }


    public static function getConsignmentSummaryReport($accountId='',$query = ''){
        $where = "";
        
        if(!empty($query))
            $where .= $query;
            
        $sql = "SELECT 
                COUNT(c.id) AS id
              FROM
                `consignment` c 
                JOIN `user` u 
                  ON u.id = c.`user_id` 
                  AND u.`user_account_id` IN (".$accountId.") $where";
        //echo $sql;exit;
        return Consignment::getConsignmentListFromSql($sql);
    }
    
    public static function getPaidDashboard($accountId=''){
        $sql = "SELECT 
                  COUNT(`consignment_id`) AS id 
                FROM
                  (SELECT 
                    cc.`consignment_id` 
                  FROM
                    `consignment_charges` cc 
                    JOIN `invoices` i 
                      ON i.`id` = cc.`invoice_id` AND i.`is_paid` = '1'
                  WHERE cc.`invoice_id` > 0 
                    AND cc.`account_id` IN 
                    (SELECT 
                      id 
                    FROM
                      user_account 
                    WHERE parentid = '".DbAccess3::escape($accountId)."') 
                    GROUP BY cc.`consignment_id`) AS tmp ";
        //echo $sql;exit;
        return Consignment::getConsignmentListFromSql($sql);
    }
    
    public static function getConsignmentIdFromMamifest($manifest, $debug=false){
        $consignmentIds = [];
        $sql =  "SELECT 
                    p.`consignment_id` AS id
                FROM
                    `parcel` p 
                    INNER JOIN `manifest_entity_mapping` mpm 
                      ON mpm.`entity_id` = p.id 
                    INNER JOIN `manifest` m 
                      ON m.`id` = mpm.`manifest_id` 
                WHERE m.`id` = '".DbAccess3::escape($manifest)."'
                GROUP BY p.`consignment_id`" ;
        if($debug)
        {
            echo $sql;exit;
        }
        $data = Consignment::getConsignmentListFromSql($sql);
        if(count($data) > 0) {
            foreach($data as $obj) {
                $consignmentIds[] = $obj->getId();
            }
        }
        return $consignmentIds;
    }
    
    public static function getConsignmentIdFromMawb($mawb){
        $consignmentIds = [];
        $sql =  "SELECT 
                    p.`consignment_id` AS id
                FROM
                    `parcel` p 
                    INNER JOIN `mawb_parcel_mapping` mpm 
                      ON mpm.`parcel_id` = p.id 
                    INNER JOIN `mawb` m 
                      ON m.`id` = mpm.`mawb_id` 
                WHERE m.`mawb_number` = '".DbAccess3::escape($mawb)."'
                GROUP BY p.`consignment_id`" ;
        //echo $sql;exit;
        $data = Consignment::getConsignmentListFromSql($sql);
        if(count($data) > 0) {
            foreach($data as $obj) {
                $consignmentIds[] = $obj->getId();
            }
        }
        return $consignmentIds;
    }
    public static function getOutBoundParcelCount($consignmentId,$type="outbound"){
        $return = 0;
        if($consignmentId > 0){
            $sql = "SELECT
                        COUNT(p.id) AS id
                    FROM
                      `parcel` p
                      JOIN `consignment` c
                        ON c.`id` = p.`consignment_id`
                    WHERE p.`consignment_id` = '".DbAccess3::escape($consignmentId)."'
                      AND c.`consignment_type` = '".DbAccess3::escape($type)."' ";
            $data = Consignment::getConsignmentListFromSql($sql);
            if(count($data) > 0) {
                $return = $data[0]->getId();
            }
        }
        return $return;
    }
    public function getDriverParcel($driverId = "",$vehicleId = "",$date="") {
        $where=[];
        $whereStr = "";
        if(!empty($driverId)){
            $where[] = "vpm.driver_id = '". DbAccess3::escape($driverId)."'";
        }
        if(!empty($vehicleId)){
            $where[] = "vpm.vehicle_id = '". DbAccess3::escape($vehicleId)."'";
        }
        if(!empty($date)){
            $where[] = "vpm.pickup_date = '". DbAccess3::escape($date)."'";
        }else if(empty($date)){
            $where[] = "vpm.pickup_date = '".date("Y-m-d")."'";
        }
        if(count($where) > 0){
            $whereStr = " AND ".implode(" AND ", $where);
        }
        $sql = "SELECT
                    c.`hawb`,
                    c.`company`,
                    c.`contact`,
                    c.`address_line_1`,
                    c.`address_line_2`,
                    c.`address_line_3`,
                    c.`city`,
                    c.`state`,
                    c.`postcode`,
                    con.`name` AS country_id,
                    con.`iso` AS sender_country_id,
                    s.`code` AS service_id,
                    s.`name` AS customized_service_id,
                    c.`telephone`,
                    p.`tracking_number` AS awb,
                    p.`parcel_status_code` AS consignment_status,
                    p.`owe_status_code` AS shipment_status
                  FROM
                    vehicle_parcel_mapping vpm
                    JOIN parcel p
                      ON p.`id` = vpm.parcel_id
                    JOIN consignment c
                      ON c.`id` = p.`consignment_id`
                    JOIN country con
                      ON con.`id` = c.`country_id`
                    JOIN `services` s
                      ON s.id = c.service_id
                  WHERE vpm.is_active = 1  ".$whereStr;
        $data = Consignment::getConsignmentListFromSql($sql);
        return $data;
    }

    public function getWhere()
    {
        $where = "";
        if (!empty($this->filter)) {
            $whereAnd = implode(" AND ", $this->filter);
        }
        if (!empty($this->orFilter)) {
            $whereOr = implode(" OR ", $this->orFilter);
        }
        $where .= (!empty($whereAnd) ? $whereAnd : '');
        $where .= (!empty($whereOr) ? (!empty($where) ? " OR " : '') . $whereOr : '');
        if (!empty($where)) {
            $where = " WHERE " . $where;
        }
        echo $where;
        die;
        return $where;
    }

    public function getListDynamic($columns = '*', $debug = false)
    {
        $join = "";
        $orderBy = "";
        $where = "";
        if (!empty($this->join)) {
            $join = $this->join;
        }
        if (!empty($this->filter)) {
            $where = $this->filter;
        }
//        echo $where;
//        die;
//        $where = $this->filter;

        if (!empty($this->orderBy)) {
            $orderBy = "ORDER BY " . implode(",", $this->orderBy);
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $columns . " FROM
                consignment c $join  WHERE               
                $where $orderBy";
        if (!empty($this->rowsPerPage) && $this->rowsPerPage > 0) {
            $sql .= " LIMIT " . ((!empty($this->pageOffset) && $this->pageOffset > 0) ? $this->pageOffset : 0) . "," . $this->rowsPerPage;
        }
        if ($debug) {
            echo $sql;
            die();
        }
        return Consignment::getConsignmentListFromSql($sql);
    }
    public function addReturnHandling($not = '') {
        $this->filter .= " AND handling $not like 'RTN%'";
    }
    
    public function getMawbOutboundReport($startDate, $endDate,$carrierId = '',$serviceId = array(),$userAccountId = '',$dateType='', $mawbNumber = '', $fieldName = '', $inboundVolumnReport = false) {
        $where = "c.`shipment_status` NOT IN ('11','22')";
        //".$startDate."
        //".$endDate."
       
        
        if(!empty($carrierId)){
            $where .="  AND ser.carrier_id = '".$carrierId."'";
        }
        if(!empty($serviceId) && count($serviceId) > 0){
            $where .=" AND c.`service_id` IN (".implode(',',$serviceId).")";
        }
        if(!empty($userAccountId)){
             //$where .=" AND u.`user_account_id` = '".$userAccountId."'";
        }        
        if(count($mawbNumber) > 0){
            foreach ($mawbNumber as $mawb) {
                $mawbNumber[] = str_replace("\r\n","",$mawb);
            }
            $where .= "  AND m.mawb_number IN ('".implode("','", $mawbNumber)."')";
        }
        $dateConsignment = '';
        /*
         * dateScanned => datetime
         * dateCreated => timestamp
         * label created => integer
         * dateBooked => integer
         * dateDelivered => integer
        */
        if($startDate != "" && $endDate != ""){
            if(!empty($dateType) && $dateType == 'dateLabelCreated'){
                $dateConsignment = "
                   AND c.`date_label_created` != '' AND c.`date_label_created` > 0 AND c.`label_file` != ''
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_label_created),
                      '%Y-%m-%d'
                    ) >= '".$startDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_label_created),
                      '%Y-%m-%d'
                    ) <= '".$endDate."' ";

            } else if(!empty($dateType) && $dateType == 'dateDelivered') {
                $dateConsignment = "
                   AND c.`date_delivered` != '' AND c.`date_delivered` > 0
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_delivered),
                      '%Y-%m-%d'
                    ) >= '".$startDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_delivered),
                      '%Y-%m-%d'
                    ) <= '".$endDate."' ";
            } else if(!empty($dateType) && $dateType == 'dateBooked') {
                $dateConsignment = "
                    AND c.`date_booked` != '' AND c.`date_booked` > 0
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_booked),
                      '%Y-%m-%d'
                    ) >= '".$startDate."'
                    AND DATE_FORMAT(
                      FROM_UNIXTIME(c.date_booked),
                      '%Y-%m-%d'
                    ) <= '".$endDate."' ";
            } else if(!empty($dateType) && $dateType == 'dateScanned') {
               $dateConsignment = "
                     AND  DATE(c.date_scanned)  >= '".$startDate."'  AND DATE(c.date_scanned) <= '".$endDate."'"; 
            } else if(!empty($dateType) && $dateType == 'dateCreated') {
               $dateConsignment = " AND DATE(c.date_created)  >= '".$startDate."'  AND DATE(c.date_created) <= '".$endDate."' "; 
            }
        }
 //(SELECT carrier_code FROM  tracking_data tdh WHERE  (tdh.carrier_code IN ('DELIVERED' , 'collected', 'Delivery Attempted', 'Delivery Attempted', 'FEE TO PAY')) AND tdh.entity_id = p.id LIMIT 1) 'buyer_delivered',
 //(SELECT carrier_desc FROM  tracking_data tdh WHERE  tdh.entity_id = p.id ORDER BY date_created DESC LIMIT 1) 'last POD Status',
        $selectColumn = "";
        $groupBy = "";
        if($inboundVolumnReport){
            $selectColumn = "count(distinct c.id) as total_shipment,
                    (SELECT  b.bagnumber FROM  parcel p INNER JOIN  parcel_bagging_mapping pbm ON pbm.parcel_id = p.id INNER JOIN  bagging b ON b.id = pbm.bag_id WHERE  p.tracking_number = c.hawb) AS first_mile_order_no,
                    c.hawb AS 'owe_super_tracking_no',
                    p.tracking_number AS 'last_mile_tracking_no',
                    m.mawb_number,
                    c.date_label_created,
                    (SELECT iso FROM  country co WHERE  co.id = c.country_id) AS 'destination',
                    (select warehouse_code from warehouse w where w.id = c.destination_warehouse_id) as 'destination_warehouse',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Delivered' AND track_point = 'FelthamGB' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = First_mile_order_no)) LIMIT 1) 'owe_pick_up_ups_hub'";
            $groupBy = "date_format(owe_pick_up_ups_hub,'%Y-%m-%d'), destination_warehouse ";
        }
        else
        {
            $selectColumn = "(SELECT  b.bagnumber FROM  parcel p INNER JOIN  parcel_bagging_mapping pbm ON pbm.parcel_id = p.id INNER JOIN  bagging b ON b.id = pbm.bag_id WHERE  p.tracking_number = c.hawb) AS first_mile_order_no,
                    c.hawb AS 'owe_super_tracking_no',
                    p.tracking_number AS 'last_mile_tracking_no',
                    m.mawb_number,
                    c.date_label_created,
                    (SELECT iso FROM  country co WHERE  co.id = c.country_id) AS 'destination',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Drop-Off' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = First_mile_order_no)) LIMIT 1) 'parcel_dropoff_ups_shop',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Pickup Scan' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = First_mile_order_no)) LIMIT 1) 'parcel_scanning_time',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Arrived At Facility' AND track_point = 'FelthamGB' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = first_mile_order_no)) LIMIT 1) 'carton_delivery_to_hub_scan',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Delivered' AND track_point = 'FelthamGB' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = First_mile_order_no)) LIMIT 1) 'owe_pick_up_ups_hub',
                    (SELECT date_created FROM  tracking_data tdh WHERE  carrier_desc = 'Arrived at Sort Facility Hayes  Service Centre - GBR' AND tdh.entity_id = (SELECT id FROM   parcel WHERE   consignment_id IN (SELECT    id    FROM   consignment    WHERE   awb = c.hawb)) LIMIT 1) 'time_arrival_owe_warehouse',
                    (SELECT date_created FROM  tracking_data tdh WHERE  TRIM(carrier_desc) = 'Departed Facility in Hayes  Service Centre - GBR' AND tdh.entity_id = p.id LIMIT 1) 'time_departed_owe_warehouse',
                    (SELECT warehouse_code FROM warehouse w WHERE w.id = c.`destination_warehouse_id`)'destinationport',
                    (SELECT date_created FROM  tracking_data tdh WHERE  TRIM(track_point) = 'Departed LHR' AND tdh.entity_id = p.id LIMIT 1) 'actual_flight_departure',
                    (SELECT date_created FROM  tracking_data tdh WHERE  TRIM(track_point) = 'Arrived Destination Country' AND tdh.entity_id = p.id LIMIT 1) 'actual_flight_arrival',
                    (SELECT date_created FROM  tracking_data tdh WHERE  (tdh.status_code_id = '117') AND tdh.tracking_number = c.awb LIMIT 1) 'custom_clearance',
                    (SELECT date_created FROM  tracking_data tdh WHERE  (tdh.status_code_id = '121') AND tdh.tracking_number = c.awb LIMIT 1) 'buyer_delivered',
                    (SELECT MAX(date_created) FROM  tracking_data tdh WHERE  tdh.entity_id = p.id) 'last_status_date'";
            $groupBy = "c.`id`";
        }
        $sql = "SELECT " .
                 $selectColumn   
                ."FROM
                  consignment c
                      INNER JOIN
                  parcel p ON p.consignment_id = c.id
                      INNER JOIN
                  user u ON u.id = c.user_id
                      INNER JOIN
                  customer_account ua ON ua.id = u.user_account_id
                      INNER JOIN
                  services ser ON ser.id = c.service_id
                      INNER JOIN
                  parcel_bagging_mapping pbm ON pbm.parcel_id = p.id
                      INNER JOIN
                  bagging b ON b.id = pbm.bag_id
                      INNER JOIN
                  mawb_parcel_mapping mpm ON mpm.bag_id = b.id
                      INNER JOIN
                  mawb m ON mpm.mawb_id = m.id
                            WHERE  
                                  $where  
                                      $dateConsignment
                            GROUP BY  " . $groupBy . " having owe_pick_up_ups_hub > 0 order by first_mile_order_no desc"; 
       //echo $sql;exit;
            $data = DbAccess3::runQuery($sql);
            $res = [];
            while ($obj = mysqli_fetch_object($data)) {
                $res[] = $obj;
            }
            return $res;
        
    }
}
