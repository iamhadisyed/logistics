<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Postcodes list page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>
        <div>
        	<div id="top-button-area">
        		<a href="postcodes_details.php?id=0&postcode_group_id=<?php echo $this->postcode_group_id;?>">Add new postcode</a>
        		<a href="postcode_groups.php">Postcode Groups</a>
        	</div>

			<table class="list-table">
			    <tr>
				    <th >Postcodes</th>
					<th align="center">Edit</th>
					<th align="center">Delete</th>
				</tr>
				<tbody>
              <?php
              if (count($this->postcodes) > 0)
			  {
			      foreach ($this->postcodes as $postcode)
				  {
			  ?>
				<tr>
					<td><?php echo $postcode->getPostcode();?></td>
					<td align="center" class="clsButton"><a href="postcodes_details.php?id=<?php echo $postcode->getId();?>&postcode_group_id=<?php echo $this->postcode_group_id;?>">Edit</a></td>
					<td align="center" class="clsButton"><a href="postcodes_details.php?id=<?php echo $postcode->getId();?>&postcode_group_id=<?php echo $this->postcode_group_id;?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Postcode?')">Delete</a></td>
				</tr>
			  <?php
			      }
		      }
			  ?>
			  </tbody>
			</table>
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
        $this->postcode_group_id                = (isset($_REQUEST['postcode_group_id'])        ? strip_tags($_REQUEST['postcode_group_id']) : '');

	    /*------------------------------------------------------------------------------*/
		// get postcodes
		$PstObj          = new Postcode;
		$where			 = "postcode_group_id = " . DbAccess3::escape($this->postcode_group_id);
        $this->postcodes = $PstObj->getAnyPostcode($where);

        $postcodeGroup = new PostCodeGroup($this->postcode_group_id);
        $this->setTitle($postcodeGroup->getName());
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
