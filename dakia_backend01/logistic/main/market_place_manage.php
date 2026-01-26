<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'marketplaces.class',
    'marketplacesfilter.class',
    'usermarketplacesmappingfilter.class',
    'usermarketplacesmapping.class',
]);

class Page extends BasePage
{

    private $user;

    /*     * *
     * Controller logic
     */

    protected function init()
    {
        // Session check
        $this->user = SessionManager::getUser();
        //BreadCrum
        if (isset($_POST['action']) && trim($_POST['action']) == "update_market_place") {
            $data = $this->form_vars;
            $marketPlace = new MarketPlaces($data['market_place_id']);
            $marketPlace->setTitle($data['title']);
            $marketPlace->setPluginKey($data['plugin_key']);
            $marketPlace->setClassName($data['class_name']);
            $marketPlace->setHelpDoc($data['help_doc']);
            if (isset($data['is_featured']) && !empty($data['is_featured'])) {
                $marketPlace->setIsFeatured(1);
            } else {
                $marketPlace->setIsFeatured(0);
            }
            if (isset($_FILES["your_logo"]) && !empty(trim($_FILES["your_logo"]["name"]))) {
                $newPath = "../images/thirdparty/";
                if (!file_exists($newPath))
                    @mkdir($newPath, 0775);

                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $temp = explode(".", $_FILES["your_logo"]["name"]);
                $extension = end($temp);
                if ((($_FILES["your_logo"]["type"] == "image/gif") || ($_FILES["your_logo"]["type"] == "image/jpeg") || ($_FILES["your_logo"]["type"] == "image/jpg") || ($_FILES["your_logo"]["type"] == "image/pjpeg") || ($_FILES["your_logo"]["type"] == "image/x-png") || ($_FILES["your_logo"]["type"] == "image/png")) && in_array($extension, $allowedExts)) {
                    if ($_FILES["your_logo"]["error"] > 0) {
                        $error_array[] = "Return Code: " . $_FILES["your_logo"]["error"] . "<br>";
                    } else {
                        $uploadLogoName = str_replace(' ', '_', time() . $_FILES["your_logo"]["name"]);
                        move_uploaded_file($_FILES["your_logo"]["tmp_name"], "../images/thirdparty/" . $uploadLogoName);
                        $marketPlace->setIntegrationLogo($uploadLogoName);
                    }
                } else {
                    $error_array[] = formatMessages(ERROR_INVALID_FILE);
                }
            }
            if (empty($error_array)) {
                $marketPlace->save();
                $this->flashMsg->success("Data saved Successfully.");
            } else {
                if (!empty($error_array)) {
                    foreach ($error_array as $error) {
                        $this->flashMsg->error($error);
                    }
                }
            }
        }

        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'market_place_manage.php' => "Manage Market Places"
        );
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "market_places_list_ajax") {
            $marketPlacesFilter = new MarketPlacesFilter();
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $title = $this->form_vars['title'];
                if (!empty($title)) {
                    $marketPlacesFilter->addFilter("        mp.title = '{$title}'");
                }

                $pluginKey = $this->form_vars['plugin_key'];
                if (!empty($pluginKey)) {
                    $marketPlacesFilter->addFilter("    mp.plugin_key = '{$pluginKey}'");
                }

            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = 'ASC';
                if ($orderBy == 'true') {
                    $orderFalse = 'DESC';
                }

                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $marketPlacesFilter->OrderBy(strtolower("mp." . $dataTableColumnName), strtoupper($orderBy));
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $marketPlacesFilter->getCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $marketPlacesFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $marketPlacesFilter->setOffset($iDisplayStart);
            $marketPlacesFilter->addFilter('        is_delete = 0');
            $marketPlacesList = $marketPlacesFilter->getPagingList('*');
            $setDataArr = array();
            foreach ($marketPlacesList as $marketPlacesListObj) {
                $currentArr = array();
//                $palletCarrierGroup = new PalletCarierGroup($palletFilterObj->getPalletCarrierId());
                $currentArr['title'] = $marketPlacesListObj->getTitle();
                $currentArr['market_place_id'] = $marketPlacesListObj->getId();
                if (!empty($marketPlacesListObj->getIntegrationLogo()) && file_exists('../images/' . $marketPlacesListObj->getIntegrationLogo())) {
                    $currentArr['integration_logo'] = '<img width="50px" height="50px" src="../images/' . $marketPlacesListObj->getIntegrationLogo() . '">';
                    $currentArr['integration_logo_preview'] = $marketPlacesListObj->getIntegrationLogo();
                } else {
                    $currentArr['integration_logo'] = '';
                    $currentArr['integration_logo_preview'] = '';
                }
                $currentArr['is_active'] = $marketPlacesListObj->getIsActive();
                $currentArr['plugin_key'] = $marketPlacesListObj->getPluginKey();
                $currentArr['is_featured'] = $marketPlacesListObj->getIsFeatured();
//                $currentArr['driver_data'] = $driverData;
                $currentArr['actions'] = "<a href='javascript:void(0);' data-marketplace_json='" . json_encode($currentArr) . "' data-vehicle_id='" . $marketPlacesListObj->getId() . "' class='btn-edit-marketplace btn btn-xs blue btn-outline'><span class='fa fa-pencil'></span> </a>";
                $currentArr['actions'] .= "<a href='' class='btn btn-xs blue btn-outline' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $marketPlacesListObj->getId() . "' 
                                            data-log_name='market_places' data-toggle='modal'> <i class='fa fa-list'></i>
                                            </a>";
                $setDataArr[] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>

        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/form-icheck.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            var grid = null;
            $(document).ready(function () {
                $('#marketplace-edit-panel').hide();
                $('body').on('click', '.btn-edit-marketplace', function () {
                    $(window).scrollTop(0);
                    var marketplace_data = $(this).data("marketplace_json");
                    $("#title").val(marketplace_data.PlatformTitle);
                    $("#plugin_key").val(marketplace_data.plugin_key);
                    $("#market_place_id").val(marketplace_data.PlatformId);
                    $("#class_name").val(marketplace_data.class_name);
					$("#class_name").select2();
                    $("#help_doc").val(marketplace_data.help_doc);
                    if (marketplace_data.is_featured == 1) {
                        $('#is_featured').prop('checked', true);
                    }
                    if (marketplace_data.PlatformIntegrationLogo != '' || marketplace_data.PlatformIntegrationLogo != 'NULL') {
                        $('.image-preview').html("<img width='200px' height='200px' src='../images/thirdparty/" + marketplace_data.PlatformIntegrationLogo + "'>");
                    }
                    $('#marketplace-edit-panel').show();
                });

                $('body').trigger('click');


                $("#search_marketplace").on('keyup keypress', function (e) {
                    $('.search_marketplace_custom').each(function (e) {
                        var current = $.trim($(this).data('name')).toLowerCase();
                        var search_str = $.trim($("#search_marketplace").val()).toLowerCase();
                        if (current.indexOf(search_str) >= 0) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            });

            function show_res_msg(type, msg) {
                $("html, body").animate({scrollTop: 0}, "slow");
                $("#show_general_msg div.alert").html(" ");
                if (type == "success") {
                    $("#show_general_msg div.alert").addClass('alert-success').removeClass('alert-danger');
                } else {
                    $("#show_general_msg div.alert").addClass('alert-danger').removeClass('alert-success');
                }
                $("#show_general_msg div.alert").html(msg);
                $("#show_general_msg").show();
                setTimeout(function () {
                    $("#show_general_msg").hide();
                }, 7000);
            }
        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <div class="portlet light" id="marketplace-edit-panel">
            <div class="portlet-title">
                <div class="caption"><i class="icon-bar-chart"></i>
                    Update Market Place
                </div>
                <div class="tools"></div>
            </div>
            <form name="market_place_update_form" id="market_place_update_form" method="post"
                  enctype="multipart/form-data">
                <div class="portlet-body">
                    <div class="row" id="show_general_msg" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-danger"></div>
                        </div>
                    </div>
                    <div class="vehicle-add-update-form">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <input type="hidden" name="action" value="update_market_place">
                                        <div class="form-group">
                                            <div class="has-float-label input-icon right">

                                                <i class="fa fa-text-height"></i>

                                                <input id="title" name="title"
                                                       value=""
                                                       type="text" placeholder="Title" required=""
                                                       class="form-control tooltipbutton" data-toggle="tooltip"
                                                       data-placement="top" title="Title"/>

                                                <label>Title <i class="fa tooltips font-red"
                                                                data-original-title="Title mandatory">*</i></label>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="has-float-label input-icon right">
                                                <i class="fa fa-key"></i>
                                                <input id="plugin_key" name="plugin_key"
                                                       value=""
                                                       type="text" placeholder="Plugin Key"
                                                       class="form-control tooltipbutton" data-toggle="tooltip"
                                                       data-placement="top" title="Plugin Key"/>

                                                <label>Plugin Key <i class="fa tooltips font-red"
                                                                     data-original-title="Plugin Key is mandatory">*</i></label>

                                            </div>
                                        </div>
                                    </div>
                                    <!--<div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="has-float-label input-icon right">
                                                <i class="fa fa-book"></i>

                                                <input id="help_doc" name="help_doc"
                                                       value=""
                                                       type="text" placeholder="Help Document"
                                                       class="form-control tooltipbutton" data-toggle="tooltip"
                                                       data-placement="top" title="Help Document"/>

                                                <label>Help Document <i class="fa tooltips font-red"
                                                                     data-original-title="Plugin Key is mandatory">*</i></label>

                                            </div>
                                        </div>
                                    </div>-->
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="has-float-label input-icon right">
                                                <i
                                                        class="fa fa-object-group"></i>
                                                <?php
                                                echo Ddl::generateArrayDDL('class_name', MarketPlaces::$marketplace_config_classes_array, '', 'Select Class', 'class="form-filter form-control select2 select" rel="tooltip" data-original-title="Class Name" placeholder="Class Name"', '', 'class_name', 'Class Name', 'class_name');
                                                ?>
<!--                                                <input id="class_name" name="class_name"-->
<!--                                                       value=""-->
<!--                                                       type="text" placeholder="Class Name"-->
<!--                                                       class="form-control tooltipbutton" data-toggle="tooltip"-->
<!--                                                       data-placement="top" title="Class Name"/>-->
<!---->
                                                <label>Class Name <i class="fa tooltips font-red" data-original-title="Plugin Key is mandatory">*</i></label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="hidden" id="integration_logo" name="integration_logo"
                                                       value="<?php echo $logo; ?>">
                                                <label for="integration_logo"> Logo</label>
                                                <br clear="all">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="fileinput-new thumbnail image-preview"
                                                         style="width: 200px; height: 100px;">
                                                        <?php
                                                        if (trim($logo) != '' && file_exists('../images/' . $logo)) {
                                                            echo '<img src="../images/' . $logo . '" >';
                                                        } else {
                                                            echo '<img src = "../images/No-image-found.jpg">';
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                                         style="max-width: 200px; max-height: 100px;"></div>
                                                    <div>
                                                                    <span class="btn default btn-file"> <span
                                                                                class="fileinput-new"> Select image </span> <span
                                                                                class="fileinput-exists"> Change </span>
                                                                        <input id="your_logo" type="file"
                                                                               name="your_logo">
                                                                    </span> <a href="javascript:;"
                                                                               class="btn red fileinput-exists"
                                                                               data-dismiss="fileinput"> Remove </a>
                                                    </div>
                                                </div>
                                                <!--<div class="clearfix margin-top-10"><span
                                                            class="label label-primary"><small>NOTE!</span>
                                                    Recommended logo dimensions (200 x 200) </small></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" value="0" name="market_place_id" id="market_place_id">
                    <div class="row">
                        <div class="row" align="center">
                            <div class="col-md-12">
                                <button href="javascript:void(0);" type="submit" id="btn_save_vehicle"
                                        class="btn btn-primary btn_save margin-bottom-5" data-original-title=""
                                        title="">
									Update Market Place
                                </button>
                            </div>
                        </div>
                    </div>
                </div><!--portlet-body-->
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php $this->flashMsg->display(); ?>
            </div>
        </div>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-list"></i>
                    Market places list

                </div>
                <div class="actions">
                    <!--<a href="#" class="btn blue"  ><i class="fa fa-plus"></i> Add New</a>-->
                    <div class="portlet-input input-inline input-small">
                        <div class="input-icon right">
                            <i class="icon-magnifier"></i>
                            <input type="text" class="form-control input-circle" placeholder="search..."
                                   name="search_marketplace" id="search_marketplace"></div>
                    </div>

                </div>
            </div>
            <div class="portlet-body">
                <!--Hadi Code-->
                <div class="row">
                    <?php
                    //GET user platforms
                    $UserMarketPlacesMappingFilter = new UserMarketPlacesMappingFilter();
                    $userAccount = -1;
                    if (isset($_GET['id']) && $_GET['id'] > 0)
                        $userAccount = DbAccess3::escape($_GET['id']);
                    $UserMarketPlacesMappingFilter->addUserIdFilter($userAccount);
                    $UserData = $UserMarketPlacesMappingFilter->getList();
                    if (count($UserData) > 0) {
                        foreach ($UserData as $U) {
                            $this->selectedShoppingPlatfrom[$U->getMarketPlacesId()] = $U->getAuthData();
                        }
                    }
                    // GET All Shopping Platform List
                    $MarketPlacesFilter = new MarketPlacesFilter();
                    $MarketPlaces = $MarketPlacesFilter->getShoppingAndAuthenticateData();
                    $PlatformArray = array();
                    foreach ($MarketPlaces as $Shopping) {
                        $PlatformArray[$Shopping->getId()][] = array("PlatformId" => $Shopping->getId(), "PlatformTitle" => $Shopping->getTitle(), "PlatformIntegrationLogo" => $Shopping->getIntegrationLogo(), "AuthenticateId" => $Shopping->getDescription(), "AuthenticateTitle" => $Shopping->getIsActive(), "AuthenticateValue" => $Shopping->getAddedBy(), "is_featured" => $Shopping->getIsFeatured(), "plugin_key" => $Shopping->getPluginKey(), "help_doc" => $Shopping->getHelpDoc(), "class_name" => $Shopping->getClassName());
                    }

                    $count = 1; ?>
                    <div class="cards">
                        <?php foreach ($PlatformArray as $shoppingArray) {
                            if (empty($shoppingArray[0]['PlatformIntegrationLogo']) || !file_exists("../images/thirdparty/" . $shoppingArray[0]['PlatformIntegrationLogo'])) {
                                $shoppingArray[0]['PlatformIntegrationLogo'] = 'No-image-found.png';
                            }
                            ?>

                            <div class="col-md-3 search_marketplace_custom"
                                 data-name="<?php echo $shoppingArray[0]['PlatformTitle']; ?>">
                                <div class="card ">
                                    <div class="card-inner-box">
                                        <center><img
                                                    src="../images/thirdparty/<?php echo $shoppingArray[0]['PlatformIntegrationLogo'] ?>"
                                                    class="img-responsive" alt="Amazon"></center>
                                    </div>
                                    <div class="card-title manage-marketplace-card">
                                        <a href="javascript:{};" class="toggle-info btn-edit-marketplace btn btn-success"
                                           data-marketplace_json='<?php echo json_encode($shoppingArray[0]); ?>'>
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="marketplace_help_doc.php?mp=<?php echo $shoppingArray[0]['plugin_key']; ?>" style="right: 85px;"
                                           class="toggle-info btn grey">
                                            <i class="fa fa-info"></i>
                                        </a>
                                        <a href="marketplace_documentation.php?mp=<?php echo $shoppingArray[0]['plugin_key']; ?>" style="right: 50px;"
                                           class="toggle-info btn btn-primary">
                                            <i class="fa fa-book"></i>
                                        </a>
                                        <h3>
                                            <?php echo $shoppingArray[0]['PlatformTitle'] ?>
                                            <small>
                                                <!--<input name="checkbox_auth[<?php /*echo $shoppingArray[0]['PlatformId']; */ ?>]"
                                                       type="checkbox"
                                                       id="<?php /*echo 'check' . $shoppingArray[0]['PlatformId']; */ ?>"
                                                       value="<?php /*echo $shoppingArray[0]['PlatformId']; */ ?>"
                                                       class="plateform_checkbox icheck"
                                                       data-checkbox="icheckbox_flat-blue"
                                                       data-id="<?php /*echo $shoppingArray[0]['PlatformId']; */ ?>"
                                                       data-original-title=""
                                                       title="" <?php /*echo((isset($this->selectedShoppingPlatfrom[$shoppingArray[0]['PlatformId']])) ? 'checked="checked"' : ''); */ ?>>
                                                Enable Platform</label>--></small>
                                        </h3>
                                    </div>
                                    <div class="card-flap flap1">
                                        <div class="card-description">
                                            <?php
                                            $authDataArray = array();
                                            if (isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) {
                                                $auth_data = $this->selectedShoppingPlatfrom[$shoppingArray[0]['PlatformId']];
                                                $authDataArray = (array)json_decode($auth_data);
                                            }
                                            foreach ($shoppingArray as $value) {
                                                if (!empty($value['AuthenticateTitle'])) {
                                                    $tmpVal = $value['AuthenticateValue'];
                                                    $name_index = "shopping_plateform[" . $value['PlatformId'] . "][" . $tmpVal . "]";
                                                    $auth_value = isset($authDataArray[$tmpVal]) ? $authDataArray[$tmpVal] : '';
                                                    ?>
                                                    <div class="form-group">

                                                        <input name="<?php echo $name_index; ?>"
                                                               type="text"
                                                               value="<?php echo $auth_value; ?>"
                                                               placeholder="<?php echo $value['AuthenticateTitle']; ?>"
                                                               class="form-control shopping_plateform_input shopping_plateform_<?php echo $value['PlatformId']; ?>" <?php echo((!isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) ? 'disabled="true"' : ''); ?>
                                                               data-original-title="" title="">
                                                    </div>
                                                    <?php
                                                }
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function renderFooter()
    {
        ?>
        <?php
    }

    /**
     * Return to source page
     * @param $filter_set
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
