<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'agentdocument.class',
    'agentdocumentfilter.class',
    'country.class',
    'countryfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'agentlog.class',
    'agentlogfilter.class',
    'services.class' ,
    'servicefilter.class',       
]);
// set up local page class
class Page extends BasePage
{
    public $agentList = "";
    public $countryname ="";
    public $agentDocument = "";
    public $agentMappingList = "";
    public $agentLog = "";
    public function init()
    {
//        if(!Permissions::checkFilePermission('agent_view.php')) 
//            util_redirect ("index.php");
        $id = util_get_num("id");
        if($id > 0)
        {
            $agentFilter = new AgentDataFilter();
            $agentFilter->addFieldFilter("a.id", $id);
            $aList = $agentFilter->getList();
            if(count($aList) > 0)
            {
                $this->agentList = $aList[0];
                $agentId = $this->agentList->getId();
                
                // GET COUNRY NAME
                $country = new CountryFilter();
                $country->addFilter(" id = '".$this->agentList->getCountryId()."'");
                $countryList = $country->getColumnList("name, iso");
                if(count($countryList) > 0)
                {
                    $this->countryname = $countryList[0]->getName();
                }
               
                //GET AGENT DOCUMENTS
                $agentDocumentFilter = new agentDocumentFilter();
                $this->agentDocument = $agentDocumentLists = $agentDocumentFilter->getAgentDoc($agentId);
                
                //GET SERVICES LIST FOR AGENT
                $servicagentMappingFilter = new ServiceAgentMappingDataFilter();
                $servicagentMappingFilter->addAgentIDFilter($agentId);
                $this->agentMappingList = $servicagentMappingFilter->getList();
                
                //GET AGENT LOGS
                $agentLogFilter = new AgentLogFilter();
                $this->agentLog = $agentLogFilter->getAuditLog($agentId, 15);
                
                
            }
        }
        else
        {
            util_redirect ("agent.php");
        }
        $this->breadCrumb['data'] = array(
            'index.php'=>Translation::GetCaption("HOME"),
            'agent.php'=>Translation::GetCaption("AGENT"),
            "View Agent" );
 	}

	 /**
	 * Override to show the menu
	 *
	 */
    public function renderMenu()
    {
           $menu = new Adminmenu();
           $menu->render();
    }

    protected function addPagelavelCss() {
    ?>
    <style type="text/css">
        .label_new{
            font-weight: bold !important;
        }
    </style>    
    <?php
        }

    public function addPagelavelJs() {
       
        }
    public function renderHead()
    {

    }
     public function renderBody()
    {
        
            ?>

<div class="portlet light">
<div class="portlet-title">
    <div class="caption"> <i class="icon-user"></i>
        Agent Detail : <?=$this->agentList->getAgentCode();?>
    </div>
    <div class="actions">
    </div>
</div>
   
<div class="portlet-body">
    <div class="invoice">
        <div class="row invoice-logo">
            <div class="col-xs-3 invoice-logo-space">
                <img src="../images/agentlogo/<?=$this->agentList->getLogo()?>" class="img-responsive" alt="" /> </div>
             <div class="col-xs-9">
                <table class="table table-striped table-bordered table-advance table-hover">
                    <tbody>
                        <tr>
                            <td class="label_new">Code </td>
                            <td><strong><?=$this->agentList->getAgentCode();?></strong></td>
                        </tr>
                        <tr>
                            <td class="label_new">Name </td>
                            <td><?=$this->agentList->getAgentCode();?></td>
                        </tr>
                        <tr>
                            <td class="label_new">Address </td>
                            <td><?=$this->agentList->getAddressLine1();?>  <?=$this->agentList->getAddressLine2();?></td>
                        </tr>
                        <tr>
                            <td class="label_new">City </td>
                            <td><?=$this->agentList->getCity();?> </td>
                        </tr>
                        <tr>
                            <td class="label_new">Country </td>
                            <td><?= $this->countryname;?></td>
                        </tr>
                        <tr>
                            <td class="label_new">Postcode </td>
                            <td><?=$this->agentList->getPostcode();?></td>
                        </tr>
                        <tr>
                            <td class="label_new">Telephone </td>
                            <td><?=$this->agentList->getTelephone();?></td>
                        </tr>
                        <tr>
                            <td class="label_new">Mobile </td>
                            <td><?=$this->agentList->getMobile();?> </td>
                        </tr>
                        <tr>
                            <td class="label_new">Email </td>
                            <td><?=$this->agentList->getEmail();?></td>
                        </tr>
                        
                    </tbody>
                </table>
        </div>
        </div>
    </div>
</div>
</div>
<div class="portlet light">
<div class="portlet-title">
    <div class="caption"> <i class="fa fa-plane"></i>
        Services
    </div>
</div>
<div class="portlet-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Service </th>
                            <th> Weight (KG) </th>
                            <th> Insurance Charges </th>
                            <th> Insurance Cover </th>
                            <th> Reroute Charges </th>
                            <th> Oversize Charges </th>
                            <th> Add. Change Charges </th>
                            <th> Return Charges </th>
                            <th> Relabel Charges </th>
                            <th> Wrong Add. Charges </th>
                            <th> Other Charges </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($this->agentMappingList) > 0){
                                $i = 1;
                            foreach($this->agentMappingList as $agent)
                            {
                                $servicesFilter = new ServiceFilter();
                                $servicesFilter->addFieldFilter("id", $agent->getServiceid());
                                $services = $servicesFilter->getList();
                                
                                
                        ?>
                        <tr>
                            <td> <?=$i;?> </td>
                            <td><?=(count($services)>0)?$services[0]->getName():''; ?> </td>
                            <td><center><?=round($agent->getFromWeight()); ?> - <?=round($agent->getToWeight());?></center></td>
                            <td><center><?=$agent->getInsuranceCharges();?></center></td>
                            <td><center><?=$agent->getInsuranceCover();?></center></td>
                            <td><center><?=$agent->getRerouteCharges();?></center></td>
                            <td><center><?=$agent->getOversizeCharges();?></center></td>
                            <td><center><?=$agent->getAddressChangeCharges();?></center></td>
                            <td><center><?=$agent->getReturnCharges();?></center></td>
                            <td><center><?=$agent->getRelabelCharges();?></center></td>
                            <td><center><?=$agent->getWrongAddressCharges();?></center></td>
                            <td><center><?=$agent->getOtherSurcharges();?></center></td>
                        </tr>
                        <?php
                        $i++;
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</div>
<?php if(count($this->agentDocument) > 0){ ?>
<div class="portlet light">
<div class="portlet-title">
    <div class="caption"> <i class="fa fa-file"></i>
        Documents
    </div>
</div>     
<div class="portlet-body">
        <div class="row">
            <div class="col-xs-12">
                  <?php
                    foreach ($this->agentDocument as $agentDocumentList) {
                        $code = $this->agentList->getAgentCode();
                        $temp = explode(".", $agentDocumentList->getDocumentName());
                        $extension = end($temp);
                        $fileFullPath = "../_assets/agent_documents/".$code."/".$agentDocumentList->getDocumentName();
                        if (!file_exists($fileFullPath) && $extension != "pdf") {
                            $fileFullPath = "../images/No-image-found.jpg";
                        }
                        if($extension == "pdf"){
                            $fileFullPath = "../images/pdf.png";
                        }
                        ?>
                        <div class="col-md-3" id="usr_doc_<?php echo $agentDocumentList->getId(); ?>">
                            <div class="thumbnail">
                                <img src="<?php echo $fileFullPath ?>" alt="<?php echo $agentDocumentList->getDocumentName(); ?>" style="max-width: 100%; max-height: 100px; display: block;" data-src="<?php echo $fileFullPath ?>">
                                <div class="caption">
                                    <h3><?php echo $agentDocumentList->getDocumentId(); ?></h3>
                                    <a target="_blank" href="../_assets/agent_documents/<?php echo $code."/".$agentDocumentList->getDocumentName(); ?>" class="btn blue"> View </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
              ?>
            </div>
        </div>
</div>
</div>
  
<?php }?>
        
<?php if(count($this->agentLog) > 0){ ?>
<div class="portlet light">
<div class="portlet-title">
    <div class="caption"> <i class="fa fa-list"></i>
        Audit
    </div>
</div>     
<div class="portlet-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Time </th>
                            <th> User </th>
                            <th> Description </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(count($this->agentLog) > 0){
                                $i = 1;
                            foreach($this->agentLog as $agentl)
                            {
                        ?>
                        <tr>
                            <td> <?=$i;?> </td>
                            <td><?=date("d-m-Y H:i",$agentl->getLogDate());?> </td>
                            <td><?=$agentl->getIpAddress();?> </td>
                            <td><?=$agentl->getMessage();?> </td>
                        </tr>
                        <?php
                        $i++;
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</div>
        <?php } ?>

<?php
    }
public function renderFooter() {

}
    
   


}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
