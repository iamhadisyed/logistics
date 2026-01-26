<?php

////////////////////////////////////////////////////
//
// Controller for Admin - Admin users list page
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	  /***
     * Controller logic goes here
     */
    public function init()
    {
    	$this->setTitle("Admin - Admin users");
      	$user = SessionManager::getUser();
		if ($user->getUserType() != "finance" && $user->getUserType() != "admin")
		{
			util_redirect("index.php");
		}

	    /*------------------------------------------------------------------------------*/
		// get admin users
		$AurObj           = new UserAccountFilter();
		$AurObj->AddUserTypeFilter('admin');
        $this->User = $AurObj->getColumnList(" full_name, user_name, user_account, company, user_type, deletedq  ");
		//print_r($this->User);
    }
    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        $user = SessionManager::getUser();
        ?>
        <!-- BEGIN PAGE CONTENT-->
<!--			<div class="note note-success">
				<p>	</p>
			</div>-->
           <div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-gift"></i>Admin Users
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse">
								</a>
								<a href="" class="fullscreen">
								</a>
								<a href="#portlet-config" data-toggle="modal" class="config">
								</a>
							</div>
							
						</div>
						<div class="portlet-body">
							<div class="scroller" style="min-height:200px;  data-rail-color="blue" data-handle-color="blue">
		<div class="table-scrollable">  						
                       		
     	<table class="table table-striped table-bordered table-advance table-hover">
         
			    <?php /*?><tr>
				    <td colspan="3"><a href="adminuser_details.php?id=0">Add new Administrator</a></td>
				</tr><?php */?>
			    <tr>
				    <th width="15" align="center">Action</th>
                    <th width="130">Administrator</th>
				    <th width="120" align="left">Username</th>
                    <th width="120" align="left">Account</th>
                    <th width="120" align="left">Company</th>
                    <th width="70" align="left">Type</th>
					
				</tr>
				<tbody>
              <?php

              if (count($this->User) > 0)
			  {
			      foreach ($this->User as $adminuser)
				  {
					if($adminuser->getDeletedq() == 'Y')
					{
						$isDeleted	=	'YES';
						$selectecRowStyle	=	'class="red-back "';
					}
					else 
					{
						$isDeleted	=	'NO';
						$selectecRowStyle = '';
					}
				  ?>
				  <tr <?php echo $selectecRowStyle; ?>>
                  	<td align="center">
                            <?php if ($user->getUserType() == "finance") { ?>
                            <a title="Edit" href="adminuser_details.php?id=<?php echo $adminuser->getId();?>"><span class="glyphicon glyphicon-pencil"></span></a> 
                    	<?php 
							if($isDeleted == 'NO')
							{
						?>
                        <a title="Delete" href="adminuser_details.php?id=<?php echo $adminuser->getId();?>&action=confirmed_delete" onclick="return confirm('Are you sure you want to delete this Administrator?')"><span class="glyphicon glyphicon-remove"></span></a>
                        <?php 
							}
							else
							{
						?>
                        <a title="Active" href="adminuser_details.php?id=<?php echo $adminuser->getId();?>&action=confirmed_active" onclick="return confirm('Are you sure you want to active this Administrator?')" style="color:#FFF !important;"><span class="glyphicon glyphicon-ok"></span></a>
                        <?php 		
							}
                            }				?>
                        </td>
                    <td><?php echo $adminuser->getFirstName();?></td>
                    <td align="left"><?php echo $adminuser->getUsername();?></td>
                    <td align="left"><?php echo $adminuser->getUserAccount();?></td>
                    <td align="left"><?php echo $adminuser->getCompany();?></td>
                    <td align="left"><?php echo $adminuser->getUserType();?></td>
					
                        
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
    	$menu = new Adminmenu(Adminmenu::ADMINISTRATORS);
    	$menu->render();
    }


  
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
