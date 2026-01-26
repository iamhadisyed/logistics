<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'shoppingplatform.class',
    'shoppingplatformfilter.class',
    ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $shoppingData;

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);
        $sessionUser = SessionManager::getUser();
        if ($sessionUser->getUserType() != "admin") {
            util_redirect("index.php");
        }
        // common initialisation for ths page
        $this->setTitle("Shopping Platform");

        if (isset($this->form_vars["form_action"])) {

            $title = trim($this->form_vars["title"]);
            $page_key = trim($this->form_vars["page_key"]);
            $description = $_POST["description"];
            $translation_key = trim($this->form_vars["translation_key"]);
            if (isset($this->form_vars['shopping_id']) && $this->form_vars['shopping_id'] > 0) {
                $ShoppingPlatform = new ShoppingPlatform($this->form_vars['shopping_id']);
            } else {
                $ShoppingPlatform = new ShoppingPlatform();
            }
            $ShoppingPlatform->setTitle($title);
            $ShoppingPlatform->setPageKey($page_key);
            $ShoppingPlatform->setDescription($description);
            $ShoppingPlatform->setTranslationKey($translation_key);


            if (isset($_FILES['integration_logo'])) {
                $fileName = '';
                if (trim($_FILES['integration_logo']['name']) != '') {
                    $file_parts = pathinfo($_FILES['integration_logo']['name']);
                    $ext = strtolower(trim($file_parts['extension']));

                    $sourcePath = $_FILES['integration_logo']['tmp_name'];
                    $imageName = trim($title) . "-" . trim($page_key) . "." . trim($ext);
                    $targetPath = "../_assets/images/integration/" . $imageName;
                    if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                        if (move_uploaded_file($sourcePath, $targetPath)) {
                            $imageName = $imageName;
                        } else {
                            $imageName = '';
                        }
                    }
                }
                $ShoppingPlatform->setIntegrationLogo($imageName);
            }


            $ShoppingPlatform->save();
        }
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $shoppingEditData = new ShoppingPlatform($_GET['id']);
            $this->form_vars['id'] = $shoppingEditData->getId();
            $this->form_vars['title'] = $shoppingEditData->getTitle();
            $this->form_vars['description'] = $shoppingEditData->getDescription();
            $this->form_vars['page_key'] = $shoppingEditData->getPageKey();
            $this->form_vars['translation_key'] = $shoppingEditData->getTranslationKey();
        }
        if (isset($_GET['del_id']) && $_GET['del_id'] > 0 && isset($_GET['action']) && $_GET['action'] == "delete") {
            $ShoppingPlatformFilter = new ShoppingPlatformFilter();
            $ShoppingPlatformFilter->DeleteRecord($_GET['del_id']);
        }

        $ShoppingPlatformFilter = new ShoppingPlatformFilter();
        $this->shoppingData = $ShoppingPlatformFilter->getList();
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                tinymce.init({
                    selector: ".tinymce",
                    theme: "modern",
                    automatic_uploads: false,
                    height: 300,
                    valid_elements: '*[*]',
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table contextmenu paste code'
                    ],
                    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                    style_formats: [
                        {title: 'Bold text', inline: 'b'},
                        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                        {title: 'Example 1', inline: 'span', classes: 'example1'},
                        {title: 'Example 2', inline: 'span', classes: 'example2'},
                        {title: 'Table styles'},
                        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                    ]
                });
                $("#btnSaveNew").click(function () {
                    $("#adminForm").submit();
                });
            });
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
            <li><a href="../main/shopping_platform.php">Shopping Platform</a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Shopping Platform
                    </div>                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Title</label>
                            <div class="form-group">
                                <div class="input-group"> 
                                    <input type="text" class="form-control" name="title" id="title" value="<?php echo @$title; ?>" placeholder="Title"/>
                                    <span class="input-group-addon"> <input type="button" data-toggle="tooltip" data-placement="right" title="Title" class="tooltipbutton"  /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Page Key</label>
                            <div class="form-group">
                                <div class="input-group"> 
                                    <input type="text" class="form-control" name="page_key" id="page_key" value="<?php echo @$page_key; ?>" placeholder="Page Key"/>
                                    <span class="input-group-addon"> <input type="button" data-toggle="tooltip" data-placement="right" title="Page Key" class="tooltipbutton"  /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Translation Key</label>
                            <div class="form-group">
                                <div class="input-group"> 
                                    <input type="text" class="form-control" name="translation_key" id="translation_key" value="<?php echo @$translation_key; ?>" placeholder="Translation Key"/>
                                    <span class="input-group-addon"> <input type="button" data-toggle="tooltip" data-placement="right" title="Translation Key" class="tooltipbutton"  /></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label>Integration Logo</label>
                            <div class="form-group">
                                <div class="input-group"> 
                                    <input type="file" class="form-control" name="integration_logo" id="integration_logo" value="<?php echo @$integration_logo; ?>" placeholder="Integration Logo"  accept="image/*"  capture="camera"/>
                                    <span class="input-group-addon"> <input type="button" data-toggle="tooltip" data-placement="right" title="Integration Logo" class="tooltipbutton"  /></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Description</label>
                            <div class="form-group">
                                <textarea id="description" name="description"  class="tinymce form-control"  rows="7" cols="150" placeholder="Description"  rel="tooltip" data-original-title="Description" data-placement="bottom"><?php echo @$description; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-sm-3">
                            <a id="btnSaveNew" href="#" name="btnSave" class="btn btn-primary btn_save">Save</a>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="shopping_id"  value="<?php echo @$id; ?>"  />
                <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
            </div>  <!-- table_container -->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Shopping platform List 
                    </div>                    
                </div>
                <div class="portlet-body">
                    <!-- describe table filter -->
                    <!-- CONSIGNMENT TABLE -->
                    <div id='table_container'  class="main_grid2">
                        <div class="table-scrollable">
                            <table id='consignment_list' class="table table-striped table-bordered table-advance table-hover" >
                                <!-- table head -->
                                <thead>
                                    <tr>
                                        <th>Title</th> 
                                        <th>Page Key</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- table head-->
                                <!-- table body -->
                                <tbody>
        <?php
        foreach ($this->shoppingData as $list) {
            ?>
                                        <tr>
                                            <td><?php echo $list->getTitle(); ?></td>
                                            <td><?php echo $list->getPageKey(); ?></td>
                                            <td><img class="center-block" src="../_assets/images/integration/<?= $list->getIntegrationLogo() ?>" style="width:70px !important;" ></td>
                                            <td align="center"><a title="Edit" href="shopping_platform.php?id=<?php echo $list->getId(); ?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a onclick="return confirm('Are you sure you want to delete this shipping platform?')" title="Delete" href="shopping_platform.php?del_id=<?php echo $list->getId(); ?>&action=delete"><span class="glyphicon glyphicon-remove"></span></a></td>
                                        </tr>


        <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>  <!-- table_container -->
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
