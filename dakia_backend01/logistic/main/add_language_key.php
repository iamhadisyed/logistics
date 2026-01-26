<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    "languagefiter.class"
]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage
{

    private $uploadfilelist = "";
    private $user;
    public $languageColumns = [];

    protected function init()
    {
        $this->user = SessionManager::getUser();
        $languagesObj = new LanguageFilter();
        $languagesObj->addFilter("l.is_active = 'Y'");
        $languagesObj = $languagesObj->getList();
        foreach ($languagesObj as $language) {
            $this->languageColumns[] = $language->getLanguage();
        }
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'Add Language Key'
        );
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == 'update_language_key') {
            $errors = '';

            if (empty($this->form_vars['language_key'])) {
                $errors .= "Please Enter language key. <br />";
            }

            if (empty($errors)) {
                $language_key = $this->form_vars['language_key'];
                $dateCreated = date("Y-m-d G:i:s");
                $createdBy = $this->user->getId();
                $languageKeyDelete = new LanguageKeysFilter();
                $languageKeyDelete->addKeywordFilterL($language_key);
                $languageKeyDelete->delete();
                foreach ($this->languageColumns as $languageColumn) {
                    $languageKeyObj = new LanguageKeys();
                    $languageKeyObj->setKeyword($language_key);
                    $languageKeyObj->setLanguage($languageColumn);
                    if(($languageColumn == 'en-GB' && empty($this->form_vars['en-GB'])) || ($languageColumn == 'en-US' && empty($this->form_vars['en-US']))) {
                        $captionValue = $language_key;
                    } else {
                        $captionValue = $this->form_vars[$languageColumn];
                    }
                    $languageKeyObj->setCaption($captionValue);
                    $languageKeyObj->setDateCreated($dateCreated);
                    $languageKeyObj->setCreatedBy($createdBy);
                    $languageKeyObj->setDateUpdated(date("Y-m-d G:i:s"));
                    $languageKeyObj->save();
                }
                if (!empty($languageKeyObj->getId())) {
                    $msg['success'] = "<div class='col-md-12 alert alert-success'>Language keyword Updated Successfully.</div>";
                } else {
                    $msg['error'] = "<div class='col-md-12 alert alert-danger'>Something went wrong.</div>";
                }
            } else {
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>" . $errors . "</div>";
            }

            echo json_encode($msg);
            exit;
        }
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == 'add_language_key') {
            if (isset($_FILES) && !empty($_FILES['bulk_import']["name"])) {
                if ($_FILES['bulk_import']['type'] == 'application/excel' || $_FILES['bulk_import']['type'] == 'application/vnd.ms-excel') {
                    $target_dir = _ASSETS_PATH . "keywords/";
                    if(!is_dir($target_dir)) {
                        mkdir($target_dir);
                    }
                    $target_file = $target_dir . basename($_FILES["bulk_import"]["name"]);
                    if (move_uploaded_file($_FILES["bulk_import"]["tmp_name"], $target_file)) {
                        $header = array(0, 1, 2);
                        $csvData = array();
                        if (($handle = fopen(_ASSETS_PATH . "keywords/" . $_FILES['bulk_import']['name'], "r")) !== FALSE) {
                            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                if (!$header)
                                    $header = $row;
                                else
                                    $csvData[] = array_combine($header, $row);


                            }

                            fclose($handle);
                        }
//                    echo '<pre>';
//                    print_r($csvdata);
//                    foreach ($csvdata as $k => $data) {
//                       echo $data[0];
//                    }
//
//                    exit;
                        if (!empty($csvData)) {
                            $language_list = array();
                            $total_language_to_add = 0;
                            foreach ($csvData as $key => $data) {
                                if ($key == 0) {
                                    $total_language_to_add = sizeof($data);

                                    for ($i = 1; $i < $total_language_to_add; $i++) {
                                        $language_list[$i] = $data[$i];
                                    }
                                }
                                if ($key != 0) {

                                    for ($i = 1; $i < $total_language_to_add; $i++) {
                                        $language_key = $data[0];
                                        $language = $language_list[$i];
                                        // $this->language = $data[1];
                                        $caption = $data[$i];
                                        $dateCreated = date("Y-m-d G:i:s");
                                        $user = Sessionmanager::getUser();
                                        $createdBy = $user->getId();

                                        $languageFilter = new LanguageKeysFilter();
                                        $languageFilter->addLanguageFilter($language);
                                        $languageFilter->addKeywordFilter($language_key);

                                        $list = $languageFilter->getList();

                                        if (count($list) == 0) {
                                            $languageKeyObj = new LanguageKeys();
                                            $languageKeyObj->setKeyword($language_key);
                                            $languageKeyObj->setLanguage($language);
                                            $languageKeyObj->setCaption($caption);
                                            $languageKeyObj->setDateCreated($dateCreated);
                                            $languageKeyObj->setDateUpdated(date("Y-m-d G:i:s"));
                                            //$languageKeyObj = new LanguageKeys();
                                            $languageKeyObj->setCreatedBy($createdBy);
                                            $languageKeyObj->save();
                                            $msg['success'] = "<div class='col-md-12 alert alert-success'>File imported Successfully.</div>";
                                        } else {
                                            $msg['error'] = "<div class='col-md-12 alert alert-danger'>Something went wrong.</div>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (!isset($_POST['btn_update']) && !isset($_POST['btn_save'])) {
                        $msg['error'] = "<div class='col-md-12 alert alert-danger'>Your File Type is not csv.</div>";
                    }
                }
            } else if (!empty($this->form_vars['file_import']) && empty($_FILES['bulk_import']["name"])) {
                $msg['error'] = "<div class='col-md-12 alert alert-danger'>Please upload csv.</div>";
            } else {
                $errors = '';

                if (empty($this->form_vars['language_key'])) {
                    $errors .= "Please Enter language key. <br />";
                }

                if (empty($errors)) {
                    $language = $this->form_vars['language'];
                    $language_key = $this->form_vars['language_key'];
                    $dateCreated = date("Y-m-d G:i:s");
                    $createdBy = $this->user->getId();
                    $languageFilter = new LanguageKeysFilter();
//                    $languageFilter->addLanguageFilter($language);
                    $languageFilter->addKeywordFilter($language_key);

                    //print_r($languageFilter);
                    $list = $languageFilter->getList();

//			print_r($list); die;
                    //echo count($list);
                    //die;
                    if (count($list) == 0) {
                        foreach ($this->languageColumns as $languageColumn) {
                            $languageKeyObj = new LanguageKeys();
                            $languageKeyObj->setKeyword($language_key);
                            $languageKeyObj->setLanguage($languageColumn);
                            if(($languageColumn == 'en-GB' && empty($this->form_vars['en-GB'])) || ($languageColumn == 'en-US' && empty($this->form_vars['en-US']))) {
                                $captionValue = $language_key;
                            } else {
                                $captionValue = $this->form_vars[$languageColumn];
                            }
                            $languageKeyObj->setCaption($captionValue);
                            $languageKeyObj->setDateCreated($dateCreated);
                            $languageKeyObj->setCreatedBy($createdBy);
                            $languageKeyObj->setDateUpdated(date("Y-m-d G:i:s"));
                            $languageKeyObj->save();
                        }
                        if (!empty($languageKeyObj->getId())) {
                            $msg['success'] = "<div class='col-md-12 alert alert-success'>Language keyword Added Successfully.</div>";
                        } else {
                            $msg['error'] = "<div class='col-md-12 alert alert-danger'>Something went wrong.</div>";
                        }
                    } else {
                        $msg['error'] = "<div class='col-md-12 alert alert-danger'>Language keyword already exists.</div>";
                    }
                } else {
                    $msg['error'] = "<div class='col-md-12 alert alert-danger'>" . $errors . "</div>";
                }
            }

            echo json_encode($msg);
            exit;
        }
    }

    protected function addPagelavelCss()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet"
              type="text/css"/>
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet"
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
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>

        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            var grid = null;

            $(document).ready(function () {
                // $('#btnSaveNew').attr('disabled', 'disabled');
                //TableDatatablesEditable.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }

                $('#btn_bulk_import').click(function () {
                    $('#file_import').val(1);
                    $("#add_language_key_form").trigger('submit');
                });

                $('#btnSaveNew').click(function () {
                    $('#file_import').val(0);
                    // $("#add_language_key_form").trigger('submit');
                });

                $("#add_language_key_form").on('submit', function (e) {
                    $.blockUI();
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'add_language_key.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function () {
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);
                            if (obj.success) {
                                $('.msg').html(obj.success);
                                $('#add_language_key_form').trigger("reset");
                                $("#select_language").val('');
                            } else if (obj.error) {
                                $('.msg').html(obj.error);
                            }
                        }
                    });
                });
            });
        </script>
        <?php
    }

    protected function renderHead()
    {
        ?>
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css"/>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody()
    {
        $sessionUser = SessionManager::getUser();
        $languageKeyObjString = [];
        if (!empty($_GET['keyword'])) {
            $languageKeyObjString = new LanguageKeysFilter();
            $languageKeyObjString->addKeywordFilter($_GET['keyword']);
            $languageKeyObjString = $languageKeyObjString->getList();
        }
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-language"></i>
                    Add Language Key
                </div>
                <div class="actions">
                    <a href="language_key_list.php" class="btn blue" data-original-title="" title=""><span></span><i
                                class="fa fa-list"></i>&nbsp;Language Key List</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <form action="javascript:{};" method="post" enctype="multipart/form-data" id="add_language_key_form"
                      name="add_language_key_form">
                    <div class="msg"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="label-account">Enter Key</label>
                            <div class="form-group">
                                <div class="input-group"><span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input type="text" class="form-control" name="language_key" id="language_key"
                                           value="<?php echo !empty($_GET['keyword']) ? $_GET['keyword'] : ''; ?>"
                                           size="100" style="" maxlength="50"
                                           rel="tooltip" placeholder="Enter Key" data-original-title="Enter Key"
                                           required/>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($this->languageColumns as $language): ?>
                            <div class="col-md-12">
                                <label class="label-account"><?php echo $language; ?> Caption</label>
                                <div class="form-group">
                                    <div class="input-group"><span class="input-group-addon"> <i
                                                    class="fa fa-ticket"></i> </span>
                                        <input type="text" class="form-control" name="<?php echo $language; ?>"
                                               id="<?php echo $language; ?>"
                                            <?php foreach ($languageKeyObjString as $languageKeyObjValue): ?>
                                                <?php if ($languageKeyObjValue->getLanguage() == $language): ?>
                                                    <?php
                                                    $objValue = $languageKeyObjValue->getCaption();
                                                    ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                               value="<?php echo !empty($objValue) ? $objValue : ''; ?>"
                                               size="100" style="" maxlength="50"
                                               rel="tooltip" placeholder="<?php echo $language; ?> Caption"
                                               data-original-title="<?php echo $language; ?> Caption"/>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-md-12">
                            <label class="label-account">&nbsp;</label>
                            <div class="form-group">
                                <button class="btn btn-primary btn_save" name="btnSave" id="btnSaveNew" type="submit">
                                    Save
                                </button>
                                <input type="hidden" name="is_update" id="is_update"
                                       value="<?php echo !empty($_GET['keyword']) ? 1 : 0; ?>">
                                <input type="hidden" name="form_action" id="form_action"
                                       value="<?php echo !empty($_GET['keyword']) ? 'update_language_key' : 'add_language_key'; ?>"/>
                            </div>
                        </div>
                    </div>
                    <?php if (empty($_GET['keyword'])): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="label-control">Import Language Key</label>
                                <br/>
                                <div class="note note-success">Note :You can Upload csv file. Your csv file have first
                                    column for Keyword and rest column you can add for language.Here is sample csv
                                    template <br> <a id="btnCancel" href="../csv/temp_keyword.csv"
                                                     class="btn btn-danger btn-block" target="_blank"
                                                     style="width:200px;"><?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                </div>
                                <br/>

                                <input type="file" name="bulk_import" id="bulk_import" value="">
                                <input type="hidden" name="file_import" id="file_import" value="">
                            </div>
                            <div class="col-md-6">
                                <input type="button" class="btn btn-primary margin-top-20" name="btn_bulk_import"
                                       id="btn_bulk_import" value="Import">
                            </div>
                        </div>
                    <?php endif; ?>
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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
