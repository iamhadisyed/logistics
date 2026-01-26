<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $table_msgs;
    private $ticket_details;
    private $ticket_message;
    private $ticket_ID;

    /*     * *
     * Controller logic
     */

    protected function init() {
        $sessionUser = SessionManager::getUser();

        // If Ticket Id is Not set Return to ticket Details
        if (!isset($_GET['id']) || $_GET['id'] < 0 || $_GET['id'] == "") {
            if ($sessionUser->getUserType() == "client")
                util_redirect("support_center.php");
            else
                util_redirect("support_center_list.php");
        }
        //Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        //Get ticket Id
        $ticket_id_temp = trim($_GET['id']);

        $HelpDeskTicket = new HelpDeskTicketFilter();
        $HelpDeskTicket->addTicketIdMd5Filter($ticket_id_temp);
        $HelpDeskTicketObj = $HelpDeskTicket->getColumnList('id');

        if (count($HelpDeskTicketObj) > 0) {
            $ticket_id = $HelpDeskTicketObj[0]->getId();
            $this->ticket_ID = $ticket_id;
        } else {
            if ($sessionUser->getUserType() == "client")
                util_redirect("support_center.php");
            else
                util_redirect("support_center_list.php");
        }


        //Close Ticket Handler
        if (isset($_GET['action']) && $_GET['action'] == "close_ticket" && isset($_GET['id']) && $ticket_id > 0) {

            $HelpDeskTicket = new HelpDeskTicket($ticket_id);
            $HelpDeskTicket->setStatus("close");
            $HelpDeskTicket->setUpdatedBy($sessionUser->getId());
            $HelpDeskTicket->setUpdatedDate(date("Y-m-d H:i:s"));
            $HelpDeskTicket->save();
            if ($sessionUser->getUserType() == "client")
                util_redirect("support_center.php");
            else
                util_redirect("support_center_list.php");
            die;
        }
        if (isset($_GET['action']) && $_GET['action'] == "reopen_ticket" && isset($_GET['id']) && $ticket_id > 0) {
            $HelpDeskTicket = new HelpDeskTicket($ticket_id);
            $HelpDeskTicket->setStatus("open");
            $HelpDeskTicket->setUpdatedBy($sessionUser->getId());
            $HelpDeskTicket->setUpdatedDate(date("Y-m-d H:i:s"));
            $HelpDeskTicket->save();
            util_redirect("ticket_details.php?id=" . $_POST['id']);
            die;
        }



        //If Form Submit 
        if (isset($this->form_vars["form_action"])) {
            $image_name_str = "";
            $image_name_str = implode(', ', $_POST['image_name']);
            $dataArray = array(
                'message' => $this->form_vars["message"],
                'attachment' => $image_name_str,
                'addedby' => $sessionUser->getId(),
                'added_date' => date("Y-m-d H:i:s"),
                'ticketid' => $this->form_vars["t_id"]
            );
            $HelpDeskMessage = new HelpDeskTicketMessage($dataArray);
            $HelpDeskMessage->save();
            if ($sessionUser->getUserType() == "admin") {
//                admin reply to client
                //Get Ticket Generator First
                $DepartmentFilter = new DepartmentFilter();
                $DepartmentData = $DepartmentFilter->getTicketGenerator($this->form_vars["t_id"]);
                $DepartmentData = $DepartmentData[0];
                $DepartmentHeadName = $DepartmentData->getTitle();
                $DepartmentHeadEmail = $DepartmentData->getDescription();
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
                <td><a href='" . SETTING_MAIN_URL_ACCOUNT . "main/ticket_details.php?id=" . $_GET['id'] . "'>Click Here for more details</a></td>
                </tr>
                </tbody>
                </table>
                <br><br>Thanks , <br> One World Express";
                $subject = "RE: " . $this->form_vars["ticket_subject"];
                $headers = "From: itsupport@oneworldexpress.com \r\n";
                $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($DepartmentHeadEmail, $subject, $message, $headers);
            } else {
//                client reply to admin
                //Get Department Head First
                $DepartmentFilter = new DepartmentFilter();
                $DepartmentData = $DepartmentFilter->getDeparmentHead($this->form_vars["department_id"]);
                $DepartmentData = $DepartmentData[0];
                $DepartmentHeadName = $DepartmentData->getTitle();
                $DepartmentHeadEmail = $DepartmentData->getDescription();
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
                <td><a href='http://oneworldexpress.co.uk/remote/main/ticket_details.php?id=" . $_GET['id'] . "'>Click Here for more details</a></td>
                </tr>
                </tbody>
                </table>
                <br><br>Thanks , <br> One World Express";
                $subject = "RE: " . $this->form_vars["ticket_subject"];
                $headers = "From: itsupport@oneworldexpress.com \r\n";
                $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($DepartmentHeadEmail, $subject, $message, $headers);
            }
            if ($sessionUser->getUserType() == "client")
                util_redirect("support_center.php?id=" . $_GET['id'] . "msg=ok");
            else
                util_redirect("support_center_list.php?id=" . $_GET['id'] . "msg=ok");
        }

//        $this->table_msg = "Invalid Command";
        // common initialisation for ths page
        $this->setTitle("Ticket Details");



        //Get details of ticket for Admin
        if ($sessionUser->getUserType() == "admin") {

            $UserDepartmentFilter = new UserDepartmentFilter();
            $UserDepartmentFilter->addByUserId($sessionUser->getId());
            $department_list = $UserDepartmentFilter->getList();
            $department_str = "";
            foreach ($department_list as $value) {
                $department_str .= $value->getDepartmentId() . ",";
            }
            $department_str = rtrim($department_str, ',');


            $HelpDeskTicketFilter = new HelpDeskTicketFilter();
            $HelpDeskTicketFilter->addTicketIdFilter($this->ticket_ID);
            // $HelpDeskTicketFilter->addDepartmentInByFilter($department_str);
            $this->ticket_details = $HelpDeskTicketFilter->getList();
        } else {
            //Get details of ticket for clients
            $HelpDeskTicketFilter = new HelpDeskTicketFilter();
            $HelpDeskTicketFilter->addTicketIdFilter($ticket_id);
            $HelpDeskTicketFilter->addAddedByFilter($sessionUser->getId());
            $this->ticket_details = $HelpDeskTicketFilter->getList();
        }
        if (empty($this->ticket_details)) {
            if ($sessionUser->getUserType() == "client")
                util_redirect("support_center.php");
            else
                util_redirect("support_center_list.php");
        }
        //Get details for ticket Messages
        $ticketMessages = new HelpDeskTicketMessageFilter();
        $ticketMessages->addTicketIdFilter($ticket_id);
        $this->ticket_message = $ticketMessages->getList();
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />

        <script src="../assets/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js" type="text/javascript"></script>
        <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js" type="text/javascript"></script>



        <script src="../assets/global/plugins/bootstrap-markdown/lib/markdown.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#btnShowReply").click(function () {
                    $("#hide_reply").show();
                    $("#ticket_reply").show();
                    $("#btnShowReply").hide();
                });

                $("#close_ticket").click(function () {
                    var cnfrm = "";
                    if ($('#close_ticket').is(':checked')) {
                        cnfrm = confirm('Are you sure you want to close this ticket?');
                        if (cnfrm != true)
                        {
                            var two = $("#close_ticket").attr('checked', false);
                            $.uniform.update(two);
                            return false;
                        } else {
                            window.location.replace('ticket_details.php?id=<?php echo urlencode(md5($this->ticket_ID)); ?>&action=close_ticket');
                        }
                    } else {
                        cnfrm = confirm('Are you sure you want to re-open this ticket?');
                        if (cnfrm != true)
                        {
                            var two = $("#close_ticket").prop('checked', true);
                            $.uniform.update(two);
                            return false;
                        } else {
                            window.location.replace("ticket_details.php?id=<?php echo urlencode(md5($this->ticket_ID)); ?>&action=reopen_ticket");
                        }
                    }

                });
            });
        </script>
        <script type="text/javascript">


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
                            $('<p/>').text(file.name).appendTo('#files');
                            $('<input type="hidden" name="image_name[]" value="' + file.name + '" />').appendTo('#files');
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
        <style type="text/css">
            .ticket_conversation{
                border:1px solid #ddd;
                display:table;
                background:#FFF;
                width:100%;
                margin:20px 0px;
                clear:both;
            }
            .ticket_conversation .ticket_user{
                background:#ededef;
                display:table-cell;
                width:224px;
                border-right:5px solid #dddddd;
                padding:10px;
                color:#3c3e43;
                font-size:20px;
            }
            .ticket_conversation .ticket_user small{
                display:block;
                color:#999;
                font-size:14px;	
            }
            .ticket_conversation .ticket_message .ticket_date{
                padding:5px 20px;
                color:#999;
                border-bottom:1px solid #eeeeee;
                font-size:14px;
            }
            .ticket_conversation .ticket_message .ticket_msg{
                padding:20px;
            }
        </style>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $dep_arr = $this->ticket_details;
        $dep_arr = $dep_arr[0];
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/ticket_details.php">Ticket Details</a></li>
        </ul>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == "ok") { ?>
            <div class="alert alert-success" >
                <span style="color:white;">  <?php echo "Your reply has been added successfully."; ?> </span> 
            </div>
        <?php } ?> 
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Ticket Details
                    </div>                    
                </div>
                <div class="portlet-body">
        <?php
        if (count(ErrorList::getItem()->getErrorCount()) > 1) {

            echo '<div class="alert alert-danger">';
            ErrorList::getItem()->render();
            echo '</div>';
        }
        ?>
                    <div class="row">                        
                        <div class="col-md-12">
                            <h3><b><?php echo "#" . $dep_arr->getTicketCode(); ?></b></h3>
                            <hr>
                        </div>
                    </div>
                    <div class="row">                        
                        <div class="col-md-8">
                            <b><?php echo ucwords($dep_arr->getSubject()); ?></b> 
                        </div>
                        <div class="col-md-4 text-right">
                            <label class="control-label"><input class="form-control" type="checkbox" name="close_ticket" id="close_ticket" data-original-title="Ticket" title="Ticket"<?php echo ($dep_arr->getStatus() == "close" ? ' checked="checked"' : ''); ?> /> <?php echo ($dep_arr->getStatus() == "close" ? 'Re-Open Ticket' : 'Close Ticket'); ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="table_container" class="main_grid2">
                                <div class="table-scrollable">
                                    <table class="table table-striped table-bordered table-advance table-hover">
                                        <thead>
                                            <tr>
                                                <th id="col1" class="bg-red">DEPARTMENT </th>
                                                <th id="col2" class="bg-red">STATUS </th>
                                                <th id="col3" class="bg-red">PRIORITY </th>
                                            </tr>
                                        </thead>
                                        <tbody>
        <?php
        $department = new Department($dep_arr->getDepartmentId());
        $department_title = $department->getTitle();
        ?>
                                            <tr>
                                                <td><?php echo ucwords($department_title); ?></td>
                                                <td><?php echo ucwords($dep_arr->getStatus()); ?></td>
                                                <td <?php
                                    if ($dep_arr->getPriority() == "low") {
                                        echo 'style="color:#8A8A8A"';
                                    } elseif ($dep_arr->getPriority() == "medium") {
                                        echo 'style="color:#000000"';
                                    } elseif ($dep_arr->getPriority() == "high") {
                                        echo 'style="color:#F07D18"';
                                    } elseif ($dep_arr->getPriority() == "urgent") {
                                        echo 'style="color:#E826C6"';
                                    } elseif ($dep_arr->getPriority() == "critical") {
                                        echo 'style="color:#FF0000"';
                                    }
        ?> ><?php echo ucwords($dep_arr->getPriority()); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="department_id" value="<?php echo $department->getId(); ?>" />
                                    <input type="hidden" name="ticket_subject" value="<?php echo ucwords($dep_arr->getSubject()); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
        <?php foreach ($this->ticket_message as $value) { ?>

                                <div class="ticket_conversation ">
                                    <div class="ticket_user" <?php if ($sessionUser->getId() == $value->getAddedBy()) { ?>style="border-right:5px solid #004E87;" <?php } ?>>
                                <?php
                                $user_id = $value->getAddedby();
                                $User = new CustomerAccount($user_id);
                                echo $User->getFirstName() . " [" . $User->getUserName() . "]";
                                ?> 
                                        <small><?php
                                        $date = new DateTime();
                                        $date->setTimestamp($value->getAddedDate());
                                        echo $date->format('Y-m-d H:i:s');
                                        ?></small>
                                    </div>
                                    <div class="ticket_message">
                                        <div class="ticket_msg">
                                            <?php
                                            $Parsedown = new Parsedown();
                                            echo $Parsedown->text($value->getMessage());
                                            $file_name = "";
                                            $file_name_arr = "";
                                            $file_ext = "";
                                            $file_name = $value->getAttachment();
                                            $file_name_arr = explode(",", $file_name);
                                            if (!empty($file_name)) {
                                                ?>
                                                <hr>
                                                <div class="row">
                                                <?php
                                                foreach ($file_name_arr as $fileName) {
                                                    if (file_exists("files/" . trim($fileName))) {
                                                        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
                                                        ?>
                                                            <div class="col-md-4">
                                                                <a target="_blank" href="files/<?php echo trim($fileName); ?>">
                                                                    <img src="files/file_icon/<?php echo $file_ext . ".png"; ?>" alt="image icon" class="pull-left margin-right-10" /> <span style="vertical-align: bottom;"><?php echo $fileName; ?></span>
                                                                </a>
                                                            </div>
                        <?php
                    }
                }
                ?>
                                                </div>
                                                    <?php
                                                }
                                                ?> 
                                        </div>
                                    </div>
                                </div>
                                        <?php } ?> 

                        </div>
                    </div>
                    <div class="row" id="hide_reply" style="display: none;">
                        <div class="col-md-12">
                            <hr>
                            <div class="form-group">
                                <textarea required="required" data-provide="markdown" class="form-control"  rows="7" cols="150" name="message" placeholder="Message" id="message" rel="tooltip" data-original-title="Message" data-placement="bottom"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <span class="btn btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>Select files...</span>
                                <!-- The file input field used as target for the file upload widget -->
                                <input id="fileupload" type="file" name="files[]" multiple>
                            </span>
                            <br>
                            <br>
                            <!-- The global progress bar -->
                            <div id="progress" class="progress">
                                <div class="progress-bar progress-bar-success"></div>
                            </div>
                            <!-- The container for the uploaded files -->
                            <div id="files" class="files"></div>
                        </div>
                    </div>
                    <div class="row text-right"> 
                        <div class="col-md-12">
                            <? if ($sessionUser->getUserType() == "client") { ?>
                            <a  href="support_center.php" class="btn btn-info"><em class="fa fa-arrow-left"></em> Back</a>
                            <? } else { ?>
                            <a  href="support_center_list.php" class="btn btn-info"><em class="fa fa-arrow-left"></em> Back</a>
                            <? } ?>
        <?php if ($dep_arr->getStatus() != "close") { ?>
                                <a id="btnShowReply" href="javascript:;" class="btn btn-primary btn_save"><em class="fa fa-reply"></em> Add Reply</a>
        <?php } ?>
                            <input type="submit" name="ticket_reply" id="ticket_reply" style="display: none;" class="btn btn-primary btn_save" value="Save Reply" />
                        </div>
                    </div>
                </div>
                <input type="hidden" name="t_id" id="t_id" value="<?php echo $this->ticket_ID; ?>"  />
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
