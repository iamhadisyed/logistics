<?php
////////////////////////////////////////////////////
//
// Controller for Tariff List
////////////////////////////////////////////////////
// get settings
require_once("../includes/settings/config.inc.php");


// set up local page class
class Page extends BasePage {
    /*     * *
     * This page's content
     * @return void
     */

    private $shiptoDDL;
    private $shipfromDDL;
    private $currencyDDL;
    private $shipto;
    private $shipfrom;
    private $currency;
    private $weight;
    private $pieces;
    private $tarrifData;

    /*const api_user = 'DEMOTEST';
    const api_password = 'DEMOTEST';
    const api_account = 'DEMOTEST';*/

    public function renderBody() {
        ?>

        <div class="row">
            <div class="col-md-8"> 
                <h3 class="page-title">Tarrif List</h3>
            </div>
            <div class="col-md-4"> 
                <!--<a href="reporting_user_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a>-->
            </div>
        </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-search"></i>Search Panel </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label">Ship From</label>
        <?php echo $this->shiptoDDL; ?>                
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Ship To</label>
        <?php echo $this->shipfromDDL; ?>                
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Currency</label>
        <?php echo $this->currencyDDL; ?>                
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">Weight</label>
                        <input type="number" name="weight" id="weight" value="<?php echo $this->weight; ?>" class="form-control" required="true" />
                    </div>
                    <div class="col-md-1">
                        <label class="control-label">Pieces</label>
                        <input type="number" name="pieces" id="pieces" value="<?php echo $this->pieces; ?>" class="form-control" required="true" />
                    </div>
                    <div class="col-md-2">
                        <label class="control-label">&nbsp;</label><br />
                        <button type="submit" name="searchbtn" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-list"></i>Tarrif List</div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sr.</th>
                                    <th>Service</th>
                                    <th>Transit Time</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($this->tarrifData) > 0){
                                    $sr = 1;
                                    foreach ($this->tarrifData as $tarrif) {
                                        $detailStr = "<table class='table table-bordered'>";
                                        $detailStr .= '<tr><th>Basic Charge</th><td>'.$tarrif->BasicCharge.' '.$tarrif->Currency.'</td></tr>';
                                        $detailStr .= '<tr><th>Fuel Charge</th><td>'.$tarrif->FuelCharge.' '.$tarrif->Currency.'</td></tr>';
                                        $detailStr .= '<tr><th>Registration Charge</th><td>'.$tarrif->RegistrationCharge.' '.$tarrif->Currency.'</td></tr>';
                                        $detailStr .= '<tr><th>Extra Charge</th><td>'.$tarrif->ExtraCharge.' '.$tarrif->Currency.'</td></tr>';
                                        $detailStr .= '<tr><th>Remote Area Charge</th><td>'.$tarrif->RemoteAreaCharge.' '.$tarrif->Currency.'</td></tr>';
                                        $detailStr .= '</table>';
                                        
                                        $totalPrice = 0;
                                        $totalPrice += floatval($tarrif->BasicCharge);
                                        $totalPrice += floatval($tarrif->FuelCharge);
                                        $totalPrice += floatval($tarrif->RegistrationCharge);
                                        $totalPrice += floatval($tarrif->ExtraCharge);
                                        $totalPrice += floatval($tarrif->RemoteAreaCharge);
                                        
                                ?>
                                <tr>
                                    <td><?php echo $sr++;?></td>
                                    <td><?php echo ucwords(strtolower(str_replace("_", " ", $tarrif->Service))) ?></td>
                                    <td><?php echo $tarrif->TransitTime ;?></td>
                                    <td data-toggle="popover" data-placement="left" data-trigger="hover" data-html="true" title="Tarrif Details" data-content="<?php echo $detailStr; ?>">
                                        <?php
                                            echo $totalPrice.' '.$tarrif->Currency;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                    }
                                }else{
                                    echo '<tr><td colspan="4">No Service Available.</td></tr>';
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
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

    public function renderHead() {
        ?>        
        <script type="text/javascript">
            $(document).ready(function() {
                $('[data-toggle="popover"]').popover();
            });
        </script>
        <?php
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {        
        if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {
            util_redirect("login.php");
        }
        $this->setTitle("Tariff List");        
        /*
         * Set tarrif defaut information
         */

        $this->shipfrom = 'GB';
        $this->shipto = 'GB';
        $this->currency = 'GBP';
        $this->weight = 1;
        $this->pieces = 1;

        /*
         * Set user given information
         */

        if (isset($_POST['searchbtn'])) {
            $this->shipfrom = $_POST['ship_from'];
            $this->shipto = $_POST['ship_to'];
            $this->currency = $_POST['currency'];
            $this->weight = $_POST['weight'];
            $this->pieces = $_POST['pieces'];

        }

        /*
         * Get basic data
         */
        $country = '';
        $country .='<select class="form-control" id="ship_from" name="ship_from">';
        $countriesList = new CountryFilter();
        $countryListData = $countriesList->getList();
        foreach ($countryListData as $countryItem) {
            $country .= '<option value="' . $countryItem->getIso() . '"' . ($countryItem->getIso() == $this->shipfrom ? ' selected="selected"' : '') . '>' . $countryItem->getName() . '</option>';
        }
        $country .='</select>';
        $countryfrom = '';
        $countryfrom .='<select class="form-control" id="ship_to" name="ship_to">';
        $countriesListFrom = new CountryFilter();
        $countryListDataFrom = $countriesListFrom->getList();
        foreach ($countryListDataFrom as $countryItem) {
            $countryfrom .= '<option value="' . $countryItem->getIso() . '"' . ($countryItem->getIso() == $this->shipto ? ' selected="selected"' : '') . '>' . $countryItem->getName() . '</option>';
        }
        $countryfrom .='</select>';
        $this->shiptoDDL = $country;
        $this->shipfromDDL = $countryfrom;
        $currencyName = '';
        $currencyName = '';
        $currencyName .='<select class="form-control" id="currency" name="currency">';
        $currencyList = new CurrencyFilter();
        $currencyListData = $currencyList->getList();
        foreach ($currencyListData as $currencyItem) {
            $currencyName .= '<option value="' . $currencyItem->getRightsymbol() . '"' . ($currencyItem->getRightsymbol() == $this->currency ? ' selected="selected"' : '') . '>' . $currencyItem->getCurrencyname() . ' [' . $currencyItem->getRightsymbol() . ']</option>';
        }
        $currencyName .='</select>';
        $this->currencyDDL = $currencyName;
        
        /*
         * Get tarrif Detail
         */
        
        $sessionManager = Sessionmanager::getUser();
        
        $user_name = $sessionManager->getUserName();
        $user_pass = $sessionManager->getUserPass();
        $user_account = $sessionManager->getUseraccount();
        
        
        $information = $user_name . "||" . $user_pass . "||" . $user_account . "||" . $this->shipfrom . "||" . $this->shipto . "||" . $this->weight . "||" . $this->pieces . "||" . $this->currency;
        $client = new SoapClient(null, array(
            'location' => "http://oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl",
            'uri' => "http://oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl"));

        $resultas = $client->__soapCall('GetTariffCodeByParams', array('information' => $information));
        $result = simplexml_load_string($resultas);
        //echo "<pre>"; print_r($result); echo "</pre>";        
        $response = $result->Response;
        $status = $response->Status;
        if ($status == 'Success') {
            $this->tarrifData = $response->Tariffs;
        }
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>