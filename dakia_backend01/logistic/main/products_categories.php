<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Product categories list page
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
        return "Admin - Product categories";
    }


    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>
        <div>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="list-table">
			    <tr>
				    <td colspan="3"><a href="products_categories_details.php?id=0">Add new product category</a></td>
				</tr>
			    <tr>
				    <th width="520">Product category</th>
					<th width="40" align="center">Edit</th>
					<th width="40" align="center">Delete</th>
				</tr>
				<tbody>
              <?php
              if (count($this->categories) > 0)
			  {
			      foreach ($this->categories as $category)
				  {
			  ?>
				<tr>
					<td><?php echo $category->getName();?></td>
					<td align="center"><a href="products_categories_details.php?id=<?php echo $category->getId();?>"><img src="../images/adminicons/application_edit.gif" /></a></td>
					<td align="center"><a href="products_categories_details.php?id=<?php echo $category->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Product category?')"><img src="../images/adminicons/delete.gif" /></a></td>
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
		// get categories
		$PctObj           = new Productcategory;
        $this->categories = $PctObj->getAnyProductcategory();
    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
 