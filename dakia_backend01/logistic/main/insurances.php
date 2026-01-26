<?php

////////////////////////////////////////////////////
//
// Insurances
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $cover_amount 		= 0;
	private $premium_amount		= 0;
	private $insruance_array	= array();


	/***
	 * Set the page header
	 * @return void
	 */
	public function getTitle ()
	{
        return "Admin - Insurances";
	}

	public function writeJavaScript() {
		?>
		<script language="JavaScript">

		$(document).ready(function(){

			$("#tbl_insurances tr:last input#tbl_premium_amount").focus(function(){
			    addTableRow("#tbl_insurances");
			});

			function addTableRow(table)
			{
				$(table).append($(table + ' tr:last').clone());
				$(table + ' tr:last #tbl_cover_amount').val('')
				$(table + ' tr:last #tbl_premium_amount').val('')

				$("#tbl_insurances tr:last input#tbl_premium_amount").focus(function(){
					addTableRow("#tbl_insurances");
				});

				return true;
			}

		});


		</script>
		<?php
	}

	/***
	 * This page's content
	 * @return void
	 */
	public function renderBody()
	{
		?>
		<div>
			<div style="clear:both">

			<br/>
			<table id="tbl_insurances">
				<tr class="textb" id="tbl_header2">
					<th width="150">Cover</th>
					<th width="150">Premium</th>
				</tr>

		<?php
		if (count($this->insurances) > 0)
		{
			foreach ($this->insurances as $insurance)
			{
		?>
				<tr class="textb">
					<td><input class="txt" id="tbl_cover_amount" style="width: 150px;" name="cover_amount[]" type="text" value="<?php echo $insurance->getCoverAmount();?>"></td>
					<td><input class="txt" id="tbl_premium_amount" style="width: 150px;" name="premium_amount[]" type="text" value="<?php echo $insurance->getPremiumAmount();?>"></td>
				</tr>
		<?php
			}
		?>
				<tr class="textb">
					<td><input class="txt" id="tbl_cover_amount" style="width: 150px;" name="cover_amount[]" type="text" value=""></td>
					<td><input class="txt" id="tbl_premium_amount" style="width: 150px;" name="premium_amount[]" type="text" value=""></td>
				</tr>
		<?php
		}
		else
		{
		?>
				<tr class="textb">
					<td><input class="txt" id="tbl_cover_amount" style="width: 150px;" name="cover_amount[]" type="text" value=""></td>
					<td><input class="txt" id="tbl_premium_amount" style="width: 150px;" name="premium_amount[]" type="text" value=""></td>
				</tr>
		<?php
		}
		?>


			</table>

			<input type="hidden" name="action" id="action" value="save" />

			<input class="submit" id="btn_save" name="btn_save" type="submit" value="Save changes" ><br/>
			</div>
		</div>
		<?php
	}

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::INSURANCES);
    	$menu->render();
    }

	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		/*------------------------------------------------------------------------------*/
		// get vars
		$this->cover_amount                    			= (isset($_POST['cover_amount'])                    		? $_POST['cover_amount'] : array());
		$this->premium_amount                  			= (isset($_POST['premium_amount'])                    		? $_POST['premium_amount'] : array());

		/*------------------------------------------------------------------------------*/
		// process form
		if (isset($_POST['btn_save']))
		{

			// delete all insurances
			$InsDelObj = new Insurance;
			$InsDelObj->truncate();

            // create tariffs if valid
			for ($key = 0; $key <= count($this->cover_amount) - 1; $key++)
			{
				$InsSavObj = new Insurance;
				$InsSavObj->setCoverAmount($this->cover_amount[$key]);
				$InsSavObj->setPremiumAmount($this->premium_amount[$key]);
				$InsSavObj->setRowOrder(0);
				$InsSavObj->setActive(1);
				$InsSavObj->setDeleted('N');

				if ($InsSavObj->isValid())
				{
					$InsSavObj->save();
				}

			}
		}

		/*------------------------------------------------------------------------------*/
		// get insurance for redisplay
		$InsObj           = new Insurance;
		$this->insurances = $InsObj->getAnyInsurance("", true, " premium_amount");

	}

}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
