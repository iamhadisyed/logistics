<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Postcode groups page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    public $id                    = NULL;
    public $name                  = NULL;

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        ?>
        Admin - Postcode groups - <?php echo $this->name;?>
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
			<p><a href="postcode_groups.php">Postcode groups</a></p>
            <form method="post">
                <fieldset id="fld_times_details">
                    <label>Name: </label>
                    <input class="txt" id="name" name="name" type="text" value="<?php echo $this->name;?>" /><br/>
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
    	$menu = new Adminmenu(Adminmenu::POSTCODES);
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
        $this->id                               = (isset($_REQUEST['id'])                       ? strip_tags($_REQUEST['id']) : '');
        $this->name                             = (isset($_POST['name'])                        ? strip_tags($_POST['name']) : '');
        $this->action                           = (isset($_REQUEST['action'])                   ? strip_tags($_REQUEST['action']) : '');

        /*------------------------------------------------------------------------------*/
        // process form
        if (isset($_POST['btn_save']))
        {
            // save
            $PgpSavObj = new Postcodegroup($this->id);
            $PgpSavObj->setName($this->name);
            $PgpSavObj->setActive(1);
            $PgpSavObj->setDeleted("N");
            $PgpSavObj->updateAdmin();

            // go to courier list
            util_redirect("postcode_groups.php");
        }

        /*------------------------------------------------------------------------------*/
        // process delete
        if ($this->action == "confirmed_delete")
        {
            // save
            $PgpDelObj = new Postcodegroup($this->id);
            $PgpDelObj->delete();

            // go to courier list
            util_redirect("postcode_groups.php");
        }

	    /*------------------------------------------------------------------------------*/
		// get couriers
		$PgpObj                                                   = new Postcodegroup($this->id);
		if (!isset($_POST['name']))         { $this->name         = $PgpObj->getName(); }
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 