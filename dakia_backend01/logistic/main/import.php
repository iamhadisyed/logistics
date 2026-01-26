<?php
// get settings
require_once("../includes/settings/config.inc.php");

class Page extends BasePage {

    private $user = null;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'client_list.php' => Translation::GetCaption("SHIPMENTS"),
            Translation::GetCaption("IMPORT")
        );
        $this->user = SessionManager::getUser();
    }

    protected function renderHead() {

    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        // transfer form variables into local values (form variables come from parent)
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-upload"></i>
                    
        <?php echo Translation::GetCaption("IMPORT_SHIPMENTS_OPTIONS"); ?>
                    
                </div>
                <div class="actions">
                    <a href="client_list.php<?php echo (isset($_GET['uaccount']) ? "&uaccount=" . $_GET['uaccount'] : '') ?>" class="btn blue"><i class="fa fa-list"></i> List </a>
                    <a href="shipment_edit.php?option=new<?php echo (isset($_GET['uaccount']) ? "&uaccount=" . $_GET['uaccount'] : '') ?>" class="btn blue"><i class="fa fa-plus"></i> Add Shipment</a>
                    <a href="show_address.php" class="btn blue"><i class="fa fa-building-o"></i> Manage Address </a>
                    <a href="javascript:;" class="collapse btn btn-circle btn-icon-only btn-default hidden" data-original-title="" title=""> </a>
                    <a href="" class="btn btn-circle btn-icon-only btn-default fullscreen hidden" data-original-title="" title=""> </a>
                    <a href="#portlet-config" data-toggle="modal" class="btn btn-circle btn-icon-only btn-default hidden"><i class="icon-wrench"></i></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="scroller"  data-rail-color="blue" data-handle-color="blue" id="rpt_container">
                    <div class="row">

                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope padding50">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="img-wrapper text-center">

                                    	
                                        <a href="client_file_con.php?id=-1">
                                            <i class="fa fa-file-excel-o fa-5x" alt="Upload CSV" uib-popover="Upload CSV" popover-trigger="mouseenter"> </i>

                                        </a>
                                    </div>
                                </div>
                                <div class="panel-footer clearfix">
                                    <a href="client_file_con.php?id=-1<?php echo (isset($_GET['uaccount']) ? "&uaccount=" . $_GET['uaccount'] : '') ?>" class="btn btn-sm dark btn-outline pull-left">
                                        <i class="fa fa-file-excel-o"></i>
                                        <?php echo Translation::GetCaption("STANDARD_CSV_IMPORT"); ?></a>
                                    <a id="btnCancel" href="../csv/template_import_consignment.csv" class="btn btn-sm blue btn-outline pull-right" target="_blank" >
                                        <i class="fa fa-file"></i>
                                        <?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                    <?php
                                    if (trim($this->user->getImportDataCsv()) == 'YES') {
                                        ?>
                                        <a href="import_data_csv.php?id=-1"><img src="../images/csv.png" ></a>
                                        <a href="import_data_csv.php?id=-1" class="btn default green-stripe"><?php echo Translation::GetCaption("TRACKING_NUMBER_DATA"); ?></a>
                                        <a id="btnCancel" href="../csv/temp_import_data.csv" class="btn default green-stripe" target="_blank" ><?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!--              <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                        <div class="panel panel-default">
                                          <div class="panel-body">
                                            <div class="img-wrapper">
                                              <a href="client_file_con.php?id=-1"><img src="../images/ebay.png" class="img-responsive center-block" alt="Ebay" ></a>
                                            </div>
                                          </div>
                                          <div class="panel-footer clearfix">
                                            <a href="client_file_con.php?id=-1" class="btn btn-sm dark btn-outline pull-left">
                                              <i class="fa fa-file-excel-o"></i> 
                        <?php echo Translation::GetCaption("EBAY"); ?></a>
                                              <a id="btnCancel" href="../csv/temp_import_ebay.csv" target="_blank" class="btn btn-sm blue btn-outline pull-right">
                                               <i class="fa fa-file"></i>
        <?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                             </div>
                                           </div>
                                         </div>
                                         <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                          <div class="panel panel-default">
                                            <div class="panel-body">
                                              <div class="img-wrapper">
                                               <a href="amazon_client_file.php?id=-1"><img src="../images/amazon.png" class="img-responsive center-block" alt="Amazon" > </a> 
                                             </div>
                                           </div>
                                           <div class="panel-footer clearfix">
                                            <a href="amazon_client_file.php?id=-1" class="btn btn-sm dark btn-outline pull-left">
                                             <i class="fa fa-file-excel-o"></i>  
                        <?php echo Translation::GetCaption("AMAZON"); ?></a>
                                             <a id="btnCancel" href="../csv/temp_import_amazone.csv" class="btn btn-sm blue btn-outline pull-right" target="_blank" >
                                               <i class="fa fa-file"></i>
        <?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                             </div>
                                           </div>
                                         </div>
                                         <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                          <div class="panel panel-default">
                                            <div class="panel-body">
                                              <div class="img-wrapper">
                                               <a href="groupon_client_file.php?id=-1"><img src="../images/groupon.png" class="img-responsive center-block" alt="Groupon"  > </a>
                                             </div>
                                           </div>
                                           <div class="panel-footer clearfix">
                                            <a href="groupon_client_file.php?id=-1" class="btn btn-sm dark btn-outline pull-left">
                                             <i class="fa fa-file-excel-o"></i>  
                        <?php echo Translation::GetCaption("GROUPON"); ?></a>
                                             <a id="btnCancel" href="../csv/temp_import_groupon.csv" class="btn btn-sm blue btn-outline pull-right" target="_blank" >
                                               <i class="fa fa-file"></i>
        <?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                             </div>
                                           </div>
                                         </div>-->
                    </div>
                </div>
                <!--             <div class="row">
                               <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 carrier ng-scope">
                                <div class="panel panel-default">
                                  <div class="panel-body">
                                    <div class="img-wrapper">
                                     <a href="ctt_client_file.php?id=-1"><img src="../images/ctt_import.png"   class="img-responsive center-block" alt="Groupon"> </a>
                                   </div>
                                 </div>
                                 <div class="panel-footer clearfix">
                                 <a href="ctt_client_file.php?id=-1" class="btn btn-sm dark btn-outline pull-left"><i class="fa fa-file-excel-o"></i>   <?php echo Translation::GetCaption("CTT"); ?>
                
                                   </a>
                                   <a id="btnCancel" href="../csv/temp_import_groupon.csv" class="btn btn-sm blue btn-outline pull-right" target="_blank" >
                                    <i class="fa fa-file"></i>
        <?php echo Translation::GetCaption("TEMPLATE"); ?></a>
                                  </div>
                                </div>
                              </div>
                            </div>-->
                <div class="form_container">
                    <div style="float:center;">
                        <div class="main_grid"  style="margin:10px;">
                            <div class="dashboard">
                                <ul>
                                    <div class="clear"></div>
                                </ul>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <?php ?>
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
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::INVOICES);
        $menu->render();
    }

}

// class
/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
