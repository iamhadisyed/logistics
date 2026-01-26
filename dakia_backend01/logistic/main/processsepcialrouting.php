<?php
//
// get settings

require_once("../includes/settings/config.inc.php");
$sessionUser = SessionManager::getUser();
$sessionUserId = $sessionUser->getId();
$getUserType = $sessionUser->getUserType();

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'special_routing_country') {


    $userId = util_get_num('uid');
    $serviceCode = util_get_num('serviceCode');
    $sercountry = util_get('country');
    $resultServices = new Services($serviceCode);

    if (count($resultServices) > 0) {
        $serviceCountry = str_replace(',', "','", $resultServices->getServiceCountry());

        $country = new CountryFilter();
        if ($resultServices->getServiceCountry() != 'ALL')
            $country->addIsoFilter($serviceCountry);
        $countryResult = $country->getColumnList(' name, iso ');
        $countCountry = 1;
        $selectallCheckbox = '';
        if (count($countryResult) >= 2) {
            $selectallCheckbox = '<input type="checkbox" class="countrycheck" value="check all" onchange="selcetAllCountryCheck(this);"/> Select All Countries';
        }
        $display_stringDemo = "<label>All Country(s) For Service " . $resultServices->getName() . "</label> <div style='float: right; position: relative;'>" . $selectallCheckbox . "</div> <br clear='all'/>";
        echo $display_stringDemo;

        $psrupdate = new UserServicesRoutingFilter();
        $psrupdate->addFilter(' service_id = "' . DbAccess3::escape($serviceCode) . '"');
        $psrupdate->addUserIdFilter($userId);
        $PartnersCountry = $psrupdate->getColumnList(' country_iso ');
        $countriesSelected = array();
        if (count($PartnersCountry) > 0) {
            foreach ($PartnersCountry as $pcountries)
                $countriesSelected[] = $pcountries->getCountryIso();
        }

        foreach ($countryResult as $countryData) {
            if (in_array($countryData->getIso(), $countriesSelected))
                $checked = 'checked="checked"';
            else
                $checked = '';

            if (strtolower(($sercountry)) == strtolower(trim($countryData->getIso()))) {
                echo '<div class="col-sm-4 pull-left"><input type="checkbox" name="country[]" class="form-group countryList" value="' . $countryData->getIso() . '" ' . $checked . '>' . $countryData->getName() . '</div>';
            } else {
                echo '<div class="col-sm-4 pull-left"><input type="checkbox" name="country[]" class="form-group countryList" value="' . $countryData->getIso() . '" ' . $checked . '>' . $countryData->getName() . '</div>';
            }
            if ($countCountry % 3 == 0) {
                echo '';
            }
            $countCountry++;
        }
        echo '	<input type="hidden" name="service_code" id="service_code" value="' . $serviceCode . '" >
				<input type="hidden" name="user_id" id="user_id" value="' . $userId . '" >
		<div style="clear:both;"></div>';
        if ($getUserType != "customerservice") {
            echo '
                        </div>
                    <div class="modal-footer">
                    <div class="form_buttons">
                        <a href="#" class="btn btn-primary pull-right" id="saveCountry" onclick="submitCountry();"><span></span>Save</a>
                        <button type="button" class="btn btn-default  pull-right" data-dismiss="modal">Close</button>
                        <br class="clear" />
                    </div>';
        }
    }
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'special_routing_weight') {

    $userId = $_GET['uid'];
    $serviceCode = $_GET['serviceCode'];
    $resultServices = new Services((int) $serviceCode);

    $display_stringDemo = " <label>Weight For Service " . $resultServices->getName() . " </label>";
    echo $display_stringDemo;
    ?>
    <div class="form_rectanglediv"  id="weightarea">
        <div class="row">
            <div class="col-sm-6">
                <label><strong>From Weight</strong></label>
                <select name="fromweight" id="fromweight" onchange="calcUpperWeight(this.value); changeweight()" class="form-control" >
    <?php
    // onchange="calcUpperWeight(this.value)"
    $weightCount = 0;
    while ($weightCount < 30) {
        if ($weightCount == $fromweight)
            echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
        else
            echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
        if ($weightCount < 2)
            $weightCount += 0.25;
        //elseif($weightCount<10)$weightCount+=0.5;
        else
            $weightCount += 0.5;
    }
    ?>	                        
                </select>
                <br class="clear" />   
            </div>
            <div class="col-sm-6">
                <label><strong>To Weight</strong></label>
                <select name="toweight" id="toweight" class="form-control" onchange="<?php echo $function; ?>">
                    <?php
                    //onchange="calcLowerWeight(this.value)"
                    $weightCount = 0.25;
                    while ($weightCount <= 30) {
                        if ($weightCount == $toweight)
                            echo "<option value=" . $weightCount . " selected='selected'>" . $weightCount . "</option>";
                        else
                            echo "<option value=" . $weightCount . ">" . $weightCount . "</option>";
                        //echo "<option value=".$weightCount.">".$weightCount."</option>";
                        if ($weightCount < 2)
                            $weightCount += 0.25;
                        //elseif($weightCount<10)$weightCount+=0.5;
                        else
                            $weightCount += 0.5;
                    }
                    ?>                     
                </select>
                <br />
                <span >More than 30Kg</span> <a href="#" onclick="morethan(); return false;">Click Here</a>
            </div>
            <div class="col-sm-6">&nbsp;</div>
            <div class="col-sm-6" id="more-than-30" style="display:none;">
                <input name="toweightmore" id="toweightmore"  class="form-control" placeholder="Enter to weight"  type="text" onkeypress="return numbersonly(event)">
            </div></div>
        <br class="clear" />
    </div>
                    <?php
                    echo '	<input type="hidden" name="service_code" id="service_code" value="' . $serviceCode . '" >
				<input type="hidden" name="user_id" id="user_id" value="' . $userId . '" >
		<div style="clear:both;"></div>';
                    if ($getUserType != "customerservice") {
                        echo '
            </div>
            <div class="modal-footer">
            <div class="form_buttons">
                <a href="#" class="btn btn-primary" id="saveCountry" onclick="checkWeightValid(); "><span></span>Save</a>
                <br class="clear" />
            </div>';
                    }
                }
                ?> 