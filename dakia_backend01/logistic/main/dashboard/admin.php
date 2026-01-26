<!-- END PAGE BREADCRUMB -->
<!-- BEGIN PAGE BASE CONTENT -->
<div class="row">
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase font-dark">Shipment Statistics</span>
                    <!--<span class="caption-helper">distance stats</span>-->
                </div>
                <div class="actions">
                    <!--<a class="btn btn-circle btn-icon-only btn-default" href="#">
                        <i class="icon-wrench"></i>
                    </a>-->
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_shipment_stats_chart" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption ">
                    <span class="caption-subject font-dark bold uppercase">Top Countries</span>
                    <span class="caption-helper">Exclude Ready To Print, Recycled, Valid</span>
                </div>
                <div class="actions">
                    <!--<a class="btn btn-circle btn-icon-only btn-default" href="#">
                        <i class="icon-wrench"></i>
                    </a>-->
                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="dashboard_top_countries_chart" class="CSSAnimationChart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php

    if (isset($_SESSION['menu-option']) && $_SESSION['menu-option'] != '')
        $displayOption = @$_SESSION['menu-option'];
    else
        $displayOption = $Sessionuser->getUserType();
    Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
    $sessionUser = SessionManager::getUser();
    $data_return = "";
    $department_list = "";
    if ($sessionUser->getUserType() == "admin") {
        $UserDepartmentFilter = new UserDepartmentFilter();
        $UserDepartmentFilter->addByUserId($sessionUser->getId());
        $department_list = $UserDepartmentFilter->getList();
        $department_str = "";
        if (is_array($department_list) && !empty($department_list)) {
            foreach ($department_list as $value) {
                $department_str .= $value->getDepartmentId() . ",";
            }
            $department_str = rtrim($department_str, ',');
            $HelpDeskTicketFilter = new HelpDeskTicketFilter();
            $data_return = $HelpDeskTicketFilter->getUnReadMessageForAdmin($department_str);
        }
    } elseif ($sessionUser->getUserType() != "admin") {
        $HelpDeskTicketFilter = new HelpDeskTicketFilter();
        $data_return = $HelpDeskTicketFilter->getUnReadMessageForClient($sessionUser->getId());
    }
    if (is_array($data_return) && !empty($data_return)) {
        $data_return = array_slice($data_return, 0, 5);
    }
    if (!empty($data_return)) {
        ?>
        <div class="col-lg-6 col-xs-12 col-sm-12">
            <div class="portlet light bordered">
                <div class="portlet-title tabbable-line">
                    <div class="caption">
                        <i class="icon-bubbles font-dark hide"></i>
                        <span class="caption-subject font-dark bold uppercase"><?php echo Translation::GetCaption("TICKET"); ?></span>
                        <span class="caption-helper"><?php echo Translation::GetCaption("NEED_RESPONSE"); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_comments_1">
                            <!-- BEGIN: Comments -->
                            <div class="mt-comments">
                                <?php foreach ($data_return as $value) {
                                    $userObj = new CustomerAccount($value->getAddedBy());
                                    $fullName = $userObj->getFirstName();
                                    $profileImage = "../assets/pages/media/profile/".$userObj->getProfileImage();
                                    if (!file_exists($profileImage)) {
                                        $profileImage = "../assets/pages/media/profile/avatar.png";
                                    }
                                    ?>
                                    <div class="mt-comment">
                                        <div class="mt-comment-img">
                                            <img src="<?php echo $profileImage;?>" />
                                        </div>
                                        <div class="mt-comment-body">
                                            <div class="mt-comment-info">
                                                <span class="mt-comment-author"><?php echo $fullName; ?></span>
                                        <span class="mt-comment-date">
                                        <?php
                                        echo $date = date("j M, g:i A", $value->getAddedDate());
                                        ?>
                                        </span>
                                            </div>
                                            <div class="mt-comment-text"> <?php echo $value->getSubject(); ?> </div>
                                            <div class="mt-comment-details">
                                                <span class="mt-comment-status mt-comment-status-pending"><?php echo ucwords($value->getPriority()); ?></span>
                                                <ul class="mt-comment-actions">
                                                    <li>
                                                        <a href="ticket_details.php?id=<?php echo md5($value->getId()); ?>" >View</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- END: Comments -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>
<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo Translation::GetCaption("MODIFY"); ?> | <?php echo Translation::GetCaption("CHANGE_MY_EMAIL"); ?> | <?php echo Translation::GetCaption("CHANGE_MY_PASSWORD"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="tab-pane" id="tab_1">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-gift"></i><?php echo Translation::GetCaption("FORM_SAMPLE"); ?>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"></a>
                                <!--<a href="#portlet-config" data-toggle="modal" class="config">
                                </a>
                                -->
                                <a href="javascript:;" class="reload"></a>
                                <a href="javascript:;" class="remove">
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="#" class="horizontal-form">
                                <div class="form-body">
                                    <h3 class="form-section"><?php echo Translation::GetCaption("PERSON_INFO"); ?></h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo Translation::GetCaption("FIRST_NAME"); ?></label>
                                                <input type="text" id="firstName" class="form-control" placeholder="Chee Kin">
                                                <span class="help-block">
                                                    This is inline help </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group has-error">
                                                <label class="control-label">Last Name</label>
                                                <input type="text" id="lastName" class="form-control" placeholder="Lim">
                                                <span class="help-block">
                                                    This field has error. </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Gender</label>
                                                <select class="form-control">
                                                    <option value="">Male</option>
                                                    <option value="">Female</option>
                                                </select>
                                                <span class="help-block">
                                                    Select your gender </span>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Date of Birth</label>
                                                <input type="text" class="form-control" placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Category</label>
                                                <select class="select2_category form-control" data-placeholder="Choose a Category" tabindex="1">
                                                    <option value="Category 1">Category 1</option>
                                                    <option value="Category 2">Category 2</option>
                                                    <option value="Category 3">Category 5</option>
                                                    <option value="Category 4">Category 4</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Membership</label>
                                                <div class="radio-list">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked> Option 1 </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2"> Option 2 </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <h3 class="form-section">Address</h3>
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <div class="form-group">
                                                <label>Street</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>City</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>State</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                    <!--/row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Post Code</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>
                                        <!--/span-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select class="form-control">
                                                </select>
                                            </div>
                                        </div>
                                        <!--/span-->
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <button type="button" class="btn default">Cancel</button>
                                    <button type="submit" class="btn blue"><i class="fa fa-check"></i> Save</button>
                                </div>
                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn blue">Save changes</button>
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
