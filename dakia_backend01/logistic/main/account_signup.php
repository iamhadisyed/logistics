<?php

require_once("../includes/settings/config.inc.php");
// set up local page class

include_classes([
    'country.class',
    'countryfilter.class',
    'useraccount.class'
]);

class Page extends BasePage {

    protected function init() {
        $error_array = array();
        if (isset($_POST['action']) && $_POST['action'] === "account_signup") {
            if ($this->form_vars["company"] == '' || $this->form_vars["contact"] == '' || $this->form_vars["telephone"] == '' || $this->form_vars["country_id"] == '' || $this->form_vars["email"] == '') {
                $error_array[] = "Check required fields.";
            } else {

                $userAccountNumber = strtoupper(preg_replace("/[^a-zA-Z]/", "", $this->form_vars["company"]));
                $userAccountNumber = (strlen($userAccountNumber) > 15) ? substr($userAccountNumber, 0, 10) : $userAccountNumber;
                $accountCheck = new UserAccountFilter();
                $accountCheck->addFilter("    user_account = '" . DbAccess3::escape($userAccountNumber) . "' OR email = '" . DbAccess3::escape($this->form_vars["email"]) . "' ");
                $accountCheckList = $accountCheck->getColumnList("user_account, id");
                if (count($accountCheckList) > 0)
                    $error_array[] = "Account with following company name or email already exists in our system." ;

                if (count($error_array) <= 0) {
                    $userAccount = new CustomerAccount();
                    $userAccount->setCompany($this->form_vars["company"]);
                    $userAccount->setUserAccount($userAccountNumber);
                    $userAccount->setFullName($this->form_vars["contact"]);
                    $userAccount->setTelephone($this->form_vars["telephone"]);
                    $userAccount->setCountryId($this->form_vars["country_id"]);
                    $userAccount->setEmail($this->form_vars["email"]);
                    $userAccount->setDateCreated(time());
                    $userAccount->setThemeId(2);
                    $userAccount->setParentid(148);
                    $userAccount->setUserServiceType('CHOICE');
                    $userAccount->save();
                    $latestId = $userAccount->getId();
               
                    if ($latestId) {
                        $to = "itsupport@oneworldexpress.com,shabbir@oneworldexpress.com,kiran.yaseen@oneworldexpress.com";
                        
                        $from = "itsupport@oneworldexpress.com";
                        $fromName = 'itsupport@oneworldexpress.com';
                        $subject = "SmartTrack New Account Signup";
                        $htmlContent = ' 
                            <html> 
                            <head> 
                                <title>SmartTrack</title> 
                            </head> 
                            <body> 
                                <h3>New Account Signup</h3>
                                <p>Hi Admin,</p>
                                <p>New account created from front end with following details</p>
                                <table cellspacing="0" style=" width: 100%;"> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th align="left">Contact Name</th> 
                                        <th>Company Name</th>
                                        <th>Telephone</th> 
                                        <th>Country</th> 
                                        <th>Email</th> 
                                        <th>Account ID (System Generated )</th> 
                                        <th>Link</th> 
                                    </tr> ';

                        $country = new Country($this->form_vars["country_id"]);
                        $htmlContent .= '<tr><td align="left">' . $this->form_vars["contact"] . '</td>'
                                . '<td  align="center">' . $this->form_vars["company"] . '</td>'
                                . '<td  align="center">' . $this->form_vars["telephone"] . '</td>'
                                . '<td  align="center">' . $country->getName() . '</td>'
                                . '<td  align="center">' . $this->form_vars["email"] . '</td>'
                                . '<td  align="center">' . $userAccountNumber . '</td>'
                                . '<td  align="center"><a target="_blank" href="' . BASE_URL . 'customers_details.php?id=' . $latestId . '" >View</a></td> '
                                . '</tr>';
                        $htmlContent .= ' 
                                </table> 
                            </body> 
                            </html>';
                        $headers = "From: itsupport@oneworldexpress.com \r\n";
                        $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        // Send email 
                         mail($to, $subject, $htmlContent, $headers); 
                       
                    }else{
                       $error_array[] = "Some error occurred please try again later. Contact itsupport@oneworldexpress.com" ;
                    }
                }  
            }
            if(count($error_array)>0){
                $message = '<ul>';
                $message .= '<li>' . implode( '</li><li>', $error_array) . '</li>';
               $message .= '</ul>';
                echo json_encode(array('status' => 'error', 'message' => $message)); 
            }else{
                echo json_encode(array('status' => 'success', 'message' => "<ul><li>Your account created successfully and information is sent. Our team will contact back you soon</li></ul>"));
            }
             exit;
        }
    }

}

$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>