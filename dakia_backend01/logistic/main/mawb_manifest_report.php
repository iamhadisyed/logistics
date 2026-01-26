
<?php
// get settings
require_once("../includes/settings/config.inc.php");

@session_start;

class Page extends BasePage {

    private $account;
    private $scandate = "";
    private $mawbno = "";
    private $manifestno = "";
    private $errormsg = "";

    protected function init() {
        // common initialisation for ths page
        $this->setTitle("Import Bookings");

        if (isset($this->form_vars["form_action"])) {
            // restore session variables
            $this->source = $_SESSION["search_source"];
            $this->page_title = $_SESSION["search_title"];


            // take appropriate action  btnSave
            if ($this->form_vars["form_action"]) {


                switch ($this->form_vars["form_action"]) {
                    case "export":
                        $this->mawbno = $this->form_vars["mawb_search"];
                        $this->manifestno = $this->form_vars["manifest_search"];

                        if ((isset($this->mawbno) && trim($this->mawbno) != '') || isset($this->manifestno) && trim($this->manifestno) != '') {
                            $this->exportCSV($this->mawbno, $this->manifestno);
                        } else {
                            $this->errormsg = "Please enter MAWB NO or MANIFEST NO.";
                        }
                        break;
                }
            }
        }
    }

    private function getHeader() {
        $record = "Bag Number, HAWB, AWB, Date Scanned";
        return $record;
    }

    private function exportCSV($mawbno, $manifestno) {

        //Bag Suammary Start Here

        $consignmentFilter = new ConsignmentFilter();
        if ($mawbno != '') {

            $consignmentFilter->addFilter("mawb = '" . DbAccess3::escape($mawbno) . "'");
            $header = $mawbno;
        }
        if ($manifestno != '') {
            $consignmentFilter->addFilter("id in (select consignmentid from manifest_consignment_mapping where manifestid = '" . DbAccess3::escape($manifestno) . "')");
            $header = $manifestno;
        }


        $list = $consignmentFilter->getColumnList("awb, hawb, mawb, bag_number, date_scanned");


        $csv = "";
        $cr = "\r\n";

        $bag_total = "0";
        $mno = "";
        $bag_number = "";
        $total_scanned = 0;
        $total_notscanned = 0;
        $carrier = "";
        if (count($list) > 0) {

            $csv .= "MAWB / MANIFEST NO:" . $header . $cr;

            $csv .= $this->getHeader() . $cr;
            foreach ($list as $consignment) {

                $csv .= preg_replace('/[\$,]/', '', $consignment->getBagNumber()) . ',';
                $csv .= preg_replace('/[\$,]/', '', $consignment->getHawb()) . ',';
                $csv .= preg_replace('/[\$,]/', '', $consignment->getAwb()) . ',';
                $csv .= $consignment->getDateScanned() . ',';



                if ($consignment->getDateScanned() != NULL && $consignment->getDateScanned() != '' && $consignment->getDateScanned() != '0000-00-00 00:00:00') {
                    $csv .= "Scanned";
                    $total_scanned ++;
                } else {
                    $csv .= "Not Scanned";
                    $total_notscanned ++;
                }
                $total1 = $total_scanned + $total_notscanned;



                $csv .= $cr;
                $mno = $consignment->getMawb();
            }

            $csv .= $cr;
            $csv .= "Total Scanned" . ',';
            $csv .= $total_scanned . ',';
            $csv .= "Total Not Scanned" . ',';
            $csv .= $total_notscanned . ',';
        }


        $filename = "Scan_Report" . date('Ymd') . ".csv";
        header("Content-Type: application/csv");
        header("Content-disposition: attachment; filename=" . $filename . ".csv");

        echo $csv;


        exit;
    }

    /**
     * Force page refresh if importing
     */
    protected function renderHead() {
        ?>
        <script>
            $(document).ready(function () {



                $("#btnexport").click(function () {
                    $("#form_action").val("export");
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
        <?php errorList::getItem()->render(); ?>MAWB / MANIFEST SCAN REPORT</div>
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
                                    <input type='text' name='mawb_search' id='mawb_search' placeholder="MAWB NUMBER" class="form-control" maxlength="30" style=""  value="<?php echo @$this->form_vars["mawb_search"] ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-globe"></i> </span>
                                    <input type='text' name='manifest_search' id='manifest_search' placeholder="MANIFEST NUMBER" class="form-control" maxlength="30" style=""  value="<?php echo @$this->form_vars["manifest_search"] ?>" />
                                </div>
                            </div>

                        </div>
                        <div class="row" > 
                            <div class="col-md-12" style="margin-left:10px;">
                                <a id="btnexport"   href="#" style="" class="btn btn-primary btn_save" ><span></span>Export CSV</a>
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
