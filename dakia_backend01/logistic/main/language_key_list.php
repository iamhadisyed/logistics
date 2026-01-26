<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'languagefilter.class',
    'languagekeysfilter.class'
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
//        if(!Permissions::checkFilePermission('flight_list.php')) {
//            header('Location: ' . BASE_URL);
//        }
        $languagesObj = new LanguageFilter();
        $languagesObj->addFilter("l.is_active = 'Y'");
        $languagesObj = $languagesObj->getList();
        foreach ($languagesObj as $language) {
            $this->languageColumns[] = $language->getLanguage();
        }
        if ($this->form_vars['action'] && $this->form_vars['action'] == 'delete_language_key') {
            if(!empty($this->form_vars['keyword'])) {
                $languageFilterObj = new LanguageKeysFilter();
                $languageFilterObj->addKeywordFilter($this->form_vars['keyword']);
                $languageKeys = $languageFilterObj->getList();
                foreach ($languageKeys as $languageKey) {
                    $languageKeyObj = new LanguageKeys($languageKey->getId());
                    $languageKeyObj->delete();
                }
                echo json_encode(['status' => 'success', 'message' => 'Language Key deleted Successfully.']);
                die;
            }
        }
        if ($_GET['action'] && $_GET['action'] == 'language_key_list') {
            $languageFilterObj = new LanguageKeysFilter();
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

                if ($dataTableColumnName == "search_etd")
                    $dataTableColumnName = "fi.etd";

                if ($dataTableColumnName == "search_eta")
                    $dataTableColumnName = "fi.eta";
                $languageFilterObj->addOrderBy($dataTableColumnName, $orderFalse);
            }
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                if (!empty($this->form_vars['language_keyword'])) {
                    $languageFilterObj->addKeywordFilter($this->form_vars['language_keyword']);
                }
            }

            $flightFilterCount = $languageFilterObj->getPagingCount();
            $iTotalRecords = $flightFilterCount;
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $languageFilterObj->setRowsPerPage($iDisplayLength);
            $languageFilterObj->setOffset($iDisplayStart);
            $languageFilterObj->addGroupByFilter(' Keyword');
            $languageFilterObj->addOrderBy('id', false);
            $dataGettingFlightInfoAndMapping = $languageFilterObj->getPagingColumnList('id, keyword, GROUP_CONCAT(caption SEPARATOR "~") AS key_captions ,GROUP_CONCAT(`language` SEPARATOR "~") AS caption_language');
            if (!empty($dataGettingFlightInfoAndMapping)) {
                foreach ($dataGettingFlightInfoAndMapping as $key => $preData) {
                    $currentArr = array();
                    $status = "<a href='javascript:void(0);' data-key_value='" . $preData->getKeyword() . "' data-toggle='tooltip' title='Delete' class='btn btn-xs btn-danger btn-outline center delete_language_key'><span class='fa fa-trash'></span></a>
<a href='add_language_key.php?keyword=" . $preData->getKeyword() . "' data-key_value='" . $preData->getKeyword() . "' data-toggle='tooltip' title='Delete' class='btn btn-xs btn-success btn-outline center edit_language_key'><span class='fa fa-edit'></span></a>";
                    $currentArr['keyword'] = $preData->getKeyword();
                    $keyCaptions = explode('~', $preData->getKeyCaptions());
                    $captionLanguage = explode('~', $preData->getCaptionLanguage());
                    $captions = array_combine($captionLanguage, $keyCaptions);
                    $array_keys = [];

                    foreach ($captions as $key => $value) {
                        if (!empty($key) && !empty($value)) {
                            $currentArr[$key] = $value;
                            $array_keys[] = $key;
                        }
                    }
                    foreach ($this->languageColumns as $languageKey) {
                        if (!in_array($languageKey, $array_keys)) {
                            $currentArr[$languageKey] = '';
                        }
                    }
                    $currentArr['action'] = $status;
                    $setDataArr[] = $currentArr;
                }
            }

            $setDataArrJson['data'] = (!empty($setDataArr) ? $setDataArr : 0);
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;

            echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
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
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"
              rel="stylesheet" type="text/css"/>
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

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"
                type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "language_key_list.php?action=language_key_list", // ajax source
                                headers: {},
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "action", "bSortable": false},
                                {"data": "keyword", "bSortable": true},
                                <?php foreach ($this->languageColumns as $language): ?>
                                {"data": "<?php echo $language; ?>", "bSortable": false},
                                <?php endforeach; ?>
                            ]
                        }
                    });
                };
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();

            function resetForm() {
                document.getElementById("add_assign_bag_to_flight").reset();
            }

            $(document).ready(function () {

                DataTableFun.init();
                $('#etd').datetimepicker({dateFormat: 'd M yy H:i', step: 10});
                $('#eta').datetimepicker({dateFormat: 'd M yy H:i', step: 10});

                $("#btnCancel").click(function () {
                    $("#form_action").val("cancel");
                    $("#adminForm").submit();

                });

                $(document).on('click', '.delete_language_key', function () {
                    var keyword = $(this).data("key_value");
                    swal({
                            title: "Are you sure you want to delete?",
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

                                $.ajax({
                                    type: "POST",
                                    url: "language_key_list.php",
                                    data: {keyword: keyword, action: 'delete_language_key'},
                                    dataType: "json",
                                    success: function (data) {
                                        if (data.status == 'success') {
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!", data.message, "success");
                                        } else if (data.status == 'error') {
                                            swal("Alert!", data.message, "info");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                });

                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();
                $('#btnSaveNew').click(function () {
                    if ($('#mawb').val() != '' && $('#weight').val() != '' && $('#pieces').val() != '' && $('#eta').val() != '' && $('#etn').val() && $('#flight').val()) {
                        $('#pre_alert_form').trigger('submit');
                    }
                });

                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        dateFormate: "yyyy-mm-dd"
                    });
                }

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
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"><i class="fa fa-language" aria-hidden="true"></i>
                    All Language Keys Caption
                </div>
                <div class="actions">
                    <a href="add_language_key.php" class="btn blue" data-original-title="" title=""><span></span><i
                                class="fa fa-plus"></i>&nbsp;Add Language Key</a>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Action</th>
                            <th>Keyword</th>
                            <?php foreach ($this->languageColumns as $language): ?>
                                <th><?php echo $language; ?></th>
                            <?php endforeach; ?>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit">
                                    <i class="fa fa-search"></i></button>
                                <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom">
                                    <i class="fa fa-times"></i></button>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="language_keyword"
                                       id="language_keyword">
                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
