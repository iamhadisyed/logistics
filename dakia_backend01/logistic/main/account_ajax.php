<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'countryfilter.class',
    'country.class',
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    protected function init() {
        $this->user = SessionManager::getUser();
        $userFilterSubObj = new UserAccountFilter();
        $userFilterSubObj->addFieldFilter('parentid', $this->user->getUserAccountId());
        $dataSubAccount = $userFilterSubObj->getList();
        $ImediateAccount = [];
        if(!empty($dataSubAccount)){
            foreach($dataSubAccount as $data){
                $ImediateAccount[] = $data->getId();
            }
        }
        if(!empty($_GET['top']) && $_GET['top'] == 'top-countires'){
            $country = [];
            $countryObj = new CountryFilter();
            $dataCountry = $countryObj->getAccountAmountTopCountries($ImediateAccount);
            $value = [];
            if(!empty($dataCountry)){
                foreach($dataCountry as $key => $data){
                    $value[$data->getName()] = $data->getId();
                }
                arsort($value);
                $count = 0;
                foreach($value as $key => $dataCountry){
                    if($count <= '19'){
                        $country[] = array(
                            'country' => $key,
                            'value' => $dataCountry,
                            'valueWithCurrnecy'=> $dataCountry.' GBP'
                        );
                    }
                    $count++;
                }
            }
            echo json_encode($country);
            exit;
        }
        
        if(!empty($_GET['top']) && $_GET['top'] == 'top-shipment'){
            $month = date('m');
            $userFilterSubObj = new UserAccountFilter();
            $userFilterSubObj->addFieldFilter('parentid', $this->user->getUserAccountId());
            $dataSubAccount = $userFilterSubObj->getList();
            $ImediateAccount = [];
            if(!empty($dataSubAccount)){
                foreach($dataSubAccount as $data){
                    $ImediateAccount[] = $data->getId();
                }
            }
            $userAccount = implode(",",$ImediateAccount);
            $data = CountryFilter::getAccountGraph($userAccount, $month);
            if(!empty($data)){
                foreach($data as $dataGraph){
                    $graph   =  [];
                    $graph['value'] = $dataGraph->getId();  
                    $graph['country'] = $dataGraph->getName();  
                    $graph['valueWithCurrnecy'] = $dataGraph->getId().' GBP';  
                }
            }
            echo json_encode($graph);
            exit;
        }
    }
    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
       
    }

    protected function addPagelavelCss() {
     
    }

    public function addPagelavelJs() {
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead() {
        
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>