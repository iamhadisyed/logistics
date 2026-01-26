<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Postcode group list page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        return "Admin - Postcode groups";
    }


    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>
        <div>
        	<div id="top-button-area">
        		<a href="postcode_groups_details.php?id=0">Add new postcode group</a>
        	</div>

			<table class="list-table">
			    <tr>
				    <th >Group</th>
				    <th align="center">Postcodes</th>
					<th align="center">Edit</th>
					<th align="center">Delete</th>
				</tr>
				<tbody>
              <?php
              if (count($this->groups) > 0)
			  {
			      foreach ($this->groups as $group)
				  {
			  ?>
				<tr>
					<td><?php echo $group->getName();?></td>
					<td align="center" class="clsButton"><a href="postcodes.php?postcode_group_id=<?php echo $group->getId();?>">Postcodes</a></td>
					<td align="center" class="clsButton"><a href="postcode_groups_details.php?id=<?php echo $group->getId();?>">Edit</a></td>
					<td align="center" class="clsButton"><a href="postcode_groups_details.php?id=<?php echo $group->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Postcode Group?')">Delete</a></td>
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
		// get postcode groups
		
		$PgpObj       = new Postcodegroup;
		//printf('------2---------'.$PgpObj);
		
        $this->groups = $PgpObj->getAnyPostcodegroup();
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 