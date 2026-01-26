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
        $user = Sessionmanager::getUser();

        if ($user == NULL) {
            util_redirect("../main/index.php");
        }

        t_on(); // turn on trace for this page
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

                $('#collection_date').datepicker({dateFormat: "d M yy"});

                //$('#etd').datepicker({dateFormat: "d M yy"});
                //$('#eta').datepicker({dateFormat: "d M yy"});



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

            function funcManifest(manifeastId)
            {

                $("#update_collection_msg").addClass('hidden');
                $("#manifested_id").val(manifeastId);
            }

            function updateCollectionData()
            {
                var id = $("#manifested_id").val();
                var colelctionDate = $("#collection_date").val();
                var collectionComment = $("#collection_comment").val();

                var form_data = new FormData();
                form_data.append('funcAction', 'UPDATE_COLLECTION');
                form_data.append('collection_date', colelctionDate);
                form_data.append('id', id);
                form_data.append('collection_comment', collectionComment);
                $.ajax({
                    url: 'coclient_user_endofday.php',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        alert("--" + response + "--");
                        if (response == 'SUCCESS')
                        {
                            $("#update_collection_msg").html('You have successfully update collection time.');
                            $("#update_collection_msg").removeClass('hidden');
                        } else
                        {
                            //$("#update_collection_msg").html('');
                        }

                    }
                });
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $collection = "N";
        if (isset($_REQUEST['collection']) && $_REQUEST['collection'] == "Y") {
            $collection = $_REQUEST['collection'];
        }
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/client_list.php">Shipments</a></li>
            <li><a href="../main/coclient_user_endofday.php">Manifest</a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i> Manifest Report</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>
                <div class="portlet-body">
                    <div class="col-md-12">
                        <table id='table_container' class='consignment_list_tbl table table-striped table-bordered table-advance table-hover'>

                            <thead>
                                <tr>
                                    <th class="red-back">Date Created</th>
                                    <th class="red-back">Manifest ID</th>
                                    <th class="red-back">Agent</th>
                                    <th class="red-back">Service</th>
                                    <th class="red-back">Number of Boxes</th>
                                    <th class="red-back">Label</th>
                                    <th class="red-back">Flight</th>
                                    <th class="red-back">Mawb</th>
                                    <th class="red-back">Download Shipment</th>
                                    <th class="red-back">Send Email</th>
                                </tr>
                            </thead>     
                            <tbody>	

        <?php
        if (count($this->manifest_list) > 0) {
            foreach ($this->manifest_list as $item) {
                echo "<tr>";
                echo "<td>";
                echo date("Y-m-d", ($item->getDateCreated()));
                echo "</td>";
                echo "<td>";
                echo $item->getId();
                echo "</td>";
                echo "<td>";
                echo $item->getAgent();
                echo "</td>";
                echo "<td>";
                echo $item->getHandling();
                echo "</td>";
                echo "<td>";
                echo $item->getPieces();
                echo "</td>";

                echo "<td>";
                echo "<a target='_blank' href='" . $item->getLabelLink() . "'>Label</a>";
                echo "</td>";

                echo "<td>";
                echo $item->getFlightNumber();
                echo "</td>";

                echo "<td>";
                echo $item->getMawb();
                echo "</td>";

                echo "<td>";
                echo '<a  href="#" onclick="downloadCsv(\'' . $item->getId() . '\',\'download\')" title="Downlaod Shipment"><span class="glyphicon glyphicon-download-alt">&nbsp;</span> </a>';
                echo "</td>";

                echo "<td>";
                echo '<a  href="#" onclick="downloadCsv(\'' . $item->getId() . '\', \'email\')" title="Please email "><span class="glyphicon glyphicon-envelope">&nbsp;</span> </a>';
                echo "</td>";

                echo "</tr>";
            }
        }
        ?>
                            <input type="hidden" id="sendemail" name="sendemail"  />
                            </tbody>
                        </table>

                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
        </form>
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
