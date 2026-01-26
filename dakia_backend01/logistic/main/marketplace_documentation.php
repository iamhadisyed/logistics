<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'marketplaces.class',
    'marketplacesfilter.class',
    'marketplacedocumentationmapping.class',
    'marketplacedocumentationmappingfilter.class',
]);

class Page extends BasePage
{
    public $user;
    public $userAccount;
    public $marketplace_documentation_title;
    public $documentation_cover_image;
    public $stepsData;
    public $marketplace_id;

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Add/Update Marketplace documentation"
        );
        /*
         * Add Consignment Data Logic
         */
        if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "save_data_marketplace_documentation") {
            $error_array = [];
            if(!empty($this->form_vars['marketplace_id'])) {
                $marketplaceObj = new MarketPlaces($this->form_vars['marketplace_id']);
                $marketplaceObj->setDocumentationTitle($this->form_vars['title']);
                if (isset($_FILES["cover_photo"]) && !empty(trim($_FILES["cover_photo"]["name"]))) {
                    $newPath = "../images/marketplace_documentation/";
                    if (!file_exists($newPath))
                        @mkdir($newPath, 0775);

                    $allowedExts = array("gif", "jpeg", "jpg", "png");
                    $temp = explode(".", $_FILES["cover_photo"]["name"]);
                    $extension = end($temp);
                    if ((($_FILES["cover_photo"]["type"] == "image/gif") || ($_FILES["cover_photo"]["type"] == "image/jpeg") || ($_FILES["cover_photo"]["type"] == "image/jpg") || ($_FILES["cover_photo"]["type"] == "image/pjpeg") || ($_FILES["cover_photo"]["type"] == "image/x-png") || ($_FILES["cover_photo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                        if ($_FILES["cover_photo"]["error"] > 0) {
                            $error_array[] = "Return Code: " . $_FILES["cover_photo"]["error"] . "<br>";
                        } else {
                            $uploadLogoName = str_replace(' ', '_', time() . $_FILES["cover_photo"]["name"]);
                            move_uploaded_file($_FILES["cover_photo"]["tmp_name"], "../images/marketplace_documentation/" . $uploadLogoName);
                            $marketplaceObj->setDocumentationCoverImage($uploadLogoName);
                        }
                    } else {
                        $error_array[] = formatMessages(ERROR_INVALID_FILE);
                    }
                }
                $marketplaceObj->save();

                if (!empty($this->form_vars["steps"])) {
                    foreach ($this->form_vars["steps"] as $key => $step) {
                        if (!empty($step['step_id'])) {
                            $marketplaceDocumantationObj = new MarketPlaceDocumentationMapping($step['step_id']);
                        } else {
                            $marketplaceDocumantationObj = new MarketPlaceDocumentationMapping();
                            $marketplaceDocumantationObj->setAddedBy($this->user->getId());
                        }
                        $marketplaceDocumantationObj->setMarketplaceId($this->form_vars['marketplace_id']);
                        $marketplaceDocumantationObj->setStepTitle($step['step_title']);
                        $marketplaceDocumantationObj->setStepDescription($step['step_description']);
                        $marketplaceDocumantationObj->setStepOrder($step['step_order']);
                        $marketplaceDocumantationObj->setUpdatedBy($this->user->getId());
                        $image_file_name = 'step_image_' . $key;
                        if (isset($_FILES[$image_file_name]) && !empty(trim($_FILES[$image_file_name]["name"]))) {
                            $newPath = "../images/marketplace_documentation/";
                            if (!file_exists($newPath))
                                @mkdir($newPath, 0775);

                            $allowedExts = array("gif", "jpeg", "jpg", "png");
                            $temp = explode(".", $_FILES[$image_file_name]["name"]);
                            $extension = end($temp);
                            if ((($_FILES[$image_file_name]["type"] == "image/gif") || ($_FILES[$image_file_name]["type"] == "image/jpeg") || ($_FILES[$image_file_name]["type"] == "image/jpg") || ($_FILES[$image_file_name]["type"] == "image/pjpeg") || ($_FILES[$image_file_name]["type"] == "image/x-png") || ($_FILES[$image_file_name]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                                if ($_FILES[$image_file_name]["error"] > 0) {
                                    $error_array[] = "Return Code: " . $_FILES[$image_file_name]["error"] . "<br>";
                                } else {
                                    $uploadLogoName = str_replace(' ', '_', time() . $_FILES[$image_file_name]["name"]);
                                    move_uploaded_file($_FILES[$image_file_name]["tmp_name"], "../images/marketplace_documentation/" . $uploadLogoName);
                                    $marketplaceDocumantationObj->setStepImage($uploadLogoName);
                                }
                            } else {
                                $error_array[] = formatMessages(ERROR_INVALID_FILE);
                            }
                        }
//                    $marketplaceDocumantationObj->setStepImage($step);
                        $marketplaceDocumantationObj->save();

                    }
                    if (empty($error_array)) {
                        $this->flashMsg->success("Data saved Successfully.");
                    } else {
                        if (!empty($error_array)) {
                            foreach ($error_array as $error) {
                                $this->flashMsg->error($error);
                            }
                        }
                    }
                }
            } else {
                $this->flashMsg->error('Something went wrong.');
            }
        }
        // Edit consignment case
        $mp_id = util_get("mp");
        $mpObj = new MarketPlacesFilter();
        $mpObj->addFieldFilter('        plugin_key', $mp_id);
        $mpObj = $mpObj->getList();
        if(!empty($mpObj)) {
            $marketplaceObj = $mpObj[0];
            $this->marketplace_id = $marketplaceObj->getId();
            $marketplaceDocumantationObj = new MarketPlaceDocumentationMappingFilter();
            $marketplaceDocumantationObj->addFilter('marketplace_id = ' . $this->marketplace_id);
            $this->stepsData = $marketplaceDocumantationObj->getList();
            $this->marketplace_documentation_title = $marketplaceObj->getDocumentationTitle();
            $this->documentation_cover_image = $marketplaceObj->getDocumentationCoverImage();
        }
    }

    protected function addPagelavelCss()
    {
        ?>
        <link rel="stylesheet" type="text/css"
              href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet"
              type="text/css"/>
        <style type="text/css">
            #map {
                width: 100%;
                height: 600px;
            }
        </style>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('span').tooltip();
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add-more-step-keys', function () {
                    elindex++;
                    var clone = $(this).parent().parent().parent().clone();
                    var stepTitle = $(clone).find('.step_title').attr('name');
                    var stepDescription = $(clone).find('.step_description').attr('name');
                    var stepOrder = $(clone).find('.step_order').attr('name');
                    var stepImage = $(clone).find('.step_image').attr('name');
                    var id = $(clone).find('.step_id').attr('name');
                    $(this).remove();
                    $(clone).find('input').val('');
                    $(clone).find('.step_title').val('');
                    $(clone).find('.step_title').attr('name', stepTitle.replace(/\d+/, elindex));
                    $(clone).find('.step_description').val('');
                    $(clone).find('.step_description').attr('name', stepDescription.replace(/\d+/, elindex));
                    $(clone).find('.step_order').val();
                    $(clone).find('.step_order').attr('name', stepOrder.replace(/\d+/, elindex));
                    $(clone).find('.step_image').val('');
                    $(clone).find('.step_image').attr('name', 'step_image_' + elindex);
                    $(clone).find('.step_id').attr('name', id.replace(/\d+/, elindex));
                    $(clone).find('button.remove-step-key').show();
                    $(clone).find('button.remove-step-key').removeClass('initial-button');
                    $(clone).appendTo($('.parcel_container'));
                });
                $(document).on('click', '.remove-step-key', function () {
                    var el = $(this);
                    swal({
                            title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                            text: "",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                if ($('.parcel_container .remove-step-key').length > 1) {
                                    $(el).parent().parent().remove();
                                    if ($('.add-more-step-keys').length == 0) {
                                        var addMore = $(el).parent().find('.add-more-step-keys').clone();
                                        $('.parcel_container .remove-step-key').last().parent().prepend(addMore);
                                    }
                                    if ($(".parcel_weight").length == 1) {
                                        $(".parcel_container .remove-parcel-key").hide();
                                    }
                                } else {
                                    $(el).parent().parent().find('input').val('');
                                }
                            }
                        });
                });
            });
        </script>
        <?php
    }

    protected function renderBody()
    {
        extract($this->form_vars);
        if ($this->form_vars["id"] > 0) {
            if (!empty($this->consignmentEditError) && $this->consignmentStatus == consignment::STATUS_INVALID) {
                $this->flashMsg->error($this->consignmentEditError);
            }
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-money"></i>
                    Add/Update marketplace documentation
                </div>
                <div class="actions">
					<a class="btn blue" href="market_place_manage.php">Back</a>
                </div>
                <div class="tools">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <div class="row" id="show_general_msg" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-danger"></div>
                    </div>
                </div>
                <form name="marketplace_documentation_save" id="marketplace_documentation_save" action="" method="post"
                      enctype="multipart/form-data">
                    <div class="caption margin-bottom-10 block">
                        Documentation Heading
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="has-float-label input-icon right">
                                    <i class="fa fa-text-width" aria-hidden="true"></i>

                                    <textarea type="text" name="title" id="title" class="form-control" rows="8"
                                              placeholder="Title"><?php echo !empty($this->marketplace_documentation_title) ? $this->marketplace_documentation_title : ''; ?></textarea>
                                    <label for="receiver_company">Documentation Title</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="hidden" id="integration_logo" name="integration_logo"
                                           value="<?php echo !empty($this->documentation_cover_image) ? $this->documentation_cover_image : ''; ?>">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail image-preview"
                                             style="width: 200px; height: 150px;">
                                            <?php
                                            if (trim($this->documentation_cover_image) != '' && file_exists('../images/marketplace_documentation/' . $this->documentation_cover_image)) {
                                                echo '<img src="../images/marketplace_documentation/' . $this->documentation_cover_image . '" >';
                                            } else {
                                                echo '<img src = "../images/No-image-found.jpg">';
                                            }
                                            ?>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                             style="max-width: 200px; max-height: 150px;"></div>
                                        <div>
                                                                    <span class="btn default btn-file"> <span
                                                                                class="fileinput-new"> Select image </span> <span
                                                                                class="fileinput-exists"> Change </span>
                                                                        <input id="cover_photo" type="file"
                                                                               name="cover_photo">
                                                                    </span> <a href="javascript:;"
                                                                               class="btn red fileinput-exists"
                                                                               data-dismiss="fileinput"> Remove </a>
                                        </div>
                                    </div>
                                    <div class="clearfix margin-top-10"><span
                                                class="label label-primary"><small>NOTE!</span>
                                        Recommended logo dimensions (1288 x 234) </small></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="caption margin-bottom-10 block">
                        Steps Details
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="parcel_container">
                                <?php
                                $totalSteps = count($this->stepsData);
                                if ($totalSteps > 0 && $this->marketplace_id > 0) {
                                    $countStep = 0;
                                    foreach ($this->stepsData as $stepsDatum) {
                                        ?>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <div class="has-float-label input-icon right">
                                                                    <i class="fa fa-text-width" aria-hidden="true"></i>

                                                                    <input type="text"
                                                                           name="steps[<?php echo $countStep; ?>][step_title]"
                                                                           class="form-control step_title"
                                                                           value="<?php echo $stepsDatum->getStepTitle(); ?>"
                                                                           placeholder="Step Title"/>
                                                                    <label for="receiver_company">Step Title</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <div class="has-float-label input-icon right">
                                                                    <i class="fa fa-file-text"></i>

                                                                    <textarea
                                                                            name="steps[<?php echo $countStep; ?>][step_description]"
                                                                            class="form-control step_description"
                                                                            value="" cols="10" rows="8"
                                                                            placeholder="Step Description"><?php echo $stepsDatum->getStepDescription(); ?></textarea>
                                                                    <label for="step_description">Step
                                                                        Description</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="has-float-label input-icon right">
                                                                <i class="fa fa-step-forward"></i>

                                                                <input type="text"
                                                                       name="steps[<?php echo $countStep; ?>][step_order]"
                                                                       class="form-control step_order"
                                                                       value="<?php echo $stepsDatum->getStepOrder(); ?>"
                                                                       placeholder="Step Order"/>
                                                                <label for="step_description">Step Order</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <input type="hidden" id="integration_logo"
                                                                           name="integration_logo" class="step_image"
                                                                           value="<?php echo $stepsDatum->getStepImage(); ?>">
                                                                    <label for="integration_logo"> Upload Step
                                                                        Image</label>
                                                                    <br clear="all">
                                                                    <div class="fileinput fileinput-new"
                                                                         data-provides="fileinput">
                                                                        <div class="fileinput-new thumbnail image-preview"
                                                                             style="width: 200px; height: 150px;">
                                                                            <?php
                                                                            if (trim($stepsDatum->getStepImage()) != '' && file_exists('../images/marketplace_documentation/' . $stepsDatum->getStepImage())) {
                                                                                echo '<img src="../images/marketplace_documentation/' . $stepsDatum->getStepImage() . '" >';
                                                                            } else {
                                                                                echo '<img src = "../images/No-image-found.jpg">';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                                             style="max-width: 200px; max-height: 150px;"></div>
                                                                        <div>
                                                                    <span class="btn default btn-file"> <span
                                                                                class="fileinput-new"> Select image </span> <span
                                                                                class="fileinput-exists"> Change </span>
                                                                        <input id="your_logo" type="file"
                                                                               class="step_image"
                                                                               name="step_image_<?php echo $countStep; ?>">
                                                                    </span> <a href="javascript:;"
                                                                               class="btn red fileinput-exists"
                                                                               data-dismiss="fileinput"> Remove </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="clearfix margin-top-10"><span
                                                                                class="label label-primary"><small>NOTE!</span>
                                                                        Recommended logo dimensions (550 x 400) </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <button type="button" class="btn btn-success add-more-step-keys"><i
                                                                class="fa fa-plus"></i></button>
                                                    <?php if ($countStep + 1 == $totalSteps): ?>
                                                        <button type="button"
                                                                class="btn btn-danger remove-step-key"><i
                                                                    class="fa fa-minus"></i></button>
                                                    <?php endif; ?>
                                                    <input type="hidden" class="step_id"
                                                           name="steps[<?php echo $countStep; ?>][step_id]"
                                                           value="<?php echo $stepsDatum->getId(); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $countStep++;
                                    }
                                } else {
                                    ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <div class="has-float-label input-icon right">
                                                                <i class="fa fa-text-width" aria-hidden="true"></i>

                                                                <input type="text" name="steps[0][step_title]"
                                                                       class="form-control step_title"
                                                                       value=""
                                                                       placeholder="Step Title"/>
                                                                <label for="receiver_company">Step Title</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <div class="has-float-label input-icon right">
                                                                <i class="fa fa-text-height"></i>

                                                                <textarea name="steps[0][step_description]"
                                                                          class="form-control step_description"
                                                                          value="" cols="10" rows="8"
                                                                          placeholder="Step Description"></textarea>
                                                                <label for="step_description">Step Description</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="has-float-label input-icon right">
                                                            <i class="fa fa-step-forward"></i>

                                                            <input type="text"
                                                                   name="steps[0][step_order]"
                                                                   class="form-control step_order"
                                                                   value=""
                                                                   placeholder="Step Order"/>
                                                            <label for="step_description">Step Order</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <input type="hidden" id="integration_logo"
                                                                       name="integration_logo" class="step_image"
                                                                       value="<?php echo $logo; ?>">
                                                                <div class="fileinput fileinput-new"
                                                                     data-provides="fileinput">
                                                                    <div class="fileinput-new thumbnail image-preview"
                                                                         style="width: 200px; height: 150px;">
                                                                        <?php
                                                                        if (trim($logo) != '' && file_exists('../images/' . $logo)) {
                                                                            echo '<img src="../images/' . $logo . '" >';
                                                                        } else {
                                                                            echo '<img src = "../images/No-image-found.jpg">';
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                                                         style="max-width: 200px; max-height: 150px;"></div>
                                                                    <div>
                                                                    <span class="btn default btn-file"> <span
                                                                                class="fileinput-new"> Select image </span> <span
                                                                                class="fileinput-exists"> Change </span>
                                                                        <input id="step_image" type="file"
                                                                               class="step_image"
                                                                               name="step_image_0">
                                                                    </span> <a href="javascript:;"
                                                                               class="btn red fileinput-exists"
                                                                               data-dismiss="fileinput"> Remove </a>
                                                                    </div>
                                                                </div>
                                                                <div class="clearfix margin-top-10"><span
                                                                            class="label label-primary"><small>NOTE!</span>
                                                                    Recommended logo dimensions (550 x 400) </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-success add-more-step-keys"><i
                                                            class="fa fa-plus"></i></button>
                                                <button type="button"
                                                        class="btn btn-danger remove-step-key"><i
                                                            class="fa fa-minus"></i></button>
                                                <input type="hidden" class="step_id" name="steps[0][id]" value="">
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <input type="hidden" name="elindex-hardcode" id="elindex-hardcode"
                                       value="<?php echo count($this->stepsData); ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="row margin-top-20">
                        <div class="col-md-12 text-center">
                            <button type="submit" name="btnSave" id="btnSave"
                                    class="btn btn-primary btn_save">Update
                            </button>
                            <input type="hidden" name="func" value="save_data_marketplace_documentation"/>
                            <input type="hidden" name="marketplace_id"
                                   value="<?php echo !empty($this->marketplace_id) ? $this->marketplace_id : 0; ?>"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }
}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
