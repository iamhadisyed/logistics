<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([   'iaddress.class',
                    'licenceplate.class',
                    'licenceplatefilter.class',
                    'consignment.class',
                    'consignmentfilter.class',]);
class Page extends BasePage {

    private $licencePlate;
    private $id = NULL;
    private $breadcrumb = '';
    private $user = NULL;
    private $isCountry = 0;

    /*     * *
     * Controller logic
     */

    protected function init() {

       // if(!Permissions::checkFilePermission('fetch_label.php')) 
        //            util_redirect ("index.php");
        $this->user = $user = SessionManager::getUser();
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'fetch_label.php' => 'Fetch Label'
        );
        $this->licencePlate = new LicencePlateFilter();
        $countryRange = 0;
        // common initialisation for ths page
        $this->setTitle("Fetch Label");
         if (isset($_POST["form_action"])) {
            $error_array = array();
            switch ($_POST["form_action"]) {
                case "open_print_dialog":
                    $fileName = $this->form_vars['filename'];

                    if ($fileName != '')
                    {
                       ?>	
                        <iframe id="iFramePdf"  src="<?php echo $fileName; ?>" style="display:none;width: 619px; height: 482px;"></iframe>
                        <script type="text/javascript">
                            var printFrame = document.getElementById('iFramePdf');

                            if (printFrame) {
                                printFrame.contentWindow.print();
                            } else {
                                PDFViewerApplication.pdfDocument.getData().then(function (res) {
                                    var src = URL.createObjectURL(new Blob([res], {type: 'application/pdf'}));
                                    printFrame = document.createElement('iframe');
                                    printFrame.id = 'print-frame';
                                    printFrame.style.display = 'none';
                                    printFrame.src = src;
                                    document.body.appendChild(printFrame);
                                    setTimeout(function () {
                                        printFrame.contentWindow.print();
                                    }, 0)
                                })
                            }
                       
                            document.getElementById("tracking").focus();
                        </script>
                        <?php 
                    }
                break;
            }
         }
        
        if (isset($_POST["action"]) && $_POST["action"] == "getShipmentLabel") {
            $output = [];

           $trackingNumber = $this->form_vars["trackingNo"];
           $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addAwbAndHawbOrFilter($trackingNumber);
            $consignmentObj = $consignmentFilter->getConList();
            
            if(count($consignmentObj) > 0)
            {
               $singleLabel = $consignmentObj[0]->getLabelFile();
              
                if(trim($singleLabel) != '')
                {
                    $output["STATUS"] = "SUCCESS";
                    $output["LABEL"] = '../_assets/pdf/'.$singleLabel;
                }
                else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "Unable to find label for this shipment.";
                }
            }
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to locate shipment.";
            }
            
            echo json_encode($output);
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

        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />



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
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>


        <?php
    }

    protected function renderFooter() {
        ?>
        <script>
           function showLabel(url, title, w, h)
            {
                var left = (screen.width / 2) - (w / 2);
                var top = (screen.height / 2) - (h / 2);
                window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', 			  height=' + h + ', top=' + top + ', left=' + left);
            }
    
            $(document).ready(function () {

               
               document.getElementById("tracking").focus();
                $("#tracking").keypress(function (event)
                {
                    if (event.keyCode == 13)
                    {
                        $("#loader").removeClass("hidden");
                        var trackingNo = $.trim($('#tracking').val());
                        if ($.trim($("#tracking").val()).length == 0)
                        {
                            $('#res_message').addClass("alert-danger");
                            $('#res_message').show().html('Please Enter hawb/tracking Number.');
                            $("#hawb_form_div").addClass("has-error has-danger");
                            $('html, body').animate({
                                scrollTop: $("#response_message").offset().top
                            }, 1000);
                            $("#loader").addClass("hidden");
                            return false;
                        } else {
                            $("#hawb_form_div").removeClass("has-error has-danger");
                            $("#res_message").removeClass("alert-danger");
                            $("#res_message").html('');
                        }
                        $.ajax({
                            type: "POST",
                            url: "fetch_label.php", // your php file name
                            data: {action: 'getShipmentLabel', trackingNo: trackingNo},
                            success: function (data)
                            {
                              
                                $("#loader").addClass("hidden");
                                var obj = JSON.parse(data);
                               
                                if (obj.STATUS == 'ERROR')
                                {
                                    $('#res_message').addClass("alert-danger");
                                    $('#res_message').show().html(obj.MESSAGE);
                                } else
                                {
                                    $('#res_message').text('');
                                    $('#res_message').removeClass('alert-danger');
                                    $('#tracking').css('background-color', 'springgreen');
                                    $("#form_action").val("open_print_dialog");
                                    $("#filename").val(obj.LABEL);
                                    $("#adminForm").submit();
                                    
                                    setTimeout(function ()
                                    {
                                        $('#tracking').css('background-color', '#EDEDE7');
                                    }, 3000);
                                }
                            }
                        });
                        return false;
                    }
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
        <?php
        //if(Permissions::checkFilePermission('range_add'))
        {
            ?>
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-plus"></i>
                            
                               Fetch Label
                            
                        </div>
                        <div class="actions">

                        </div>
                    </div>
                    <div class="portlet-body">
                        <form name="adminForm" id="adminForm" action="" method="POST">           
                            <div class="row display-none alert alert-success" id="res_message">
                                
                            </div>
                            <div class="row"> 
                               
                                <div class="col-md-3" id="hawb_form_div">
                                    <div class="form-group"> 
                                       
                                       
                                       
                                        <label>Tracking Number / Hawb Number</label>
                                        <div class="input-group input-group-sm input-icon right" > 
                                            <span class="input-group-addon"> <i class="fa fa-map-marker"></i> </span>
                                            <input name="tracking" id="tracking" value="" size="50" class="form-control" title="Tracking Number" maxlength="35" placeholder="Tracking / Order Number" rel="tooltip" data-original-title="Tracking / Order Number" type="text" required>
                                                 <i id="loader" name="loader" class="fa fa-spinner fa-spin icon-large hidden"></i>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                           
                            <div style="clear:both"></div> 
                            <input type="hidden" name="id" id="id" value="<?php echo $this->id; ?>"  class="form-control"/>                                                                                                        <!--<input type="hidden" name="new" id="new" value="<?php echo @$new; ?>" />-->
                            <input type="hidden" name="form_action" id="form_action" value="" />
                            <input type="hidden" name="filename" id="filename" value="" />
                            <br />
                            <div class="row " style="text-align:centre;" align="center">
                                
                            </div>   
                        </form>
                    </div>
                </div>
        <?php } ?>
          
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
