<?php
// get settings
require_once("../includes/settings/config.inc.php");
class Page extends BasePage {
    private $support_data;
    private $department_data;
    private $success_message;
    private $user = null;
	
    protected function init() {
        $this->user = SessionManager::getUser();
        $this->breadCrumb['data']   =   array(
                        'index.php'=>Translation::GetCaption("HOME"),
                        'support_center.php'=>Translation::GetCaption("TICKET"),
                        '0'=>Translation::GetCaption("SUPPORT_CENTER")
                    );
    
        $this->table_msg = "Invalid Command";
        // common initialisation for ths page
        $this->setTitle("Support Center");
        if ($this->table_msg == "")
            $this->table_msg = "Support Center";
        //Get Department List
        //$this->department_data = "";
        if (isset($this->form_vars["form_action"])) {
            if($this->form_vars["form_action"] == "save_ticket")
            {
                $image_name_str = "";
                 $path = "../_assets/customer_support/";

                    if (!file_exists($path))
                        @mkdir($path, 0775);
                    if(!empty($_FILES['fileupload']['name'][0])){

                        $count = 0;
                        $scanned_documents = "";
                        foreach ($_FILES['fileupload']['name'] as $file) {
                            $allowedExts = array("gif", "jpeg", "jpg", "png","pdf,doc, docx, xls, xlsx");
                            $temp = explode(".", $_FILES["fileupload"]["name"][$count]);
                            $extension = end($temp);
                            if ((($_FILES["fileupload"]["type"][$count] == "image/gif") || ($_FILES["fileupload"]["type"][$count] == "image/jpeg") || ($_FILES["fileupload"]["type"][$count] == "image/jpg") || ($_FILES["fileupload"]["type"][$count] == "image/pjpeg") || ($_FILES["fileupload"]["type"][$count] == "image/x-png") || ($_FILES["fileupload"]["type"][$count] == "image/png") || ($_FILES["fileupload"]["type"][$count] == "application/pdf" ) || ($_FILES["fileupload"]["type"][$count] == "application/msword" )) && in_array($extension, $allowedExts)){
                                if ($_FILES["fileupload"]["error"][$count] > 0) {
                                $error_array[] = "Return Code: " . $_FILES["fileupload"]["error"][$count] . "<br>";
                            } else {
                                $uploadName = $file;
                                $image_name_str .= $file . "||";
                                move_uploaded_file($_FILES["fileupload"]["tmp_name"][$count], $path . $uploadName);
                                $count++;
                            }
                            }else {
                                $error_array[] = "Invalid file";
                            }
                            
                        }
                    }
               
                $TicketId = "";
                if($this->form_vars["deparment"] == '')
                    $departmentId = '5';
                else
                    $departmentId = $this->form_vars["deparment"];
            // Save Tciket First
                $dataArray = array(
                    'ticket_code' => 'TMP',
                    'department_id' => $departmentId,
                    'priority' => $this->form_vars["priority"],
                    'subject' => $this->form_vars["subject"],
                    'status' => 'open',
                    'addedby' => $this->user->getId(),
                    'added_date' => date("Y-m-d H:i:s")
                    );
                $HelpDesk = new HelpDeskTicket($dataArray);
                $HelpDesk->save();
                $ticket_id = $HelpDesk->getId();
                $department = new Department($departmentId);
                $department_code = $department->getDepartmentCode();
                $code = 'TKT-' . $department_code . '-' . str_pad($ticket_id, 5, '0', STR_PAD_LEFT);
                $HelpDeskUpdate = new HelpDeskTicket($ticket_id);
                $HelpDeskUpdate->setTicketCode($code);
                $HelpDeskUpdate->save();
            // save new Ticket Message details
                $TicketDataArray = array(
                    'ticketid' => $ticket_id,
                    'message' => $this->form_vars["message"],
                    'attachment' => $image_name_str,
                    'addedby' => $this->user->getId(),
                    'added_date' => date("Y-m-d H:i:s")
                    );
                $HelpDeskMessage = new HelpDeskTicketMessage($TicketDataArray);
                $HelpDeskMessage->save();
                $ticket_id = MD5($ticket_id);
//            //Get Department Head First
                $DepartmentFilter = new DepartmentFilter();
                $DepartmentData = $DepartmentFilter->getDeparmentHead($departmentId);
                if(count($DepartmentData) > 0)
                {
                    $DepartmentData = $DepartmentData[0];               
                    $DepartmentHeadName = $DepartmentData->getTitle();
                    $DepartmentHeadEmail = $DepartmentData->getDescription();
                }
                else
                {
                    $DepartmentHeadName = "Customer Service";
                    $DepartmentHeadEmail = "Customer Service";
                }
                $DepartmentMessage = $this->form_vars["message"];
            //Send Mail to Admin
                $message = "<table>
                <tbody>
                    <tr>
                        <td>Hi " . $DepartmentHeadName . " ,</td>
                    </tr>
                    <tr>
                        <td>" . $DepartmentMessage . "</td>
                    </tr>
                    <tr>
                        <td><a href='".SETTING_MAIN_URL_ACCOUNT."main/ticket_details.php?id=" . $ticket_id . "'>Click Here for more details</a></td>
                    </tr>
                </tbody>
            </table>
            <br><br>Thanks , <br> One World Express";
            $subject = ucwords($this->form_vars["subject"]);
            $headers = "From: itsupport@oneworldexpress.com \r\n";
            $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            if(mail("mruga@oneworldexpress.com", $subject, $message, $headers))
            {
                $this->success_message = Translation::GetCaption("HELP_MESSAGE") . $code . ".";
            }
            else
            {
                echo "fail";
            }
        }
             //  util_redirect("support_center_list.php");
    }
}
    /*     * *
     * Insert content into HEAD section of html page.
     */
    public function renderHead() {
        ?>
      <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
      
        <?php
    }
    public function renderFooter() {
        ?>
        <script src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/components-select2.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js" type="text/javascript"></script>
        
        <script type="text/javascript">
       $(document).ready(function() {
          $("#ticket_submit").click(function() {
            $("#form_action").val("save_ticket");
            $("#adminForm").submit();
        });
      });



       /*jslint unparam: true */
       /*global window, $ */
       $(function () {
        'use strict';
                // Change this to the location of your server-side upload handler:
                var url = 'multiupload.php';
                $('#fileupload').fileupload({
                    url: url,
                    dataType: 'json',
                    acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc|docx|xls|xlsx)$/i,
                    done: function (e, data) {
                        $.each(data.result.files, function (index, file) {
                            if(file.hasOwnProperty('error')){
                             $('<p style="color:#FF0000;"/>').text(file.error).appendTo('#files'); 
                         }else{
                            $('<p style="color:#36c6d3;"/>').text(file.name).appendTo('#files');
                            $('<input type="hidden" name="image_name[]" value="' + file.name + '" />').appendTo('#files');
                        }
                    });
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .progress-bar').css(
                            'width',
                            progress + '%'
                            );
                    },
                    processfail: function (e, data) {
                        alert(data.files[data.index].name + "\n" + data.files[data.index].error);
                    }
                }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('a').tooltip();
            });


            </script>
        <?php
        
        
    }
    /*     * *
     * Content View
     */
    protected function renderBody() {
      ?>
     
    <div class="main_formpage">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-list"></i>
                     <?php echo Translation::GetCaption("SUPPORT_CENTER"); ?>
                </div>  
                <div class="actions">
                </div>                  
        </div>
        <div class="portlet-body">
            <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm"  role="form">
            <?php
            if (count(ErrorList::getItem()->getErrorCount()) > 1) {
                echo '<div class="alert alert-danger">';
                ErrorList::getItem()->render();
                echo '</div>';
            }
            ?>
            <div id="success_message">
                <?php 
                if($this->success_message != '')
                {
                    echo '<div class="alert alert-success">';
                    echo $this->success_message; 
                    echo '</div>';
                }
                ?>
            </div>
            <?php 
            if($this->user->getUserType() != User::USER_TYPE_CLIENT)
              { ?>
          <div class="row">
            <div class="col-md-12">
                <p><?php echo Translation::GetCaption("IF_YOU_CANT_FIND_TEXT"); ?>.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                </div>  
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    
                </div>  
            </div>
        </div>
        <?php
    }
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                    <input required="required" class="form-control" name="subject" placeholder="<?php echo Translation::GetCaption("SUBJECT"); ?>" id="subject" type="text" value="" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("SUBJECT"); ?>" data-placement="bottom">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group"><!--data-provide="markdown"-->
                <textarea required="required"  class="form-control"  rows="7" cols="150" name="message" placeholder="<?php echo Translation::GetCaption("MESSAGE"); ?>" id="message" rel="tooltip" data-original-title="<?php echo Translation::GetCaption("MESSAGE"); ?>" data-placement="bottom"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <span> <?php echo Translation::GetCaption("SELECT_FILES"); ?> </span><i class="fa fa-upload"></i>
        </div>
        <div class="col-md-9">
                <input id="fileupload"  type="file" name="fileupload[]" multiple >
                <span class="red-18"><?php echo Translation::GetCaption("ALLOWED_FILE_TYPES"); ?>  gif, jpeg ,jpg ,png ,pdf ,doc ,docx ,xls , xlsx</span>
            <br>
            <br>
          
          
            <!-- The container for the uploaded files -->
            <div id="files" class="files"></div>
        </div>
    </div>
    <div class="row text-right" >  
        <div class="col-md-6">
            <br/>
            <input type="submit" id="ticket_submit" name="ticket_submit" class="btn btn-primary btn_save" value="<?php echo Translation::GetCaption("SUBMIT_REQUEST"); ?>" />
            <br/>
        </div>                    
    </div>                    
    <input type="hidden" name="form_action" id="form_action" enctype="multipart/form-data" value="<?php echo @$form_action; ?>"  />
    </form>
</div>

</div>  <!-- table_container -->
</div>
<?php

            $Hfilter = new HelpDeskTicketFilter(); // $sessionUser->getId()
            $Hfilter->addAddedByFilter($this->user->getId());
            $Hfilter->AddOrderByAddedDate($ascending = false);
            $ticket_list = $Hfilter->getList();
            ?>
            <div class="main_formpage">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-list"></i>
                        <?php echo Translation::GetCaption("TICKET_LIST"); ?>
                            
                        </div>  
                    <!--<div class="actions">
                        <a href="support_center.php" class="btn btn-success">
                            <?php // echo Translation::GetCaption("ADD_NEW_TICKET"); ?>
                        </a>
                    </div>-->
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12" >
                            <?php if (!empty($ticket_list)) { ?>
                            <!-- describe table filter -->
                            <!-- CONSIGNMENT TABLE -->
                            <div id='table_container'  class="main_grid2">
                                <div class="table-scrollable">
                                    <table class='table table-striped table-bordered table-advance table-hover'>
                                        <thead>
                                            <tr>
                                                <th id="col1"><?php echo Translation::GetCaption("TICKET_ID"); ?></th>
                                                <th id="col1"><?php echo Translation::GetCaption("SUBJECT"); ?></th>
                                                <th id="col1"><?php echo Translation::GetCaption("LAST_REPLIER"); ?></th>
                                                <th id="col3"><?php echo Translation::GetCaption("DEPARTMENT"); ?></th>
                                                <th id="col3"><?php echo Translation::GetCaption("STATUS"); ?></th>
                                                <th id="col2"><?php echo Translation::GetCaption("DATE"); ?></th>
                                                <!--<th id="col3" class="bg-red">Priority</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $helpDeskDataObj = "";
                                            Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
                                            $sessionUser = SessionManager::getUser();
                                            $count = 1;
                                            foreach ($ticket_list as $value) {
                                                    //Get Client ID
                                                $ClientId = $value->getAddedBy();
                                                    //Get logged IN User Data From session Class
                                                $HelpDeskTicketFilter = new HelpDeskTicketMessageFilter();
                                                $HelpDeskTicketFilter->addTicketIdFilter($value->getId());
                                                $HelpDeskTicketFilter->AddOrderByAddedDate($ascending = false);
                                                $helpDeskDataObj = $HelpDeskTicketFilter->getColumnList('addedby', '1');
                                                ?>
                                                <tr class="<?php echo $count;
                                                $count++; ?>" <?php
                                                if (is_array($helpDeskDataObj) && !empty($helpDeskDataObj)) {
                                                        //For Admin
                                                    if ($sessionUser->getUserType() == "admin" && $helpDeskDataObj[0]->getAddedBy() == $ClientId && $value->getStatus() != "close") {
                                                        echo 'style="background-color: rgba(230, 173, 176, 0.5)"';
                                                    }
                                                        //For Client
                                                    elseif ($sessionUser->getUserType() != "admin" && $helpDeskDataObj[0]->getAddedBy() != $ClientId && $value->getStatus() != "close") {
                                                        echo 'style="background-color: rgba(230, 173, 176, 0.5)"';
                                                    }
                                                }
                                                ?> >
                                                <td><a href="ticket_details.php?id=<?php echo md5($value->getId()); ?>" ><?php echo $value->getTicketCode(); ?></a></td>
                                                <td <?php if ($value->getStatus() == "close") { ?> style="text-decoration: line-through;" <?php } ?> ><?php echo $value->getSubject(); ?></td>
                                                <td>
                                                    <?php
                                                    if (is_array($helpDeskDataObj) && !empty($helpDeskDataObj)) {
                                                        $User = new CustomerAccount($helpDeskDataObj[0]->getAddedBy());
                                                        echo $User->getFirstName()." [".$User->getUserName()."]";
                                                    }
                                                    ?>  
                                                </td>
                                                <td><?php
                                                    $department = new Department($value->getDepartmentId());
                                                    echo ucwords($department->getTitle());
                                                    ?></td>
                                                    <td><?php echo ucwords(str_replace("_", " ", $value->getStatus())); ?></td>
                                                    <td><?php
                                                        $date = new DateTime();
                                                        $date->setTimestamp($value->getAddedDate());
                                                        echo $date->format('Y-m-d H:i:s');
                                                        ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>  <!-- table_container -->
                                    <?php
                                } else {
                                    echo "<label>No data Found</label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
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