<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Product category details page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    public $id                    = NULL;
    public $name                  = NULL;
    public $action                = NULL;

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        ?>
        Admin - Product category details - <?php echo $this->name;?>
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
    	$menu = new Adminmenu(Adminmenu::PRODUCTS);
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
        $this->data								= (isset($_FILES['logo']['name']) 				? $_FILES['logo'] : '');
        $this->action                           = (isset($_REQUEST['action'])                   ? strip_tags($_REQUEST['action']) : '');

        /*------------------------------------------------------------------------------*/
        // process form
        if (isset($_POST['btn_save']))
        {
            // save times and details
            $PctSavObj = new Productcategory($this->id);
            $PctSavObj->setName($this->name);
            $PctSavObj->setActive(1);
            $PctSavObj->setDeleted("N");
            $PctSavObj->save();

            // go to courier list
            util_redirect("products_categories.php");
        }

        /*------------------------------------------------------------------------------*/
        // process delete
        if ($this->action == "confirmed_delete")
        {
            // save times and details
            $PctDelObj = new Productcategory($this->id);
            $PctDelObj->delete();

            // go to courier list
            util_redirect("products_categories.php");
        }

	    /*------------------------------------------------------------------------------*/
		// get couriers
		$PctObj                                                   = new Productcategory($this->id);
		if (!isset($_POST['name']))         { $this->name         = $PctObj->getName(); }
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 