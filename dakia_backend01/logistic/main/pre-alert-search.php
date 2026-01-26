<?php
// get settings
require_once("../includes/settings/config.inc.php");
@session_start;

class Page extends BasePage {

    private $prealerfilter = NULL;
    private $prealert_list = NULL;
    private $mawbno = "";
    private $flightno = "";
    private $etd = "";
    private $eta = "";

    protected function init() {


        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        t_on(); // turn on trace for this page
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

            // take appropriate action  btnSave
            if ($this->form_vars["form_action"]) {


                switch ($this->form_vars["form_action"]) {
                    case "search":
                        $this->prealerfilter = new PreAlertFilter();
                        if (isset($this->form_vars["mawb_search"]) || isset($this->form_vars["flight_search"]) || isset($this->form_vars["etd"]) || isset($this->form_vars["eta"])) {

                            $this->mawbno = $this->form_vars["mawb_search"];
                            $this->flightno = $this->form_vars["flight_search"];
                            $this->etd = date('Y-m-d', strtotime($this->form_vars["etd"]));
                            $this->eta = date('Y-m-d', strtotime($this->form_vars["eta"]));


                            if (isset($this->etd) && $this->etd != '1970-01-01') {
                                $this->prealerfilter->addEtdFilter($this->etd);
                            }
                            if (isset($this->eta) && $this->eta != '1970-01-01') {
                                $this->prealerfilter->addEtaFilter($this->eta);
                            }
                            if (isset($this->mawbno) && trim($this->mawbno) != '') {
                                $this->prealerfilter->addMawbFilter($this->mawbno);
                            }
                            if (isset($this->flightno) && trim($this->flightno) != '') {
                                $this->prealerfilter->addFlighNumberFilter($this->flightno);
                            }
                            $this->prealert_list = $this->prealerfilter->getColumnList('id,mawb,flight_number,pieces,weight,etd,eta,current_status,files,account');
                        }
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

                $('#etd').datepicker({dateFormat: "d M yy"});
                $('#eta').datepicker({dateFormat: "d M yy"});
                $('input').tooltip();
                $('select').tooltip();
                $('textarea').tooltip();


            });
            function noSpeciatCharacter(e)
            {
                var unicode = e.charCode ? e.charCode : e.keyCode
                //alert(unicode);
                if (unicode != 8)
                {
                    if ((unicode == 31) || (unicode >= 33 && unicode <= 35) || (unicode >= 39 && unicode <= 43) || (unicode >= 36 && unicode <= 38) || unicode == 163 || unicode == 94 || unicode == 64 || unicode == 126) //if not a number
                        return false //disable key press
                }
            }

        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>

        <br><br>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i>
                        PRE-ALERT SEARCH	</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
        <?php errorList::getItem()->render(); ?>
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket "></i> </span>
                                        <input type='text' name='mawb_search' id='mawb_search' class="form-control" maxlength="30"   value="<?php echo @$this->form_vars["mawb_search"] ?>" class="form_field_col_bag" onkeypress="return noSpeciatCharacter(event);" rel="tooltip" placeholder="MAWB" data-original-title="MAWB"/>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-plane"></i> </span>
                                        <input type='text' name='flight_search' id='flight_search' class="form-control" maxlength="30"  onkeypress="return noSpeciatCharacter(event);" value="<?php echo @$this->form_vars["flight_search"] ?>" class="form_field_col_bag" rel="tooltip" placeholder="FLIGHT NUMBER" data-original-title="FLIGHT NUMBER"/>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-table"></i> </span>
                                        <input type="text" class="form-control" name="etd" id="etd" value="<?php echo @$etd; ?>" size="100"   maxlength="35" rel="tooltip" placeholder="ETD" data-original-title="ETD"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group col-md-12">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-table"></i> </span>
                                        <input type="text" class="form-control" name="eta" id="eta" value="<?php echo @$eta; ?>" size="100"   maxlength="35" rel="tooltip" placeholder="ETA" data-original-title="ETA"/>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="row" style="text-align:center;">
                        <br/> <br/>
                        <a id="btnSearch"   href="#" class="btn btn-primary btn_save" ><span></span>Search</a>
                    </div>      
                    <div class="col-md-12" >  
                        <br/> <br/>

                        <div class="table-scrollable">
                            <table  class="table table-striped table-bordered table-advance table-hover"> 
                                <thead>
                                    <tr class="alert alert-info">
                                        <th class="red-back">Edit</th>
                                        <th class="red-back">MAWB#</th>
                                        <th class="red-back">Flight#</th>
                                        <th class="red-back">Pieces</th>
                                        <th class="red-back">Weight</th>
                                        <th class="red-back">ETD</th>
                                        <th class="red-back">ETA</th>
                                        <th class="red-back">Status</th>
                                        <th class="red-back">Files</th>
                                    </tr>
                                </thead>	 
                                <tbody id="tableValueId"> 
        <?php
        if (is_array($this->prealert_list)) {
            foreach ($this->prealert_list as $preData) {
                ?>
                                            <tr>
                                                <td><?php
                if ($preData->getCurrentStatus() == "IN TRANSIT")
                    echo '<a class="ebayButton" href=pre-alert.php?action=edit&id=' . $preData->getId() . ' >Edit</a>';
                ?></td>
                                                <td><?php echo $preData->getMawb(); ?></td>
                                                <td><?php echo $preData->getFlightNumber(); ?></td>
                                                <td><?php echo $preData->getPieces(); ?></td>
                                                <td><?php echo $preData->getWeight(); ?></td>
                                                <td><?php echo $preData->getEtd(); ?></td>
                                                <td><?php echo $preData->getEta(); ?></td>
                                                <td><?php echo $preData->getCurrentStatus(); ?></td>
                                                <td style="text-align:left; padding-left:10px;"><?php
                                            $filesArray = explode('||', $preData->getFiles());
                                            if (count($filesArray) > 0) {
                                                foreach ($filesArray as $filename) {
                                                    $actual_link = SETTING_MAIN_ASSETS . "preadvice/" . $preData->getAccount() . "/" . $filename;

                                                    //$actual_link = "https://".$_SERVER['HTTP_HOST']."/remote/_assets/mawb-pre-alert/".$filename;
                                                    echo '<a class="ebayButton" href="' . $actual_link . '" title="Download" target="_blank">
									<span class="glyphicon glyphicon-download-alt">&nbsp;</span></a>  ' . $filename . '<br>';
                                                }
                                            }
                                            ?></td>

                                            </tr>
                                                <?php
                                                }
                                            }
                                            ?>            
                                </tbody>			
                            </table>
                        </div>
                    </div>
                    <div style="clear:both;"></div>

                </div>
            </div>


        </div>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />

                                    <?php
                                    // report any errors
                                    // has file been chosen yet?		
                                }

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
                            