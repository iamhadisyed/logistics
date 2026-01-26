<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Courier details page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    public $id                    = NULL;
    public $name                  = NULL;
    public $short_name            = NULL;
    public $action                = NULL;

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        ?>
        Admin - Courier details - <?php echo $this->name;?>
        <?
    }


    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>
        <div>
            <form method="post">
                <fieldset id="fld_times_details">
                    <label>Name: </label>
                    <input class="txt" id="name" name="name" type="text" value="<?php echo $this->name;?>" /><br/>
                    <label>Short name: </label>
                    <input class="txt" id="short_name" name="shortname" type="text" value="<?php echo $this->shortname;?>" /><br/>
                    <label>&nbsp;</label>
                    <input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes"><br/>
                </fieldset>
            </form>
		</div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }

    /***
     * Controller logic goes here
     */
    public function init()
    {
        // check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR  is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

        /*------------------------------------------------------------------------------*/
        // get vars
        $this->id                               = (isset($_REQUEST['courier_id'])               ? strip_tags($_REQUEST['courier_id']) : '');
        $this->name                             = (isset($_POST['name'])                        ? strip_tags($_POST['name']) : '');
        $this->shortname                       = (isset($_POST['shortname'])                  ? strip_tags($_POST['shortname']) : '');
        $this->data								= (isset($_FILES['logo']['name']) 				? $_FILES['logo'] : '');
        $this->action                           = (isset($_REQUEST['action'])                   ? strip_tags($_REQUEST['action']) : '');

        /*------------------------------------------------------------------------------*/
        // process form
        if (isset($_POST['btn_save']))
        {
            // save times and details
            $CorSavObj = new Courier($this->id);
            $CorSavObj->setName($this->name);
            $CorSavObj->setShortname($this->shortname);
            $CorSavObj->setIsactive(1);
            $CorSavObj->setDeleted("N");
            $CorSavObj->updateAdmin();

            // go to courier list
            util_redirect("couriers.php");
        }

        /*------------------------------------------------------------------------------*/
        // process delete
        if ($this->action == "confirmed_delete")
        {
            // save times and details
            $CorDelObj = new Courier($this->id);
            $CorDelObj->delete();

            // go to courier list
            util_redirect("couriers.php");
        }

	    /*------------------------------------------------------------------------------*/
		// get couriers
		$CorObj                                                   = new Courier($this->id);
		if (!isset($_POST['name']))         { $this->name         = $CorObj->getName(); }
		if (!isset($_POST['shortname']))   { $this->shortname   = $CorObj->getShortname(); }
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
