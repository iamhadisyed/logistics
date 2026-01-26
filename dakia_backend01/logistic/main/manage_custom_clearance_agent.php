<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = null;
    private $agentList = [];
    private $oauthId = 0;
    private $resMessage = 0;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Custom Clearance Agent"
        );
        if (isset($_GET['id']) && $_GET['id'] > 0)
            $this->oauthId = $_GET['id'];

        $this->user = SessionManager::getUser();
        if (isset($this->form_vars['form_action']) && $this->form_vars['form_action'] == "save") {
            $id = "";
            if (isset($this->form_vars['id']) && $this->form_vars['id'] > 0)
                $id = $this->form_vars['id'];
            
            if (isset($this->form_vars['agent_id']) && $this->form_vars['agent_id'] > 0)
                $agentId = $this->form_vars['agent_id'];
            
            foreach ($this->form_vars['oauth_field'] as $index => $oauthField) {
                $customClearanceAgentOauth = new CustomClearanceAgentOauth($id);
                $customClearanceAgentOauth->setAgentId($agentId);
                $customClearanceAgentOauth->setOauthField($oauthField);
                $customClearanceAgentOauth->setOauthFieldLabel($this->form_vars['oauth_field_label'][$index]);
                $customClearanceAgentOauth->setAddedBy($this->user->getId());
                $customClearanceAgentOauth->setAddedDate(time());
                $customClearanceAgentOauth->save();
            }
            $this->resMessage = "Agent oath field added successfully";
//            if ($customClearanceAgent->getId() > 0) {
//                $this->resMessage = "Agent oath field added successfully";
//            } else {
//                $this->resMessage = "Agent oath field added successfully";
//            }
        }
        if ($this->oauthId > 0) {
            $customClearanceAgentOauthFilter = new CustomClearanceAgentOauthFilter();
            $customClearanceAgentOauthFilter->addFieldFilter("id", $this->oauthId);
            $this->agentList = $customClearanceAgentOauthFilter->getList("");
        }
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>


        <script type="text/javascript">
            $(document).ready(function () {
                $("#btnSave").click(function(){
                    $("#adminForm").submit();
                });
            });
            $(".repeater-add").click(function () {
                var index_of_agent = $(".show_remove_btn").map(function () {
                    return $(this).data('index-of-agent');
                }).get();//get all data values in an array
                var highest_index_of_agent = Math.max.apply(Math, index_of_agent);//find the highest value from them
                highest_index_of_agent = parseInt(highest_index_of_agent) + 1;
                $(".clone_div").children().clone().appendTo(".append_here");
                $('.append_here .row').last().attr('data-index-agent', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').last().attr('data-index-of-agent', highest_index_of_agent);
                $('.append_here .row .show_remove_btn').show();
                setInputFeilds(highest_index_of_agent);
            });
            $(document).on('click', '.show_remove_btn', function () {
                var current_index = $(this).data('index-of-agent');
                $('.append_here [data-index-agent="' + current_index + '"]').remove();
            });
            function setInputFeilds(id) {
                $('.append_here [data-index-agent="' + id + '"] .agent_select').next().remove();
                $('.append_here [data-index-agent="' + id + '"] :text').val("");
                $('.append_here [data-index-agent="' + id + '"] .agent_select').attr("name", 'agent_id[' + id + ']');
                $('.append_here [data-index-agent="' + id + '"] :text').first().attr("name", 'oauth_field_label[' + id + ']');
                $('.append_here [data-index-agent="' + id + '"] :text').last().attr("name", 'oauth_field[' + id + ']');
                var select2Parentid = $('.append_here [data-index-agent="' + id + '"] .agent_select').select2();
                select2Parentid.val("").trigger('change');
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <form name="adminForm" id="adminForm" action="" method="POST">        
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-dropbox"></i>
                        Add/Update Custom Clearance Agent
                    </div>
                    <div class="actions">
                        <a href="javascript:;" class="btn btn-info repeater-add">
                            <i class="fa fa-plus"></i> Add Agent Oath
                        </a>
                    </div>
                    <div class="tools"> </div>
                </div>
                <div class="portlet-body">
                    <div class="row display-none">
                        <div class="col-md-12">
                            <div class="alert alert-success" id="res_message"></div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if(!empty($this->resMessage)){ ?>
                            <div class="col-md-12">
                                <div class="alert alert-success"> <?php echo $this->resMessage; ?></div>
                            </div>
                        <?php   } ?>
                    </div>
                    <?php
                    if (!empty($this->agentList)) {
                        $inc = 0;
                        foreach ($this->agentList as $agentList) {
                            if ($inc == 0) {
                                echo '<div class="clone_div">';
                            }
                            ?>                        
                            <div class="row">
<!--                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Agent</label>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <?php
                                            //$agentName = "agent_id[" . $inc . "]";
                                            //$selected_value = $agentList->getAgentId();
                                           // echo Ddl::generateDDL('agent_id', 'CustomClearanceAgentFilter', '  added_by>0 ', 'agent_name', 'id', $selected_value, ' class="form-control select2 agent_select" rel="tooltip" ', "Please Select Agent", '', 'agent_id', '');
                                            ?>
                                            <span class="input-group-addon red-18">*</span> 
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Oath Field Label</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <input type="text" name="oauth_field_label[<?php echo $inc; ?>]" id="oauth_field_label" required="required" title="Oauth Field Label" value="<?php echo $agentList->getOauthFieldLabel(); ?>" class="form-control" placeholder='Oauth Field Label' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Oath Field</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <input type="text" name="oauth_field[<?php echo $inc; ?>]" id="oauth_field" required="required" title="Oauth Field" value="<?php echo $agentList->getOauthField(); ?>" class="form-control" placeholder='Oauth Field' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 show_remove_btn" data-index-of-agent='<?php echo $inc; ?>' <?php if ($inc == 0) { ?> style="display: none;" <?php } ?> >
                                    <label class="control-label">&nbsp;</label><br/>
                                    <a href="javascript:;"  class="btn btn-danger repeater-delete">
                                        <i class="fa fa-close"></i>
                                    </a>
                                </div>
                            </div>
                            <?php
                            if ($inc == 0) {
                                echo '</div><div class="append_here">';
                            }
                            $inc++;
                        }
                        echo '</div>';
                    } else {
                        ?>
                        <div class="clone_div">
                            <div class="row">
<!--                                <div class="col-sm-3">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Agent Name</label>
            <?php $agent = "";
           // echo Ddl::generateDDL('agent_id[]', 'CustomClearanceAgentFilter', '  added_by>0 ', 'agent_name', 'id', $agent, ' class="form-control select2 agent_select" rel="tooltip" ', "Please Select Agent", '', 'agent_id', ''); ?>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Oath Field Label</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <input type="text" name="oauth_field_label[]" id="oauth_field_label" required="required" title="Oauth Field Label" value="" class="form-control" placeholder='Oauth Field Label' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="first_form_col">
                                        <div class="form-group">
                                            <label>Oath Field</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                                <input type="text" name="oauth_field[]" id="oauth_field" required="required" title="Oauth Field" value="" class="form-control" placeholder='Oauth Field' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 show_remove_btn" data-index-of-agent='0' style="display: none;">
                                    <label>&nbsp;</label><br/>
                                    <div class="input-group input-group-sm">
                                        <a href="javascript:;"  class="btn btn-danger repeater-delete">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="append_here"></div>
        <?php } ?>
                    <hr>     
                    <div class="row">  
                        <div class="col-md-12 text-center">  
                            <a id="btnSave"   href="javascript:;" class="btn btn-primary"><span></span>Save</a>               
                            <a href="custom_clearance_agent.php" id="btnCancel" class="btn_cancel btn btn btn-default"><span></span>Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo @$this->form_vars["id"]; ?>" />
            <input type="hidden" name="agent_id" id="agent_id" value="<?php echo @$_GET['agent_id']; ?>" />
            <input type="hidden" name="form_action" id="form_action" value="save" />
        </form>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>