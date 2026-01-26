<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'currency.class',
    'currencyfilter.class',
	'country.class',
	'countryfilter.class'
    ]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $currencyList;
    private $id = NULL;
    private $breadcrumb = '';

    /*     * *
     * Controller logic
     */

    protected function init() {


//        if(!Permissions::checkFilePermission('add_ranges.php')) 
//                    util_redirect ("index.php");
        $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'currency_list.php' => 'Currency List'
        );
        //$this->constantlist = new ServiceConstantFilter();
        // common initialisation for ths page
        $this->setTitle("Currency List");

         if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            
            $currency_id             = $this->form_vars["id"];
            $currency_name           = $this->form_vars["currency_name"];
            $left_symbol             = $this->form_vars["left_symbol"];
            $right_symbol            = $this->form_vars["right_symbol"];
            $country_id              = $this->form_vars["country_id"];
            $exchange_rate           = $this->form_vars["exchange_rate"];
            $status                  = $this->form_vars["status"];
            if(!empty($status))
                $active = 1;
            else
                $active = 0;
        
            $error_array = array();
            $headerMessage = '';
            $constantobj = new Currency($currency_id);
            $constantobj->setCurrencyName($currency_name);
            $constantobj->setLeftsymbol($left_symbol);
            $constantobj->setRightsymbol($right_symbol);
            $constantobj->setCountryId($country_id);
            $constantobj->setCurrencyexchangerate($exchange_rate);
            $constantobj->setIsdefault(0);
            $constantobj->setClientdisplay(1);
            $constantobj->setIsactive($active);
            $constantobj->save();
               
            die;
            
        }
        else if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "editrecord") {
            $currencyid = $_POST['currencyid'];
            $this->id = $currencyid;
            $edit_array = array();
            $currencyValue = new Currency($currencyid);
            $edit_array['id']            = $currencyid;
            $edit_array['currency_name']     = $currencyValue->getCurrencyName();
            $edit_array['left_symbol']       = $currencyValue->getLeftSymbol();
            $edit_array['right_symbol']    = $currencyValue->getRightSymbol();
			$edit_array['country_id']    = $currencyValue->getCountryId();
            $edit_array['exchange_rate'] = $currencyValue->getCurrencyexchangerate();
            $edit_array['active'] = $currencyValue->getIsactive();
            echo json_encode($edit_array);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == "currency_ajax") {
            // Get current user
            $this->currencyList = new CurrencyFilter();
			$this->currencyList->addJoin('country coun','coun.id = c.country_id','LEFT');
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                
                    $dataTableColumnName = str_replace("_","",trim($dataTableColumnName));
                if(trim($dataTableColumnName) == 'exchangerate')
                    $dataTableColumnName = "currencyexchangerate";
                
                if(trim($dataTableColumnName) == 'activeflag')
                    $dataTableColumnName = "isactive";
                $this->currencyList->AddOrderBy("c." .$dataTableColumnName, $orderFalse);
                
            }
            else
            {
                $this->currencyList->AddOrderBy('c.currencyname', false);                
            }
           
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $search_currency = $this->form_vars['search_currency'];
                if (!empty($search_currency)) {
                    $this->currencyList->addFieldLikeFilter('c.currencyname', $search_currency);
                }
                $search_leftsymbol = $this->form_vars['search_leftsymbol'];
                if (!empty($search_leftsymbol)) {
                    $this->currencyList->addFieldLikeFilter('c.leftsymbol', $search_leftsymbol);
                }
                $search_rightsymbol = $this->form_vars['search_rightsymbol'];
                if (!empty($search_rightsymbol)) {
                    $this->currencyList->addFieldLikeFilter('c.rightsymbol', $search_rightsymbol);
                }
                $search_exchangerate = $this->form_vars['search_exchangerate'];
                if (!empty($search_exchangerate)) {
                    $this->currencyList->addFieldLikeFilter('c.currencyexchangerate', $search_exchangerate);
                }
                $search_isactive = $this->form_vars['search_isactive'];
                if (trim($search_isactive) != "") {
                    $this->currencyList->addIsactiveFilter($search_isactive);
                }
                $search_country_id = $this->form_vars['search_country_id'];
                if (!empty($search_country_id)) {
                    $this->currencyList->addFieldFilter('c.country_id', $search_country_id);
                }
            }

            $iTotalRecords = $this->currencyList->getPagingCount();
           
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->currencyList->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->currencyList->setOffset($iDisplayStart);
            $currency_list = $this->currencyList->getPagingList("c.*, coun.name AS country_name", false);
            
            $constantDataArr = array();
            foreach ($currency_list as $currency) {
                $constantArr['actionss'] = "<a data-id =" . $currency->getId() . " class='update_rate btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-refresh'></span> </a>";
                $constantArr['actionss'] .= "<a data-id =" . $currency->getId() . " class='btnedit btn-xs blue btn mt-ladda-btn ladda-button btn-outline' title='Edit'><span class='fa fa-pencil'></span> </a>";
                $constantArr['currency_name'] = $currency->getCurrencyName();
                $constantArr['left_symbol'] = $currency->getLeftSymbol();
                $constantArr['right_symbol'] = $currency->getRightSymbol();
				$constantArr['country_id'] = $currency->getCountryName();
                $constantArr['exchange_rate'] = $currency->getCurrencyExchangeRate();
                $constantArr['active_flag'] = '<div class="text-center">' . ($currency->getIsActive() ? '<span class="label label-sm label-success active_status" data-id="'.$currency->getId().'" data-new_status="0">Yes</span>' : '<span class="label label-sm label-danger active_status" data-id="'.$currency->getId().'" data-new_status="1">No</span>') . '</div>';

                $constantDataArr[] = $constantArr;                
            }
            $constantDataArr['data'] = $constantDataArr;
            $constantDataArr['draw'] = $sEcho;
            $constantDataArr['recordsTotal'] = $iTotalRecords;
            $constantDataArr['recordsFiltered'] = $iTotalRecords;
            //echo json_encode($constantDataArr);
            echo json_encode($constantDataArr, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        }

		if (isset($this->form_vars["func"]) && $this->form_vars["func"] == "update_status") {
			$currencyid = $this->form_vars['currencyid'];
			$new_status = $this->form_vars['new_status'];
			$currency = new Currency($currencyid);
			$currency->setIsActive($new_status);
			$currency->save();
			$output = ['status' => 'success','isactive' => $new_status];
			echo json_encode($output);
			exit;
		}
    }

    /*     * *
     * Insert content in to HTML Head section
     */

    protected function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
			.active_status{
				cursor: pointer;
			}
		</style>
        <?php
    }

    public function addPagelavelJs() {
	?>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        
	<?php
    }

    protected function renderFooter() {
        ?>
        <script>
              $(document).ready(function () {
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "currency_list.php",
                        data: {func: "cancelrecord"}
                    }).done(function (data) {
                        $("#rangeForm")[0].reset();
                    });
                });
                $(document).on('click', '#btn_Save', function () {
                   
                    $('#rangeForm').validator().on('submit', function (e) {
                        if (e.isDefaultPrevented())
                        {
                            return false;
                        } else
                        {
                            var id = $('#id').val();
                            $.ajax({
                                method: "POST",
                                url: "currency_list.php",
                                data: $('#rangeForm').serialize()
                            }).done(function (data) {
                                
                                $('#btn_Save').val("Save");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $("#rangeForm")[0].reset();
                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                                $('#manage-data-table').DataTable().ajax.reload();
                            });
                            return false;
                        }
                    });
                    $("#rangeForm").submit();
                });

                $(document).on('click', '.btnedit', function () {
                    var e = $(this);
                    var currencyid = e.data('id');
                    $.ajax({
                        method: "POST",
                        url: "currency_list.php",
                        data: {currencyid: currencyid, func: "editrecord"}
                    }).done(function (data){
                        //By using javasript json parse
                        var t = JSON.parse(data);
                        $('#btn_Save').val("Update");                        
                        $('#currency_name').val(t.currency_name);
                        $('#left_symbol').val(t.left_symbol);
                        $('#right_symbol').val(t.right_symbol);
						$('#country_id').val(t.country_id);
                        $('#exchange_rate').val(t.exchange_rate);
						$("#country_id").selectpicker('refresh');
                        if(t.active === '1')
                            $('input[name=status]').bootstrapSwitch('state', true);
                        else
                             $('input[name=status]').bootstrapSwitch('state', false);
                        $('#id').val(t.id);
                        $('html, body').animate({scrollTop: '0px'}, 300);
                    });
                });
			  	$(document).on('click', '.active_status', function () {
					var element = $(this);
					var currencyid = $(element).data("id");
					var new_status = $(element).data("new_status");
					$.ajax({
						method: "POST",
						dataType: 'json',
						url: "currency_list.php",
						data: {currencyid: currencyid, new_status:new_status, func: "update_status"},
					}).done(function (data){
						if(data.status == 'success') {
							if (data.isactive == '1') {
								$(element).data("new_status", "0");
								$(element).removeClass('label-danger').addClass('label-success').html('Yes');
							} else {
								$(element).data("new_status", "1");
								$(element).removeClass('label-success').addClass('label-danger').html('No');
							}
						}
					});
				});

				$(document).on('click', '.update_rate', function () {
					var element = $(this);
					var currencyid = $(element).data("id");
					$.ajax({
					  method: "POST",
					  dataType: 'json',
					  url: "../schedular/cron_currency_rate_update.php",
					  data: {cid: currencyid},
					}).done(function (data){
					  if(data.status == 'success') {
						  var rate = parseFloat(data.data[currencyid].rate).toFixed(4);
						  $(element).parent().parent().find("td.update_rate_td").html(rate);
					  }
					});
				});

            });
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var grid = new Datatable();
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
                                [10, 20, 50, 100],
                                [10, 20, 50, 100] // change per page values here 
                            ],
                            "pageLength": 10, // default record count per page
                            "ajax": {
                                "url": "currency_list.php?action=currency_ajax", // ajax source
                                headers: {
                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actionss","bSortable": false},
                                {"data": "currency_name"},
                                {"data": "left_symbol"},
                                {"data": "right_symbol"},
								{"data": "country_id"},
                                {"data": "exchange_rate","sClass":"update_rate_td"},
                                {"data": "active_flag"}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();

            $(document).ready(function () {
                DataTableFun.init();

            });

        </script>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <?php
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        if (errorList::getItem()->getErrorCount() > 0) {
            ?>
            <div class="alert alert-info"><?php errorList::getItem()->render(); ?></div>
            <?php
        }
        ?>
        <div class="main_formpage">
             <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-plus"></i>
                       
                           Add Currency
                    </div>
                    <div class="actions">
                        
                    </div>
                </div>
                <div class="portlet-body">
                    <form name="rangeForm" id="rangeForm" action="" method="POST">           
                        <div class="row">
                            <div class="col-md-12 hidden" id="successmsg">
                                <div class="alert alert-success" id="success_msg"> </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-sm-2">
								<div class="form-group">
									<label >Currency Name</label>
									<div class="input-group input-group-sm">
										<div class="input-group-addon"> <i class="fa fa-user"></i> </div>
										<input name="currency_name" id="currency_name" value="" size="50" class="form-control" title="Currency Name" maxlength="35" placeholder="Currency Name" rel="tooltip" data-original-title="Currency Name" type="text" required>
										<span class="input-group-addon red-18">*</span>
									</div>
								</div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group"> 
                                    <label>Left Symbol</label>
                                    <div class="input-group input-group-sm input-icon right" > 
                                        <input name="left_symbol" id="left_symbol" value="" size="50" class="form-control" title="Left Symbol" maxlength="35" placeholder="Left Symbol" rel="tooltip" data-original-title="Left Symbol" type="text" required>
                                        <span class="input-group-addon red-18">*</span> 
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-group"> 
                                    <label>Right Symbol</label>
                                    <div class="input-group input-group-sm input-icon right" > 
                                        <input name="right_symbol" id="right_symbol" value="" size="50" class="form-control" title="Right Symbol" maxlength="35" placeholder="Right Symbol" rel="tooltip" data-original-title="Right Symbol" type="text" required>
                                        <span class="input-group-addon red-18">*</span> 
                                    </div>
                                </div>
                            </div>
							<div class="col-sm-2">
								<div class="form-group">
									<label >Currency Country</label>
									<div class="input-group input-group-sm">
										<div class="input-group-addon"> <i class="fa fa-globe"></i> </div>
										<?php echo Ddl::generateCountryDDL('country_id', @$country_id, 'id', ' class="not_clear form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" '); ?>
									</div>
								</div>
							</div>
                            <div class="col-md-2">
                                <div class="form-group"> 
                                    <label>Exchange Rate</label>
                                    <div class="input-group input-group-sm input-icon right" > 
                                        <input name="exchange_rate" id="exchange_rate" value="" size="50" class="form-control" title="Exchange Rate" maxlength="35" placeholder="Exchange Rate" rel="tooltip" data-original-title="Exchange Rate" type="text" required>
                                        <span class="input-group-addon red-18">*</span> 
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group col-md-2">
                            <label class="label-account">Status</label>
                            <div class="input-group"> 
                                <input id="status" name="status" type="checkbox" class="make-switch" <?php echo ($this->status == '1' ? 'checked="checked"' : ''); ?>  data-on-text="Yes" check data-off-text="No" data-on-color="primary" data-off-color="danger">
                            </div>
                        </div>
                        </div> 
                        <div style="clear:both"></div> 
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                        <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="<?php echo Translation::GetCaption("SAVE"); ?>"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>   
                    </form>
                </div>
            </div>
            
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="icon-list"></i>
                            Currency List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th>Action</th>
                                    <th>Currency Name</th>
                                    <th>Left Symbol</th>
                                    <th>Right Symbol</th>
									<th>Country</th>
                                    <th>Exchange Rate</th>
                                    <th>Active</th>
                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn-xs filter-submit margin-bottom blue btn btn-default mt-ladda-btn ladda-button btn-outline"><i class="fa fa-search"></i></button>
                                            <button class="btn-xs red filter-cancel btn mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-xs" name="search_currency" id ="search_currency" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-xs" name="search_leftsymbol" id ="search_leftsymbol" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-xs" name="search_rightsymbol" id ="search_rightsymbol" />
                                    </td>
                                    <td>
										<div class="col-md-12">
											<div class="row">
												<?php echo Ddl::generateCountryDDL('search_country_id', '', 'id', ' class="not_clear form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" '); ?>
											</div>
										</div>
                                    </td>
									<td>
                                        <input type="text" class="form-control form-filter input-xs" name="search_exchangerate" id ="search_exchangerate" />
                                    </td>
                                    <td>
										<select name="search_isactive" class="form-control form-filter">
											<option value="">All</option>
											<option value="1">Active</option>
											<option value="0">In-active</option>
										</select>
									</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="id" value="<?php echo @$id; ?>" />
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

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
