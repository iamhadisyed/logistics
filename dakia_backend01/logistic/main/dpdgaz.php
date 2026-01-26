<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $error_list = array();
    private $msg = "";
    private $file = "../_assets/export_files/";

    protected function init() {


        /* 		   switch ($this->form_vars["form_action"])
          {
          case "continue":
         */

        $mysql_access = mysql_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
        if (!$mysql_access) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db(SETTING_DB_DATABASE, $mysql_access);


        // for Reamus Product Service
        $local_file = "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/main/GEOGAZ.TXT";  //. $file_name;
        $file = $local_file;
        $f = fopen($file, "r");
        $ln = 0;
        while ($line = fgets($f)) {
            ++$ln;
            if ($line === FALSE)
                print ("FALSE\n");
            else {
                //  echo $line  . "<br>";
                if ($ln == 1) {
                    $array = explode("|", $line);
                    $version = trim($array[1]);
                    $issue_date = trim($array[2]);
                }
                if ($ln > 1) {
                    $service = substr($line, 0, 7);
                    $group = substr($line, 0, 5);
                    $offshore = substr($line, 0, 8);
                    $times = substr($line, 0, 5);
                    $d = substr($line, 0, 1);
                    $i = substr($line, 0, 1);
                    $country = substr($line, 0, 7);
                    if ($service == "SERVICE") {
                        $array = explode("|", $line);
                        $service_2_digit_code = trim($array[1]);
                        $service_3_digit_code = trim($array[2]);
                        $dpd_product_desc = trim($array[3]);
                        $dpd_label_service = trim($array[4]);
                        $ilk_product_desc = trim($array[5]);
                        $ilk_label_service = trim($array[6]);
                        $ilk_alternative_service = trim($array[7]);
                        $premium = trim($array[8]);
                        $sec_dpd = trim($array[9]);
                        $sec_ilk = trim($array[10]);
                        $ilk_max_parcels_per_con = trim($array[11]);
                        $ilk_max_weight_per_parcel = trim($array[12]);
                        $dpd_max_parcels_per_con = trim($array[13]);
                        $dpd_max_weight_per_parcel = trim($array[14]);


                        $sql = "insert into services_dpd(2_digit_service_code, 3_digit_service_code , dpd_product_desc, dpd_label_service, ilk_product_desc, ilk_alternative_service_desc, premium, sec_dpd, sec_ilk, ilk_max_parcels_per_con, ilk_max_weight_per_parcel, dpd_max_parcels_per_con, dpd_max_weight_per_parcel) values ('" . $service_2_digit_code . "', '" . $service_3_digit_code . "', '" . $dpd_product_desc . "', '" . $dpd_label_service . "', '" . $ilk_product_desc . "', '" . $ilk_alternative_service . "', '" . $premium . "', '" . $sec_dpd . "', '" . $sec_ilk . "', '" . $ilk_max_parcels_per_con . "', '" . $ilk_max_weight_per_parcel . "', '" . $dpd_max_parcels_per_con . "', '" . $dpd_max_weight_per_parcel . "' ) ";
                        //  echo  $sql;
                        // mysql_query($sql);
                        //echo $dpd_max_weight_per_con . "<br>";
                    } else if ($group == "GROUP") {
                        $array = explode("|", $line);
                        $lookup_code = trim($array[1]);
                        $list_of_available_services = trim($array[2]);
                        $business = trim($array[3]);
                        //        $sql = "insert into groups(lookup_code, list_of_available_services , Business) values ('". $lookup_code ."', '". $list_of_available_services ."', '" . $business . "') ";
                        //        echo  $sql;
                        //		 mysql_query($sql);
                    } else if ($offshore == "OFFSHORE") {
                        $array = explode("|", $line);
                        $lookup_code = trim($array[1]);
                        $list_of_available_services = trim($array[2]);
                        $business = trim($array[3]);
                        //     $sql = "insert into offshore(lookup_code, list_of_available_services , Business) values ('". $lookup_code ."', '". $list_of_available_services ."', '" . $business . "') ";
                        //     echo  $sql;
                        //	 mysql_query($sql);
                    } else if ($times == "TIMES") {
                        $array = explode("|", $line);
                        $timeslot_code = trim($array[1]);
                        $list_of_available_timeslots = trim($array[2]);
                        // echo "<br>" . $list_of_available_timeslots . "<br>";
                        //$sql = "insert into times(timeslot_code, list_of_available_slots) values ('". $timeslot_code ."', '". $list_of_available_timeslots ."') ";
                        //echo  $sql;
                        //mysql_query($sql);
                    } else if ($d == "D") {
                        $array = explode("|", $line);
                        $postcode_sector = trim($array[1]);
                        $dpd_depot = trim($array[2]);
                        $dpd_services_group = trim($array[3]);
                        $dpd_offshore_zone = trim($array[4]);
                        $timeslots_code = trim($array[5]);
                        $cluster = trim($array[6]);
                        $ilk_depot = trim($array[8]);
                        $ilk_services_group = trim($array[9]);
                        $ilk_offshore_zone = trim($array[10]);
                        $ilk_alternative_service = trim($array[11]);
                        $new_postcode = trim($array[14]);
                        /*   $sql = "insert into domestic(postcode_sector, dpd_depot , dpd_services_group, dpd_offshore_zone, timeslots_code, cluster, ilk_depot, ilk_services_group, ilk_offshore_zone, ilk_alternate_service, new_postcode) values ('". $postcode_sector ."', '". $dpd_depot ."', '" . $dpd_services_group . "', '" . $dpd_offshore_zone . "', '" . $timeslots_code . "', '" . $cluster . "', '" . $ilk_depot . "', '" . $ilk_services_group . "', '" . $ilk_offshore_zone . "', '" . $ilk_alternative_service . "', '" . $new_postcode . "' ) ";
                          echo  $sql;
                          mysql_query($sql);
                         */

//                        echo "<br>" . $postcode_sector . " " . $ilk_depot . "<br>";
                    } else if ($i == "I") {
#INTERNATIONAL|IATA Country Code|Zip From|Zip To|Air Express Depot|Air Express Osort|Air Express Dsort|DPD Classic Depot|DPD Classic Osort|DPD Classic Dsort||||ALT-CC|DPD Direct||||||||||						 
                        $array = explode("|", $line);
                        $iata_country_code = trim($array[1]);
                        $zipcode_from = trim($array[2]);
                        $zipcode_to = trim($array[3]);
                        $air_express_depot = trim($array[4]);
                        $air_express_osort = trim($array[5]);
                        $air_express_dsort = trim($array[6]);
                        $dpd_classic_deport = trim($array[7]);
                        $dpd_classic_osort = trim($array[8]);
                        $dpd_classic_dsort = trim($array[9]);

                        $sql = "insert into international(iata_country_code, zipcode_from , zipcode_to, air_express_depot, air_express_osort, air_express_dsort, dpd_classic_deport, dpd_classic_osort, dpd_classic_dsort) values ('" . $iata_country_code . "', '" . $zipcode_from . "', '" . $zipcode_to . "', '" . $air_express_depot . "', '" . $air_express_osort . "', '" . $air_express_dsort . "', '" . $dpd_classic_deport . "', '" . $dpd_classic_osort . "', '" . $dpd_classic_dsort . "') ";
                        echo $sql;
                        mysql_query($sql);


//                        echo "<br>" . $postcode_sector . " " . $ilk_depot . "<br>";
                    } else if ($country == "COUNTRY") {
                        $array = explode("|", $line);
                        $iata_code = trim($array[1]);
                        $iso_code = trim($array[2]);
                        $allow_express = trim($array[3]);
                        $allow_classic = trim($array[4]);
                        $eu_country = trim($array[5]);
                        $name = trim($array[6]);
                        $shipping_advice = trim($array[7]);

                        /*  $sql = "insert into country(iso , name, iso_number, allow_express, allow_classic, eu_country, shipping_advice) values ('". $iata_code ."', '". $name ."', '" . $iso_code . "', '" . $allow_express . "', '" . $allow_classic . "', '" . $eu_country . "', '" . $shipping_advice . "') ";
                          echo  $sql;
                          mysql_query($sql);
                         */

//                        echo "<br>" . $postcode_sector . " " . $ilk_depot . "<br>";
                    }
                }
            }
        }





        mysql_close($mysql_access);

        //	$complete = true;
        // if ($complete)
        //     {
        //	util_redirect("../main/client_list.php");
        /* 		  //}
          break;
          case "cancel":
          default:
          break;







          }


         */
        die;
    }

    /*     * *
     * Head section
     */

    protected function renderHead() {
        ?>
        <script type="text/javascript">
            /*	$(document).ready(function(){
             $("#form_action").val("continue");
             
             if (true)
             {
             $("#bookingForm").submit();
             }
             else
             {
             $("#btnNext").css("display", "inline");
             }
             
             $("#btnNext").click(function(){
             $("#form_action").val("continue");
             $("#bookingForm").submit();
             });
             });*/
        </script>
        <?php
    }

    /*     * *
     * Content
     */

    protected function renderBody() {
        ?>
        <div id='label_generation'>
            <br/><br/><br/>
            <h1>Transferring Hermes POS File</h1>

            <p><?php echo $this->msg; ?></p>

            <h2>please wait...</h2>

        </div>

        <input type="hidden" name="consignment_total" value="<?php echo @$consignment_total; ?>" />

        <input type="submit" name="btnNext" id="btnNext" style="display:none" />

        <?php
    }

}

/* ------------------------------------------------------------------------------ */
$page = new Page(CONFIG_TEMPLATE_MAIN);
$page->show();
