<?php
// get settings
require_once("../includes/settings/config.inc.php");
// 
class Page extends BasePage {

    private $label_list = array();
    private $label_msg;

    /*     * *
     * Controller logic
     */

    protected function init() {
        Sessionmanager::checkUserAccess(USER::PRIVILEGE_CLIENT);

        $user = Sessionmanager::getUser();
        if ($user == NULL) {
            util_redirect("../main/index.php");
        }

        if (isset($this->form_vars["form_action"]) && ($_POST["hawb"] != "" || $_POST["awb"] != "")) {

            //set_time_limit(90);
            // Get all consignments to include in list
            $filter1 = new ConsignmentFilter();
            $filter1->addAccountFilter($user->getUserAccount());
            $filter1->AddStatusFilter(Consignment::STATUS_LABEL_CREATED);
			if($_POST["hawb"] != "")
	            $filter1->addHawbFilter(trim($_POST["hawb"]));
			if($_POST["awb"] != "")
				 $filter1->addawbFilter_bag(trim($_POST["awb"]));
				 
            $consignment_array = $filter1->getColumnList('hawb, awb, contact, company, printed_file_id');
			// Check there are labels to print.
            if (sizeof($consignment_array) == 0) {
                $this->label_msg = "No consignments to print.."; // GL_TODO: Sort out;	
            } else {
                foreach ($consignment_array as $consignment) {
                    @$label_file_id = $consignment->getPrintedFileId();
                    if (@$label_file_id > 0) {
                        $filter = new LabelFileFilter();
						$filter->addIdFilter($label_file_id);
						 $this->label_list = $filter->getColumnList('id, hawb_list, created_date, file_name, error_list');
						
                       /* if ($_POST['type'] == '1') {
                            ?>



                            <iframe id="iFramePdf" src="<? echo $label->getFullPath(); ?>" style="width: 619px; height: 482px; display:none;"></iframe>
                            <script type="text/javascript">
                                var getMyFrame = document.getElementById('iFramePdf');
                                window.onload = setTimeout("getMyFrame.contentWindow.print()", 1000);
                                document.frame1.printMe()


                            </script>
                            <?
                        } else {
                            ?>
                            <script language="javascript">
                            <?
                            echo "window.open('" . $label->getFullPath() . "','','width=600,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');";
                            ?>

                            </script>
                            <?
                        }*/
                    }
                }
            }
        }
		else
		{

        // Get list of labels
        $filter = new LabelFileFilter();
        $filter->addAccountFilter($user->getUserAccount());
        $filter->setLimit(50);
        $this->label_list = $filter->getColumnList('id, hawb_list, created_date, file_name');
		}
        //print_r($this->label_list );
        //die;
    }

    protected function renderHead() {
        ?>
        <script type="text/javascript">
		
			 $(document).ready(function(){
			
			$(".collapse").collapse('show');
			$("#btnSearch").click(function(){
   		 			$("#form_action").val("Search");
                    $("#adminForm").submit();
   			});
		});
            window.onload = function () {
//                document.getElementById("hawb").focus();
            };

           

            function popitup(url) {
                newwindow = window.open(url, 'name', 'height=200,width=300');
                if (window.focus) {
                    newwindow.focus();
                }
                return false;
            }

        </script>
       

        <?
    }

    /*     * *
     * Content
     */

    protected function renderBody() {
        ?>

        <ul class="breadcrumb">
            <li><a href="../main/index.php"><?php echo Translation::GetCaption("HOME"); ?></a></li>
            <li><a href="../main/label_list.php"><?php echo Translation::GetCaption("LABELS"); ?></a></li>
            <li><a href="#">List</a></li>
        </ul>
        <style>
            .row{
                margin-bottom:15px;
            }
        </style>
         <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-search"></i><?php echo Translation::GetCaption("SEARCH_LABELS"); ?></div>                        
                </div>
                <div class="portlet-body">
                    <div class="row">                            
                      
                        <div class="col-md-3">
                            <div class="form-group">
                                <!--<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>-->
                                <input type='text' name='hawb' id='hawb' placeholder="<?php echo Translation::GetCaption("ORDER_NUMBER"); ?>" class="form-control" maxlength="30"  onkeypress="return noSpeciatCharacter(event);" value="<?php echo @$this->form_vars["hawb"] ?>" title="<?php echo Translation::GetCaption("ORDER_NUMBER"); ?>" />
                                <!--</div>-->
                            </div>
                        </div> 
                         <div class="col-md-3">
                            <div class="form-group">
                                <!--<div class="input-group"> <span class="input-group-addon"> <i class="fa fa-tag"></i> </span>-->
                                <input type='text' name='awb' id='awb' placeholder="<?php echo Translation::GetCaption("AWB"); ?>" class="form-control" maxlength="30"  onkeypress="return noSpeciatCharacter(event);" value="<?php echo @$this->form_vars["awb"] ?>" title="<?php echo Translation::GetCaption("AWB"); ?>" />
                                <!--</div>-->
                            </div>
                        </div> 
                      
                                                  
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-left">  
                            <input type="hidden" name="form_action" id="form_action"  />
                            <a id="btnSearch"   href="#" class="btn_save btn btn-primary" ><span></span><?php echo Translation::GetCaption("SEARCH"); ?></a>
                            <a id="btnCancel" href="#" class="btn_cancel btn btn-danger"><span></span><?php echo Translation::GetCaption("CANCEL"); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"> <i class="icon-users"></i>
                    <?php echo Translation::GetCaption("LABEL_LIST"); ?><?php if ($displayname != '') print(' for ' . $displayname); ?>
                </div>
                <div class="tools"> <a href="javascript:;" class="collapse"> </a> <a href="" class="fullscreen"> </a> <a href="#portlet-config" data-toggle="modal" class="config"> </a> </div>
            </div>

            <div class="portlet-body">
        <?php
        $user = SessionManager::getUser();
        //if  ($user->getID() != "")
        //{
        //$filteruser = new UserAccountFilter();
        //$filteruser->addIdFilter($user->getID());
        //$routinguser = $filteruser->getList();
        //if (!empty($routinguser)) 
        //{
        $displayname = $user->getFirstName();
        //} 
        //else 
        //{
        //$displayname = '';	
        //}			
        //}
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $key = $val;
        }
        ?>
          
                <div style="text-align:right; padding-right:20px; margin-bottom:10px;">
                    <a href="../main/client_list.php"><?php echo Translation::GetCaption("SHIPMENT_LIST"); ?></a>
                    <label>||</label>
                    <a href="../main/search_label_list.php"><?php echo Translation::GetCaption("ADVANCE_SEARCH"); ?></a>
                </div>
               
                <div class="row">
                	<!-- <div class="col-md-9 col-sm-6 col-xs-6">
                        <div class="panel panel-default">
                            <div class="alert alert-warning" role="tab" id="headingTwo">
                              <h5 class="panel-title">
                                <a class="collapsed" style="color:#000"; data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                 <span class="glyphicon glyphicon-info-sign"></span> <?php //echo Translation::GetCaption("INFROMATION_REGARDING_BELOW_TEXT"); ?>
                                </a>
                              </h5>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo" style="margin:5px;">
                              There Are Two Options To Search And Print Labels.<br  />

							  <b>By Clicking The Button "List Of Shipments With Labels"</b> You Will Be Directed To The List Of All Your SHIPMENTS With Valid Labels. Click "View Label Details" (Column "Labels") To See And Print The Label. <br  />

							  <b>By Clicking The Button "List Of All Labels"</b> You Will Be Directed To A List Of All Your LABELS Including The Generation Date. <br />

                            </div>
                          </div>
                    </div>
                                          <div class="col-md-9">
                         	&nbsp;
                         </div>-->
					 <div class="col-md-12">
                     	 <div class="col-md-3">
                         	<a href="client_list.php?show=printed"  class="btn btn-primary"><?php echo Translation::GetCaption("LIST_OF_ALL_SHIPMENTS_TEXT"); ?></a>
                         </div>
                         <div class="col-md-3">
                         	<a href="label_list.php"  class="btn btn-primary"><?php echo Translation::GetCaption("SHOW_LIST_OF_LABELS"); ?></a>
                         </div>
                        <?  if ($user->getThemeId() == '1') { ?>
                          <div class="col-md-3">
                         	<a href="label_list_x.php"  class="btn btn-primary"><?php echo Translation::GetCaption("RELABEL"); ?></a>
                         </div>
                        <? } ?>
                     </div>

                		
                    <div class="col-md-9">

                        <div class="table-scrollable">
                            <table class="table table-striped table-bordered table-advance table-hover">
                                <thead>
                                    <tr>
                                        <th class="red-back"><?php echo Translation::GetCaption("DATE"); ?></th>
                                        <th class="red-back"><?php echo Translation::GetCaption("NUMBER_OF_SHIPMENT_IN_LABEL"); ?></th>
                                        <th class="red-back"><?php echo Translation::GetCaption("LINK"); ?></th>
                                        <th class="red-back"><?php echo Translation::GetCaption("VIEW_SHIPMENTS"); ?></th>
                                        <th class="red-back"><?php echo Translation::GetCaption("ORDER_NUMBER"); ?></th>
                                        <th class="red-back"><?php echo Translation::GetCaption("AWB"); ?> </th>

                                    </tr>
                                </thead>
                                <tbody>
        <?php
        foreach ($this->label_list as $label) {
            $hawblist = $label->getHawbList();
			$lastcharcter = substr($hawblist,-1);
            $hawbdetails = explode(',', $hawblist);
			if($lastcharcter == ",")
				$numberoflabel = sizeof($hawbdetails) - 1;
			else
				$numberoflabel = sizeof($hawbdetails);


            $consignmentStringArry = implode("','", $hawbdetails);
            $hawbstringfinal = '';
            ?>
                                        <tr>
                                            <td><?php echo Date("d M Y H:i", $label->getCreatedDate()); ?></td>
                                            <td><?php echo $numberoflabel ; ?></td>
                                            <td>
                                            <?
											 if($numberoflabel == 1)
											 {
												$filter1 = new ConsignmentFilter();
												$filter1->AddStatusFilter(Consignment::STATUS_LABEL_CREATED);
												if($hawblist != "")
													$filter1->addHawbFilter($hawblist);
													 
												$awb_list = $filter1->getColumnList('awb, single_label');
												if(count($awb_list) > 0)
												{
													$tracking_number = $awb_list[0]->getAwb();
													$single_label = $awb_list[0]->getSingleLabel();
												}
												
											 }
											  
											 $user = SessionManager::getUser();
												if($user->getFinalMileOverLabel() == "YES")
												{
													 if($numberoflabel == 1)
													 {
														$label_file = str_replace("/pdf", "/relabel", $single_label); 
													 }
													 else
													 {
														  $label_file = $label->getFullPath();
													 }	
												}
												else
												{
												   $label_file = $label->getFullPath();
												}
											?>
                                                <a href="<?php echo $label_file ?>"  class="btn btn-primary" target="_blank">View</a>
                                        <?php
                                        if (trim($label->getErrorList()) != '') {
                                            echo '<a href="#" class="btn btn-primary" onclick="return popitup(\'../main/errorlistlabel.php?id=' . $label->getId() . '\')"
	>'.Translation::GetCaption("ERRORS").'</a>';
                                        }
                                        ?>
                                            </td>
                                            <td><span title="<?php echo $hawblist ?>"><?php echo '<a  class="btn btn-primary"
	class="btn btn-primary" href="client_list.php?LabelId=' . $label->getId() . '">'.Translation::GetCaption("HOME").' #' . $label->getId() . ' Shipments</a>'; ?></span></td>
    										  <?php 
											 if($numberoflabel == 1)
											 {
												 ?>
                                            
											
    										 <td><? echo $hawblist	 ; ?></td>
                                             <td><? echo $tracking_number	 ; ?></td>
                                            
											<?
											 }
                                            ?>

                                        </tr>
                                                <?php
                                            }
                                            ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    }
    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
