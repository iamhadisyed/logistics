<?php
////////////////////////////////////////////////////
//
// List of tariffs
//
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

    private $courier_id = 0;
    private $_title = "";
    private $service_id = 0;
    private $collection_postcode_group_id = 0;
    private $destination_postcode_group_id = 0;
    private $tariff_array = array();
    private $new_flag = false;

    /**
     * Render javascript to head section
     *
     */
    public function renderHead() {
        ?>
        <style type="text/css">
            .title_white{
                color: #FFFFFF;
            }
        </style>
        <script language="JavaScript">

            $(document).ready(function () {

                $("#tbl_tariffs tr:last input#tbl_unit_size").focus(function () {
                    addTableRow("#tbl_tariffs");
                });

                function addTableRow(table)
                {
                    $(table).append($(table + ' tr:last').clone());
                    $(table + ' tr:last #tbl_weight').val('')
                    $(table + ' tr:last #tbl_ex_tariff').val('')
                    $(table + ' tr:last #tbl_ex_unit_price').val('')
                    $(table + ' tr:last #tbl_tariff').val('')
                    $(table + ' tr:last #tbl_unit_price').val('')
                    $(table + ' tr:last #tbl_unit_size').val('')

                    $("#tbl_tariffs tr:last input#tbl_customer_id").focus(function () {
                        addTableRow("#tbl_tariffs");
                    });

                    return true;
                }

            });

            function updateTariff(tariffId, tariffaction)
            {
                var weight = $("#tbl_weight_" + tariffId).val();
                var exTariff = $("#tbl_ex_tariff_" + tariffId).val();
                var exUnitPrice = $("#tbl_ex_unit_price_" + tariffId).val();
                var tariff = $("#tbl_tariff_" + tariffId).val();
                var ubitPrice = $("#tbl_unit_price_" + tariffId).val();
                var unitSize = $("#tbl_unit_size_" + tariffId).val();
                var customerId = $("#tbl_customer_id_" + tariffId).val();
                var formula = $("#tbl_formula_" + tariffId).val();



                $.ajax({
                    type: 'POST',
                    url: 'ajax_tariff.php',
                    data: {
                        tariffId: tariffId,
                        weight: weight,
                        exTariff: exTariff,
                        exUnitPrice: exUnitPrice,
                        tariff: tariff,
                        ubitPrice: ubitPrice,
                        unitSize: unitSize,
                        customerId: customerId,
                        formula: formula,
                        ACTION: 'UPDATE'
                    },
                    /* beforeSend:function(){
                     // this is where we append a loading image
                     $('#ajax-panel').html('<div class="loading"><img src="/images/loading.gif" alt="Loading..." /></div>');
                     },*/
                    success: function (data) {
                        // successful request; do something with the data
                        $('#ajax-panel').empty();
                        alert(data);
                    },
                    error: function () {
                        // failed request; give feedback to user
                        alert(' Oops!  Try that again in a few moments.');
                    }
                });

            }

            function addNewTariff(tariffId, tariffaction)
            {
                var service_id = '<?php echo $this->service_id; ?>';
                var collection_rateband_id = '<?php echo $this->collection_rateband_id; ?>';
                if (collection_rateband_id == '0')
                    collection_rateband_id = $('#collection_rateband_id').val();
                var destination_rateband_id = '<?php echo $this->destination_rateband_id; ?>';

                if (destination_rateband_id == '0')
                    destination_rateband_id = $('#destination_rateband_id').val();
                var collection_postcode_group_id = '<?php echo $this->collection_postcode_group_id; ?>';
                if (collection_postcode_group_id == '0')
                    collection_postcode_group_id = $('#collection_postcode_group_id').val();
                var destination_postcode_group_id = '<?php echo $this->destination_postcode_group_id; ?>';
                if (destination_postcode_group_id == '0')
                    destination_postcode_group_id = $('#destination_postcode_group_id').val();
                var fr_weight = $("#tbl_weight_fr").val();
                var weight = $("#tbl_weight").val();
                var formula = $("#tbl_formula").val();

                var exTariff = $("#tbl_ex_tariff").val();
                var exUnitPrice = $("#tbl_ex_unit_price").val();
                var tariff = $("#tbl_tariff").val();
                var ubitPrice = $("#tbl_unit_price").val();
                var unitSize = $("#tbl_unit_size").val();
                var customerId = $("#tbl_customer_id").val();

                if (collection_rateband_id <= 0)
                {
                    alert('Please select the collection rateband ')
                    return false;
                }
                if (destination_rateband_id <= 0)
                {
                    alert('Please select the destination rateband ')
                    return false;
                }

                if (weight == '')
                {
                    alert('Please select the weight')
                    return false;
                }
				
				if (tariff == '')
                {
                    alert('Please enter chargeable tariff price')
                    return false;
                }
				
				if (ubitPrice == '')
                {
                    alert('Please enter chargeable unit price')
                    return false;
                }
				
				if (customerId == '')
                {
                    alert('Please enter Tariff name')
                    return false;
                }
				
				if (parseFloat(fr_weight) >= parseFloat(weight) )
                {
                    alert('Please enter correct weight to add/update tariff.');
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    url: 'ajax_tariff.php',
                    data: {
                        service_id: service_id,
                        collection_rateband_id: collection_rateband_id,
                        destination_rateband_id: destination_rateband_id,
                        collection_postcode_group_id: collection_postcode_group_id,
                        destination_postcode_group_id: destination_postcode_group_id,
                        tariffId: tariffId,
                        weight: weight,
                        fr_weight: fr_weight,
                        exTariff: exTariff,
                        exUnitPrice: exUnitPrice,
                        tariff: tariff,
                        ubitPrice: ubitPrice,
                        unitSize: unitSize,
                        customerId: customerId,
                        formula: formula,
                        ACTION: 'ADD'
                    },
                    success: function (data) {
                        if (data == 'SUCCESS')
                        {
                            alert('You have successfully save the tariff');
                            setTimeout(function () {
                                window.location = 'tariffs_details.php?service_id=' + service_id + '&collection_rateband_id=' + collection_rateband_id + '&destination_rateband_id=' + destination_rateband_id + '&collection_postcode_group_id=' + collection_postcode_group_id + '&destination_postcode_group_id=' + destination_postcode_group_id + '&tariff_name=' + customerId;
                                //window.location.reload(false);
                            }, 1000);
                        } else
                        {
                            alert(data);
                        }
                    },
                    error: function () {
                        // failed request; give feedback to user
                        alert(' Oops!  Try that again in a few moments.');
                    }
                });

            }


        </script>
        <?php
    }

    private function buildPostcodeGroupDropDown($dropdown_name, $selected_postcode_group_id) {
        $PgpObj = new Postcodegroup;
        ?>
        <select name="<?php echo $dropdown_name ?>" id="<?php echo $dropdown_name; ?>" class="form-control">
            <option value="0"> - All Postcodes - </option>
            <?php
            foreach ($PgpObj->getAnyPostcodegroup() as $postcodegroup) {
                ?>
                <option value="<?php echo $postcodegroup->getId(); ?>" <?php
                if ($postcodegroup->getId() == $selected_postcode_group_id) {
                    echo "selected";
                }
                ?>><?php echo $postcodegroup->getName(); ?></option>
                        <?php
                    }
                    ?>
        </select>
        <?php
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        $fromWeight = 0;
        $tarrifName = '';
        $tarrifFormula = '';


        $serviceData = new Services($this->service_id);
        // get zone list
        $zoneOptions = "";
        $zoneList = new RatebandFilter();
        $zoneList->addFieldFilter("courier_service_id", $this->service_id);
        $zoneList = $zoneList->getList();
        if (count($zoneList) > 0) {
            foreach ($zoneList as $zone) {
                $zoneOptions .= "<option value=\"" . $zone->getId() . "\">" . $zone->getName() . "</option>";
            }
        }
        ?>




        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Tariffs Details: <?= @$_REQUEST['tariff_name']; ?>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>

            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:200px" data-always-visible="0" data-rail-visible="1" data-rail-color="blue" data-handle-color="red">





                    <div id="tariff_details">
                        <div style="width:100%">
                            <?php
                               if (trim($_REQUEST['tariff_name']) != '') {
                                   ?><a class="btn btn-primary pull-right" href="tariffs.php?service_id=<?php echo $this->service_id;
                       echo (isset($_REQUEST['tariff_name'])) ? '&tariff_name=' . $_REQUEST['tariff_name'] : '';
                                   ?>">Tariffs</a>
                                   <?php
                            } else {
                                $serviceObject = new Services($_REQUEST['service_id']);
                                $this->courier_id = $serviceObject->getCarrier();
                                ?><a  class="btn btn-primary pull-right"  href="services.php?courier_id=<?php echo $this->courier_id ?>">Services</a>
            <?php
        }
        ?>
                        </div>

                        <div style="clear:both">
                            <br/>
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr class="textb" id="tbl_header">
                                        <th class="bg-blue"><label class="title_white">Service Name</label></th>
                                        <th class="bg-blue title_white"><?php echo $serviceData->getName(); ?></th>
                                        <th class="red-back title_white"><label class="title_white">Currency </label></th>
                                        <th class="red-back title_white"><?php echo $serviceData->getUploadedCurrency(); ?></th>
                                    </tr>
                                    <tr class="textb" id="tbl_header">
                                        <td><label>From Rateband</label></td>
                                        <td>

                                            <?php
                                            if ($this->new_flag) {
                                                echo Rateband::buildRatebandDropDown("collection_rateband_id", $this->service_id, $this->collection_rateband_id);
                                            } else {
                                                $rateBand = new RateBand($this->collection_rateband_id);
                                                echo $rateBand->getName();
                                            }
                                            ?>
                                        </td>
                                        <td><label>Postcode group</label></td>
                                        <td>
                                            <?php
                                            if ($this->new_flag) {
                                                echo $this->buildPostcodeGroupDropDown("collection_postcode_group_id", $this->collection_postcode_group_id);
                                            } else {
                                                if ($this->collection_postcode_group_id <= 0) {
                                                    echo "All Postcodes";
                                                } else {
                                                    $postcodeGroup = new PostCodeGroup($this->collection_postcode_group_id);
                                                    echo $postcodeGroup->getName();
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label>To Rateband</label></td>
                                        <td>
                                            <?php
                                            if ($this->new_flag) {
                                                echo Rateband::buildRatebandDropDown("destination_rateband_id", $this->service_id, $this->destination_rateband_id);
                                            } else {
                                                $rateBand = new RateBand($this->destination_rateband_id);
                                                echo $rateBand->getName();
                                            }
                                            ?>
                                        </td>
                                        <td><label>Postcode group</label></td>
                                        <td>
                                            <?php
                                            if ($this->new_flag) {
                                                echo $this->buildPostcodeGroupDropDown("destination_postcode_group_id", $this->destination_postcode_group_id);
                                            } else {
                                                if ($this->destination_postcode_group_id <= 0) {
                                                    echo "All Postcodes";
                                                } else {
                                                    $postcodeGroup = new PostCodeGroup($this->destination_postcode_group_id);
                                                    echo $postcodeGroup->getName();
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table id="tbl_tariffs" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr class="textb" id="tbl_header2">
                                        <th rowspan="2"  style="vertical-align:middle; text-align:center">Weight</th>
                                        <th colspan="2" class="red-back "><center>
                                    Chargeable Prices
                                </center></th>
                                <th rowspan="2" style="vertical-align:middle"><center>
                                    Unit Size (Kgs)
                                </center></th>
                                <th rowspan="2" style="vertical-align:middle"><center>
                                    Tariff Name
                                </center></th>
                                </tr>
                                <tr>
                                    <th class="charge">Tariff (CHRG)</th>
                                    <th class="charge">Unit Price (ITMCHR)</th>
                                    <th class="charge">Formula</th>
                                    <th class="charge">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (count($this->tariffs) > 0) {
                                        foreach ($this->tariffs as $tariff) {
                                            $fromWeight = $tariff->getWeightTo();
                                            $tarrifName = $tariff->getCustomerId();
                                            $tarrifFormula = $tariff->getFormula();
                                            ?>
                                            <tr class="textb">
                                                <td><input class="form-control" id="tbl_weight_<?php echo $tariff->getId() ?>" name="weight[]" type="text" value="<?php echo $tariff->getWeightTo(); ?>" readonly="readonly"></td>
                                                <td style="display:none;"><input class="form-control" id="tbl_ex_tariff_<?php echo $tariff->getId() ?>" name="ex_tariff[]" type="text" value="<?php echo $tariff->getExtraTariff(); ?>"></td>
                                                <td style="display:none;"><input class="form-control" id="tbl_ex_unit_price_<?php echo $tariff->getId() ?>" name="ex_unit_price[]" type="text" value="<?php echo $tariff->getExtraAddUnitCost(); ?>"></td>
                                                <td><input class="form-control" id="tbl_tariff_<?php echo $tariff->getId() ?>" name="tariff[]" type="text" value="<?php echo $tariff->getTariff(); ?>"></td>
                                                <td><input class="form-control" id="tbl_unit_price_<?php echo $tariff->getId() ?>" name="unit_price[]" type="text" value="<?php echo $tariff->getAddUnitCost(); ?>"></td>
                                                <td><input class="form-control" id="tbl_unit_size_<?php echo $tariff->getId() ?>" name="unit_size[]" type="text" value="<?php echo $tariff->getUnitSize(); ?>"></td>
                                                <td><input class="form-control" id="tbl_customer_id_<?php echo $tariff->getId() ?>" name="unit_customer_id[]" type="text" value="<?php echo $tariff->getCustomerId(); ?>"></td>
                                                <td><input class="form-control" id="tbl_formula_<?php echo $tariff->getId() ?>" name="tbl_formula_[]" type="text" value="<?php echo $tariff->getFormula(); ?>"></td>
                                                <td><input type="button" name="update" id="update-tariff" value="Update" class="btn btn-primary"  onclick="return updateTariff('<?php echo $tariff->getId() ?>', 'update');" /></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <table class="table table-striped table-bordered table-hover">
                                <tr class="bg-red">
                                    <td colspan="4" style="text-align:center;"><strong><h4>Please Insert New Tariff</h4></strong></td>
                                </tr>

                                <tr>
                                    <td width="20%">From Weight</td>
                                    <td width="40%"><input class="form-control" id="tbl_weight_fr" name="weight_fr" type="text" value="<?php echo $fromWeight; ?>" readonly="readonly"></td>
                                    <td width="50%" colspan="2" rowspan="10">
                                        <?php
                                        $detailsFormulla = Tariff::formullaDetails();
                                        echo "<div class='col-sm-12'>";
                                        $countIndex = 0;
                                        foreach ($detailsFormulla as $index => $detailsData) {
                                            if ($countIndex % 2 == 0)
                                                $classes = 'bg-red';
                                            else
                                                $classes = 'bg-blue';
                                            echo "<div class='col-sm-4 " . $classes . "'><strong class='title_white'>" . $index . ": </strong></div><div class='col-sm-8 title_white " . $classes . "'>" . $detailsData . "</div>";
                                            $countIndex++;
                                        }
                                        echo "</div>";
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>To Weight</td>
                                    <td><input class="form-control" id="tbl_weight" name="weight" type="text"></td>
                                </tr>
                                <tr class="bg-red">
                                    <td>Chargeable Tariff Price (CHRG)</td>
                                    <td><input class="form-control" id="tbl_tariff" name="tariff" type="text"></td>
                                </tr>
                                <tr class="bg-red">
                                    <td>Chargeable Unit Price(ITMCHR)</td>
                                    <td><input class="form-control" id="tbl_unit_price" name="unit_price" type="text"></td>
                                </tr>
                                <tr class="bg-red">
                                    <td>Unit Size (Kgs)<br />Optional</td>
                                    <td><input class="form-control" id="tbl_unit_size" name="unit_size" type="text"></td>
                                </tr>
                                <tr style="display:none;">
                                    <td>Cost Tariff Price </td>
                                    <td><input class="form-control" id="tbl_ex_tariff" name="ex_tariff" type="text"></td>
                                </tr>
                                <tr style="display:none;">
                                    <td>Cost Unit Price</td>
                                    <td><input class="form-control" id="tbl_ex_unit_price" name="ex_unit_price" type="text"></td>
                                </tr>


                                <tr>
                                    <td>Tariff Name</td>
                                    <td><input class="form-control" id="tbl_customer_id" name="unit_customer_id" type="text" value="<?php echo $tarrifName; ?>"></td>
                                </tr>

                                <tr>
                                    <td>Tarif Formula</td>
                                    <td>                  
                                            <?php
                                            $serviceFormullaArray = Tariff::formulla();
                                            ?>
                                        <select name="tbl_formula" id="tbl_formula" class="form-control">
                                            <?php
                                            foreach ($serviceFormullaArray as $ind => $valFormula) {
                                                echo '<option value="' . $valFormula['formulla'] . '">' . $valFormula['name'] . '</option>';
                                            }
                                            ?>
                                        </select>

        <?php /* ?><input class="txt" id="tbl_formula" name="tbl_formula" type="text" value="<?php echo $tarrifFormula;?>"><?php */ ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><input class="btn btn-primary btn-block" id="btn_save_ajax" name="btn_save_ajax" type="button" value="Save"  onclick="return addNewTariff('', 'ADD');"></td>
                                </tr>

                            </table>

                            <input type="hidden" name="action" id="action" value="save" class="btn btn-primary" />
                            <input type="hidden" name="service_id" value="<?php echo $this->service_id; ?>" class="btn btn-primary" />

        <?php /* ?><input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes" ><br/><?php */ ?>

                        </div>
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
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        //
        //echo  $size = (int)$_SERVER['CONTENT_LENGTH'];
        // check admin user is authenticated

        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }
        $tariff_name = (isset($_REQUEST['tariff_name']) ? strip_tags($_REQUEST['tariff_name']) : '');
        /* ------------------------------------------------------------------------------ */
        // get vars

        (int) $this->service_id = (isset($_REQUEST['service_id']) ? strip_tags($_REQUEST['service_id']) : '');

        // Look for get or post variables
        (int) $this->collection_rateband_id = (isset($_REQUEST['collection_rateband_id']) ? strip_tags($_REQUEST['collection_rateband_id']) : 0);
        (int) $this->destination_rateband_id = (isset($_REQUEST['destination_rateband_id']) ? strip_tags($_REQUEST['destination_rateband_id']) : 0);
        (int) $this->collection_postcode_group_id = (isset($_REQUEST['collection_postcode_group_id']) ? strip_tags($_REQUEST['collection_postcode_group_id']) : 0);
        (int) $this->destination_postcode_group_id = (isset($_REQUEST['destination_postcode_group_id']) ? strip_tags($_REQUEST['destination_postcode_group_id']) : 0);

        // If updating an existing value, then collection rateband id will not have been passed
        $this->new_flag = (util_get_num("collection_rateband_id") <= 0);
        //print_r($_POST['weight']);
        $this->weight = (isset($_POST['weight']) ? $_POST['weight'] : array());
        $this->tariff = (isset($_POST['tariff']) ? $_POST['tariff'] : array());
        $this->unit_price = (isset($_POST['unit_price']) ? $_POST['unit_price'] : array());
        $this->unit_size = (isset($_POST['unit_size']) ? $_POST['unit_size'] : array());
        $this->unit_customer_id = (isset($_POST['unit_customer_id']) ? $_POST['unit_customer_id'] : array());
        $this->ex_tariff = (isset($_POST['ex_tariff']) ? $_POST['ex_tariff'] : array());
        $this->ex_unit_price = (isset($_POST['ex_unit_price']) ? $_POST['ex_unit_price'] : array());

        /* ------------------------------------------------------------------------------ */
        // delete
        if (util_request("action") == "confirmed_delete") {
            $tariff_name = util_request("tariff_name");
            if (trim($tariff_name) != '') {
                // delete all tariffs for this courier service
                $TarDelObj = new Tariff;
                $TarDelObj->setCourierServiceId($this->service_id);
                $TarDelObj->setCourierServiceId($this->service_id);
                $TarDelObj->setCollectionRatebandId($this->collection_rateband_id);
                $TarDelObj->setDestinationRatebandId($this->destination_rateband_id);
                $TarDelObj->setCollectionPostcodeGroupId($this->collection_postcode_group_id);
                $TarDelObj->setDestinationPostcodeGroupId($this->destination_postcode_group_id);
                $TarDelObj->setCustomerId($tariff_name);

                $TarDelObj->expunge();
            }
            //
            util_redirect("tariffs.php?service_id=" . $this->service_id . '&tariff_name=' . $tariff_name);
        }


        /* ------------------------------------------------------------------------------ */
        //echo "<pre>";
        //print_r($_POST);
        // pre-process form
        if (isset($_POST['btn_save'])) {
            //	echo "ME HERE";
            //	die;
            $count = 1;

            // iterate the weight input fields from form.
            $arrayCount = 0;
            $this->tariff_array = array();
            foreach ($this->weight as $key => $val) {
                //	echo $val." -- ";
                if ($val > 0) {
                    //	echo " me in array <br>";
                    /* // want to add an additional row at start, so add for first item.
                      if ($key == 0)
                      {
                      $this->tariff_array[$key]['weight'] 	 = 0;
                      $this->tariff_array[$key]['tariff'] 	 = $this->tariff[$key];
                      $this->tariff_array[$key]['unit_price'] = 0;
                      $this->tariff_array[$key]['unit_size'] = 0;
                      $this->tariff_array[$key]['customer_id'] = 0;
                      $this->tariff_array[$key]['ex_tariff'] 	 = $this->ex_tariff[$key];
                      $this->tariff_array[$key]['ex_unit_price'] = 0;
                      } */
                    // add subsequent entries (key + 1, as we added addditional first row).
                    $this->tariff_array[$arrayCount]['weight'] = $val;
                    $this->tariff_array[$arrayCount]['tariff'] = $this->tariff[$key];
                    $this->tariff_array[$arrayCount]['unit_price'] = $this->unit_price[$key];
                    $this->tariff_array[$arrayCount]['unit_size'] = $this->unit_size[$key];
                    $this->tariff_array[$arrayCount]['customer_id'] = $this->unit_customer_id[$key];
                    $this->tariff_array[$arrayCount]['ex_tariff'] = $this->ex_tariff[$key];
                    $this->tariff_array[$arrayCount]['ex_unit_price'] = $this->ex_unit_price[$key];
                    //
                    $count++;
                    $arrayCount++;
                }
            }
        }

        /* ------------------------------------------------------------------------------ */
        // process form
        if (isset($_POST['btn_save'])) {
            //print_r($this->tariff_array);
            //die;
            // delete all tariffs for this courier service
            $TarDelObj = new Tariff;
            $TarDelObj->setServiceId($this->service_id);
            $TarDelObj->setCollectionRatebandId($this->collection_rateband_id);
            $TarDelObj->setDestinationRatebandId($this->destination_rateband_id);
            $TarDelObj->setCollectionPostcodeGroupId($this->collection_postcode_group_id);
            $TarDelObj->setDestinationPostcodeGroupId($this->destination_postcode_group_id);
            $TarDelObj->expunge();

            // create tariffs if valid
            for ($key = 0; $key < sizeof($this->tariff_array); $key++) {
                $TarSavObj = new Tariff;
                $TarSavObj->setServiceId($this->service_id);
                $TarSavObj->setCollectionRatebandId($this->collection_rateband_id);
                $TarSavObj->setDestinationRatebandId($this->destination_rateband_id);
                $TarSavObj->setCollectionPostcodeGroupId($this->collection_postcode_group_id);
                $TarSavObj->setDestinationPostcodeGroupId($this->destination_postcode_group_id);

                if (sizeof($this->tariff_array) > $key) {
                    if ($key == 0)
                        $TarSavObj->setWeightFrom(0);
                    else
                        $TarSavObj->setWeightFrom(($this->tariff_array[$key - 1]['weight'] + 0.01));

                    $TarSavObj->setWeightTo($this->tariff_array[$key]['weight']);
                    $TarSavObj->setTariff($this->tariff_array[$key]['tariff']);
                    $TarSavObj->setAddUnitCost($this->tariff_array[$key]['unit_price']);
                    $TarSavObj->setUnitSize($this->tariff_array[$key]['unit_size']);
                    $TarSavObj->setCourierTariff($this->tariff_array[$key]['ex_tariff']);
                    $TarSavObj->setCourierUnitCost($this->tariff_array[$key]['ex_unit_price']);
                    $TarSavObj->setCustomerId($this->tariff_array[$key]['customer_id']);
                }
                $TarSavObj->setOrderq(0);
                $TarSavObj->setActive(1);
                $TarSavObj->setDeletedq('N');

                //if ($TarSavObj->isValid())
                {
                    $TarSavObj->save();
                }
            }

            //util_redirect ("tariffs.php?service_id=" . $this->service_id);
        }

        /* ------------------------------------------------------------------------------ */
        // get tariff data for redisplay
        // BY USING Weight_from > 0, IGNORES FIRST ROW - WHICH IS WHAT WE WANT!
        $TarObj = new TariffFilter();
        $TarObj->addFieldFilter('courier_service_id', $this->service_id);
        $TarObj->addFieldFilter('collection_rateband_id', $this->collection_rateband_id);
        $TarObj->addFieldFilter('destination_rateband_id', $this->destination_rateband_id);
        $TarObj->addFieldFilter('collection_postcode_group_id', $this->collection_postcode_group_id);
        $TarObj->addFieldFilter('destination_postcode_group_id', $this->destination_postcode_group_id);
        if (trim($tariff_name) != '') {
            $TarObj->addFieldFilter('customer_id', $tariff_name);
        }
        $TarObj->addFieldOrderBy(' weight_from asc');
        //$TarObj->addWeightFromGresterEqualFilter('0.000');
        // It is the "weight from" that people enter - so order by this field (the weight to will automatically be corrected)
        $this->tariffs = $TarObj->getList();

        $service = new Services($this->service_id);
        $this->setTitle("Tariff for " . $service->getName() . " (" . $service->getPrivateName() . ")");
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?> 
