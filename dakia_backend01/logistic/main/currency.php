<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Country list page
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
		 <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="glyphicon glyphicon-List"></i>Currency List
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller" style="min-height:400px; max-height:450px"  data-rail-color="blue" data-handle-color="blue">
                	<div class="table-scrollable">  
                    <table class="table table-striped table-bordered table-advance table-hover">
                    <thead>
                    <tr>
                        <td colspan="4" style="text-align:center;">
                         <a href="currency.php" class="btn btn-primary">All</a>
                         <a href="currency.php?status=1" class="btn btn-warning">Active Currency</a>
                         <a href="currency.php?status=0" class="btn btn-danger">InActive Currency</a>
                         <a href="currency_details.php?id=0" class="btn btn-success">Add new currency</a>
                        </td>
                    </tr>
                    <tr>
                        <th align="center"  width="15">Action</th>
                        <th width="480">Currency</th>
                        <th width="40" align="center">Rate</th>
                        <th width="40" align="center">Status</th>
                        
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                //	echo count($this->currency); exit;
                //	print_r($this->currency);exit;
                    if (count($this->currency) > 0)
                    {
                        foreach ($this->currency as $curr)
                        {
                            //echo $curr->getCurrencyId().'gjgjh'; exit;
                            ?>
                            
                            <tr class="red-back" style=" <?php echo ($curr->getIsactive() == '1')? 'color:#fff;':'';?> "> 
                            <td class="clsButton"><a title="Edit" href="currency_details.php?currency_id=<?php echo $curr->getId();?>"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp;<a href="currency_details.php?currency_id=<?php echo $curr->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Currency?')" title="Remove"><span class="glyphicon glyphicon-remove"></span></a></td>
                            <td><?php echo $curr->getCurrencyName();?></td>
                            <td><?php echo $curr->getCurrencyexchangerate();?></td>
                            <td><?php echo ($curr->getIsactive() == '1')? 'Active':'Inactive';?></td>
                            
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                    </table>
                    </div>
					</div>
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
//		 Adminmenu::CURRENCY
	 	$menu = new Adminmenu();
	 	$menu->render();
	 }


	/***
	* Controller logic goes here
	*/
	public function init()
	{
		// check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

		//
		$this->setTitle("Admin Currency");

		/*------------------------------------------------------------------------------*/
		// get countries
		$CouObj = new CurrencyFilter();
		if(isset($_REQUEST['status']) && $_REQUEST['status'] == '0')
		{
			$activeStatuse	=	false;
			$CouObj->addFieldFilter('isactive',$_REQUEST['status']);
		}
		elseif(isset($_REQUEST['status']) && $_REQUEST['status'] == '1')
		{
			$activeStatuse	=	true;
			$CouObj->addFieldFilter('isactive',$_REQUEST['status']);
		}
		else
		{
			
			$activeStatuse	=	'';
		}
		$this->currency = $CouObj->getList();
		
 	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
