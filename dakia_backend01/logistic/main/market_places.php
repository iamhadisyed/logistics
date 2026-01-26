<?php
// get settings 
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $title = null;
    public $description = null;
    public $error_message = null;
    public $market_places_data = null;
    public $market_places_authenticate_data = null;
    public $success_message = null;

    protected function init() {
        // common initialisation for ths page
        $this->setTitle("Market Places");
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
        $sessionUser = SessionManager::getUser();
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == "save" && isset($this->form_vars['title']) && $this->form_vars['title'] != "" && isset($this->form_vars['description']) && $this->form_vars['description'] != "") {
            $is_active = "0";
            $title = "";
            $description = "";
            $PageLink = "";
            $TranslationKey = "";
            $title = $this->form_vars['title'];
            $description = $this->form_vars['description'];
            $PageLink = $this->form_vars['page_link'];
            $title = $this->form_vars['title'];
            $TranslationKey = $this->form_vars['translation_key'];
            if (isset($this->form_vars['is_active'])) {
                $is_active = "1";
            }
            if (isset($this->form_vars["id"]) && $this->form_vars["id"] > 0) {
                $MarketPlaces = new MarketPlaces($this->form_vars["id"]);
                $MarketPlaces->setUpdatedBy($sessionUser->getId());
                $MarketPlaces->setUpdatedDate(time("Y-m-d H:i:s"));
                $MarketPlacesAuthenticateFieldFilter = new MarketPlacesAuthenticateFieldFilter();
                $MarketPlacesAuthenticateFieldFilter->DeleteRecordByMarketPlacesformId($this->form_vars['id']);
                $this->success_message = "Your record is updated successfully";
            } else {
                $MarketPlaces = new MarketPlaces();
                $MarketPlaces->setAddedBy($sessionUser->getId());
                $MarketPlaces->setAddedDate(time("Y-m-d H:i:s"));
                $MarketPlaces->setIsDelete("0");
            }
            $MarketPlaces->setTitle($title);
            $MarketPlaces->setDescription($description);
            $MarketPlaces->setPageLink($PageLink);
            $MarketPlaces->setIsActive($is_active);
            $MarketPlaces->setTranslationKey($TranslationKey);
            $MarketPlaces->setPluginKey(str_replace(" ", "", $title));
            $MarketPlaces->save();
            $LastId = $MarketPlaces->getId();
            if ($LastId > 0) {
                $fieldname = $this->form_vars['fieldname'];

                $fieldvalue = $this->form_vars['fieldvalue'];
                $autoGenerateValue = $this->form_vars['autoGenerateValue'];
                $count = 0;
                foreach ($fieldname as $value) {
                    if (!empty($value)) {
                        $MarketPlacesAuthenticateField = new MarketPlacesAuthenticateField();
                        $MarketPlacesAuthenticateField->setFieldName(strip_tags($value));
                        $MarketPlacesAuthenticateField->setFieldValue(strip_tags($fieldvalue[$count]));
                        if (in_array($count, $autoGenerateValue))
                            $MarketPlacesAuthenticateField->setAutoGenerateValue(1);
                        else
                            $MarketPlacesAuthenticateField->setAutoGenerateValue(0);
                        $MarketPlacesAuthenticateField->setMarketPlacesId($LastId);
                        $MarketPlacesAuthenticateField->setAddedBy($sessionUser->getId());
                        $MarketPlacesAuthenticateField->setAddedDate(time("Y-m-d H:i:s"));
                        $MarketPlacesAuthenticateField->setIsDelete('0');
                        $MarketPlacesAuthenticateField->save();
                        $count++;
                    }
                }
            }
            if (!isset($this->form_vars['id'])) {
                util_redirect("market_places_list.php?new=added");
            }
        }
        if (isset($_GET['id']) && $_GET['id'] != "" && isset($_GET['action'])) {
            $action = "";
            $md5Id = "";
            $action = $_GET['action'];
            $md5Id = $_GET['id'];
            $MarketPlacesFilter = new MarketPlacesFilter();
            $MarketPlacesFilter->addMd5IdByFilter($md5Id);
            $data = $MarketPlacesFilter->getList();
            if (!empty($data) && $action == "Edit") {
                $this->market_places_data = $data[0];
                $MarketPlacesAuthenticateFieldFilter = new MarketPlacesAuthenticateFieldFilter();
                $MarketPlacesAuthenticateFieldFilter->addMarketPlacesIdMd5Filter($md5Id);
                $MarketPlacesAuthenticateFieldFilter->addIsNotDeletedFilter();
                $this->market_places_authenticate_data = $MarketPlacesAuthenticateFieldFilter->getList();
            } else {
                $this->error_message = "invalid command";
            }
        }
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        ?>
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () {
                var auto_id = 0;
                $('input').tooltip();
                $('checkbox').tooltip();
                $("#add_btn").click(function () {
                    auto_id = auto_id + parseInt(1);
                    var clone_div = "";
                    clone_div = $("#clone_me").html();
                    clone_div = clone_div.replace("###", auto_id);
                    //alert(clone_div);
                    $("#add_here").append(clone_div);
                    $("#add_here").find(".auto-generate-value").uniform();


                });
                $(document).on("click", ".remove_me", function () {
                    if (window.confirm("Are you sure you want to delete?")) {
                        $(this).closest("div .row").remove();
                    }
                });
            });
        </script> 
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        $market_places_data = "";
        if (isset($this->market_places_data) && !empty($this->market_places_data)) {
            $market_places_data = $this->market_places_data;
        }
        ?>
        <ul class="breadcrumb">
            <li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/market_places.php">Market Places</a></li>
        </ul>
        <?php if (!empty($this->error_message)) { ?>
            <div class="alert alert-danger">
                <strong>Warning!</strong> <?php echo $this->error_message; ?>
            </div>
        <?php } else if (!empty($this->success_message)) { ?>
            <div class="alert alert-success">
                <strong>Success!</strong> <?php echo $this->success_message; ?>
            </div>
        <?php } ?>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-list"></i>
                        Add Market Places
                    </div>                    
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tags"></i> </span>
                                <input required="required" type="text" name="title" id="title" value="<?php echo (is_object($market_places_data) ? $market_places_data->getTitle() : ""); ?>" oninvalid="this.setCustomValidity('Please enter title')" oninput="setCustomValidity('')" style=""  class="form-control" title="Title" rel="tooltip" placeholder="Title"/>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <input required="required" type="text" name="description" id="description" value="<?php echo (is_object($market_places_data) ? $market_places_data->getDescription() : ""); ?>" oninvalid="this.setCustomValidity('Please enter Description')" oninput="setCustomValidity('')" style=""  class="form-control" title="Description" rel="tooltip" placeholder="Description"/>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <input required="required" type="text" name="page_link" id="page_link" value="<?php echo (is_object($market_places_data) ? $market_places_data->getPageLink() : ""); ?>" oninvalid="this.setCustomValidity('Please enter page link')" oninput="setCustomValidity('')" style=""  class="form-control" title="Page Link" rel="tooltip" placeholder="Page Link"/>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                <input required="required" type="text" name="translation_key" id="translation_key" value="<?php echo (is_object($market_places_data) ? $market_places_data->getTranslationKey() : ""); ?>" oninvalid="this.setCustomValidity('Please enter translation key')" oninput="setCustomValidity('')" style=""  class="form-control" title="Translation Key" rel="tooltip" placeholder="Translation Key"/>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (!empty($this->market_places_authenticate_data)) {
                        $count = 0;
                        foreach ($this->market_places_authenticate_data as $market_places_authenticate_data) {
                            ?>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                        <input  type="text" name="fieldname[]" class="form-control" title="Authenticate Field Name" rel="tooltip"  placeholder="Authenticate Field Name" value="<?php echo $market_places_authenticate_data->getFieldName(); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                        <input  type="text" name="fieldvalue[]" class="form-control" title="Authenticate Field Value" rel="tooltip"  placeholder="Authenticate Field Value" value="<?php echo $market_places_authenticate_data->getFieldValue(); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <div class="input-group"> 
                                        <?
                                        $checked =	$market_places_authenticate_data->getAutoGenerateValue() == 1 ? 'checked' : '';
                                        ?>
                                        <input  type="checkbox" name="autoGenerateValue[]" class="form-control auto-generate-value" <?= $checked ?> id="auto_generate_0"  value="0"/>
                                        <span>AUTO GENERATE VALUE</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <?php if ($count == 0) { ?>
                                        <a href="javascript:;" id="add_btn" class="btn btn-circle green btn-sm"><i class="fa fa-plus"></i> Add </a>
                                    <?php } else { ?>
                                        <a href="javascript:;" class="remove_me btn btn-circle red-sunglo btn-sm"><i class="fa fa-remove"></i> Remove </a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                            $count++;
                        }
                    } else {
                        ?>
                        <div class="row">
                            <div class="form-group col-md-3">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input  type="text" name="fieldname[]" class="form-control" title="Authenticate Field Name" rel="tooltip"  placeholder="Authenticate Field Name" />
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                                    <input  type="text" name="fieldvalue[]" class="form-control" title="Authenticate Field Value" rel="tooltip"  placeholder="Authenticate Field Value" />
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group"> 
                                    <input  type="checkbox" name="autoGenerateValue[]" class="form-control auto-generate-value" value="0" />
                                    <span>AUTO GENERATE VALUE</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a href="javascript:;" id="add_btn" class="btn btn-circle green btn-sm"><i class="fa fa-plus"></i> Add </a>
                            </div>
                        </div>        
        <?php } ?>
                    <div id="add_here"></div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Active</label>
                            <div class="md-checkbox-inline" align="center" style="width: 20%;float: left;">
                                <div class="md-checkbox">
                                    <input <?php
                                    if (is_object($market_places_data) && $market_places_data->getIsActive() == "1") {
                                        echo 'checked="checked"';
                                    }
                                    ?>   type="checkbox" id="is_active" name="is_active" class="md-check">
                                    <label for="is_active"><span></span><span class="check"></span> <span style="border: 2px solid #e5e5e5 !important;" class="box"></span> </label>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="row" style="">  
                        <div class="col-sm-3">
                            <br/><br/>
                            <input type="submit" name="save" id="save" value="Save" class="btn btn-primary btn_save"/>
                            <?php if (is_object($market_places_data)) { ?>
                                <input type="hidden" name="id"  value="<?php echo $market_places_data->getId(); ?>" />
        <?php } ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="form_action" id="form_action" value="<?php echo "save"; ?>"  />
            </div>  <!-- table_container -->
        </div>
        <?php
    }

    public function renderFooter() {
        ?>
        <div id="clone_me" style="display: none;">
            <div class="row">
                <div class="form-group col-md-3">
                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                        <input  type="text" name="fieldname[]" class="form-control" title="Authenticate Field Name" rel="tooltip" placeholder="Authenticate Field Name"/>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-ticket"></i> </span>
                        <input  type="text" name="fieldvalue[]" class="form-control" title="Authenticate Field Value" rel="tooltip" placeholder="Authenticate Field Value"/>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label><input  type="checkbox" name="autoGenerateValue[]"  class="form-control auto-generate-value" value="###" />
                        AUTO GENERATE VALUE</label>
                </div>
                <div class="col-md-3">
                    <a href="javascript:;"  class="remove_me btn btn-circle red-sunglo btn-sm"><i class="fa fa-remove"></i> Remove </a>
                </div>
            </div> 
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
