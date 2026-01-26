<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'permissionslog.class',
    'permissionslogfilter.class',
]);
/* * *
 * Page for editing a user
 */
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("PERMISSION")
        );
        $user = SessionManager::getUser();
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_WAREHOUSE_LIST);
        t_on(); // turn on trace for this page
        // is this form being posted back?
        if (isset($this->form_vars["frm_filter"])) {
            switch ($this->form_vars["frm_filter"]) {
                    
                case "search":
                    
                    $sreachArr['lang_key'] = $this->form_vars["search_lang_key"];
                    $sreachArr['file_name'] = $this->form_vars["search_file_name"];
                    $sreachArr['description'] = $this->form_vars["search_file_description"];
                    $sreachArr['menu'] = $this->form_vars["is_menu"];
                    $sreachArr['active'] = $this->form_vars["is_active"];
                    $permissionsData = Permissions::ViewPermissions("","",$sreachArr);
                    $output["status"] = "success";
                    $output["tableData"] = $permissionsData;
                    $output["message"] = "";
                    echo json_encode($output);
                    die;
                    break;
                
                case "cancel":
                default:
                    util_redirect("add_permission.php");
                    break;
            }
        }
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new Permissions new Permissions details - if OK, save and return to booking list
                case "save":
                    
                    $translationNameArr = $this->form_vars["translation_name"];
                    $translationKeyArr = $this->form_vars["translation_key"];
//                    $translationValArr = $this->form_vars["translation_val"];                    
                    $userId = $user->getId();
                    $langKey = "";
                    $translationNameArr['en-GB'] = trim($translationNameArr['en-GB']); 
                    if(!empty($translationNameArr['en-GB'])){
                        if (isset($this->form_vars['is_menu_item'])) {
                            $langKey = "LEFT_MENU_".str_replace(' ', '_',strtoupper($translationNameArr['en-GB']));
                        }else{
                            $langKey = "NAV_".str_replace(' ', '_',strtoupper($translationNameArr['en-GB']));
                        }
                            foreach ($translationNameArr as $langType => $translationName) {
                                if(LanguageKeys::checkKeyExist($langKey,$langType,$translationName) == 0){                                  
                                    if (!empty($translationKeyArr[$langType]) && $translationKeyArr[$langType] > 0) {
                                        $languageKeys = new LanguageKeys($translationKeyArr[$langType]);
                                    }else{
                                        $languageKeys = new LanguageKeys();
                                    }
                                    $date = date ("Y-m-d H:i:s");
                                    $languageKeys->setKeyword($langKey);
                                    $languageKeys->setLanguage($langType);
                                    $languageKeys->setDateCreated($date);
                                    $languageKeys->setCreatedby($userId);
                                    $languageKeys->setDateUpdated($date);
                                    $languageKeys->setUpdatedby($userId);
                                    $languageKeys->setCaption($translationName);
                                    $languageKeys->save();
                                } 
                            }
                    }
                    $parentId = 0;
                    if (isset($this->form_vars["parent_id"]) && $this->form_vars["parent_id"] > 0) {
                        $parentId = $this->form_vars["parent_id"];
                    }
                    $fileName = $this->form_vars["file_name"];
                    $queryString = $this->form_vars["query_string"];
                    $sortOrder = $this->form_vars["sort_order"];
                    $description = $this->form_vars["description"];
                    $icon = $this->form_vars["icon"];
                    $isActive = "0";
                    $isMenuItem = "0";                    
                    $currentDate = time();
                    if (isset($this->form_vars['is_menu_item'])) {
                        $isMenuItem = "1";
                    }
                    if (isset($this->form_vars['is_active'])) {
                        $isActive = "1";
                    }
                    if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0) {
                        $permissionsObj = new Permissions(intval($this->form_vars["id"]));
                        $oldPermissionData = serialize($permissionsObj);
                    } else {
                        $permissionsObj = new Permissions();
                    }
                    $permissionsObj->setLangKey($langKey);
                    $permissionsObj->setParentId($parentId);
                    $permissionsObj->setFileName($fileName);
                    $permissionsObj->setQueryString($queryString);
                    $permissionsObj->setSortOrder($sortOrder);
                    $permissionsObj->setDescription($description);
                    $permissionsObj->setIcon($icon);
                    $permissionsObj->setIsMenuItem($isMenuItem);
                    $permissionsObj->setIsActive($isActive);
                    $permissionsObj->setAddedBy($userId);
                    $permissionsObj->setAddedDate($currentDate);                    
                    $permissionsObj->setIsDeleted('0');                    
                    $permissionsObj->save();
                    /*
                    * Add permissions Log details
                    */
                    $permissionsLog = new PermissionsLog();
                    $newPermissionsData = serialize($permissionsObj);
                    if ((int)$this->form_vars['id'] <= 0) {
                        $permissionsLog->createlog($user->getId(),'',$permissionsObj->getId(),'PERMISSIONS',$user->getUserName() . ' has added new permissions ' . $fileName,'', $newPermissionsData);
                    } else {
                            $permissionsLog->createlog($user->getId(),'',$permissionsObj->getId(),'PERMISSIONS',$user->getUserName() . ' has updated ' . $fileName,$oldPermissionData, $newPermissionsData);
                    }
                    $output["status"] = "success";
                    if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0) {
                        $output["message"] = Translation::GetCaption("PERMISSION_UPDATE_MESSAGE");
                    }else{
                        $output["message"] = Translation::GetCaption("PERMISSION_SUCCESS_MESSAGE");                        
                    }
                    echo json_encode($output);
                    die;
                    break;

                case "edit":
                    if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0) {
                        $permissionsObj = new Permissions($this->form_vars['id']);
                        $permissionsArr['id'] = $permissionsObj->getId();
                        $permissionsArr['lang_key'] = $permissionsObj->getLangKey();
                        $permissionsArr['parent_id'] = $permissionsObj->getParentId();
                        $permissionsArr['file_name'] = $permissionsObj->getFileName();
                        $permissionsArr['description'] = $permissionsObj->getDescription();
                        $permissionsArr['query_string'] = $permissionsObj->getQueryString();
                        $permissionsArr['icon'] = $permissionsObj->getIcon();
                        $permissionsArr['sort_order'] = $permissionsObj->getSortOrder();
                        $permissionsArr['is_menu_item'] = $permissionsObj->getIsMenuItem();
                        $permissionsArr['is_active'] = $permissionsObj->getIsActive();
                        $languageKeysFilter = new LanguageKeysFilter();
                        $languageKeysFilter->addKeywordFilter($permissionsArr['lang_key']);
                        $languageKeysList = $languageKeysFilter->getList();
                        $languageKeyArr = array();
                        if(!empty($languageKeysList)){
                            foreach ($languageKeysList as $languageKey) {
                                $languageKeyArr[$languageKey->getLanguage()] = $languageKey->getCaption().":::".$languageKey->getId();
                            }                            
                        }
                    }
                    $output["status"] = "success";
                    $output["res"] = $permissionsArr;
                    $output["language"] = $languageKeyArr;
                    $output["message"] = "";
                    echo json_encode($output);
                    die;
                    break;
                    
                case "loadTableData":
                    $permissionsData = Permissions::ViewPermissions("","");
                    $output["status"] = "success";
                    $output["tableData"] = $permissionsData;
                    $output["message"] = "";
                    echo json_encode($output);
                    die;
                    break;
                
                case "getParentList":
                    $permissionsData = Permissions::dropdownParentBox('parent_id', 'parent_id',$this->form_vars['selected_parent']);                    
                    $output["status"] = "success";
                    $output["parentList"] = $permissionsData;
                    echo json_encode($output);
                    die;
                    break;
                    
                case "delete":
                    $permissions = new Permissions(intval($this->form_vars["id"]));
                    $fileName = $permissions->getFileName();
                    $permissionsObjectOld = $permissions;
                    $permissions->setIsDeleted('1');
                    $permissions->save();
                    $permissionsObjectOld = $permissions;
                    /*
                    * Add permissions Log details
                    */
                    $permissionsLog = new PermissionsLog();
                    $newPermissionsData = serialize($permissions);
                    $oldPermissionData = serialize($permissionsObjectOld);
                            $permissionsLog->createlog($user->getId(),'',$permissions->getId(),'PERMISSIONS',$user->getUserName() . ' has deleted ' . $fileName,$oldPermissionData, $newPermissionsData);
                    $output["status"] = "success";
                    $output["message"] = Translation::GetCaption("PERMISSION_DELETE_MESSAGE");
                    echo json_encode($output);
                    die;
                    break;

                case "cancel":
                default:
                    util_redirect("add_permission.php");
                    break;
            }
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        ?>

        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST">        
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-dropbox"></i>
                        <?php echo Translation::GetCaption("ADD_UPDATE_PERMISSION"); ?>
                    </div>
                    <div class="actions">
                        <?php if(Permissions::checkFilePermission('add_groups.php')){ ?>
                        <a href="add_groups.php" class="btn blue"><span></span><i class="fa fa-plus"></i>&nbsp;<?=Translation::GetCaption("ADD_GROUPS")?></a>
                        <?php }
                        if(Permissions::checkFilePermission('groups_list.php')){
                        ?>
                        <a href="groups_list.php" class="btn blue"><span></span><i class="fa fa-list"></i>&nbsp;<?=Translation::GetCaption("GROUPS_LIST");?></a>
                        <?php } ?>
                    </div>
                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger display-none"  id="res_message" ></div>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                            $languageFilter = new LanguageFilter();
                            $languageFilter->AddActiveFilter();
                            $languageList = $languageFilter->getList();
                            foreach ($languageList as $language) {                            
                        ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                     <div class="has-float-label input-icon right"> 
                                    
                                   <i class="fa fa-globe"></i>
                                        <input type="text" name="translation_name[<?php echo $language->getLanguage(); ?>]"  title="<?php echo Translation::GetCaption("PLEASE_ENTER_NAME")." ".$language->getLanguage(); ?>" value="" class="language_input <?php echo $language->getLanguage()."_field"; ?> form-control" placeholder='<?php echo Translation::GetCaption("PLEASE_ENTER_NAME")." ".$language->getLanguage(); ?>' title='<?php echo Translation::GetCaption("PLEASE_ENTER_NAME")." ".$language->getLanguage(); ?>' rel="tooltip" id="permission_name_<?php echo $language->getLanguage(); ?>"/>
                                        <input type="hidden" name="translation_key[<?php echo $language->getLanguage(); ?>]" class="<?php echo $language->getLanguage()."_id"; ?> translation_key_class"  value="0"/>
                                        <label><?php echo Translation::GetCaption("PLEASE_ENTER_NAME")." ".$language->getLanguage(); ?></label>


                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                             
                                <div class="has-float-label input-icon right"> 
                                    <div id="parent_dropdown_div">
                                        <?php echo Permissions::dropdownParentBox('parent_id', 'parent_id'); ?>
                                           <label>Parent</label>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                    
                                    <div class="has-float-label input-icon right">  <i class="fa fa-user"></i>
                                        <input type="text" name="file_name" id="file_name" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_FILE_NAME"); ?>" value="" class="form-control" placeholder='<?= Translation::GetCaption("PLEASE_ENTER_FILE_NAME"); ?>' rel="tooltip" />
                                        <label for="file_name">File Name</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="first_form_col">
                                <div class="form-group">
                                   
                                    <div class="has-float-label input-icon right"> <i class="fa fa-file"></i>
                                        <input type="text" name="query_string" id="query_string" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_QUERY_STRING"); ?>" value="" class="form-control" placeholder='<?= Translation::GetCaption("PLEASE_ENTER_QUERY_STRING"); ?>' rel="tooltip" />
                                         <label for="query_string">Query String</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                               
                                <div class="has-float-label input-icon right"> <i class="fa fa-sort"></i>
                                    <input type="text" name="sort_order" id="sort_order" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_SORT_ORDER"); ?>" value="" class="form-control" placeholder='<?= Translation::GetCaption("PLEASE_ENTER_SORT_ORDER"); ?>' title='<?= Translation::GetCaption("PLEASE_ENTER_SORT_ORDER"); ?>' rel="tooltip"/>
                                     <label for="sort_order">Sort Order</label>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                
                                <div class="has-float-label input-icon right"> <i class="fa fa-file"></i>
                                    <input type="text" name="description" id="description" required="required" title="<?= Translation::GetCaption("DESCRIPTION"); ?>" value="" class="form-control" placeholder='<?= Translation::GetCaption("DESCRIPTION"); ?>' title='<?= Translation::GetCaption("DESCRIPTION"); ?>' rel="tooltip"/>
                                    <label for="description">Description</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                
                                 <div class="has-float-label input-icon right">  <i class="fa fa-shopping-cart"></i>
                                    <input type="text" name="icon" id="icon" required="required" title="<?= Translation::GetCaption("PLEASE_ENTER_ICON"); ?>" value="" class="form-control" placeholder='<?= Translation::GetCaption("PLEASE_ENTER_ICON"); ?>' title='<?= Translation::GetCaption("PLEASE_ENTER_ICON"); ?>' rel="tooltip"/>
                                    <label for="icon">Icon</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                         
                            <div class="md-checkbox">
                                <input type="checkbox" id="is_menu_item" name="is_menu_item" class="md-check">
                                <label for="is_menu_item">
                                    <span></span>
                                    <span class="check"></span>
                                    <span class="box" style="border: 2px solid #e5e5e5 !important;"></span> <?= Translation::GetCaption("IS_MENU_ITEM"); ?> </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                           
                            <div class="md-checkbox">
                                <input type="checkbox" id="is_active" name="is_active" class="md-check">
                                <label for="is_active">
                                    <span></span>
                                    <span class="check"></span>
                                    <span class="box" style="border: 2px solid #e5e5e5 !important;"></span> <?= Translation::GetCaption("IS_ACTIVE"); ?> </label>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="col-md-12 text-center">  
                            <a id="btnSave" href="javascript:void(0);" class="btn btn-primary btn_save"><span></span>Save</a>               
                            <a href="javascript:void(0);" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="data_id" value="" />
            <input type="hidden" name="form_action" id="form_action" value="" />
        </form>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-dropbox"></i>
                    <?= Translation::GetCaption("PERMISSION_DETAILS"); ?>
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body" id="append_all_permission_data">
                <?php
                $permission = "";
                $parent_child = "";
                echo Permissions::ViewPermissions($permission, $parent_child)
                ?>
                <div id="overlay_div" class="blockUI blockOverlay" style="display: none; z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; opacity: 0.05; cursor: wait; position: absolute;"></div>
                <div id="loading_img_div" class="blockUI blockMsg blockElement" style="display: none; z-index: 1011; position: absolute; padding: 0px; margin: 0px; width: 30%; top: 3%; left: 407.5px; text-align: center; color: rgb(0, 0, 0); border: 0px; cursor: wait;">
                    <div class="loading-message loading-message-boxed">
                        <img src="../assets/global/img/loading-spinner-grey.gif" align=""><span>&nbsp;&nbsp;Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('a').tooltip();
                $("#btnSave").click(function () {
                    
                    if ($.trim($("#permission_name_en-GB").val())===""){
                        swal("","<?= Translation::GetCaption("PERMISSION_NAME_EMPTY_ERROR"); ?>", "error");
                        return false;
                    }else{
                        $("#form_action").val("save");
                        handleActionAjax('save');
                    }
                });
                $(document).on('click', '#searchFilter', function () {
                    $("#frm_filter").val("search");
                    handleActionAjax('search');
                });

                $("#btnCancel").click(function () {
                   emptyinputFeilds();
                });
            });
            $(document).on('click', '.permission_edit', function () {
                var permissionId = $(this).attr('data-permission-id');
                $("#form_action").val("edit");
                $("#data_id").val(permissionId);
                handleActionAjax('edit');
            });
            $(document).on('click', '.permission_delete', function () {
                var permissionId = $(this).attr('data-permission-id');
                swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD"); ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                },
                function(isConfirm) {
                if (isConfirm) {
                    $("#form_action").val("delete");
                    $("#data_id").val(permissionId);
                    handleActionAjax('delete');
                }
                });
            });
            function handleActionAjax(action){
                var dataString = "";
                if(action == "search"){
                    dataString = $("#form_filter").serialize();
                }else{
                    dataString = $("#adminForm").serialize();
                }
                $.ajax({
                    type: "POST",
                    url: "add_permission.php",
                    data: dataString,
                    dataType: "json",
                    success: function (data) {
                        $('#res_message').removeClass('alert-danger');
                        $('#res_message').removeClass('alert-success');
                        $('#res_message').removeClass('alert-info');
                        if (data.status == 'success') {
                            $('#res_message').addClass('alert-success');
                            if (action == 'edit') {
                                $.each(data.language, function(key, value){
                                    var lang_info = value.split(":::");
                                    $("."+key+"_field").val(lang_info[0]);
                                    $("."+key+"_id").val(lang_info[1]);
                                });
                                var select2Parentid = $("#parent_id").select2();
                                select2Parentid.val(data.res.parent_id).trigger('change');
                                $('#selected_parent').val(data.res.parent_id);
                                $('#file_name').val(data.res.file_name);
                                $('#query_string').val(data.res.query_string);
                                $('#sort_order').val(data.res.sort_order);
                                $('#description').val(data.res.description);
                                $('#icon').val(data.res.icon);
                                if (data.res.is_menu_item == '1') {
                                    $('#is_menu_item').prop("checked", true);
                                }
                                if (data.res.is_active == '1') {
                                    $('#is_active').prop("checked", true);
                                }
                                $('#data_id').val(data.res.id);
                                getLatestParentList();
                            }
                            if (action == 'delete') {
                                $('#data_id').val('');
                                loadPermissions();
                                getLatestParentList();
                            }
                            if (action == 'loadTableData' || action == 'search') {
                                $('#append_all_permission_data').html(" ");
                                $('#append_all_permission_data').append(data.tableData);
                                $('#overlay_div').hide();
                                $('#loading_img_div').hide();
                            }
                            if (action == 'save') {
                               emptyinputFeilds();
                               loadPermissions();
                               getLatestParentList();
                            }
                            if(action != "getParentList"){
                                $('html, body').animate({scrollTop: 0}, 0);                                
                            }
                            if(action == "getParentList"){
                                $("#parent_dropdown_div").html(" ");
                                $("#parent_dropdown_div").html(data.parentList);
                                $("#parent_id").select2();                                
                            }
                        } else if (data.status == 'info') {
                            $('#res_message').addClass('alert-info');
                        } else {
                            $('#res_message').addClass('alert-danger');
                        }
                        if(action != "getParentList" && action !="loadTableData" && action!="edit" && action!="search"){
                            $('#res_message').html(data.message);
                            $('#res_message').show();                            
                        }
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }
            function loadPermissions(){
                $('#overlay_div').show();
                $('#loading_img_div').show();
                $("#form_action").val("loadTableData");
                handleActionAjax('loadTableData');
            }
            function emptyinputFeilds(){
                var select2Parentid = $("#parent_id").select2();
                    select2Parentid.val("").trigger('change');
                    $('.language_input').val("");
                    $('#file_name').val("");
                    $('#query_string').val("");
                    $('#sort_order').val("");
                    $('#description').val("");
                    $('#icon').val("");
                    $('#is_menu_item').prop("checked", false);
                    $('#is_active').prop("checked", false);
                    $('#data_id').val("");
                    $('.translation_key_class').val("");
            }
            function getLatestParentList(){
                $("#form_action").val("getParentList");
                handleActionAjax("getParentList");
            }
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
