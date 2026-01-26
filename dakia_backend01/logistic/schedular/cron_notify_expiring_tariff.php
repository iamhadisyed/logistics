<?php
$localSettingsFile = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : '';
if (substr($localSettingsFile, 0, 4) == "www.")
    $localSettingsFile = substr($localSettingsFile, 4);
$localSettingsFile = str_replace(".", "_", $localSettingsFile);

if (empty($localSettingsFile)) {
    $localSettingsFile = $_SERVER["SCRIPT_NAME"];
    if (strpos($localSettingsFile, "beta") !== false) {
        $localSettingsFile = "beta_smarttrack_co";
    } else if (strpos($localSettingsFile, "staging") !== false) {
        $localSettingsFile = "staging_smarttrack_co";
    } else if (strpos($localSettingsFile, "local") !== false) {
        $localSettingsFile = "local_oneworldexpress_co_uk";
    } else {
        $localSettingsFile = "smarttrack_co";
    }
}
 
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
                     'tariffs.class',
                    'tariffsfilter.class',
]);

 
        if(isset($_GET['debugcron']) && $_GET['debugcron']=='yes' )
        {
            error_reporting(E_ALL);
            ini_set("display_errors", "1");
            $debug = true;
        }else{
            $debug = false;
        }
	$tariffFilterObj 	=	 new TariffsFilter();
        $tariffFilterObj->addFilter('   (DATEDIFF( t.end_date,CURDATE()) BETWEEN 0 AND 5)  '   );
	$tariffFilterObj->addFieldFilter('t.status','1');
        $tariffFilterObj->addCustomJoin("  INNER JOIN
                                            customer_account ua ON t.user_account_id = ua.id
                                             INNER JOIN
                                            carrier c ON t.carrier_id = c.id
                                            INNER JOIN
                                            services s ON t.service_id = s.id ");
        
	$tariffData		=	 $tariffFilterObj->getList(' t.id,t.user_account_id,t.name,t.tariff_type,t.start_date,t.end_date, ua.billing_contact, ua.billing_email, ua.full_name,c.carrier carrier_name,s.name as service_name',$debug);
        $emailData = array();
        
        //////////////////compile email data//////////////////
	if(count($tariffData)>0){
                foreach($tariffData as $tariff){
 
                     $emailData[$tariff->getUserAccountId()]['tariffs'][] = array (
                         "tariff_name" => $tariff->getName(),
                         "tariff_type" => $tariff->getTariffType(),
                         "tariff_expiry" => $tariff->getEndDate(),
                         "tariff_start" => $tariff->getStartDate(),
                         "tariff_id" => $tariff->getId(),
                         "carrier" => $tariff->getCarrierName(),
                             "service" => $tariff->getServiceName() 
                         
                     );
                     $emailData[$tariff->getUserAccountId()]['full_name'] = (trim($tariff->getBillingContact())=='')?$tariff->getfullName():$tariff->getBillingContact();
                     $emailData[$tariff->getUserAccountId()]['email'] = $tariff->getBillingEmail();
                     
                }   
        }
        /*echo "<pre>";
        print_r($emailData);
        die;*/
        //////////////////sending email data//////////////////
         if(count($emailData)>0){
             foreach($emailData as $data){
                       // $to = $data['email']; 
                        $to = " itsupport@oneworldexpress.com, ".$data['email']; 
                        $subject = "SmartTrack Tariffs! expiring soon notifictaion"; 
                        $htmlContent = ' 
                            <html> 
                            <head> 
                                <title>SmartTrack</title> 
                            </head> 
                            <body> 
                                <h3>SmartTrack Tariffs! expiring soon</h3>
                                <p>Dear '.$data['full_name'].',</p>
                                <table cellspacing="0" style=" width: 100%;"> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th align="left">Tariff Name</th> 
                                        <th>Tariff Type</th>
                                        <th>Carrier</th> 
                                        <th>Service</th> 
                                        <th>Start Date</th> 
                                        <th>End Date</th> 
                                        <th>Link</th> 
                                    </tr> ';
                        
                        
                        
                            foreach($data['tariffs'] as $tdata){
                                 $htmlContent .= '<tr><td align="left">'.$tdata['tariff_name'].'</td>'
                                                . '<td  align="center">'.$tdata['tariff_type'].'</td>'
                                                . '<td  align="center">'.$tdata['carrier'].'</td>'
                                                . '<td  align="center">'.$tdata['service'].'</td>'
                                                . '<td  align="center">'.$tdata['tariff_start'].'</td>'
                                                . '<td  align="center">'.$tdata['tariff_expiry'].'</td>'
                                                . '<td  align="center"><a target="_blank" href="'.BASE_URL.'tariff_add.php?id='.$tdata['tariff_id'].'" >View</a></td> '
                                                . '</tr>';

                            }
                            $htmlContent .= ' 
                                </table> 
                           <p><br>Thanks & Regards 
                           <br>SmartTrack Portal Team 
                           </p>
                            </body> 
                            </html>'; 
                    if($debug){
                       echo $htmlContent."<br>------------------------------<br>"; 
                        }
                        // Set content-type header for sending HTML email                    
                        $headers = "From: itsupport@oneworldexpress.com \r\n";                                              
                        $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                        // Send email 
                        if(mail($to, $subject, $htmlContent, $headers)){ 
                            echo 'Email has sent successfully.'; 
                        }else{ 
                           echo 'Email sending failed.'; 
                        } 
             }
         }
        
        
        
 
?>
