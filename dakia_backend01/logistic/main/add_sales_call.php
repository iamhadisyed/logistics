<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../includes/mapping/setemailreminder.class.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $table_msg;
    private $department_data;
    private $User;
	private $old_customer;
	private $error_message = array();

    protected function init() {
		$user = SessionManager::getUser();
		if ($user->getUserType() != "sales" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}
        $this->table_msg = "Invalid Command";
        // common initialisation for ths page
        $this->setTitle("Sales Calls");
        if ($this->table_msg == "")
            $this->table_msg = "Sales Calls";
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        //Get admin users list
       

        if (isset($this->form_vars["form_action"])) {
//            echo "<pre>";print_r($_POST);echo "</pre>"; die;
            switch ($this->form_vars["form_action"]) {
                case "save":
					
                    if($this->form_vars["newcustomerlist"] != "" || $this->form_vars["newcustomertxt"] != "")
					{
						 $send_email = "N";
						$SalesCallLog = new SalesCallLog(intval($this->form_vars["id"]));
						$SalesCallLog->setDateCall(time());
						if(isset($this->form_vars["meeting_date"]) && $this->form_vars["meeting_date"] != '' && $this->form_vars["meeting_date"] != '1970-01-01 00:00:00')
							$SalesCallLog->setMeetingDate(strtotime($this->form_vars["meeting_date"]));
						$customer_check = $this->form_vars["new_customer"];
						if($customer_check == "OLD_CUSTOMER")
						{
							$SalesCallLog->setCustomerCode($this->form_vars["newcustomerlist"]);
						}
						else
						{
							$SalesCallLog->setCustomerCode($this->form_vars["newcustomertxt"]);
						}
	
						$SalesCallLog->setCompany($this->form_vars["comapny"]);
						$SalesCallLog->setContact($this->form_vars["contact"]);
						$SalesCallLog->setAddress($this->form_vars["address"]);
						$SalesCallLog->setTelephone($this->form_vars["telephone"]);
						$SalesCallLog->setEmail($this->form_vars["email"]);
						$SalesCallLog->setDetailDiscussed($this->form_vars["detail_discussed"]);
						if (isset($this->form_vars["send_email"]))
                        $send_email = "Y";
						$SalesCallLog->setEmailSend($send_email);
						
						if (isset($_FILES["rates_offered"]) && trim($_FILES["rates_offered"]["name"]) != '') {
							$allowedExts = array("pdf");
							$temp = explode(".", $_FILES["rates_offered"]["name"]);
							$extension = end($temp);
	
							if (($_FILES["rates_offered"]["type"] == "application/pdf")) {
								if ($_FILES["rates_offered"]["error"] > 0) {
									$error_array[] = "Return Code: " . $_FILES["rates_offered"]["error"] . "<br>";
								} else {
									$uploadRatesFile = time() . $_FILES["rates_offered"]["name"];
									move_uploaded_file($_FILES["rates_offered"]["tmp_name"], "../_assets/sales_rates_offered/" . $uploadRatesFile);
								}
							} else {
								return $this->error_message[] = "Invalid file. Please select PDF file.";
							}
						
							$SalesCallLog->setDocumentLink("../_assets/sales_rates_offered/" . $uploadRatesFile);
						 }
						if(isset($this->form_vars["followup_meeting_date"]) && $this->form_vars["followup_meeting_date"] != '' && $this->form_vars["followup_meeting_date"] != '1970-01-01 00:00:00')
							$SalesCallLog->setFollowMeetingDate(strtotime($this->form_vars["followup_meeting_date"]));
							
						$SalesCallLog->setUserid($sessionUser->getId());
						$SalesCallLog->save();
						   
						$email = $sessionUser->getEmail();
						if($email == "")
							$email = $sessionUser->getAlternativeEmail();
						
						if($this->form_vars["email"] != "")
								$to_address = $email . "," . $this->form_vars["email"];
						else
							$to_address = $email;
							
						if($this->form_vars["followup_meeting_date"] != '' && $this->form_vars["followup_meeting_date"] != '1970-01-01 00:00:00')
						{
							$from_name = "sales";        
							$from_address = "sales@oneworldexpress.com";        
							$to_name = $sessionUser->getCompany();     

							
							
							
							$startTime = date("Ymd His",strtotime($this->form_vars["followup_meeting_date"]));        
							$endTime = date("Ymd His", strtotime( '+1 hour', strtotime($this->form_vars["followup_meeting_date"])));
							$subject = "FOLLOW UP MEETING REMINDER";        
							$message .= "Hi You have a meeting with ";
							if($SalesCallLog->getCompany() != "")
								$message .= $SalesCallLog->getCompany() . " ";
							if($SalesCallLog->getContact() != "")
								$message .= $SalesCallLog->getContact();
								
								$message .= "@ " . $this->form_vars["followup_meeting_date"];
							$description = $mesasge;        
							$location = $SalesCallLog->getCustomerCode();
							
							SetEmailReminder::sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);
							
						}
						
						if($this->form_vars["send_email"] == "Y")
						{
							$to = $to_address;
						}
						else
						{
							$to = $email;
						}
							$subject = "MINUTE OF MEETING";
							$headers = "From: sales@oneworldexpress.com"."\r\n";
							$headers .= "Reply-To: sales@oneworldexpress.com"."\r\n";
							$headers .= "Return-Path: sales@oneworldexpress.com"."\r\n"; 
							$headers .= "Content-type: text/html\r\n"; 
							
							//$message = "<div> Dear ". $SalesCallLog->getCustomerCode() .",</div>";
							$message = "<div>".nl2br($this->form_vars["detail_discussed"])."</div>";
							
							if (!mail($to, $subject, $message, $headers)) {
								return $this->error_message[] = "Enable to MINUTE OF MEETING to Customer.";
								
							}
						
					util_redirect("sales_call_list.php");
					}
					else
					{
						$this->error_message[] = "Please Select Customer Code";
					}
					
                    break;
                default:
                    break;
            }
        } else {
            $id = util_get_num("id");
			if($id > 0)
			{
				$this->form_vars["id"] = $id;
	
	
				// get address values
				 $SalesCallLog = new SalesCallLog($id);
				//
				if($SalesCallLog->getMeetingDate() != "")
					$this->form_vars["meeting_date"] = date("Y-m-d H:i:s",$SalesCallLog->getMeetingDate());
				$this->form_vars["newcustomerlist"] = $SalesCallLog->getCustomerCode();
				$this->form_vars["comapny"] = $SalesCallLog->getCompany();
				$this->form_vars["contact"] = $SalesCallLog->getContact();
				$this->form_vars["address"] = $SalesCallLog->getAddress();
				$this->form_vars["telephone"] = $SalesCallLog->getTelephone();
				$this->form_vars["email"] = $SalesCallLog->getEmail();
				$this->form_vars["detail_discussed"] = $SalesCallLog->getDetailDiscussed();
				if($SalesCallLog->getFollowMeetingDate() != "")
					$this->form_vars["followup_meeting_date"] = date("Y-m-d H:i:s",$SalesCallLog->getFollowMeetingDate());
				$this->old_customer = $SalesCallLog->getCustomerCode();
				$this->form_vars["send_email"] = $SalesCallLog->getEmailSend();
			}
        }
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
        <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>   
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
				
				
				
				$("input[name$='new_customer']").click(function () {
				var value = $(this).val();
				if (value == 'NEW_CUSTOMER') {
					$("#newcustomercode").show();
					$("#oldcustomercode").hide();
				} else if (value == 'OLD_CUSTOMER') {
					$("#newcustomercode").hide();
					$("#oldcustomercode").show();
				}
			});
			
			$('#meeting_date').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true});
            $('#followup_meeting_date').datetimepicker({dateFormat: 'yyyy-mm-dd hh:ii', use24hours: true});
			
				
                $("#btnSave").click(function () {
                    $("#form_action").val("save");
                    $("#adminForm").submit();
                });
               
                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();
                });
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('a').tooltip();
            });
			
			function validateEmail()
            {
                sEmail = $('#email').val();
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test(sEmail))
                    return true;
                else if (sEmail != '')
                {

                    alert('Please enter valid email address');
                    $("#email").focus();
                    return false;
                }
            }

        </script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/sales_call_list.php">Sales Calls List</a></li>
            <li><a href="../main/add_sales_call.php">Add Sales Calls</a></li>
            <!--<li><a href="#">List</a></li>-->
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        <?php
                        if (!isset($_GET['dep_id']) || (isset($_GET['dep_id']) && ($_GET['dep_id'] == "" || $_GET['dep_id'] < 0)))
                            echo "Add Sales Calls ";
                        else
                            echo "Edit Sales Calls ";
                        ?>
                    </div>                    
                </div>
                <div class="portlet-body">
                    <? if(sizeof($this->error_message) > 0)
					{ ?>
                       <div class="alert alert-danger">
                       <?
					    echo implode(",",$this->error_message); ?>
                       </div>
                      <? } ?>
                    
                    <div class="row">
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <? 
								
									if($this->old_customer != ''){
										 $old_customer_checked = 'checked="checked"';
										 $new_customer_checked = '';
										 $display_new = 'style="display:none;"';
										 $display_old = '';
									}
									else{
										 $new_customer_checked = 'checked="checked"';
										 $old_customer_checked = '';
										 $display_new = '';
										 $display_old = 'style="display:none;"';
									}
								?>
	                                 <label class="label-control">
                                        <input checked type="radio" id="new_customer"	name="new_customer" <?  echo  $new_customer_checked; ?>
                                               value="NEW_CUSTOMER" /><b>NEW CUSTOMER</b>
                                    </label>
                                    <label class="label-control">
                                        <input type="radio"	id="old_customer" name="new_customer"  <? echo $old_customer_checked; ?>
                                               value="OLD_CUSTOMER" /><b>OLD CUSTOMER</b>
                                    </label>
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3" id="newcustomercode" <? echo $display_new; ?>>
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
	                               <input  required type="text" name="newcustomertxt" id="newcustomertxt" value="<?php echo htmlspecialchars($newcustomertxt); ?>" placeholder="NEW CUSTOMER CODE"  class="form-control" rel="tooltip"  title="NEW CUSTOMER CODE">
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3" id="oldcustomercode" <? echo $display_old; ?>>
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <?
                                	$salescalllog = new SalesCallLogFilter();
									$salescalllog->addgGroupBy("customer_code");
									$customerlist = $salescalllog->getColumnList("customer_code, id");
									$customercode = array();
									if(count($customerlist) > 0)
									{
										foreach($customerlist as $customer)
										{
											$customercode[$customer->getCustomerCode()] = $customer->getCustomerCode();
										}
									}
									$userFilter = new UserAccountFilter();
									$userFilter->addActiveFlagFilter();
									$userFilter->AddOrderByAccount();									
									$userlist = $userFilter->getDistinctColumnList("user_account, id");
									if(count($userlist) > 0)
									{
										$customercode[""] = "----ACTIVE CUSTOMER----";
										foreach($userlist as $ulist)
										{
											$customercode[$ulist->getUserAccount()] = $ulist->getUserAccount();
										}
									}
									
									
								?>
	                               <select id="newcustomerlist" name="newcustomerlist" class="form-control">
                                   <option value="">Select Customer</option>
                                   <option value="">----NOT ACTIVE CUSTOMER----</option>
                                   <?
                                   	if(sizeof($customercode) > 0)
									{
										foreach($customercode as $key=>$cusList) 
										{
										 $selected = ($this->old_customer == $key) ? " selected" : "";
							             echo "<option ". $selected ." value='".$key."'>". $cusList . " </option>" ;
										}
									}
								   ?>
                                   </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                	 <input id="meeting_date" value="<? echo $this->form_vars["meeting_date"];?>" name="meeting_date" type="text" size="16" data-date-format="yyyy-mm-dd hh:ii"  class="form-control" rel="tooltip" placeholder="Meeting Date" title="Meeting Date"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input  type="text" name="comapny" id="comapny" value="<?php echo htmlspecialchars($this->form_vars["comapny"]); ?>" placeholder="comapny"  class="form-control" rel="tooltip"  title="comapny">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input  type="text" name="contact" id="contact" value="<?php echo htmlspecialchars($this->form_vars["contact"]); ?>" placeholder="contact"  class="form-control" rel="tooltip"  title="contact">
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                	<textarea name="address" id="address" placeholder="Address" class="form-control" cols="50" rows="5"><? echo $this->form_vars["address"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                	<input  type="text" name="telephone" id="telephone" value="<?php echo htmlspecialchars($this->form_vars["telephone"]); ?>" placeholder="Telephone"  class="form-control" rel="tooltip"  title="Telephone">
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                	<input  type="text" name="email" id="email" onblur="return validateEmail();"  value="<?php echo htmlspecialchars($this->form_vars["email"]); ?>" placeholder="E-Mail"  class="form-control" rel="tooltip"  title="E-Mail">
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                	 <input id="rates_offered"  type="file" name="rates_offered" >
                                </div>
                            </div>
                        </div>
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                 <?php
                                $selected = (($this->form_vars["send_email"] == "Y") ? "checked" : "");
                                
                                ?>
                                	 <input id="send_email" name="send_email" value="Y" type="checkbox" <? echo $selected; ?> class="form-control"/>SEND MINUTE OF MEETING EMAIL
                                </div>
                            </div>
                        </div>
                        
                         <div class="form-group col-md-3">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                	 <input id="followup_meeting_date" name="followup_meeting_date" value="<? echo $this->form_vars["followup_meeting_date"];?>" type="text" size="16" data-date-format="yyyy-mm-dd hh:ii"  class="form-control" rel="tooltip" placeholder="Follow Up Meeting Date" title="Follow Up Meeting Date"/>
                                </div>
                            </div>
                        </div>
                      
                      
                    </div>
                    <div class="row"> 
                    	  <div class="form-group col-md-12">
                            <div class="form-group col-md-12">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                	<textarea data-provide="markdown" class="form-control"  rows="7" cols="150" name="detail_discussed" placeholder="DETAIL DISCUSSED" id="detail_discussed" ><? echo $this->form_vars["detail_discussed"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                   </div>
                    <div class="row"> 
                        <div class="col-md-12">
                            <a id="btnSave"   href="#" class="btn btn-primary btn_save"><em class="fa fa-floppy-o"></em><?php
                                if (@$id > 0) {
                                    echo " Update";
                                } else {
                                    echo " Save";
                                }
                                ?> </a>
                        </div>
                    </div>

                </div>
                <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>"  class="form-control"/>
                <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
            </div>  <!-- table_container -->
        </div>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
