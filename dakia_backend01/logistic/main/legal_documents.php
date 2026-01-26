<?php
// get settings
require_once("../includes/settings/config.inc.php");
@session_start;

class Page extends BasePage {

    protected function init() {


        // user must be CLIENT
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        $user = Sessionmanager::getUser();

        if ($user == NULL) {
            util_redirect("../main/index.php");
        }

        //	t_on(); // turn on trace for this page
    }

    /**
     * Force page refresh if importing
     */
    /*     * *
     * Content View
     */
    protected function renderBody() {
        ?>





        <br><br>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i>
        <?php echo Translation::GetCaption("LEGAL_DOCUMENTS"); ?></div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
        <?php errorList::getItem()->render(); ?>
                    <div class="row">
                        <div class="form-group col-md-6">

                        </div>	
                    </div>
                </div>
                <div style="clear:both;"></div>

            </div>
        </div>


        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />





        <?php
        // report any errors
        // has file been chosen yet?		
    }

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
