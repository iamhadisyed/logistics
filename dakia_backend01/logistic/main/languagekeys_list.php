<?php
// get settings

require_once("../includes/settings/config.inc.php");
//include 'PDFMerger.php';


//ini_set('max_execution_time', 300);


class Page extends BasePage {

// consignment filter
    private $languageFilter;
// table description
    private $table_msg;
    private $num_valid; // number of valid consignments.
    private $column_name;
    private $sortby;
    private $flag;
    private $getLanguageList;

    /*     * *
     * Controller logic
     */

    protected function init() {
        $user = SessionManager::getUser();
        if ($user->getUserType() != "admin") {
            util_redirect("index.php");
        }


        if (isset($_POST['action']) && $_POST['action'] == "SAVE_LANGUAGE") {
//                    echo "<pre>";
//                    print_r($_POST);
//                    echo "</pre>";die;
            $language_key = $_POST['language_key'];
            $languageArr = $_POST['language'];
            $captionArr = $_POST['caption'];
            foreach ($languageArr as $index => $language) {
                $caption = $captionArr[$index];
                if (!empty($caption)) {
                    $languageFilter = new LanguageKeysFilter();
                    $languageFilter->addLanguageFilter($language);
                    $languageFilter->addKeywordFilter($language_key);
                    $list = $languageFilter->getList();
                    if (count($list) == 0) {
                        $languageKeyObj = new LanguageKeys();
                        $languageKeyObj->setKeyword($language_key);
                        $languageKeyObj->setLanguage($language);
                        $languageKeyObj->setCaption($caption);
                        $languageKeyObj->setDateCreated(date("Y-m-d H:i:s"));
//$languageKeyObj = new LanguageKeys();
//$languageKeyObj->setCreatedBy($createdBy);
                        $languageKeyObj->save();
//$this->successmsg = 'Your data has been saved.';
                    } else {
                        $list_data = $list[0];
                        $id = $list_data->getId();
                        $languageKeyObjUpdate = new LanguageKeys($id);
                        $languageKeyObjUpdate->setCaption($caption);
                        $languageKeyObjUpdate->setDateUpdated(date("Y-m-d H:i:s"));
//$languageKeyObjUpdate->setUpdatedBy($user->getId());
                        $languageKeyObjUpdate->save();
                    }
                }
            }
            echo 'SUCCESS';
            exit;
        }
        $this->setTitle("Admin - View Language Keys");
        $LanguageKeysFilter = new LanguageKeysFilter();
        $LanguageKeysFilter->addGroupByFilter('language');
        $this->getLanguageList = $LanguageKeysFilter->getColumnList('language');

        $this->languageFilter = new LanguageKeysFilter();
        $this->languageFilter->addGroupByFilter('keyword');
        $this->caption = trim($_POST["caption"]);
        $this->keyword = trim($_POST["keyword"]);
        $this->ddlLanguage = $_POST["ddlLanguage"];



        if ($this->caption != '') {
            $this->languageFilter->addCaptionLikeFilter($this->caption);
        }

        if ($this->keyword != '') {
            $this->languageFilter->addKeywordLikeFilter($this->keyword);
        }

        if ($this->ddlLanguage != 'Select Language' && $this->ddlLanguage != '') {
            $this->languageFilter->addLanguageFilter($this->ddlLanguage);
        }

        if (isset($this->form_vars["form_action"]) && $this->form_vars["form_action"] == "export") {
            $report_data_export = $this->languageFilter->getColumnList(" keyword, group_concat(`language` order by language asc SEPARATOR '||') as `language` , group_concat(id order by language asc SEPARATOR '||') as id,  group_concat(caption order by language asc  SEPARATOR '||') as caption, 
							 date_created, createdby");
            $this->ExportCsv($report_data_export);
        }
    }

    public function ExportCsv($language_list) {
        if (count($language_list) > 0) {


            $csv = "";
            $cr = "\r\n";

            foreach ($language_list as $language) {

                $csv .= $language->getKeyword() . ',';
                $caption_language = explode("||", $language->getCaption());
                for ($i = 0; $i < count($this->getLanguageList); $i++) {

                    if (array_key_exists($i, $caption_language)) {
                        $csv .= @$caption_language[$i] . ',';
                    } else {
                        $csv .= "" . ',';
                    }
                }


                $csv .= $cr;
            }

            $csvHeader = "KeyWord" . ',';
            foreach ($this->getLanguageList as $language_name) {
                $csvHeader .= $language_name->getLanguage() . ',';
            }



            $uniqueFileName = uniqid();
            $data = $csvHeader . $cr . $csv;

            header("Content-Type: application/csv");

            header("Content-disposition: attachment; filename=" . $uniqueFileName . ".csv");
            header("Content-Type: application/vnd.ms-excel");
            echo $data;
            exit;
        }
    }

    public function renderBody() {






        $numrows = $this->languageFilter->getPagingCount();

//echo $numrows;
//die;
//$numrows = GeneralReporting::getTotalRecordsFromSql($totalSql);

        if (isset($_POST['ddlPageSize'])) {
            $_SESSION['ddlPageSize'] = $_POST['ddlPageSize'];
            $rowsperpage = $_SESSION['ddlPageSize'];
            $_GET['pagesize'] = $rowsperpage;
        } else if (isset($_GET['pagesize'])) {
            $_SESSION['ddlPageSize'] = $_GET['pagesize'];
            $rowsperpage = $_SESSION['ddlPageSize'];
        } else if (isset($_SESSION['ddlPageSize'])) {
            $rowsperpage = $_SESSION['ddlPageSize'];
        } else {
            $rowsperpage = 10;
        }
//$rowsperpage = 1;
// get consignment count
        $totalpages = ceil($numrows / $rowsperpage);
// get the current page or set a default
        if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
// cast var as int
            $currentpage = (int) $_GET['currentpage'];

//echo "currrrent page " . $_GET['currentpage'];
        } else {
// default page num
            $currentpage = 1;
        } // end if
// if current page is greater than total pages...
        if ($currentpage > $totalpages) {
// set current page to last page
            $currentpage = $totalpages;
        } // end if
// if current page is less than first page...
        if ($currentpage < 1) {
// set current page to first page
            $currentpage = 1;
        } // end if
// the offset of the list, based on current page
//echo "current page final value " . $currentpage;
//$_GET['currentpage']


        $offset = ($currentpage - 1) * $rowsperpage;

//echo  $offset . " " . $rowsperpage;
//$SQL = $this->sql . " LIMIT " . $offset . "," . $rowsperpage;
//$this->report_data = GeneralReporting::getReportFromSql($SQL);
//$languageFilter = new LanguageKeysFilter();
        $this->languageFilter->setOffset($offset);
        $this->languageFilter->setRowsPerPage($rowsperpage);



        /* $locationFilter->AddActiveFilter("Y");
          $locationFilter->AddTypeFilter("L"); */

        /* if(isset($_POST["name"]) && $_POST["name"] != '')
          {
          $name = trim($_POST["name"]);
          $locationFilter->addLocationNameLikeFilter($name);
          } */

//$locationFilter->AddWarehouseIdFilter($user->getWarehouseId());

        $this->report_data = $this->languageFilter->getPagingColumnList("id, keyword, group_concat(`language` order by language asc SEPARATOR '||') as `language` , group_concat(id order by language asc SEPARATOR '||') as id,  group_concat(caption order by language asc  SEPARATOR '||') as caption, 
							 date_created, createdby");

//print_r($this->report_data);
//die;
        ?>
        <!-- END STYLE CUSTOMIZER -->
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title">Language Keys</h3>
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" data-rail-color="blue" data-handle-color="blue">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <?php $this->populateLanguageDropDown(); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="keyword" id="keyword" size="100" value="<?php echo htmlspecialchars($this->keyword); ?>" class="form-control" placeholder="Keyword" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="caption" id="caption" size="100" value="<?php echo htmlspecialchars($this->caption); ?>" class="form-control" placeholder="Caption" />
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <input id="form_action" type="hidden" name="form_action">
                                    <button type="submit" name="btnSearch" class="btn btn-primary">Search</button>
                                    <a id="btnExport" href="#" class="btn btn-primary" ><span></span>Export</a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-users"></i>Result
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse">
                        </a>
                        <a href="" class="fullscreen">
                        </a>
                        <a href="#portlet-config" data-toggle="modal" class="config">
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <div class="table-bordered">
                            <table class="table table-striped table-bordered table-advance table-hover" id="sample_editable_1">
                                <thead>
                                    <tr>
                                        <th class="red-back">Keyword</th>

                                        <?php
                                        if(count($this->getLanguageList) > 0)
                                        {
                                        foreach($this->getLanguageList as $language)
                                        {
                                        ?>
                                        <th class="red-back"><?php echo $language->getLanguage(); ?></th>
                                        <?php
                                        }
                                        }
                                        ?>
                                        <!--
                                         <th class="red-back">Language</th>
                                        <th class="red-back">Caption</th>
                                         <th class="red-back">Date Created</th>-->
                                        <th class="red-back">Edit</th>
                                        <!--<th class="red-back">Delete</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (count($this->report_data) > 0) {
                                        foreach ($this->report_data as $report_data) {
                                            ?>
                                            <tr>
                                                <td id="<?php echo $report_data->getKeyWord(); ?>" class="<?php echo $report_data->getId(); ?>"><?php echo $report_data->getKeyWord(); ?></td>
                                                <?php
                                                $caption_language = explode("||", $report_data->getCaption());
                                                $id = explode("||", $report_data->getId());
                                                for ($i = 0; $i < count($this->getLanguageList); $i++) {
                                                    ?>
                                                    <td id="caption_<?php echo $id[$i]; ?>" data-language_key="<? echo $report_data->getKeyWord();?>" data-language="<? echo $this->getLanguageList[$i]->getLanguage();  ?>">
                                                        <?php
                                                        if (array_key_exists($i, $caption_language))
                                                            echo @$caption_language[$i];
                                                        else
                                                            echo "";
                                                        ?>
                                                    </td>
                                                    <?php
                                                }
                                                ?>


                                                <td><a class="edit" id="<? echo $report_data->getKeyWord();?>" href='javascript:;'>Edit</a></td>
                                                <!--<td><a class="delete" id="<?php //echo $report_data->getKeyWord();     ?>" href='javascript:;'>Delete</a></td>-->
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr><td colspan="5"> No record found</td></tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <br/>
                            <?php
                            if (count($this->report_data) > 0) {
                                ?>
                                <div class="row">
                                    <div class="col-md-7">
                                        <?php
                                        if ($numrows > 10) {
                                            ?>

                                            <ul class="pagination">
                                                <?php
                                                /**                                                 * ***  build the pagination links ***** */
                                                // range of num links to show
                                                $range = 3;
                                                // if not on page 1, don't show back links
                                                if ($currentpage > 1) {
                                                    // show << link to go back to page 1
                                                    echo " &nbsp;<li><a href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=1&pagesize=$rowsperpage'><<</a></li> ";
                                                    // get previous page num
                                                    $prevpage = $currentpage - 1;
                                                    // show < link to go back to 1 page
                                                    echo " &nbsp;<li><a class='prev' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$prevpage&pagesize=$rowsperpage'><</a></li> ";
                                                } // end if
                                                // loop to show links to range of pages around current page
                                                for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                                                    // if it's a valid page number...
                                                    if (($x > 0) && ($x <= $totalpages)) {
                                                        // if we're on current page...
                                                        if ($x == $currentpage) {
                                                            // 'highlight' it but don't make a link
                                                            echo "<li class='active'><a href='#'><b>$x</b></a></li>";
                                                            // if not current page...
                                                        } else {
                                                            // make it a link
                                                            echo " &nbsp;<li><a  href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$x&pagesize=$rowsperpage'>$x</a></li> ";
                                                        } // end else
                                                    } // end if
                                                } // end for
                                                // if not on last page, show forward and last page links
                                                if ($currentpage != $totalpages) {
                                                    // get next page
                                                    $nextpage = $currentpage + 1;
                                                    // echo forward link for next page
                                                    echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$nextpage&pagesize=$rowsperpage'>></a></li> ";
                                                    // echo forward link for lastpage
                                                    echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . $_GET['action'] . "&currentpage=$totalpages&pagesize=$rowsperpage'>>></a></li> ";
                                                } // end if
                                                //echo "<li style='vertical-align:middle'>Total records <b>$numrows</b>&nbsp;&nbsp;</li>";
                                                ?>
                                            </ul>
                                            <?php
                                            /**                                             * *** end build pagination links ***** */
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-5" style="text-align: right;margin-top: 10px;">
                                        View&nbsp;<select class="select_dropdown form-control input-sm" style="display: inline-block; width: 65px;" name="ddlPageSize" onchange="SetPageSize();">
                                            <option value="10" <?php if ($rowsperpage == 10) echo "selected='selected'" ?> >10</option>
                                            <option value="25" <?php if ($rowsperpage == 25) echo "selected='selected'" ?> >25</option>
                                            <option value="50" <?php if ($rowsperpage == 50) echo "selected='selected'" ?> >50</option>
                                            <option value="100" <?php if ($rowsperpage == 100) echo "selected='selected'" ?> >100</option>
                                        </select>&nbsp;records | Found Total <?php echo $numrows; ?> records
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
            <?php
        }

        public function renderHead() {
            ?>
            <link rel="stylesheet" href="../_assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" type="text/css" >
            <style type="text/css">
                .table-scrollable{
                    margin: 0px 0!important;
                    overflow: hidden !important;
                }
            </style>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
            <script src="../assets/global/plugins/datatables/datatables.min.js"></script>
            <script src="../_assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
            <script src="../_assets/admin/pages/scripts/table-editable.js"></script>
            <script type="text/javascript">
                                        $(document).ready(function () {
                                            $('#btnExport').click(function () {
                                                $('#form_action').val('export');
                                                $('#adminForm').submit();
                                            });
                                            TableEditable.init();

                                            $("#sample_editable_1_length").parent().parent().remove();
                                            $("#sample_editable_1_paginate").parent().remove();
                                        });

                                        function SetPageSize() {
                                            document.getElementById('adminForm').submit();
                                        }



            </script>
            <?php
        }

        /**
         * Override to show the menu
         *
         */
        public function populateLanguageDropDown() {
            $languageFilter = new LanguageFilter();
            $languageFilter->AddOrderByLanguage();
            $list = $languageFilter->getColumnList("id, language");

            echo "<select id='ddlLanguage' name='ddlLanguage' class='form-control'>";
            echo "<option  value='Select Language'>Select Language</option>";

            $selected = "";

            foreach ($list as $language) {
                if ($language->getLanguage() == $this->ddlLanguage)
                    $selected = " selected ";
                else
                    $selected = "";

                echo "<option value='" . $language->getLanguage() . "' $selected>" . $language->getLanguage() . "</option>";
            }

            echo "</select>";
        }

        public function renderMenu() {
            $menu = new Adminmenu(Adminmenu::DASHBOARD);
            $menu->render();
        }

    }

// class

    /* ------------------------------------------------------------------------------ */
// create and render page
    $page = new Page(CONFIG_TEMPLATE_ADMIN);
    $page->show();
