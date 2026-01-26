<?php
// get settings

require_once("../includes/settings/config.inc.php");

@session_start;

class Page extends BasePage {

    private $account;
    private $scandate = "";
    private $mawbno = "";
    private $errormsg = "";

    protected function init() {

        $this->consigfilter = new ConsignmentFilter();
        // user must be CLIENT
        // Tool bar
//        $toolbar = Toolbar::getItem();
//        $toolbar->showSearchOption();
//        $toolbar->showImportOption();
//        $toolbar->showLabelList();
//        $toolbar->showWarehousePage();
//        $toolbar->showAddConsignment();
//        $toolbar->showCSVOption();
//        $toolbar->showReleaseList();
//        $toolbar->showConsignmentList();
//        $toolbar->showSaveAddress();
//		$toolbar->showSearchOption();
//	    $toolbar->showAddUser();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
//	
//		// common initialisation for ths page
//		$this->setTitle("Import Bookings");
//		$toolbar = Toolbar::getItem();                           
//		$toolbar->showSearchOption();
//		$toolbar->showExportOption();
//		$toolbar->showEndOfDayOption();
//		$toolbar->showManifestList();
//		$toolbar->showLabelCreation();
//		$toolbar->showUserList();
//        $toolbar->showCSVOption();
//		$toolbar = Toolbar::getItem();
//        $toolbar->showAddConsignment();
//		$toolbar->showUserList();
//        $toolbar->showCSVOption();

        if (isset($this->form_vars["form_action"])) {
            // restore session variables
            $this->source = $_SESSION["search_source"];
            $this->page_title = $_SESSION["search_title"];


            // take appropriate action  btnSave
            if ($this->form_vars["form_action"]) {

                switch ($this->form_vars["form_action"]) {
                    case "search":
                        if ((isset($this->form_vars["mawb_search"]) && ($this->form_vars["mawb_search"]) != "")) {
                            //					((isset($this->form_vars["date_created"]) && ($this->form_vars["date_created"]) != "") 				 ||	
                            //$this->scandate = date('Y-m-d', strtotime($this->form_vars["date_created"]));
                            $this->mawbno = $this->form_vars["mawb_search"];
                            $carrier = $this->form_vars["service"];


                            if ($carrier == "HungaryPost") {
                                $handling = "'REGPOSTHUNEUR', 'REGPOSTHUNINT'";
                            } else if ($carrier == "SwedenPost") {
                                $handling = "'REGPOSTINT', 'REGPOST'";
                            } else if ($carrier == "RoyalMailIntTrackedAndSigned") {
                                $handling = "'RMINTRS', 'RMEURTS'";
                            } else if ($carrier == "RoyalMailIntSigned") {
                                $handling = "'RMINTS'";
                            } else if ($carrier == "Correos") {
                                $handling = "'CORREOS'";
                            } else if ($carrier == "RoyalMailIntUntracked") {
                                $handling = "'PRIORITY'";
                            } else if ($carrier == "RoyalMailEurUntracked") {
                                $handling = "'EURPRIOR'";
                            }


                            /* 	if (isset($this->scandate) && $this->scandate != '1970-01-01' && isset ($this->mawbno) && trim($this->mawbno) != '')
                              {
                              BagSummaryReport::buildPDFDocuments($this->scandate, $this->mawbno, "");
                              }
                              else if (isset($this->scandate) && $this->scandate != '1970-01-01')
                              {
                              BagSummaryReport::buildPDFDocuments($this->scandate, $this->mawbno, "");
                              } */
                            if (isset($this->mawbno) && trim($this->mawbno) != '') {

                                BagSummaryReport::buildPDFDocuments("", $this->mawbno, $handling);
                            }
                        } else {
                            $this->errormsg = "Please enter MAWB no or Select Date.";
                        }
                        break;
                }
            }
        }
    }

    /**
     * Force page refresh if importing
     */
    protected function renderHead() {
        ?>
        <script>
            $(document).ready(function () {


                $("#btnSearch").click(function () {
                    $("#form_action").val("search");
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
        ?>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        <br><br>
        <div class="main_formpage">	
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-bar-chart"></i>
                        END OF DAY SUMMARY REPORT SEARCH</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <div style="color:red; font-weight:bold; font-size:12px;">
        <?php echo $this->errormsg; ?>
                    </div>	
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-credit-card"></i> </span>
                                    <input class="form-control" id="mawb_search" name="mawb_search" placeholder="MAWB NUMBER" type="text" value="<?php echo @$this->mawb_search; ?>">
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                    <select id = 'service' name='service' class="form-control">
                                        <option value="">Select Service</option>
                                        <option value="HungaryPost">HungaryPost</option>
                                        <option value="SwedenPost">SwedenPost</option>
                                        <option value="RoyalMailIntTrackedAndSigned">Royal Mail Int Tracked And Signed</option>
                                        <option value="RoyalMailIntSigned">Royal Mail Int Signed</option>
                                        <option value="Correos">Correos</option>
                                        <option value="RoyalMailIntUntracked">Royal Mail INT Priority Untracked</option>
                                        <option value="RoyalMailEurUntracked">Royal Mail EUR Priority Untracked</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="row" > 
                        <div class="col-md-12">
                            <a id="btnSearch"   href="#" class="btn btn-primary" ><span></span>Export PDF</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <?php
        // report any errors
        // has file been chosen yet?		
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
