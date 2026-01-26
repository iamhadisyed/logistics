<?php
// get settings
require_once("../includes/settings/config.inc.php");
ini_set('max_input_vars', 50000);
include_classes([

]);
class Page extends BasePage
{

    public $theme = [];
    private $themeId = 0;
    private $customStyle;
    private $user;

    protected function init()
    {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Add/Update Themes"
        );

        $this->user = SessionManager::getUser();
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $this->themeId = $_GET['id'];
            $this->theme = new Themes($this->themeId);

            if($this->theme->getSlug() != "") {
                $this->customStyle = file_get_contents( BASE_PATH."assets/layouts/layout4/css/custom.".$this->theme->getSlug().".min.css");
            }
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'saveTheme') {
            $themeId = $this->form_vars['theme_id'];
            $themeName = $this->form_vars['theme_name'];
            $slug = $this->form_vars['slug'];
            $loginTemplate = $this->form_vars['login_template'];
            $dashboardTemplate = $this->form_vars['dashboard_template'];
            $styleSheet = $this->form_vars['style_sheet'];
            $customStyle = $this->form_vars['custom_style'];
            $isActive = 0;
            if (isset($this->form_vars['chkActive']) && ($this->form_vars['chkActive'] == 'on')) {
                $isActive = 1;
            }
            $loginTemplateCheck = 0;
            if (isset($this->form_vars['login_page']) && ($this->form_vars['login_page'] == 'on')) {
                $loginTemplateCheck = 1;
            }
            $dateCreated = time();
            $createdBy = $this->user->getId();
            $checkSlug = new ThemesFilter();
            $checkSlug->where(['slug' => $slug]);
            $checkSlugExist = $checkSlug->getCount(false);
            $themeObj = new Themes();
            $editCase = 0;
            if($themeId != "" && $themeId > 0)  {
                $themeObj = new Themes($themeId);
                $checkSlugExist = 0;
                $editCase = 1;
            }
            if(!$checkSlugExist) {
                $loginTemplateName = "login-".$slug.".php";
                $themeObj->setName($themeName);
                if($themeId == "" && $themeId > 0) {
                    $themeObj->setSlug($slug);
                }
                $themeObj->setStyleSheet($styleSheet);
                $themeObj->setDashboardTemplate($dashboardTemplate);
                $themeObj->setIsActive($isActive);
                $themeObj->setCreatedBy($createdBy);
                $themeObj->setCreatedAt($dateCreated);
                $themeObj->save();
                $return = [];
                if(!empty($customStyle)) {
                    $stylesheetUrl = BASE_PATH."assets/layouts/layout4/css/custom.".$slug.".min.css";
                    file_put_contents($stylesheetUrl, $customStyle);
                }
                if(!$editCase) {
                    if ($loginTemplateCheck) {
                        @$login_file = $_FILES['login_template_file'];
                        if (!empty($login_file['name'])) {
                            $file_name = $login_file['name'];
                            $path_parts = pathinfo($file_name);
                            $ext = strtolower($path_parts['extension']);
                            $basename = $path_parts['basename'];
                            if ($ext == 'php') {
                                $new_file_name = "login_template_" . time() . "_" . $basename;
                                $relPath = '../_assets/login_template/' . $new_file_name;
                                if (!file_exists("../_assets/login_template/")) {
                                    @mkdir("../_assets/login_template/", 0775);
                                }
                                if (move_uploaded_file($login_file['tmp_name'], $relPath)) {
                                    $loginTemplateUrl = BASE_PATH . "main/login-" . $slug . ".php";
                                    $loginFileContent = file_get_contents(BASE_PATH . "_assets/login_template/" . $new_file_name);
                                    file_put_contents($loginTemplateUrl, $loginFileContent);
                                    unlink($relPath);
                                    $return['message'] = 'Template Save Successfully';
                                    $return['status'] = 'success';
                                } else {
                                    $return['message'] = 'file not uploaded success.';
                                    $return['status'] = 'fail';
                                }
                            } else {
                                $return['message'] = 'Invalid PHP File.';
                                $return['status'] = 'fail';
                            }
                        } else {
                            $return['message'] = 'No file found to import data.';
                            $return['status'] = 'fail';
                        }
                    } else {
                        $copyLoginTemplateUrl = BASE_PATH . "main/" . $loginTemplate;
                        $loginTemplateUrl = BASE_PATH . "main/" . $loginTemplateName;
                        $loginFileContent = file_get_contents($copyLoginTemplateUrl);
                        file_put_contents($loginTemplateUrl, $loginFileContent);
                        $return['message'] = 'Template Save Successfully';
                        $return['status'] = 'success';
                    }
                } else {
                    $return['message'] = 'Template Save Successfully';
                    $return['status'] = 'success';
                }
            } else {
                $return['message'] = 'This slug is already exist';
                $return['status'] = 'fail';
            }
            echo json_encode($return);
            die;
        }

    }

    protected function numberFormat($number,$decimalPoints = 2){
        return number_format($number,$decimalPoints,'.','');
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
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet"
              type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"
                type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/ace.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/mode-html.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/theme-dreamweaver.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/ace/jquery-ace.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('.htmlcode').ace({theme: 'dreamweaver', lang: 'css'});
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on("submit", "form", function(event)
                {
                    event.preventDefault();
                    var url = $(this).attr("action");
                    $.ajax({
                        url: url,
                        type: $(this).attr("method"),
                        dataType: "JSON",
                        data: new FormData(this),
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function (data)
                        {
                            if(data.status == "success") {
                                swal("Success!", data.message, "success");
                            } else {
                                swal("Sorry!", data.message, "error");
                            }
                        },
                        error: function (xhr, desc, err)
                        {
                            swal("Sorry!", "Please contact to admin or try later", "error");
                        }
                    });

                });
                $('#login_page').on('switchChange.bootstrapSwitch', function (event, state) {
                    if(state) {
                        $('#select_login_template_box').hide();
                        $('#upload_login_template_box').show();
                    } else {
                        $('#upload_login_template_box').hide();
                        $('#select_login_template_box').show();
                    }
                });
            });
        </script>
        <?php
    }

    protected function renderBody()
    {
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-money"></i>
                    Add/Update Theme
                </div>
                <div class="actions">
                    <a href="themes_list.php" class="btn btn-sm blue"><i class="fa fa-list"></i>&nbsp; Theme Listing</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                    $this->flashMsg->display();
                    ?>
                </div>
                <form name="theme_form" id="theme_form" action="themes_add.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="saveTheme">
                    <input type="hidden" name="theme_id" value="<?php echo $this->themeId; ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Theme Name</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="theme_name" id="theme_name" class="form-control validate_check" value="<?php echo (!empty($this->theme) ? $this->theme->getName() : "") ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Slug</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="slug" id="slug" class="form-control validate_check" value="<?php echo (!empty($this->theme) ? $this->theme->getSlug() : "") ?>"  <?php echo (($this->themeId > 0) ? "readonly='readonly'" : "" ); ?>) />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <?php
                                $stylesheertArr = [];
                                foreach(glob(BASE_PATH.'assets/layouts/layout4/css/*.*') as $filename){
                                    $cssname =  basename($filename);
                                    if(strpos($cssname,"custom") !== false) {
                                        $stylesheertArr[$cssname] = $cssname;
                                    }
                                }
                            ?>
                            <div class="form-group">
                                <label>Style sheet</label>
                                <?php $selected =  (!empty($this->theme) ? $this->theme->getStyleSheet() : "");  ?>
                                <?php echo Ddl::generateArrayDDL("style_sheet",$stylesheertArr,$selected,"Select Style Sheet",' class="form-control input-sm select2" ') ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Dashboard Template</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <input type="text" name="dashboard_template" id="dashboard_template" class="form-control validate_check" value="<?php echo (!empty($this->theme) ? $this->theme->getDashboardTemplate() : "") ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if($this->themeId == 0) { ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Login Page</label><br/>
                                <?php $chkActive = 0; ?>
                                <input <?php echo($chkActive == '1' ? 'checked="checked"' : ''); ?> id="login_page" name="login_page" type="checkbox" class="make-switch" data-on-text="Upload" check data-off-text="Select" data-on-color="primary" data-off-color="danger">
                            </div>
                        </div>
                        <div class="col-md-6" id="select_login_template_box">
                            <?php
                            $loginPageArr = [];
                            foreach(glob(BASE_PATH.'main/*.*') as $filename){
                                $loginname =  basename($filename);
                                if(strpos($loginname,"login") !== false) {
                                    $loginPageArr[$loginname] = $loginname;
                                }
                            }
                            ?>
                            <div class="form-group">
                                <label>Select Login Template</label>
                                <?php $selected =  (!empty($this->theme) ? $this->theme->getLoginTemplate() : "");  ?>
                                <?php echo Ddl::generateArrayDDL("login_template",$loginPageArr,$selected,"Select Login Template",' class="form-control input-sm select2" ') ?>
                            </div>
                        </div>
                        <div class="col-md-6" id="upload_login_template_box">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> Upload Login Template</label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium"
                                             data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="login_template_file" id="login_template_file">
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists"  data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Active</label><br/>
                                <?php $chkActive = (!empty($this->theme) ? $this->theme->getIsActive() : 0) ?>
                                <input <?php echo($chkActive == '1' ? 'checked="checked"' : ''); ?> name="chkActive" type="checkbox" class="make-switch" data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Custom Style</label><br/>
                                <textarea id="custom_style" name="custom_style"
                                          placeholder="tracking will be on same number or other number <br>Contact Details of operaton<br>Contact Details of customer Service <br>Contact Dtails of sales<br>Key contact number"
                                          rows="6" class="htmlcode form-control" data-toggle="tooltip"
                                          data-placement="top" title=""
                                          data-original-title="Additional Details"><?php echo $this->customStyle; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <input type="reset" id="btnReset" value="Reset" style="display:none;">
                            <button type="submit" id="btnSave" class="btn btn-sm blue">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    protected function renderHead() {
        ?>
        <style>
            #upload_login_template_box{
                display: none;
            }
        </style>
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