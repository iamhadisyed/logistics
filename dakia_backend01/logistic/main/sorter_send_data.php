<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([  
                    'ftpimplicitssl.class',
                    ], '3rdparty');
include_classes([  
                    'iaddress.class',
                    'optimussorter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'carrierdatafilelog.class',
                    'carrierdatafilelogfilter.class',
                    ]);

/* * *
 * Page for editing a user
 */

class Page extends BasePage {

    private $constantlist;
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
                                        'index.php'=>Translation::GetCaption("HOME"),
                                        'sorter_send_data.php'=>'Sorter Send Data'
            );
        //$this->constantlist = new ServiceConstantFilter();
        // common initialisation for ths page
        $this->setTitle("Constant List");
        if (isset($_POST["form_action"]) && $_POST["form_action"] == "saverecord") {
            
            $hawb             = trim($this->form_vars["hawb"]);
            if (trim($hawb) != '' ) {
             $hawb = nl2br($hawb);
             $HawbArray = explode('<br />', $hawb);
             if(count($HawbArray) > 0){
             foreach ($HawbArray as $k => $value) {
                 $HawbArray[$k] = trim($value);
                 $awbArray[$k] = "'" . trim($value) . "'";
             }
             
             $consignmentFilter = new ConsignmentFilter();
             $consignmentFilter->addJoin("services s", "s.id = c.service_id");
             $consignmentFilter->addJoin("user u", "u.id = c.user_id");
             $consignmentFilter->addJoin("customer_account ua", "ua.id = u.user_account_id");
             $consignmentFilter->addJoin("country con", "con.id = c.country_id");
             $consignmentFilter->addFilterNew(" c.awb in (".implode(",", $awbArray).")");
             #$consignmentFilter->addOrFilter("    c.awb in (".implode(",", $awbArray).")");
             $consignmentList = $consignmentFilter->getListNew("c.id, c.awb,c.hawb, ua.user_account, s.code as service_code,s.name as service_name, s.carrier_id as carrier_id,c.other_routing_code, c.number_pieces, con.iso as country_iso_code, con.name as country_name, c.postcode, c.weight, c.routing_code_eur, c.optimus_sorter", false, false);
             if(count($consignmentList) > 0)
             {
                $optimus = new OptimusSorter();
                if($optimus->addConsignment($consignmentList))
                {
                        $optimus->sendBookings();			
                }
             }
             }
            }
            die;
            
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
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
            
            
            
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
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../js/validator.min.js" type="text/javascript"></script> 
        

        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
            $(document).ready(function () {
             
                $(document).on('click', '#btn_Cancel', function () {
                    $.ajax({
                        method: "POST",
                        url: "sorter_send_data.php",
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
                                url: "sorter_send_data.php",
                                data: $('#rangeForm').serialize()
                            }).done(function (data) {
                                
                                $('#btn_Save').val("Send");
                                $('#success_msg').html(" ");
                                if (id == "") {
                                    $('#success_msg').html("Record has been Added Successfully");
                                } else {
                                    $('#success_msg').html("Record has been Updated Successfully");
                                }
                                $('#hawb').val('').trigger('change');
                                $("div").removeClass("hidden");
                                $('#successmsg').show().fadeTo(3000, 1000).slideUp(1000);
                                $('#id').val("");
                            });
                            return false;
                        }
                    });
                    $("#rangeForm").submit();
                });

                

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
                           Send Data to Sorter
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
                            <div class="col-sm-3">
                                <label >Hawb / Tracking Number</label>
                            <div class="input-group">
                                <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                <textarea name="hawb" id="hawb" type="text" placeholder="HAWB/ TRACKING NO" rel="tooltip" data-original-title="HAWB/ TRACKING NO" class="form-control form-filter" style="resize: none; height: 143px;"></textarea>
                                <span class="input-group-addon red-18">*</span> 
                            </div>
                            </div>
                           
                        </div> 
                        <div style="clear:both"></div> 
                        <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                        <input type="hidden" name="form_action" id="form_action" value="saverecord" />
                        <div class="row " style="text-align:centre;" align="center">
                            <div class="col-md-12">
                                <input id="btn_Save" type="button"  class="btn btn-primary" value="SEND"/>
                                <input id="btn_Cancel" type="button"  class="btn btn-default" value="<?php echo Translation::GetCaption("CANCEL"); ?>"/>
                            </div>
                        </div>   
                    </form>
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
