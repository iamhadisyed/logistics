<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $report_list = "";
    public $from_date = "";
    public $to_date = "";
    public $user_type = "";

    protected function init() {
        // common initialisation for ths page
        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "sales" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }
        $this->setTitle("Sale Pot Commission");

        $sessionUser = SessionManager::getUser();
        t_on(); // turn on trace for this page
        if (isset($_GET['paid_id']) && $_GET['paid_id'] != "") {
            $Md5RecordId = $_GET['paid_id'];
            $UserId = $sessionUser->getId();
            $TodayDateTime = time("Y-m-d H:i:s");
            $SalesPotComissionFilter = new SalesPotComissionFilter();
            $SalesPotComissionFilter->addSalesPotComissionMd5IdFilter($Md5RecordId);
            $SalesPotComissionFilter->addIsNotPaidFilter();
            $SalesPotComissionObj = $SalesPotComissionFilter->getColumnList('id');
            if (empty($SalesPotComissionObj)) {
                util_redirect("sale_pot_commission.php");
                die;
            }
            $commission_id = $SalesPotComissionObj[0]->getId();
            $SalesPotComission = new SalesPotComission($commission_id);
            $SalesPotComission->setIsPaid('1');
            $SalesPotComission->setPaidBy($UserId);
            $SalesPotComission->setPaidDate($TodayDateTime);
            $SalesPotComission->save();
            util_redirect("sale_pot_commission.php");
        }
        if (isset($this->form_vars["form_action"])) {

            $this->from_date = $this->form_vars["date_from"];
            $this->to_date = $this->form_vars["date_to"];
            $this->user_type = $this->form_vars["user_type"];
            if (empty($this->from_date)) {
                $this->from_date = "";
            } else {
                $this->from_date = date("Y-m-d", strtotime($this->from_date));
            }
            if (empty($this->to_date)) {
                $this->to_date = "";
            } else {
                $this->to_date = date("Y-m-d", strtotime($this->to_date));
            }
            $_SESSION['from_date'] = $this->from_date;
            $_SESSION['to_date'] = $this->to_date;
            $_SESSION['user_type_session'] = $this->user_type;

            $SalesPotComissionFilter = new SalesPotComissionFilter();
            if ($this->user_type == "paid" && !empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionPaidFromToFilter($this->from_date, $this->to_date);
                $SalesPotComissionFilter->addIsPaidFilter();
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "paid" && empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionPaidBeforeDateFilter($this->to_date);
                $SalesPotComissionFilter->addIsPaidFilter();
                $this->from_date = "";
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "paid" && !empty($this->from_date) && empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionPaidAfterToDateFilter($this->from_date);
                $SalesPotComissionFilter->addIsPaidFilter();
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = "";
            } elseif ($this->user_type == "paid" && empty($this->from_date) && empty($this->to_date)) {

                $SalesPotComissionFilter->addIsPaidFilter();
                $this->from_date = "";
                $this->to_date = "";
            } elseif ($this->user_type == "unpaid" && !empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionFromToAddedDateFilter($this->from_date, $this->to_date);
                $SalesPotComissionFilter->addIsNotPaidFilter();
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "unpaid" && empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionBeforeDateFilter($this->to_date);
                $SalesPotComissionFilter->addIsNotPaidFilter();
                $this->from_date = "";
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "unpaid" && !empty($this->from_date) && empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionAfterToDateFilter($this->from_date);
                $SalesPotComissionFilter->addIsNotPaidFilter();
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = "";
            } elseif ($this->user_type == "unpaid" && empty($this->from_date) && empty($this->to_date)) {
                $SalesPotComissionFilter->addIsNotPaidFilter();
                $this->from_date = "";
                $this->to_date = "";
            } elseif ($this->user_type == "all" && !empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionFromToAddedDateFilter($this->from_date, $this->to_date);
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "all" && empty($this->from_date) && !empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionBeforeDateFilter($this->to_date);
                $this->from_date = "";
                $this->to_date = date("d-m-Y", strtotime($this->to_date));
            } elseif ($this->user_type == "all" && !empty($this->from_date) && empty($this->to_date)) {

                $SalesPotComissionFilter->addSalesPotComissionAfterToDateFilter($this->from_date);
                $this->from_date = date("d-m-Y", strtotime($this->from_date));
                $this->to_date = "";
            } elseif ($this->user_type == "all" && empty($this->from_date) && empty($this->to_date)) {

                $this->from_date = "";
                $this->to_date = "";
            }


            $this->report_list = $SalesPotComissionFilter->getList();
        } else {

            if (isset($_SESSION['from_date']) && !empty($_SESSION['from_date'])) {
                $this->from_date = $_SESSION['from_date'];
            } else {
                $this->from_date = date("Y-m-d", strtotime("-1 week"));
            }
            if (isset($_SESSION['to_date']) && !empty($_SESSION['to_date'])) {
                $this->to_date = $_SESSION['to_date'];
            } else {
                $this->to_date = date("Y-m-d");
            }
            $SalesPotComissionFilter = new SalesPotComissionFilter();
            $SalesPotComissionFilter->addSalesPotComissionFromToAddedDateFilter($this->from_date, $this->to_date);
            if (isset($_SESSION['user_type_session']) && !empty($_SESSION['user_type_session'])) {
                if ($_SESSION['user_type_session'] == "paid") {
                    $SalesPotComissionFilter->addIsPaidFilter();
                    $this->user_type = "paid";
                } else if ($_SESSION['user_type_session'] == "unpaid") {
                    $SalesPotComissionFilter->addIsNotPaidFilter();
                    $this->user_type = "unpaid";
                } else {
                    $this->user_type = "all";
                }
            } else {
                $this->user_type = "all";
            }
            $this->report_list = $SalesPotComissionFilter->getList();
            $this->to_date = date("d-m-Y", strtotime($this->to_date));
            $this->from_date = date("d-m-Y", strtotime($this->from_date));
        }
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#date_from").datepicker({dateFormat: "d M yy"});
                $("#date_to").datepicker({dateFormat: "d M yy"});

                //                $("#btnSearchReport").click(function () {
                //
                //                    var $myForm = $('#adminForm');
                //                    if (!$myForm[0].checkValidity()) {
                //                        // If the form is invalid, submit it. The form won't actually submit;
                //                        // this will just cause the browser to display the native HTML5 error messages.
                //                        $myForm.find(':submit').click()
                //                    }
                //                    $("#form_action").val("Search");
                //        //                    $("#adminForm").submit();
                //                });

                $('select').tooltip();
                $('input').tooltip();
            });
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/sale_pot_commission.php">Sale Pot Commission</a></li>
            <li><a href="#">List</a></li>
        </ul>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i>Search Sale Pot Commission</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                <input  class="form-control" name="date_from" id="date_from" type="text" value="<?php echo $this->from_date; ?>" placeholder="Choose Date From" rel="tooltip"  title="Choose Date From">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                <input  class="form-control" name="date_to" id="date_to" type="text" value="<?php echo $this->to_date; ?>" placeholder="Choose Date To" rel="tooltip" title="Choose Date To">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group">
                                <select id="user_type" name="user_type" class="form-control" rel="tooltip" placeholder="Commission Type" title="Commission Type" >
                                    <option <?php echo ($this->user_type == 'all' ? "selected" : "") ?> value="all">All</option>
                                    <option <?php echo ($this->user_type == 'paid' ? "selected" : "") ?> value="paid">Paid</option>
                                    <option <?php echo ($this->user_type == 'unpaid' ? "selected" : "") ?> value="unpaid">Un-Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="submit" value="Sreach" name="search" class="btn btn-primary btn_save">
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Sale Pot Commission 
                    </div>                    
                </div>
                <div class="portlet-body">
                    <?php
                    if (count(ErrorList::getItem()->getErrorCount()) > 1) {

                        echo '<div class="alert alert-danger">';
                        ErrorList::getItem()->render();
                        echo '</div>';
                    }
                    ?>
                    <!-- describe table filter -->
                    <!-- CONSIGNMENT TABLE -->
                    <div id='table_container'  class="main_grid2">
                        <div class="table-scrollable">
                            <table id='consignment_list' class="table table-striped table-bordered table-advance table-hover" >
                                <!-- table head -->
                                <thead>
                                    <tr>
                                        <th>Date</th> 
                                        <th>No of Shipment</th>
                                        <th>Commission</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <!-- table head-->
                                <!-- table body -->
                                <tbody>
                                    <?php
                                    if (empty($this->report_list)) {
                                        echo "<tr><td colspan='4'>No record found</td></tr>";
                                    } else {
                                        foreach ($this->report_list as $report_list) {
                                            ?>
                                            <tr>
                                                <td><?php
                            $date = new DateTime($report_list->getDateAdded());
//                $date->setTimestamp($report_list->getDateAdded());
                            echo $date->format('Y-m-d');
                                            ?>
                                                </td>
                                                <td><?php echo $report_list->getNoOfShipments(); ?></td>
                                                <td><?php echo $report_list->getComission(); ?></td>
                                                <td><?php
                                    if ($report_list->getIsPaid() == '1') {
                                        $date = new DateTime();
                                        $date->setTimestamp($report_list->getPaidDate());
                                        $user_id = $report_list->getPaidBy();
                                        $User = new CustomerAccount($user_id);
                                        echo "<b>" . $User->getFirstName() . "</b><br>" . "<span class='help-inline'>" . $date->format('Y-m-d') . "</span>";
                                    } else {
                                                ?>
                                                        <a href="sale_pot_commission.php?paid_id=<?php echo md5($report_list->getId()); ?>" class="btn btn-primary btn_save" onclick="return confirm('Are you sure you want to pay it?')"/>Pay now</a>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                <?php
            }
        }
        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>  <!-- table_container -->
                </div>
                <input type="hidden" name="form_action" id="form_action" value="form_action"  />
            </div>  <!-- table_container -->
        </div>
        <?php
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
