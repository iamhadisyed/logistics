<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country details page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    private $error_msg = "";
    private $id = NULL;
    private $language_key = '';
    private $language = '';
    private $caption = '';
    private $successmsg = '';

    public function init()
    {

        // check admin user is authenticated
        if (!isset($_SESSION['admin']['id']) || is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }

        if (isset($_FILES) && !empty($_FILES['bulk_import']["name"])) {
            if ($_FILES['bulk_import']['type'] == 'application/excel' || $_FILES['bulk_import']['type'] == 'application/vnd.ms-excel') {
                $target_dir = "files/keyword/";
                $target_file = $target_dir . basename($_FILES["bulk_import"]["name"]);
                if (move_uploaded_file($_FILES["bulk_import"]["tmp_name"], $target_file)) {
                    $header = array(0, 1, 2);
                    $csvdata = array();
                    if (($handle = fopen('files/keyword/' . $_FILES['bulk_import']['name'], "r")) !== FALSE) {
                        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                            if (!$header)
                                $header = $row;
                            else
                                $csvdata[] = array_combine($header, $row);


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
                    if (!empty($csvdata)) {
                        $language_list = array();
                        $total_language_to_add = 0;
                        foreach ($csvdata as $key => $data) {
                            if ($key == 0) {
                                $total_language_to_add = sizeof($data);

                                for ($i = 1; $i < $total_language_to_add; $i++) {
                                    $language_list[$i] = $data[$i];
                                }
                            }
                            if ($key != 0) {

                                for ($i = 1; $i < $total_language_to_add; $i++) {
                                    $this->language_key = $data[0];
                                    $this->language = $language_list[$i];
                                    // $this->language = $data[1];
                                    $this->caption = $data[$i];
                                    $dateCreated = date("Y-m-d G:i:s");
                                    $user = Sessionmanager::getUser();
                                    $createdBy = $user->getId();

                                    $languageFilter = new LanguageKeysFilter();
                                    $languageFilter->addLanguageFilter($this->language);
                                    $languageFilter->addKeywordFilter($this->language_key);

                                    $list = $languageFilter->getList();

                                    if (count($list) == 0) {
                                        $languageKeyObj = new LanguageKeys();
                                        $languageKeyObj->setKeyword($this->language_key);
                                        $languageKeyObj->setLanguage($this->language);
                                        $languageKeyObj->setCaption($this->caption);
                                        $languageKeyObj->setDateCreated($dateCreated);
                                        //$languageKeyObj = new LanguageKeys();
                                        $languageKeyObj->setCreatedBy($createdBy);
                                        $languageKeyObj->save();
                                        $this->successmsg = 'Your data has been saved.';
                                    } else {

                                        $list_data = $list[0];
                                        $id = $list_data->getId();
                                        $languageKeyObjUpdate = new LanguageKeys($id);
                                        $languageKeyObjUpdate->setKeyword($this->language_key);
                                        $languageKeyObjUpdate->setLanguage($this->language);
                                        $languageKeyObjUpdate->setCaption($this->caption);
                                        $languageKeyObjUpdate->setDateUpdated(date("Y-m-d G:i:s"));
                                        $languageKeyObjUpdate->setUpdatedBy($user->getId());
                                        $languageKeyObjUpdate->save();
                                        $this->successmsg = 'Your data has been saved.';
                                    }
                                }
                            }
                        }
                    }
                    $this->language = '';
                    $this->language_key = '';
                    $this->caption = '';
                }
            } else {
                if (!isset($_POST['btn_update']) && !isset($_POST['btn_save'])) {
                    $this->error_msg = "Your File Type is not csv.";
                }
            }
        }

        if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
            $languageKeyId = $_GET['id'];
            $languageKeyObj = new LanguageKeys($languageKeyId);
            $this->id = $languageKeyObj->getId();
            $this->language_key = $languageKeyObj->getKeyword();
            $this->caption = $languageKeyObj->getCaption();
            $this->language = $languageKeyObj->getLanguage();
        }

        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_update'])) {

//            echo "<pre>";
//            print_r($_POST);
//            
//            echo "</pre>";
            //die;
            $user = Sessionmanager::getUser();

            $id = $_POST['id'];
            $this->language_key = $_POST['language_key'];
            $this->caption = $_POST['caption'];
            $this->language = $_POST['language'];


            $languageKeyObj = new LanguageKeys($id);


            $languageKeyObj->setKeyword($this->language_key);
            $languageKeyObj->setLanguage($this->language);
            $languageKeyObj->setCaption($this->caption);

            $languageKeyObj->setDateUpdated(date("Y-m-d G:i:s"));
            $languageKeyObj->setUpdatedBy($user->getId());


            //$createdBy = $user->getId();


            $languageKeyObj->save();


            util_redirect("languagekeys_list.php");
        }
        if (isset($_POST['btn_save'])) {
            $this->language = $_POST['language'];
            $this->language_key = $_POST['language_key'];
            $this->caption = $_POST['caption'];
            $dateCreated = date("Y-m-d G:i:s");
            $user = Sessionmanager::getUser();
            $createdBy = $user->getId();

            $languageFilter = new LanguageKeysFilter();
            $languageFilter->addLanguageFilter($this->language);
            $languageFilter->addKeywordFilter($this->language_key);

            //print_r($languageFilter);
            $list = $languageFilter->getList();

//			print_r($list); die;  		
            //echo count($list);
            //die;
            if (count($list) == 0) {
                $languageKeyObj = new LanguageKeys();
                $languageKeyObj->setKeyword($this->language_key);
                $languageKeyObj->setLanguage($this->language);
                $languageKeyObj->setCaption($this->caption);
                $languageKeyObj->setDateCreated($dateCreated);
                $languageKeyObj->setCreatedBy($createdBy);
                $languageKeyObj->save();

                util_redirect("languagekeys_list.php");
            } else {
                $this->error_msg = "Language keyword already exists.";
            }
            //print_r($languageKeyObj);
            //die;
            //$languageKeyObj->setIsActive('N');
            // go to courier list
            //util_redirect("manual_invoices.php");
        }
        /* ------------------------------------------------------------------------------ */
        // process delete
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == "confirmed_delete") {

            // save times and details
            /* $CouDelObj = new AgentData($this->id);
              $CouDelObj->delete();

              // go to courier list
              util_redirect("agent.php"); */
        }


        /* ------------------------------------------------------------------------------ */
        $this->agent_name = '';
        $this->setTitle("Admin - Agent Detail - " . $this->agent_name);
    }


    protected function addPagelavelCss()
    {
        ?>
        <link href="../includes/3rdparty/calendar/calendar.css" rel="stylesheet" type="text/css"/>
        <?php
    }

    public function addPagelavelJs()
    {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#btn_bulk_import').click(function () {
                    $('#caption').removeAttr('required');
                    $('#language_key').removeAttr('required');
                    $('#adminForm').submit();
                });

            });

            function SetPageSize() {
                document.getElementById('adminForm').submit();
            }

            $(document).ready(function () {

//                $('#adminForm').validator().on('submit', function (e) {
//        //                    if (e.isDefaultPrevented()) {
//        //                        $('.save').attr("disabled");
//        //                        return true;
//        //                    } else
//        //                    {
//        //                        $('.save').removeAttr("disabled");
//        //                        return true;
//        //                    }
//                });

                // Set the date pickers
                $("#btn_save").click(function () {
                    $("#form_action").val("Submit");
                    $("#adminForm").submit();
                });


            });
        </script>
        <script language="javascript" src="../includes/3rdparty/calendar/calendar.js"></script>
        <?php
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody()
    {
        ?>
        <form method="post" enctype="multipart/form-data" id="adminForm" name="adminForm" role="form" novalidate="true">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"><i class="glyphicon glyphicon-briefcase"></i>Language Keys</div>
                    <div class="tools"><a href="javascript:;" class="collapse"></a></div>
                </div>
                <div class="portlet-body">
                    <div class="note note-default">
                        <p><b>Add Language Key</b></p>
                    </div>
                    <div style="min-height:200px; max-height:1200px; height:auto !important;" data-rail-color="blue"
                         data-handle-color="blue">

                        <div class="note note-error">
                            <?php if ($this->error_msg != "") { ?>
                            <p class="alert alert-danger"> <?php echo $this->error_msg; ?></p>
                        </div>
                        <?php } ?>
                        <?php if (!empty($this->successmsg)) { ?>
                            <div class='alert alert-success'><?php echo $this->successmsg; ?></div>
                        <?php } ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-user"></i> </span>
                                                <?php $this->languageDropdown(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="invoceData">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-magic"></i> </span>
                                                <input type="text" name="language_key" id="language_key"
                                                       class="form-control" placeholder="Enter Key"
                                                       value="<?php echo $this->language_key; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" id="invoceData">
                                            <div class="input-group"><span class="input-group-addon"> <i
                                                            class="fa fa-calendar"></i> </span> <input type="text"
                                                                                                       name="caption"
                                                                                                       id="caption"
                                                                                                       class="form-control"
                                                                                                       placeholder="Enter Caption"
                                                                                                       value="<?php echo $this->caption; ?>"
                                                                                                       required>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group"><a class="btn btn-primary" href="languagekeys_list.php">List</a>&nbsp;&nbsp;&nbsp;
                                            <button type="submit" class="btn btn-primary save" id="btn_save"
                                                    name="<?php echo ($this->id != "" || $this->id > 0) ? 'btn_update' : 'btn_save'; ?>"
                                                    value="Save Changes" onclick="return validateFunction();"> Save
                                                changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="label-control">Import Language Key</label>
                                <br/>
                                <div class="note note-success">Note :You can Upload csv file. Your csv file have first
                                    column for Keyword and rest column you can add for language.Here is sample csv
                                    templete <br> <a id="btnCancel" href="../csv/temp_keyword.csv"
                                                     class="btn btn-danger btn-block" target="_blank"
                                                     style="width:200px;"><? echo Translation::GetCaption("TEMPLATE"); ?></a>
                                </div>
                                <br/>

                                <input type="file" name="bulk_import" id="bulk_import" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="button" class="btn btn-primary margin-top-20" name="btn_bulk_import"
                                       id="btn_bulk_import" value="Import">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="form_action" id="form_action" value=""/>
        </form>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }

    private function languageDropdown()
    {


        $languageFilter = new LanguageFilter();
        $languageFilter->AddOrderByLanguage();
        $list = $languageFilter->getColumnList("id, language");

        echo "<select id='language' name='language' class='form-control'>";
        echo "<option  value='Select Language'>Select Language</option>";

        $selected = "";

        foreach ($list as $language) {
            if ($language->getLanguage() == $this->language)
                $selected = " selected ";
            else
                $selected = "";

            echo "<option value='" . $language->getLanguage() . "' $selected>" . $language->getLanguage() . "</option>";
        }

        echo "</select>";
    }

    public function renderHead()
    {
        ?>
        <?php
    }

    /**
     * Returns boolean to indicate if the form is valid
     *
     */
    private function validate_form()
    {
        // Check that the country name is not blank
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
