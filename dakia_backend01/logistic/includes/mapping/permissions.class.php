<?php

/**
 * Permissions Object
 *
 */
class Permissions extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'lang_key' => 'string',
            'parent_id' => 'number',
            'file_name' => 'string',
            'description' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'query_string' => 'string',
            'icon' => 'string',
            'sort_order' => 'number',
            'is_menu_item' => 'number',
            'is_active' => 'number',
            'is_deleted' => 'number',
            'first_name' => 'undefined',
            'user_type' => 'undefined'

        );
        parent::__construct("permissions", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getPermissionsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPermissionsFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function dropdownParentBox($dropdwonName = 'parent_id', $dropdwonId = 'parent_id', $selectedParent = 0) {
        $html = '';
        $html .= '<select name = "' . $dropdwonName . '" id= "' . $dropdwonId . '" class="form-control select2" rel="tooltip" title = "' . $dropdwonName . '" placeholder = "' . $dropdwonName . '"  >';
        $html .= "<option value=''>Select Parent</option>";
        $permissionObjList = self::LoadPermissionComboCustom();
        if (count($permissionObjList) > 0) {
            foreach ($permissionObjList as $result) {
                $checked = "";
                if($result['id'] == $selectedParent)
                    $checked = "selected=selected";
                $html .= '<option '.$checked.' value="' . $result['id'] . '">' . $result['name'] . '</option>';
            }
        }
        $html .= '</select><input type="hidden" name="selected_parent" id="selected_parent" value="" />';
        return $html;
    }

    public static function LoadPermissionComboCustom($parentId = 0, $spacing = '', $category_tree_array = '') {
        $parentId = intval($parentId);
        $currentLang = "en-GB";
        if (isset($_SESSION['lang']))
            $currentLang = $_SESSION['lang'];
            
        $permissionObj = new PermissionsFilter();
        $permissionObj->addLanguageKeysTableJoin();
        $permissionObj->addFilter(" is_deleted = '0'");
        $permissionObj->addFilter(" is_active = '1'");
        $permissionObj->addFilter(" lk.language = '" . $currentLang . "'");
        if($parentId > 0){
            $permissionObj->addFilter(" parent_id ='" . $parentId . "'");
        }else{
            $permissionObj->addFilter(" (parent_id = 0 OR parent_id IS NULL)");
        }
        $permissionObjList = $permissionObj->getList();

        if (!is_array($category_tree_array))
            $category_tree_array = array();

        if (count($permissionObjList) > 0) {
            foreach ($permissionObjList as $permission) {
                $category_tree_array[] = array("id" => $permission->getId(), "parent_id" => $permission->getParentId(), "name" => $spacing . $permission->getLangKey());
                $category_tree_array = self::LoadPermissionComboCustom($permission->getId(), '&nbsp;&nbsp;&nbsp;&nbsp;' . $spacing . '&nbsp;', $category_tree_array);
            }
        }
        return $category_tree_array;
    }

    public static function ViewPermissions($permission, $parent_child, $searchArr = "") {
        if (is_array($searchArr)) {
            $LangKey = "";
            if (!empty($searchArr['lang_key'])) {
                $LangKey = $searchArr['lang_key'];
            }
            $fileName = "";
            if (!empty($searchArr['file_name'])) {
                $fileName = $searchArr['file_name'];
            }
            $description = "";
            if (!empty($searchArr['description'])) {
                $description = $searchArr['description'];
            }
            $menuTrue = "";
            $menuFalse = "";
            if ($searchArr['menu'] != "" && $searchArr['menu'] == '1') {
                $menuTrue = "selected=selected";
            } else if ($searchArr['menu'] != "" && $searchArr['menu'] == '0') {
                $menuFalse = "selected=selected";
            }
            $activeTrue = "";
            $activeFalse = "";
            if ($searchArr['active'] != "" && $searchArr['active'] == '1') {
                $activeTrue = "selected=selected";
            } else if ($searchArr['active'] != "" && $searchArr['active'] == '0') {
                $activeFalse = "selected=selected";
            }
        }
        $arrayTypeValues    =   array('0'=>'No', '1'=>'Yes');
        $out = '<form name="form_filter" id="form_filter" action="" method="post" >
            <div class="table-scrollable">
                <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr role="row" class="heading">
                    <th scope="col">Options</th>
                    <th scope="col">Name</th>
                    <th scope="col">File Name / keyword</th>
                    <th scope="col">Description</th>
                    <th scope="col">Is Menu</th>
                    <th scope="col">Is Active</th>
                    <th scope="col">Created By</th>
                </tr>
                <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                    <a href="javascript:void(0);" id="searchFilter" class="btn btn-xs btn-default blue btn-outline pull-left"><i class="fa fa-search"></i></a>
                                     <a href="javascript:void(0);" id="cancle_filter" class="btn btn-xs btn-default red btn-outline pull-left"><i class="fa fa-times"></i></a>
                            </div>
                        </td>
                        <td>
                            <input id="search_lang_key" type="text" class="form-control form-filter input-sm " name="search_lang_key" value="' . $LangKey . '">
                        </td>
                        <td>
                            <input id="search_file_name" type="text" class="form-control form-filter input-sm " name="search_file_name" value="' . $fileName . '">
                        </td>
                        <td>
                            <input id="search_file_description" type="text" class="form-control form-filter input-sm " name="search_file_description" value="' . $description . '">
                        </td>
                        <td>
                        '.
                        Ddl::generateArrayDDL('is_menu', $arrayTypeValues, $searchArr['menu'] , "Select Select", ' class="form-control form-filter select2"', "", 'is_menu' ,'Select Select','')
                        .'
                        </td>
                        <td>
                        '.Ddl::generateArrayDDL('is_active', $arrayTypeValues, $searchArr['active'] , "Select Select", ' class="form-control form-filter select2"', "", 'is_active' ,'Select Select','')
                        .'
                        </td>
                        <td><input type="hidden" name="frm_filter" id="frm_filter" value=""/></td>
                </tr>
            </thead>
        <tbody>';
        $out .= self::loadTableData(0, 0, $permission, $parent_child, $searchArr);
        $out .= "</tbody>
            </table></div></form>";
        return $out;
    }

    public static function loadTableData($parent_id = 0, $level = 0, $permission, $parent_child, $searchArr = "") {
        $permissionObj = new PermissionsFilter();
       // $user = SessionManager::getUser();
        $currentLang = "en-GB";
        if (isset($_SESSION['lang']))
            $currentLang = $_SESSION['lang'];
        if (is_array($searchArr)) {
            if (!empty($searchArr['lang_key'])) {
                $permissionObj->addFieldLikeFilter("lk.caption", $searchArr['lang_key']);
            }
            if (!empty($searchArr['file_name'])) {
                $permissionObj->addFieldLikeFilter("p.file_name", $searchArr['file_name']);
            }
            if (!empty($searchArr['description'])) {
                $permissionObj->addFieldLikeFilter("p.description", $searchArr['description']);
            }
            if ($searchArr['menu'] != "") {
                $permissionObj->addFilter("p.is_menu_item = " . $searchArr['menu']);
            }
            if ($searchArr['active'] != "") {
                $permissionObj->addFilter("p.is_active = " . $searchArr['active']);
            }
        }
        $output = "";
        $permissionObj->addFilter(" p.is_deleted = 0 ");
        //if($parent_id > 0)
            $permissionObj->addFilter(" p.parent_id = '" . $parent_id . "'");
        $permissionObj->addUserTableLeftJoin();
        $permissionObj->addLanguageKeysTableLeftJoin("AND lk.language = '" . $currentLang . "'");
        $permissionObjList = $permissionObj->getList();
        $counter = 0;
        
        $status = array();
        $status[0] = '<span class="label label-sm label-danger">No</span>';
        $status[1] = '<span class="label label-sm label-success">Yes</span>';
        if (count($permissionObjList) > 0) {
            foreach ($permissionObjList as $permission) {
                $userName = "";
                $indent = str_repeat('&nbsp;', $level * 5);
                //$userObj = new User($permission->getAddedBy());
                //$userName = $userObj->getFirstName() . " (" . $userObj->getUserType() . ")";
                $userName = $permission->getFirstName() . " (" . $permission->getUserType() . ")";
                $counter++;
                $output .= '<tr class="search_permisson_filter_custom" data-per-name="' . $permission->getLangKey() . '" data-per-file-name="' . $permission->getFileName() . '" data-per-desc="' . $permission->getDescription() . '"  data-per-menu="' . $permission->getIsMenuItem() . '"  data-per-active="' . $permission->getIsActive() . '" >';
                $output .= '<td>
                                <a href="javascript:void(0);" data-permission-id = "' . $permission->getId() . '" class="btn btn-xs btn-default blue btn-outline pull-left permission_edit" rel="tooltip" data-toggle="tooltip" placeholder="Edit" title="Edit"> <span class="fa fa-pencil"></span> </a>
                                <a href="javascript:void(0);" data-permission-id = "' . $permission->getId() . '"  class="btn btn-xs btn-default red btn-outline pull-left permission_delete" rel="tooltip" placeholder="Delete" title="Delete"><span class="fa fa-trash"></span> </a>
                                    
                                <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" rel="tooltip"  placeholder="View Audit" data-log_key="'.$permission->getId().'" 
                                 data-log_name="permissions" data-toggle="modal"> <span class="fa fa-list"></span> 
                                 </a>
                            </td>';
                $output .= '<td>' . $indent . '<i class="' . $permission->getIcon() . '"></i> ' . $permission->getLangKey() . '</td>';
                $output .= '<td>' . $permission->getFileName() . '</td>
                        <td>' . $permission->getDescription() . '</td>
                        <td class="text-center">' . $status[$permission->getIsMenuItem()] . '</td>
                        <td class="text-center">' . $status[$permission->getIsActive()] . '</td>';
                $output .= '<td class="' . ($level + 1 ) . '">' . $userName . '</td>';
                $output .= '</tr>';
                $output .= self::loadTableData($permission->getId(), $level + 1,"","");
            }
        }
        return $output;
    }

    public static function DisplayMenu($adminId) {
        $PermissionArr = array();
        $urls = array();
        $fileName = self::permissionListMenu($adminId);
        foreach ($fileName as $data) {           
            $permID = $data['id'];
            $parentID = $data['parent_id'];
            if (!empty($parentID)) {
                if (!is_array($PermissionArr[$parentID]['children'])){                    
                    $PermissionArr[$parentID]['children'] = [];                    
                }
                $PermissionArr[$parentID]['children'][$permID] = [];
                $PermissionArr[$parentID]['children'][$permID] = $data;
            } else {
                $PermissionArr[$data['id']] = $data;
            }
        }
        static $current_url;
        $current_url = pathinfo($_SERVER['REQUEST_URI']);
        if (!empty($PermissionArr)) {
            $C = 0;
            $ittrete = 0;
            //print_r($PermissionArr); exit;
            if(is_array($PermissionArr)){
                foreach ($PermissionArr as $data) {
                    $C = $C + 1;
                    if(is_array($data['children'])){
                        foreach ($data['children'] as $submenu) {
                            $urls[] = $submenu['file_name'] . $submenu['query_string'];
                        }
                    }
                    if ($ittrete == 0) {
                        if (in_array($current_url['basename'], $urls)) {
                            $ittrete = $ittrete + 1;
                            $class = '';
                        }
                    } else {
                        $class = "collapsed";
                    }
                    $data['name_'] = strtolower(str_replace(" ", "", $data['name']));
                    ?>
                    <li class="nav-item">
                        <a
                                class="nav-link menu-link <?php echo $class; ?>"
                                <?php if(!empty($data['children'])) {?>href="#<?= $data['name_'];?>_menu" data-bs-toggle="collapse" role="button"
                                aria-expanded="<?php echo ($current_url['basename'] == $data['file_name'] . $data['query_string']) ? 'true' : 'false';?>"
                                aria-controls="<?= $data['name_'];?>_menu"<?php }
                                else {?>href="<?php echo BASE_URL . $data['file_name'] . $data['query_string']; ?>"
                                aria-expanded="<?php echo ($current_url['basename'] == $data['file_name'] . $data['query_string']) ? 'true' : 'false';?>"
                            <?php echo ($current_url['basename'] == $data['file_name'] . $data['query_string']) ? 'style="color:#695eef"' : '';?>
                            <?php }?>
                        >
                            <i class="<?php echo $data['icon']; ?>"></i>
                            <span class=""><?php echo $data['name']; ?></span>
                        </a>
                        <?php  if(!empty($data['children'])){ ?>
                            <div class="collapse menu-dropdown "
                                 id="<?= $data['name_'];?>_menu">
                                <ul class="nav nav-sm flex-column">
                                    <?php foreach ($data['children'] as $child) { ?>
                                        <li class="nav-item <?php echo 'hadi_'.$current_url['basename'].'---'.$child['file_name'] . $child['query_string'];?>">
                                            <a href="<?php echo BASE_URL . $child['file_name'] . $child['query_string']; ?>" class="nav-link "
                                               aria-expanded="<?php echo ($current_url['basename'] == $child['file_name'] . $child['query_string']) ? 'true' : 'false';?>"
                                               data-key="t-analytics"
                                                <?php echo ($current_url['basename'] == $child['file_name'] . $child['query_string']) ? 'style="color:#695eef"' : '';?>
                                            >
                                                <?php echo $child['children']; ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php  } ?>
                    </li>
                    <?php
                }
            }
        }
    }
    public static function getAllPermissions($adminid) {
        $allPermissionData = array();
        $allPermissionsRes = "SELECT 
                                            p.`file_name`,
                                            IFNULL(ap.perm_id, 0) AS allowed 
                                          FROM
                                            `permissions` p 
                                            LEFT JOIN 
                                              (SELECT 
                                                ghp.perm_id AS perm_id 
                                              FROM
                                                grouphaspermissions ghp 
                                              WHERE group_id IN 
                                                (SELECT 
                                                  group_id 
                                                FROM
                                                  `userhasgroups` 
                                                WHERE admin_id = '" . $adminid . "')) AS ap 
                                              ON ap.perm_id = p.id 
                                          WHERE p.`is_deleted` = 0 
                                            AND p.`file_name` != '#' AND p.`file_name` != '' AND p.is_active = 1 group by p.file_name,ap.perm_id";

        $result = self::runQuery($allPermissionsRes);
        while ($row = mysqli_fetch_assoc($result)) {
            $file_name = $row['file_name'];
            $allowed = $row['allowed'];
            $allPermissionData[$file_name] = $allowed;
        }
        return $allPermissionData;
    }

    public static function permissionListMenu($adminid) {
        $currentLang = "en-GB";
        if (isset($_SESSION['lang']))
            $currentLang = $_SESSION['lang'];
        $query = "SELECT perm.id,
                            perm.file_name,
                            perm.parent_id,
                            perm.icon,
                            perm.query_string,
                            perm.sort_order, 
                            perm.is_menu_item,
                            lk.caption AS `name`
                        FROM userhasgroups uhp
                        INNER JOIN grouphaspermissions grouphas ON 
                        grouphas.group_id = uhp.group_id and uhp.group_id in (select group_id from `groups` where is_active = 1)
                        INNER JOIN permissions perm ON 
                        perm.id = grouphas.perm_id
                        LEFT JOIN language_keys lk ON
                        perm.lang_key = lk.keyword AND lk.caption IN ('Dashboard','Customers','Members','Carriers','Carriers List','Services List','Carrier List')
                        WHERE lk.language = '" . $currentLang . "' AND perm.is_menu_item = 1 AND perm.is_deleted = 0 AND perm.is_active = 1 AND uhp.admin_id = '" . trim($adminid) . "' ORDER BY perm.`parent_id`, perm.`sort_order` ASC ";
        $result = self::runQuery($query);
        $returnResult = array();
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['parent_id'] == 0) {
                $returnResult[] = array('id' => $row['id'], 'name' => $row['name'], 'parent_id' => $row['parent_id'], 'file_name' => $row['file_name'], 'children' => '', 'icon' => $row['icon'], 'sort_order' => $row['sort_order'], 'query_string' => $row['query_string'], 'is_menu_item' => $row['is_menu_item']);
            } else {
                $returnResult[] = array('id' => $row['id'], 'name' => '', 'parent_id' => $row['parent_id'], 'file_name' => $row['file_name'], 'children' => $row['name'], 'icon' => $row['icon'], 'sort_order' => $row['sort_order'], 'query_string' => $row['query_string'], 'is_menu_item' => $row['is_menu_item']);
            }
        }
        return $returnResult;
    }

    public static function checkFilePermission($fileName,$debug=false) {
        if($debug)
        {
            echo "<pre>";
            print_r($_SESSION['allPermissions']);
            die;
        }
        if((!empty($_SESSION['allPermissions'][$fileName]) && $_SESSION['allPermissions'][$fileName] > 0) || $fileName =="401.php" || $fileName =="login.php"){
            return TRUE;
        }else{
            return FALSE;
        }
    }

}
