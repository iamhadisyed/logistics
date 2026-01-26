<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Product details page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	public $id		 		= NULL;
	public $action	 		= NULL;
	public $_error_array 	= array();
    public $name		    = NULL;
    public $summary_line_1  = NULL;
    public $summary_line_2  = NULL;
    public $category_id	    = NULL;
    public $description	    = NULL;
    public $image		    = NULL;
    public $path		    = NULL;
    public $data		    = array();

	/***
	 * Set the page header
	 * @return void
	 */
	public function getTitle ()
	{
		?>
		Admin - Product details - <?php echo $this->product->getName(); ?>
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
			<fieldset id="fld_times_details">
				<label>Name: </label>
				<input class="txt" id="name" name="name" type="text" value="<?php echo $this->product->getName();?>" /><br/>
				<label>Summary line 1: </label>
				<input class="txt" id="summary_line_1" name="summary_line_1" type="text" value="<?php echo $this->product->getSummaryLine1();?>" /> (80 characters)<br/>
				<label>Summary line 2: </label>
				<input class="txt" id="summary_line_2" name="summary_line_2" type="text" value="<?php echo $this->product->getSummaryLine2();?>" /> (80 characters)<br/>
				<label>Description: </label>
				<textarea name="description" id="description" cols="" rows=""><?php echo $this->product->getDescription();?></textarea><br/>
				<label>Price(&pound;): </label>
				<input class="txt" id="price" name="price" type="text" value="<?php echo $this->product->getPrice();?>" /><br/>
				<?php echo '<label>Image: </label>' .  $this->image;?><br/>
				<label>Upload Image: </label>
				<input class="txt" id="image" name="image" type="file"><br/>

				<label>&nbsp;</label>
				<input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes"><br/>
			</fieldset>
			<input name="id" type="hidden" value="<?php echo $this->product->getId();?>">
		<br clear="all"/>
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
        $this->action                           = (isset($_REQUEST['action'])                   ? strip_tags($_REQUEST['action']) : '');
        $this->id                               = (isset($_REQUEST['id'])                       ? strip_tags($_REQUEST['id']) : '');
        $this->name                             = (isset($_POST['name'])                        ? strip_tags($_POST['name']) : '');
        $this->summary_line_1                   = (isset($_POST['summary_line_1'])              ? strip_tags($_POST['summary_line_1']) : '');
        $this->summary_line_2                   = (isset($_POST['summary_line_2'])              ? strip_tags($_POST['summary_line_2']) : '');
        $this->category_id                      = (isset($_POST['category_id'])                 ? strip_tags($_POST['category_id']) : '');
        $this->description                      = (isset($_POST['description'])                 ? $_POST['description'] : '');
        $this->price	                        = (isset($_POST['price'])                 		? strip_tags($_POST['price']) : '');

		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{
		
			// get vars
			$PrdSavObj = new Product($this->id);
			$PrdSavObj->setName($this->name);
			$PrdSavObj->setSummaryLine1($this->summary_line_1);
			$PrdSavObj->setSummaryLine2($this->summary_line_2);
			$PrdSavObj->setName($this->name);
			$PrdSavObj->setCategoryId($this->category_id);
			$PrdSavObj->setDescription($this->description);
			$PrdSavObj->setPrice($this->price);
			$PrdSavObj->setActive(1);
			$PrdSavObj->setDeleted('N');
			$PrdSavObj->save();

			// make image directories if not present
			$PrdSavObj->makeDirectories();

			// upload logo small image
			$this->data = $_FILES['image'];
			
			$ImgSObj = new Image;
			$ImgSObj->setId($PrdSavObj->getId());
			$ImgSObj->setData($this->data);
			$ImgSObj->setResizeWidth($PrdSavObj->getImageSmallWidth());
			$ImgSObj->setPath($PrdSavObj->getImagePath("../"));
			$ImgSObj->upload();

			// upload logo large image
			$this->data = $_FILES['image'];

			$ImgLObj = new Image;
			$ImgLObj->setId($PrdSavObj->getId());
			$ImgLObj->setData($this->data);
			$ImgLObj->setResizeWidth($PrdSavObj->getImageLargeWidth());
			$ImgLObj->setPath($PrdSavObj->getImagePath("../", "large"));
			$ImgLObj->upload();

			// go to product list
			util_redirect("products.php");

		}

		/*------------------------------------------------------------------------------*/
		// process delete
		if ($this->action == "confirmed_delete")
		{
			// delete
			$PrdDelObj = new Product($this->id);
			$PrdDelObj->delete();

			// go to courier list
			util_redirect("products.php");
		}

		/*------------------------------------------------------------------------------*/
		$this->product = new Product($this->id);
		$this->image = $this->product->getImage("../");

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
 