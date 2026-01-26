<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");
$getValidSaleAgentQuery = " 
                                        SELECT
                                           u.id,
                                          ua.`sale_date`,
                                          u.commission_break_event_amount,
                                          DATE_ADD(ua.sale_date, INTERVAL `sales_pot_time_period` MONTH) AS end_date,
                                          SUM((SELECT SUM(net_amount) FROM `invoices` WHERE invoice_date <= end_date AND user_account_id = ua.`id` AND `is_cancel` != 1)) AS final_total
                                        FROM
                                          `user` u
                                          JOIN customer_account ua
                                          ON ua.`sales_person` = u.`id`
                                        WHERE u.`user_type` = 'sales_agent' AND u.is_sale_pot_eligible = '0' GROUP BY u.`id`";
$getValidSaleAgentResult = DbAccess3::runQuery($getValidSaleAgentQuery);
if (mysqli_num_rows($getValidSaleAgentResult) > 0) {
    while ($rowData  = mysqli_fetch_array($getValidSaleAgentResult)) {
        if($rowData['commission_break_event_amount'] <= $rowData['final_total']){
            $updateQry = "UPDATE  `user`
                                    SET 
                                        `is_sale_pot_eligible` = 1
                                    WHERE 
                                        id = " . $rowData['id'] . "";
            DbAccess3::runQuery($updateQry);
        }
    }
}