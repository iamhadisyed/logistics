<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Invoices list page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

//require_once("../includes/3rdparty/calendar/classes/tc_calendar.php");
// set up local page class
class Page extends BasePage {
    /*     * *
     * This page's content
     * @return void 
     */

    private $page_count;

    public function renderHead() {
        ?>
        <link href="includes/3rdparty/calendar/calendar.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            $(document).ready(function () {
                // Set the date pickers
                $('#date_printed').datepicker({dateFormat: "d M yy"});
                $('#date_printed1').datepicker({dateFormat: "d M yy"});


                $("#btnSubmit").click(function () {
                    $("#form_action").val("Submit");
                    $("#adminForm").submit();
                });

                $('input[name="deleteConsignments[]"]').change(function () {
                    if ($('input[name="deleteConsignments[]"]:checked').length > 0)
                    {
                        $('#btn_multi_div').show();
                        $('#btn_regenerate_shipment').show();
                    } else
                    {
                        $('#btn_multi_div').hide();
                        $('#btn_regenerate_shipment').hide();
                    }
                });

                // Create Labels
                $("#btnLabelMerge").click(function () {
                    if (!consignmentSelectionConfirmation('There is no invoice to merge.', 'Please select invoice to merge.'))
                    {
                        return false;
                    }

                    if (confirm('Are you sure you want to merge invoice(s)'))
                    {
                        $("#form_action").val("btnLabelMerge");
                        $("#adminForm").submit();
                    }
                });
            });

            function consignmentSelectionConfirmation(errorMessage, errorMessagenew)
            {
                if (typeof (document.getElementsByName("deleteConsignments[]")) == 'undefined')
                {
                    alert(errorMessage);
                    return false;
                }
                var deleteConsign = document.getElementsByName("deleteConsignments[]");
                var txt = "";
                var i;
                for (i = 0; i < deleteConsign.length; i++) {
                    if (deleteConsign[i].checked) {
                        txt = txt + deleteConsign[i].value + "";
                    }
                }

                if (txt == "")
                {
                    alert(errorMessage);
                    return false;
                }
                return true;
            }

            function SetPageSize()
            {
                document.getElementById('adminForm').submit();
            }
            var count = 0;
            function checkedAll(group)
            {

                if (count == 0)
                {

                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        if (group[i].checked == false)
                            group[i].click();
                        count = 1;
                    }
                } else
                {
                    for (var i = 0, len = group.length; i < len; i++)
                    {
                        if (group[i].checked == true)
                            group[i].click();
                        count = 0;
                    }
                }
            }

        </script>
        <script language="javascript" src="includes/3rdparty/calendar/calendar.js"></script>
        <?php
        }

        public function renderBody()
        {  
        $countInvoiceFilterId	=	0;
        $invoiceFilterNew	=	new InvoiceFilter();
        $invoiceFilterNew->orderBySort('id DESC');
        $invoiceFilterNew->limitFilter('1');
        $invoiceFilterNewList	=	$invoiceFilterNew->getColumnList('invoice_no');
        if(count($invoiceFilterNewList)> 0)
        {
        $countInvoiceFilterId		=	$invoiceFilterNewList[0]->getInvoiceNo();			
        }


        $this->invoice_filter =  $_SESSION['invoice_filter'];
        if (isset($_POST['ddlPageSize']))
        {
        $_SESSION['ddlPageSize'] = $_POST['ddlPageSize'];
        $rowsperpage = $_SESSION['ddlPageSize'];
        $_GET['pagesize'] = $rowsperpage;

        }
        else if (isset($_GET['pagesize']))
        {
        $_SESSION['ddlPageSize']= $_GET['pagesize'];
        $rowsperpage = $_SESSION['ddlPageSize'];
        }
        else if (isset($_SESSION['ddlPageSize']))
        {
        $rowsperpage = $_SESSION['ddlPageSize'];
        }
        else
        {
        $rowsperpage = 25;
        }	

        // get consignment count
        $numrows = @$this->invoice_filter->getPagingCount();


        $totalpages = ceil($numrows / $rowsperpage);


        // get the current page or set a default
        if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
        // cast var as int
        $currentpage = (int) $_GET['currentpage'];
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
        $offset = ($currentpage - 1) * $rowsperpage;

        $this->invoice_filter->setRowsPerPage($rowsperpage);

        // the offset of the list, based on current page
        $this->invoice_filter->setOffset($offset);

        // get consignment list to display
        $this->invoices = @$this->invoice_filter->getPagingList();


        ?>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Invoice </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:70px;"  data-rail-color="blue" data-handle-color="blue">
                    <input type="hidden" name="form_action" id="form_action"  />
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                    <input type="text" name="date_printed" id="date_printed" value="<?php echo @$date_printed; ?>" style="background-color:#FFC" class="form-control" readonly="readonly" placeholder="From Date" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                    <input type="text" name="date_printed1" id="date_printed1" value="<?php echo @$date_printed1; ?>" style="background-color:#FFC" class="form-control" readonly="readonly" placeholder="Choose Date To" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
        <?php
        $this->userDropdown();
        ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input id="btnSubmit" name="btnSubmit" type="button" class="btn btn-primary" value='Submit'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <div class="portlet box  blue " id="btn_multi_div" style="display:none;">
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="button" class="btn btn-danger" id="btnLabelMerge" name="btnLabelMerge"> Merge Invoices </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-List"></i>Invoice List </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px; max-height:400px"  data-rail-color="blue" data-handle-color="blue">
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-advance table-hover">
                            <thead>
                                <tr>
                                    <td colspan="12"><?

                                        if($numrows > 25)
                                        {
                                        ?>
                                        <ul class="pagination" style="width:80%; ">
                                            Set Page Size &nbsp;&nbsp;
                                            <select style="display:inline;min-width:10px;width:50px !important; margin-bottom:1px; padding:1px"  class="select_dropdown" name="ddlPageSize" onchange="SetPageSize();">
                                                <option value="10" <?php if ($rowsperpage == 10) echo "selected='selected'" ?> >10</option>
                                                <option value="25" <?php if ($rowsperpage == 25) echo "selected='selected'" ?> >25</option>
                                                <option value="50" <?php if ($rowsperpage == 50) echo "selected='selected'" ?> >50</option>
                                                <option value="100" <?php if ($rowsperpage == 100) echo "selected='selected'" ?> >100</option>
                                            </select>
        <?php
        /*         * ****  build the pagination links ***** */

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
                    echo " &nbsp;<li><a  href='{$_SERVER['PHP_SELF']}?action=" . @$_GET['action'] . "&currentpage=$x&pagesize=$rowsperpage'>$x</a></li> ";
                } // end else
            } // end if
        } // end for
        // if not on last page, show forward and last page links
        if ($currentpage != $totalpages) {
            // get next page
            $nextpage = $currentpage + 1;
            // echo forward link for next page
            echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . @$_GET['action'] . "&currentpage=$nextpage&pagesize=$rowsperpage'>></a></li> ";
            // echo forward link for lastpage
            echo " &nbsp;<li><a class='next' href='{$_SERVER['PHP_SELF']}?action=" . @$_GET['action'] . "&currentpage=$totalpages&pagesize=$rowsperpage'>>></a></li> ";
        } // end if

        echo "<li style='vertical-align:middle'>Total records <b>$numrows</b>&nbsp;&nbsp;</li>";
        ?>
                                        </ul>
                                        <?			/****** end build pagination links ******/						


                                        }
                                        else
                                        {
                                        $this->invoices	=	$this->invoice_filter->getList();
                                        }
                                        ?></td>
                                </tr>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Account</th>
                                    <th>Net Amount</th>
                                    <th>Vat Amount</th>
                                    <th>Total Amount</th>
                                    <th>Currency</th>
                                    <th>Generated By</th>
                                    <th>Active/Inactive</th>
                                    <th></th>


        <!--                    <th class="bg-red"><input type='checkbox' name='checkall' onclick='checkedAll(delete55);'></th>
        <th class="bg-red">Invoice No</th>
        <th class="bg-red">Invoice Date</th>
        <th class="bg-red">Account</th>
        <th class="bg-red">Total Amount</th>
        <th class="bg-red">Currency</th>
        <th>Generated By</th>
        <th class="bg-red">Active/Inactive</th>
        <th class="bg-red"></th>
        <th class="bg-red"></th> -->
                                </tr>
                            </thead>
                            <tbody>
        <?php
        if (count(@$this->invoices) > 0) {
            foreach ($this->invoices as $invoice) {

                if ($invoice->getIsDeleted() == 'Y') // [val1] can be 'approved'
                    echo "<tr>";
                else if ($invoice->getIsDeleted() == 'N')
                    echo "<tr>";
                ?>
                                    <td><?php echo "INV" . $invoice->getInvoiceNo(); ?></td>
                                    <td><?php echo $invoice->getInvoiceDate(); ?></td>
                                    <td><?php echo $invoice->getAccount(); ?></td>

                                    <td><?php echo $invoice->getTotalAmount(); ?></td>
                                    <td><?php echo $invoice->getCurrency(); ?></td>
                                    <td><?php echo ($invoice->getIsActive() == 'Y') ? 'YES' : 'NO'; ?></td>
                                    <td class="clsButton"><a class="btn default blue-stripe" href="invoice_details.php?id=<?php echo $invoice->getId(); ?>&action=Invoice_Info" >Invoice Info</a>
                                        <?php
                                        if ($countInvoiceFilterId == $invoice->getId()) {
                                            echo '<a class="btn default blue-stripe" href="invoice_details.php?id=' . $invoice->getInvoiceNo() . '&action=confirmed_delete" onclick="return confirm(\'Are you sure you want to delete this Invoice?\')">Delete</a>';
                                        }
                                        ?></td>
                                    <td class="clsButton"><a class="btn default blue-stripe" href="../InvoicesFiles/pdf/<?php echo $invoice->getInvoiceFile(); ?>" target="_blank" >PDF</a>
                <?php
                $csvFile = "../InvoicesFiles/csv/INV" . $invoice->getId();
                if (file_exists($csvFile)) {
                    echo '<a class="btn default blue-stripe" href="' . $csvFile . '" >CSV</a>';
                } else {
                    echo '<a class="btn default blue-stripe" href="invoice_details.php?id=' . $invoice->getInvoiceNo() . '&action=Display_Invoice_csv" >CSV</a>';
                }
                ?>
                                    </td>

                                        <?php /* ?><!--<td class="clsButton"><a class="btn default blue-stripe" href="invoice_details.php?id=<?php echo $invoice->getInvoiceNo();?>&action=Display_Invoice_csv" >CSV</a></td>
                                          <td class="clsButton"><a class="btn default blue-stripe" href="invoice_details.php?id=<?php echo $invoice->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Invoice?')">Delete</a></td> --><?php */ ?>
                                    </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>
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
                                $menu = new Adminmenu(Adminmenu::INVOICES);
                                //echo $menu ;
                                $menu->render();
                            }

                            private function userDropdown() {

                                $filter = new UserAccountFilter();
                                $filter->AddOrderByAccount();
                                $filter->addActiveFlagFilter();
                                $userList = $filter->getColumnList("id, user_account");

                                echo '<select name="Account"  class="form-control" >';
                                echo '<option>Select Customer Account </option>';

                                foreach ($userList as $userData) {
                                    $selected = ($userAccount == $userData->getUserAccount()) ? " selected" : "";
                                    echo '<option' . $selected . ' value="' . $userData->getUserAccount() . '">' . $userData->getUserAccount() . '</option>';
                                }

                                echo '</select>';
                            }

                            private function agentCodeDropdown() {

                                $filter = new ServiceFilter();
                                $serviceList = $filter->getColumnList("id, name");

                                echo '<select name="agent_code">';
                                echo '<option>Please Select Agentcode </option>';

                                foreach ($serviceList as $serviceData) {
                                    $selected = ($agentCode == $serviceData->getName()) ? " selected" : "";
                                    echo '<option' . $selected . ' value="' . $serviceData->getName() . '">' . $serviceData->getName() . '</option>';
                                }

                                echo '</select>';
                            }

                            private function countryDropdown() {
                                //echo "sdfsdfsasdfsdfsadfasdfas";
                                echo '<select name="country">';
                                echo '<option>Please Select Country</option>';
                                $countriesList = new Country();
                                $countryListData = $countriesList->getAnyCountry();

                                //echo "sdfsdfsasdfsdfsadfasdfas";
                                //print_r($countryListData);


                                foreach ($countryListData as $countryItem) {
                                    if ($countryItem->getName() == $country)
                                        echo '<option value="' . $countryItem->getName() . '" selected="selected">' . $countryItem->getName() . '</option>';
                                    else
                                        echo '<option value="' . $countryItem->getName() . '" >' . $countryItem->getName() . '</option>';
                                }

                                echo '</select>';
                            }

                            /*                             * *
                             * Controller logic goes here
                             */

                            public function init() {

                                
                                // check admin user is authenticated
                                if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
                                    util_redirect("login.php");
                                }
                                ?>
        <script type="text/jscript">
            //document.getElementById('Date_from').value = Date();
            //     document.getElementById('Date_to').value = Date();

        </script>
        <?php
        //
        //$this->setTitle("Admin Invoices");

        /* ------------------------------------------------------------------------------ */
        // get Invoices
        //echo $this->form_vars["form_action"]; exit;
        //	print_r($_POST);
        ///die;
        if (@$_POST["form_action"] == 'btnLabelMerge') {
            $fileFlag = false;
            $generateLabel = $_POST['deleteConsignments'];
            $generatelabelstr = implode(',', $generateLabel);

            if (count($generatelabelstr) > 0) {
                $pdf2 = new PDFMerger();
                // Create pdf
                //$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                //$pdf->SetX(1.0);
                //$filter->addServiceTypeFilter("DBP");
                $filterCon = new InvoiceFilter();
                //$filter->addStatusFilter(Consignment::STATUS_READY_TO_PRINT);
                $filterCon->addIdArrayFilter($generatelabelstr);
                //$filterCon->AddOrderByID();
                //$filterCon->addCommentIntoQuery('btnLabelMerge Query in client_list.php on line 209');
                $consignment_array = $filterCon->getColumnList('invoice_file, account, id');

                if (count($consignment_array) > 0) {
                    foreach ($consignment_array as $consignmentInformation) {
                        $fileNameNew = '../InvoicesFiles/pdf/' . $consignmentInformation->getInvoiceFile();
                        if (trim($fileNameNew) != '') {
                            $fileFlag = true;
                            $pdf2->addPDF($fileNameNew, 'all');
                        }
                    }
                    if ($fileFlag == true) {
                        $outFile = '../InvoicesFiles/temp/MERGE_INVOICE_' . date("d_M_Y__H_i_s", time()) . '.pdf';
                        //$pdf->Output($outFile, "F");
                        $pdf2->merge('file', $outFile);

                        echo '<script language="javascript">';
                        echo "window.open('" . $outFile . "','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');";
                        echo 'document.location.href="../main/invoices.php";</script>';
                        die;
                    }
                }
            }
            util_redirect("../main/invoices.php");
        }

        $CouObj = new InvoiceFilter();
        if (@$_POST["form_action"] == 'Submit') {

            $CouObj->fromDateFilter(date('Y-m-d 00:00:00', strtotime($_POST["date_printed"])));
            $CouObj->toDateFilter(date('Y-m-d 23:59:59', strtotime($_POST["date_printed1"])));
            $CouObj->IsActiveFilter('Y');
            $CouObj->IsDeletedFilter('N');

            if ($_POST["Account"] != "--- Please Select ---") {
                $CouObj->addFieldFilter('account', $_POST["Account"]);
            }
            $CouObj->orderBySort(' invoice_date DESC ');
            //$this->invoices = $CouObj->getList();		
        } else {
            $CouObj->IsActiveFilter('Y');
            $CouObj->IsDeletedFilter('N');
            $CouObj->orderBySort(' invoice_date DESC ');
            //$this->invoices = $CouObj->getList();		
        }


        $_SESSION['invoice_filter'] = $CouObj;
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
