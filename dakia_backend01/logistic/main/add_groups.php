<?php
// get settings
require_once("../includes/settings/config.inc.php");

/* * *
 * Page for editing a user
 */
include_classes([   
                    'groups.class',
                    'groupsfilter.class',
                    'grouphaspermissions.class',
                    'grouphaspermissionsfilter.class',
                    'groupslog.class',
                    'groupslogfilter.class'
                    ]);
class Page extends BasePage
{
    /*     * *
     * Controller logic
     */

    private $group;
    private $user;

    protected function init()
    {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("GROUP")
        );
        $user = SessionManager::getUser();
        $this->user = SessionManager::getUser();
        t_on(); // turn on trace for this page
        if (isset($_GET["gid"]) && base64_decode(trim($_GET["gid"])) > 0) {
            $groupId = base64_decode(trim($_GET["gid"]));
            $groupsObj = new Groups($groupId);
            $groupDataArr['group_id'] = $groupsObj->getGroupId();
            $groupDataArr['group_name'] = $groupsObj->getGroupName();
            $groupDataArr['group_user_type'] = $groupsObj->getGroupType();
            $groupDataArr['group_desc'] = $groupsObj->getGroupDesc();
            $groupDataArr['is_active'] = $groupsObj->getIsActive();

            $this->group = $groupDataArr;
        }
        if (isset($_GET["func"]) && $_GET["func"] == 'get_tree_data') {
            $parent = ($_GET["parent"] != "#" ? $_GET["parent"] : 0);
            $data = array();
            if (isset($_GET['groupid']) && trim($_GET['groupid']) > 0) {
                $groupHasPermissionsFilterObj = new GroupHasPermissionsFilter();
                $groupHasPermissionsFilterObj->addFilter('group_id = ' . trim($_GET['groupid']));
                $groupHasPermissions = $groupHasPermissionsFilterObj->getList();
                $permisn = array();
                foreach ($groupHasPermissions as $groupHasPermission) {
                    $permisn[] = $groupHasPermission->getPermId();
                }
            }
            $currentLang = "en-GB";
            if (isset($_SESSION['lang']))
                $currentLang = $_SESSION['lang'];
            $permissionObj = new PermissionsFilter();
            $permissionObj->addLanguageKeysTableJoin();
            if ($parent > 0) {
                $permissionObj->addFilter(" parent_id ='" . DbAccess3::escape($parent) . "'");
            } else {
                $permissionObj->addFilter(" (parent_id = 0 OR parent_id IS NULL)");
            }
            $permissionObj->addFilter(" is_deleted = 0 AND is_active = 1 AND lk.language = '" . DbAccess3::escape($currentLang) . "'");
            $permissionList = $permissionObj->getList();
            if (count($permissionList) > 0) {
                foreach ($permissionList as $key => $COAArr) {
                    $parent1 = $COAArr->getId();
                    $permissionObjNew = new PermissionsFilter();
                    $permissionObjNew->addFilter("parent_id = '" . $parent1 . "' AND is_deleted = 0 AND is_active = 1 AND lk.language = '" . DbAccess3::escape($currentLang) . "'");
                    $COARes1 = $permissionObjNew->getCount();
                    $data1["id"] = $parent1;
                    $data1["text"] = $COAArr->getLangKey();
                    $data1["children"] = (count($COARes1) > 0 ? true : false);
                    if ($parent == 0) {
                        $data1["type"] = "root";
                    }
                    if (isset($_GET['groupid']) && trim($_GET['groupid']) > 0) {
                        if (in_array($COAArr->getId(), $permisn)) {
                            $data1["state"] = array('opened' => true, 'selected' => true);
                        } else {
                            $data1["state"] = array('opened' => true, 'selected' => false);
                        }
                    } else {
                        if (isset($_GET['COAID']) && $COAArr["COAID"] == $_GET['COAID']) {
                            $data1["state"] = "{opened:true, selected:true}";
                        } else if ($parent == 0) {
                            $data1["state"] = "{opened:true}";
                        }
                    }
                    $data[] = $data1;
                }
            }//exit;
            header('Content-type: text/json');
            header('Content-type: application/json');
            echo json_encode($data);
            exit;
        }
        // is this form being posted back?
        if (isset($this->form_vars["form_action"])) {
            // take appropriate action
            switch ($this->form_vars["form_action"]) {
                // SAVE
                // - save new Groups new Groups details - if OK, save and return to booking list
                case "save":
                    $oldGroupIds = [];
                    $newGroupIds = [];
                    $isActive = "0";
                    $groupName = $this->form_vars["name"];
                    $groupSlug = util_slugify($groupName);
                    //update case
                    $groupSlugCheck = new GroupsFilter();
                    if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                        $groupSlugCheck->addFilter("group_id != '" . $this->form_vars["id"] . "'");
                    }
                    $groupSlugCheck->addFieldLikeFilter("group_slug", $groupSlug);
                    $groupSlugCheckCount = $groupSlugCheck->getCount();

                    $validUniqueSlug = ($groupSlugCheckCount > 0) ? ($groupSlug . '-' . ($groupSlugCheckCount+1)) : $groupSlug;

                    $groupPermissions = $newGroupIds = explode(',', $this->form_vars["parent_id"]);
                    if (isset($this->form_vars['is_active'])) {
                        $isActive = "1";
                    }
                    $groupDescription = $this->form_vars["description"];
                    $groupUserType = $this->form_vars['group_user_type'];
                    $currentDate = time();
                    $userId = $user->getId();
                    if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                        $groupsObj = new Groups($this->form_vars["id"]);
                        $oldGroupsData = serialize($groupsObj);
                        $groupHasPermissionsFilterObj = new GroupHasPermissionsFilter();
                        $groupHasPermissionsFilterObj->addFilter('group_id = ' . $this->form_vars["id"]);
                        $groupHasPermissions = $groupHasPermissionsFilterObj->getList();
                        foreach ($groupHasPermissions as $groupHasPermission) {
                            $groupHasPermissions = new GroupHasPermissions($groupHasPermission->getId());
                            $oldGroupIds[] = $groupHasPermissions->getPermId();
                            $groupHasPermissions->delete();
                        }
                    } else {
                        $groupsObj = new Groups();
                    }
                    $newGroupData = [];
                    $oldGroupData = [];
                    if($groupsObj->getGroupName() != $groupName) {
                        $newGroupData['group_name'] = $groupName;
                        $oldGroupData['group_name'] = $groupsObj->getGroupName();
                    }
                    if($groupsObj->getGroupType() != $groupUserType) {
                        $newGroupData['group_type'] = $groupUserType;
                        $oldGroupData['group_type'] = $groupsObj->getGroupType();
                    }
                    if($groupsObj->getGroupDesc() != $groupDescription) {
                        $newGroupData['group_description'] = $groupDescription;
                        $oldGroupData['group_description'] = $groupsObj->getGroupDesc();
                    }
                    if($groupsObj->getIsActive() != $isActive) {
                        $newGroupData['is_active'] = $isActive;
                        $oldGroupData['is_active'] = $groupsObj->getIsActive();
                    }
                    $new_data = array_diff($newGroupIds, $oldGroupIds);
                    $old_data = array_diff($oldGroupIds, $newGroupIds);

                    $groupsObj->setGroupName($groupName);
                    $groupsObj->setGroupSlug($validUniqueSlug);
                    $groupsObj->setGroupType($groupUserType);
                    $groupsObj->setGroupDesc($groupDescription);
                    $groupsObj->setIsActive($isActive);
                    $groupsObj->setAddedBy($userId);
                    $groupsObj->setAddedDate($currentDate);
                    $groupsObj->setIsDeleted('0');

                    if(!empty($old_data) || !empty($new_data)) {
                        $new_data_values = [];
                        $old_data_values = [];
                        $new_data_p = $newGroupData;
                        $old_data_p = $oldGroupData;
                        if(!empty($new_data)) {
                            foreach ($new_data as $datum) {
                                $permission = new Permissions($datum);
                                $new_data_values[] = $permission->getLangKey();
                            }
                            $new_data_p = $newGroupData;
                            $new_data_p['permissions'] = $new_data_values;

                        }else {
                            $new_data_p['permissions'] = [];
                        }

                        if(!empty($old_data)) {
                            foreach ($old_data as $datum) {
                                $permission = new Permissions($datum);
                                $old_data_values[] = $permission->getLangKey();
                            }
                            $old_data_p = $oldGroupData;
                            $old_data_p['permissions'] = $old_data_values;
                        } else {
                            $old_data_p['permissions'] = [];
                        }
                        $groupsObj->logMoreDataOld = $old_data_p;
                        $groupsObj->logMoreDataNew = $new_data_p;
                    }

                    $groupsObj->save();
                    /*
                     * Add Groups Log details
                     */
                    $groupsLog = new GroupsLog();
                    $newGroupsData = serialize($groupsObj);
                    if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                        $groupId = $this->form_vars["id"];
                        $groupsLog->createlog($user->getId(), '', $groupId, 'GROUPS', $user->getUserName() . ' has updated ' . $groupName, $oldGroupsData, $newGroupsData);
                    } else {
                        $groupId = $groupsObj->getGroupId();
                        $groupsLog->createlog($user->getId(), '', $groupId, 'GROUPS', $user->getUserName() . ' has added new group ' . $groupName, '', $newGroupsData);
                    }
                    foreach ($groupPermissions as $groupPermission) {
                        $groupHasPermissions = new GroupHasPermissions();
                        $groupHasPermissions->setGroupId($groupId);
                        $groupHasPermissions->setPermId($groupPermission);
                        $groupHasPermissions->save();
                    }
                    $output["status"] = "success";
                    if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                        $output["message"] = Translation::GetCaption("GROUP_UPDATE_MESSAGE");
                    } else {
                        $output["message"] = Translation::GetCaption("GROUP_SUCCESS_MESSAGE");
                    }
                    echo json_encode($output);
                    die;
                    break;

                case "cancel":
                default:
                    util_redirect("add_groups.php");
                    break;
            }
        }
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead()
    {
        ?>

        <?php
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-dropbox"></i>
                        <?php echo Translation::GetCaption("ADD_GROUP"); ?>
                    </div>
                    <div class="actions">
                        <a href="add_permission.php" class="btn blue"><span></span><i
                                    class="fa fa-lock"></i>&nbsp;<?= Translation::GetCaption("ADD_PERMISSIONS"); ?></a>
                        <a href="groups_list.php" class="btn blue"><span></span><i
                                    class="fa fa-list"></i>&nbsp;<?php echo Translation::GetCaption("VIEW_ALL_GROUPS"); ?>
                        </a>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger display-none" id="res_message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Name</label>
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="name" id="name" required="required"
                                           title="<?= Translation::GetCaption("PLEASE_ENTER_NAME"); ?>"
                                           value="<?php echo(isset($this->group['group_name']) ? $this->group['group_name'] : "") ?>"
                                           class="form-control"
                                           placeholder='<?= Translation::GetCaption("PLEASE_ENTER_NAME"); ?>'
                                           title='<?= Translation::GetCaption("PLEASE_ENTER_NAME"); ?>' rel="tooltip"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Description</label>
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="description" id="description" required="required"
                                           title="<?= Translation::GetCaption("DESCRIPTION"); ?>"
                                           value="<?php echo(isset($this->group['group_desc']) ? $this->group['group_desc'] : "") ?>"
                                           class="form-control"
                                           placeholder='<?= Translation::GetCaption("DESCRIPTION"); ?>'
                                           title='<?= Translation::GetCaption("DESCRIPTION"); ?>' rel="tooltip"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>Group User Type</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                                    <?php
                                    $IsPeArr = User::USER_ROLES;//array('client' => 'General User', 'corporate' => 'Company', 'admin' => 'Super Admin');
                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                        $IsPeArr = array('corporate' => 'Company', 'client' => 'Client');

                                    $group_type = (isset($this->group['group_user_type']) ? $this->group['group_user_type'] : "");
                                    echo Ddl::generateArrayDDL('group_user_type', $IsPeArr, $group_type, '', ' class="form-control select2 select" rel="tooltip" data-original-title="User Type" placeholder="Group User Type"');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>&nbsp;</label><br/>
                            <div class="md-checkbox">
                                <input <?php echo(isset($this->group['is_active']) && $this->group['is_active'] == "1" ? "checked= checked" : "") ?>
                                        type="checkbox" id="is_active" name="is_active" class="md-check">
                                <label for="is_active">
                                    <span></span>
                                    <span class="check"></span>
                                    <span class="box"
                                          style="border: 2px solid #e5e5e5 !important;"></span> <?= Translation::GetCaption("IS_ACTIVE"); ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Permissions</label><br/>
                                <div id="tree_2" class="tree-demo"></div>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="col-md-12 text-center">
                            <a id="btnSave" href="javascript:void(0);" class="btn btn-primary btn_save"><span></span>Save</a>
                            <a href="javascript:void(0);" id="btnCancel"
                               class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id"
                   value="<?php echo(isset($_GET['gid']) && base64_decode(trim($_GET['gid'])) > 0 ? base64_decode(trim($_GET['gid'])) : "") ?>"/>
            <input type="hidden" name="form_action" id="form_action" value=""/>
            <input type="hidden" name="parent_id" id="parent_id" value=""/>
        </form>
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

    public function renderFooter()
    {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('input').tooltip();
                $('select').tooltip();
                $('a').tooltip();
                $("#btnSave").click(function () {
                    if ($.trim($("#name").val()) === "") {
                        swal("", "<?= Translation::GetCaption("GROUP_NAME_EMPTY_ERROR"); ?>", "error");
                        return false;
                    } else {
                        $("#form_action").val("save");
                        var selectedElmsIds = [];
                        var selectedElms = $('#tree_2').jstree("get_selected", true);
                        $.each(selectedElms, function () {
                            selectedElmsIds.push(this.id);
                            document.getElementById('parent_id').value = selectedElmsIds.join(",");
                        });
                        handleActionAjax('save');
                    }
                });
                $("#btnCancel").click(function () {
                    emptyinputFeilds();
                });
                UITree.init();
            });

            function handleActionAjax(action) {
                var dataString = "";
                dataString = $("#adminForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "add_groups.php",
                    data: dataString,
                    dataType: "json",
                    success: function (data) {
                        $('#res_message').removeClass('alert-danger');
                        $('#res_message').removeClass('alert-success');
                        $('#res_message').removeClass('alert-info');
                        if (data.status == 'success') {
                            $('#res_message').addClass('alert-success');
                            /*if (action == 'save') {
                            emptyinputFeilds();
                            }*/
                            $('html, body').animate({scrollTop: 0}, 0);
                        } else if (data.status == 'info') {
                            $('#res_message').addClass('alert-info');
                        } else {
                            $('#res_message').addClass('alert-danger');
                        }
                        $('#res_message').html(data.message);
                        $('#res_message').show();
                    },
                    error: function () {
                        $('#res_message').removeClass('alert-success').addClass('alert-danger');
                        $('#res_message').html("Some error occurred");
                        $('#res_message').show();
                    }
                });
            }

            var UITree = function () {
                var tree = function () {
                    $("#tree_2").jstree({
                        "core": {
                            "themes": {
                                "responsive": false,
                                "icons": false
                            },
                            //"keep_selected_style": false,
                            // so that create works
                            "check_callback": false,
                            'data': {
                                'url': function (node) {
                                    return 'add_groups.php';
                                },
                                <?php if (isset($_GET["gid"]) && base64_decode(trim($_GET["gid"])) > 0) { ?>
                                'data': function (node) {
                                    return {
                                        'func': 'get_tree_data',
                                        'groupid': <?php echo base64_decode(trim($_GET["gid"])); ?>,
                                        'parent': node.id
                                    };
                                }

                                <?php } else { ?>

                                'data': function (node) {
                                    return {
                                        'func': 'get_tree_data',
                                        'parent': node.id <?php echo(isset($_GET["Update"]) && isset($_GET["COAID"]) ? ", 'COAID':" . $_GET["COAID"] : ""); ?>};
                                }

                                <?php } ?>

                            }
                        },
                        "checkbox": {
                            three_state: false,
                            <?php if (isset($_GET["gid"]) && base64_decode(trim($_GET["gid"])) > 0) {
                            } else { ?>
                            cascade: 'down'
                            <?php } ?>
                        },
                        <?php if (isset($_GET["gid"]) && base64_decode(trim($_GET["gid"])) > 0) { ?>
                        "plugins": ["checkbox", "json_data", "ui"]
                        <?php } else { ?>
                        "plugins": ["checkbox", "ui"]
                        <?php } ?>
                    })

                }

                return {
                    //main function to initiate the module
                    init: function () {
                        tree();
                    }
                };
            }();

            function emptyinputFeilds() {
                $('#name').val("");
                $('#description').val("");
                $('#is_active').prop("checked", false);
                $('#id').val("");
            }
        </script>
        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
