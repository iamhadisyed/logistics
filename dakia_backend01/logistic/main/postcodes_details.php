<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Postcode detail page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    public $id                    = NULL;
    public $postcode_group_id     = NULL;
    public $postcode			  = NULL;

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        ?>
        Admin - Postcodes - <?php echo $this->postcode;?>
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
			<p><a href="postcodes.php?postcode_group_id=<?php echo $this->postcode_group_id;?>">Postcodes</a></p>
            <form method="post">
                <fieldset id="fld_times_details">
                    <label>Postcode: </label>
                    <input class="txt" id="postcode" name="postcode" type="text" value="<?php echo $this->postcode;?>" /><br/>
                    <label>&nbsp;</label>
                    <input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes"><br/>
                </fieldset>
				<input name="postcode_group_id" type="hidden" value="<?php echo $this->postcode_group_id;?>">
				<input name="id" type="hidden" value="<?php echo $this->id;?>">
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
        $this->postcode_group_id                = (isset($_REQUEST['postcode_group_id'])        ? strip_tags($_REQUEST['postcode_group_id']) : '');
        $this->postcode                         = (isset($_POST['postcode'])                    ? strip_tags($_POST['postcode']) : '');
        $this->action                           = (isset($_REQUEST['action'])                   ? strip_tags($_REQUEST['action']) : '');

        /*------------------------------------------------------------------------------*/
        // process form
        if (isset($_POST['btn_save']))
        {
            // save
            $PstSavObj = new Postcode($this->id);
            $PstSavObj->setPostcodeGroupId($this->postcode_group_id);
            $PstSavObj->setPostcode($this->postcode);
            $PstSavObj->setActive(1);
            $PstSavObj->setDeleted("N");
            $PstSavObj->updateAdmin();

            // go to list
            util_redirect("postcodes.php?postcode_group_id=" . $this->postcode_group_id);
        }

        /*------------------------------------------------------------------------------*/
        // process delete
        if ($this->action == "confirmed_delete")
        {
            // save
            $PstDelObj = new Postcode($this->id);
            $PstDelObj->delete();

            // go to courier list
            util_redirect("postcodes.php?postcode_group_id=" . $this->postcode_group_id);
        }

	    /*------------------------------------------------------------------------------*/
		// get couriers
		$PstObj                                                            			   = new Postcode($this->id);
		if (!isset($_POST['postcode']))               { $this->postcode       		   = $PstObj->getPostcode(); }
		if (!isset($_REQUEST['postcode_group_id']))   { $this->postcode_group_id       = $PstObj->getPostcodeGroupId(); }
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 