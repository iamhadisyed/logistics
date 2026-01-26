<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {

private $id;
private $name;
/* * *
 * Set the page header
 * @return void
 */

public function getTitle() 
{
    return "Admin - Index";
}

/* * *
 * This page's content
 * @return void
 */

public function renderBody() {
?>			

<!-- END STYLE CUSTOMIZER -->
<!-- BEGIN PAGE HEADER-->

<div class="row">
    <div class="col-md-12">
        <!--<h3 class="page-title">Create Barcode</h3>-->
    </div>
    <!-- <div class="col-md-4"> <a href="reporting_services_fullscreen.php" target="_blank" class="btn btn-success pull-right">Show Full Screen</a> </div>-->
</div>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption"> <i class="glyphicon glyphicon-search"></i>Add Warehouse Location</div>

        <div class="tools"> <a href="javascript:;" class="collapse"> </a> 
            <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> 
        </div>
    </div>

    <div class="portlet-body" style="padding-top:50px">
        <div class="scroller" data-rail-color="blue" data-handle-color="blue">                
<?php
if ($this->error_msg != "")
{
?>
            <div style="padding-left:750px" class="alert alert-danger">
                <p><strong>Error! </strong>	<?	echo $this->error_msg;	 ?>	 </p>
            </div>

            <?  
            }
            ?>

            <form method="post">    
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="font-size:25px;color:#000065; padding-left:395px; width:345">Location Name:</label> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-thumb-tack"></i> </span>
                                <input style="font-size:25px;height:50px;width:311px" maxlength="500" class="form-control" id="name" name="name" placeholder="Location name" type="text" value="<? echo $this->name ?>">
                            </div>
                        </div>
                    </div>    
                </div>      
                <div class="row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <input type="hidden" name="form_action" id="form_action"  />

                        <button type="submit" class="btn btn-primary" style="width:350px; height:50px"  class="btn btn-primary" id="btnSaveLoc" name="btnSaveLoc" value="Save">
                            Save changes
                        </button>                     



                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />
                        <br />

                    </div>
                </div>

        </div>
        </form>
    </div>
    <!--
<div class="portlet box blue">
<div class="portlet-title">
<div class="caption"> <i class="icon-users"></i>Result </div>
<div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
</div>
<div class="portlet-body">
<div class="scroller" style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
    

      

</div>
</div>
</div>
</div>
    -->
    <!-- END PAGE CONTENT-->
<?php
}
}

/**
 * Override to show the menu
 *
 */
public function renderMenu() {
$menu = new Adminmenu(Adminmenu::DASHBOARD);
$menu->render();
}

public function renderHead()
{



}



/* * *
 * Controller logic goes here
 */

private function validate_form()
{
$validate = false;

if (trim($this->name) == "")
{
if ($this->error_msg != "") $this->error_msg .= "<br />";
$this->error_msg .= "Location name cannot be blank.";
}
else
{
$validate = true;
}

return $validate;
}

public function init()
{
$user = Sessionmanager::getUser();

if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");
}

$this->id = (isset($_REQUEST['id']) ? strip_tags($_REQUEST['id']) : '');
$this->name = (isset($_POST['name']) ? strip_tags($_POST['name']) : '');




if (isset($_POST['btnSaveLoc']))
{

if ($this->validate_form())
{
$locObj = new Location($this->id);
$locObj->setName($this->name);

if($this->id == 0)
{
$locObj->setDateCreated(date("Y-m-d G:i:s"));
$locObj->setCreatedBy($user->getId());
$locObj->setWarehouseId($user->getWarehouseId());
$locObj->setActive("Y");
$locObj->setType("L");
}
else
{
$locObj->setDateUpdated(date("Y-m-d G:i:s"));
$locObj->setUpdatedBy($user->getId());
$locObj->setWarehouseId($user->getWarehouseId());
$locObj->setType("L");
}
//echo "<pre>";


$locObj->save();
util_redirect("viewlocations.php");
}
}
else
{
$locObj = new Location($this->id);
$this->name = $locObj->getName();
}

//$user = SessionManager::getUser();


}

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
