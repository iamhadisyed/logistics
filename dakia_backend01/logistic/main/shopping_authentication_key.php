<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $sucess_msg = "";

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        // common initialisation for ths page
        $this->setTitle("Shopping Authentication Key");


        if (isset($_POST['form_action']) && $_POST['form_action'] == "update") {
            $sessionUser = SessionManager::getUser();
            $newUserId = $sessionUser->getId();
            // Set User Shopping Platforms
            if (!empty($newUserId) && $newUserId > 0) {
                UserShoppingPlatformMapping::deleteUserShoppingPlatformMappingList($newUserId);
            }
            $shopping_plateform = $this->form_vars["shopping_plateform"];
            if (!empty($shopping_plateform)) {
                foreach ($shopping_plateform as $platform_id => $plateform) {
                    $userPlatform = new UserShoppingPlatformMapping();
                    $userPlatform->setShoppingPlatformId($platform_id);
                    $userPlatform->setUserId($sessionUser->getId());
                    $userPlatform->setAuthData(json_encode($plateform));
                    $userPlatform->save();
                }
                $this->sucess_msg = "Authentication details are successfully updated.";
            }
        }
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>

        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $sessionUser = SessionManager::getUser();
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/shopping_authentication_key.php">Shopping Authentication Key</a></li>
        </ul>
        <?php if (!empty($this->sucess_msg)) { ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo $this->sucess_msg; ?>
            </div>
        <?php } ?>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Shopping Authentication Key
                    </div>                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 ">
                            <?php
                            //GET user platforms
                            $UserShoppingPlatformMappingFilter = new UserShoppingPlatformMappingFilter();
                            $UserShoppingPlatformMappingFilter->addUserIdFilter($sessionUser->getId());
                            $UserData = $UserShoppingPlatformMappingFilter->getList();
                            if (count($UserData) > 0) {
                                foreach ($UserData as $U) {
                                    $this->selectedShoppingPlatfrom[$U->getShoppingPlatformId()] = $U->getAuthData();
                                }
                            }
                            //GET Allowed shopping platform
                            // GET All Shopping Platform List
                            $ShoppingPlatformFilter = new ShoppingPlatformFilter();
                            $ShoppingData = $ShoppingPlatformFilter->getAllowedShoppingAndAuthenticateData($sessionUser->getId());
                            if (!empty($ShoppingData)) {
                                $PlatformArray = array();
                                foreach ($ShoppingData as $Shopping) {
                                    $PlatformArray[$Shopping->getId()][] = array("PlatformId" => $Shopping->getId(), "PlatformTitle" => $Shopping->getTitle(), "AuthenticateId" => $Shopping->getDescription(), "AuthenticateTitle" => $Shopping->getIsActive(), "AuthenticateValue" => $Shopping->getAddedBy());
                                }
                                $count = 1;
                                foreach ($PlatformArray as $ShoppingArray) {
                                    ?>

                                    <div class="panel-group accordion" id="accordion<?php echo $count; ?>">
                                        <div class="panel panel-default">

                                            <div class="panel-heading" <?php
                                            $data_key = (array) json_decode($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']]);
                                            $style = "";
                                            foreach ($data_key as $value) {
                                                if (empty($value)) {
                                                    $style = 'style="background-color: #DD2323 !important;color: #FFFFFF !important;"';
                                                    break;
                                                }
                                            }
                                            echo $style;
                                            ?> >
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion1" href="#collapse_<?php echo $count; ?>" aria-expanded="false">
                                                        <?php echo $ShoppingArray[0]['PlatformTitle']; ?> </a>
                                                </h4>
                                            </div>
                                            <div id="collapse_<?php echo $count; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <?php
                                                        $authDataArray = array();
                                                        if (isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) {
                                                            $auth_data = $this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']];
                                                            $authDataArray = (array) json_decode($auth_data);
                                                        }
                                                        foreach ($ShoppingArray as $value) {
                                                            $tmpVal = $value['AuthenticateValue'];
                                                            $name_index = "shopping_plateform[" . $value['PlatformId'] . "][" . $tmpVal . "]";
                                                            $auth_value = isset($authDataArray[$tmpVal]) ? $authDataArray[$tmpVal] : '';
                                                            ?>
                                                            <div class="col-md-3">
                                                                <label><?php echo $value['AuthenticateTitle']; ?></label> 
                                                                <input name="<?php echo $name_index; ?>" type="text" value="<?php echo $auth_value; ?>" placeholder="<?php echo $value['AuthenticateTitle']; ?>" class="form-control shopping_plateform_input shopping_plateform_<?php echo $value['PlatformId']; ?>" <?php echo ((!isset($this->selectedShoppingPlatfrom[$ShoppingArray[0]['PlatformId']])) ? 'disabled="true"' : ''); ?> />
                                                            </div>  
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $count++;
                                }
                            } else {
                                echo "<b>No data found!</b>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    if (!empty($ShoppingData)) {
                        ?>
                        <div class="row" style="">  
                            <div class="col-sm-3">
                                <input type="hidden" name="form_action" id="form_action" value="update"  />
                                <input type="submit" name="save" id="save" value="Save" class="btn btn-primary btn_save"/>
                            </div>
                        </div>
                    <?php } ?>
                </div>

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
