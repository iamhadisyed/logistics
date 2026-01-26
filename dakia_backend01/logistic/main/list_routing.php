<?php
// get settings
require_once("../includes/settings/config.inc.php");

/***
 * Page for editing a user
 */
class Page extends BasePage
{
	/***
	* Controller logic
	*/
	protected function init()
	{
		t_on(); // turn on trace for this page

		// common initialisation for ths page
		$this->setTitle("User Edit");
	}
	
	protected function renderHead()
	{
		?>
				<script>
			
				function showSelectedService(str,showfield,vale,fieldName, fieldId)
                {
					$.get( "getservices.php?q="+str+'&n='+fieldName+'&i='+fieldId+'&sel='+vale, function( data ) {
							$( "#"+showfield ).html( data );
							});
			
                }
				
				function showCountries(showfield, str,fieldName, fieldId	)
                {
					$.get( "getcountries.php?q="+str+"&n="+fieldName+"&i="+fieldId, function( data ) {
							$( "#"+showfield ).html( data );
							});
			
                
                }
				function showCarrier(showfield, str,fieldName, fieldId	)
                {
					$.get( "getcarrier.php?q="+str+"&n="+fieldName+"&i="+fieldId, function( data ) {
							$( "#"+showfield ).html( data );
							});
			
                
                }
				function showServiceType(showfield, str,fieldName, fieldId	)
                {
					$.get( "getservicetype.php?q="+str+"&n="+fieldName+"&i="+fieldId, function( data ) {
							$( "#"+showfield ).html( data );
							});
			
                
                }
				
	function calcUpperWeight(pweight)
	{
		var toweight	=	parseFloat(pweight);
		if(toweight<2)toweight+=0.25;
		else if(toweight<10)toweight+=0.5;
		else toweight+=5;
		$('#toweight').val(toweight);
		return false;
		
	}
	
	function calcLowerWeight(pweight)
	{
		var fromweight	=	parseFloat(pweight);
		if(fromweight<2)fromweight-=0.25;
		else if(fromweight<10)fromweight-=0.5;
		else fromweight-=5;
		$('#fromweight').val(fromweight);
		return false;
		
	}
	function addDropdown(spaid,recid,crcode,userid)
	{
		$.get( "getallservices.php?sel="+crcode+"&re="+recid+"&user_id="+userid, function( data ) {
			$( "#"+spaid ).html( data );
			$( "#"+spaid ).attr( "onclick",'');

		});
		
	}
	
	function save_service_change(mval, rid,userid)
	{
		$.get( "updaterule.php?services="+mval+"&reid="+rid+"&user_id="+userid, function( data ) {
			$( "#ser-"+rid ).html( data );
			$( "#ser-"+rid ).attr( "onclick",'addDropdown(\'ser-\''+rid+','+rid+','+mval+','+userid+')');
	//alert('#ser-'+rid)
			//$("#ser-"+rid ).live('click', addDropdown('ser-'+rid,rid,mval,userid));

		});
	}
	function remove_service(rid,act, inact)
	{
		if( confirm("Are you sure, you want to inactive this option"))
		{	
			$.get( "updaterule.php?reid="+rid+"&action=delete", function( data ) {
				$( "#"+rid ).attr( "class",'inactive');
				$( "#"+act ).show();
				$( "#"+inact ).hide();
				//alert($( "#"+rid ).html());
				//$( "#"+rid ).html(data);
				//alert($( "#"+rid ).html());
			
				
			});
		}
	}
	function add_service(rid,act, inact)
	{
		if( confirm("Are you sure, you want to active this option"))
		{	
			$.get( "updaterule.php?reid="+rid+"&action=active", function( data ) {
				$( "#"+rid ).attr( "class",'active');
				$( "#"+act ).show();
				$( "#"+inact ).hide();
				//$( "#"+rid ).html(data);
			});
		}
	}
	function routing_service(rid,act, inact)
	{
		if( confirm("Are you sure, you want to change service type"))
		{	
			$.get( "updaterule.php?reid="+rid+"&action=routing", function( data ) {
				$( "#"+rid ).attr( "class",'active');
				$( "#"+act ).show();
				$( "#"+inact ).hide();
			});
		}
	}
	function oneworld_service(rid,act, inact)
	{
		if( confirm("Are you sure, you want to change service type"))
		{	
			$.get( "updaterule.php?reid="+rid+"&action=oneworld", function( data ) {
				$( "#"+rid ).attr( "class",'active');
				$( "#"+act ).show();
				$( "#"+inact ).hide();
			});
		}
	}
                </script>        
        <?
		
	}
	
	private function getCellData($countryId, $lowerLimit,$upperLimit)
	{
		$colData	=	'';
		$psr = new PartnerServicesRoutingFilter();
		$psr->addUserIdFilter(util_get_num("id"));
		$psr->addToWeightFilter($upperLimit);
		$psr->addCountryFilter($countryId);
		$psr->addORPRServicesFilter();
		$resultCountry 	=	$psr->getList();
		if($psr->getCount()> 0)
		{
			foreach($resultCountry as $rowSer)
				$colData	.= '<div style=" min-width: 30px; text-align:center; cursor:pointer;" class="'.$rowSer->getStatus().'" id="ser-'.$rowSer->getId().'" ondblclick="addDropdown(\'ser-'.$rowSer->getId().'\',\''.$rowSer->getId().'\',\''.$rowSer->getServiceName().'\','.util_get_num("id").');">'.$rowSer->getServiceName();
				$colData	.= '<div style="clear:both;"></div>';
				/*--- Here status check  */
					$colData	.= '
				</div>';
				if($rowSer->getStatus() == 'active')
				{
					$active	=	'display:block;';
					$inactive	=	'display:none;';
				}
				else
				{
					$active	=	'display:none;';
					$inactive	=	'display:block;';
				}
				if($rowSer->getServiceType() == Consignment::ROUTING_SERVICE_ONEWORLD)
				{
					$owe	=	'display:block;';
					$oweps	=	'display:none;';
				}
				else
				{
					$owe	=	'display:none;';
					$oweps	=	'display:block;';
				}
					$colData	.= '<div title="Make Disable" id="act-'.$rowSer->getId().'" style="color:blue; float:left; font-size:10px; cursor:pointer; '.$active.' " onclick="remove_service(\'ser-'.$rowSer->getId().'\',\'inact-'.$rowSer->getId().'\',\'act-'.$rowSer->getId().'\');"> [x] </div>';
					$colData	.= '<div title="Make Enable"  id="inact-'.$rowSer->getId().'" style="color:red; float:left; font-size:10px;  cursor:pointer;'.$inactive.'" onclick="add_service(\'ser-'.$rowSer->getId().'\',\'act-'.$rowSer->getId().'\',\'inact-'.$rowSer->getId().'\');"> [-] </div>';
					/*--- Here Services Type check check  */
					$colData	.= '<div  title="Make Routing Service" id="ows-'.$rowSer->getId().'"  style="color:green; float:left; font-size:10px; cursor:pointer; '.$owe.'" onclick="routing_service(\'ser-'.$rowSer->getId().'\',\'oweps-'.$rowSer->getId().'\',\'ows-'.$rowSer->getId().'\');"> [O] </div>';
					$colData	.= '<div  title="Make One World Service" id="oweps-'.$rowSer->getId().'"  style="color:green; float:left; font-size:10px;  cursor:pointer;'.$oweps.'" onclick="oneworld_service(\'ser-'.$rowSer->getId().'\',\'ows-'.$rowSer->getId().'\',\'oweps-'.$rowSer->getId().'\');"> [R] </div>';
				$colData	.= '<div style="clear:both;"></div>
				';
				
				
				return $colData;
		}
		else
		{
			return '';
		}
	}

	/***
	* Content View
	*/
	protected function renderBody()
	{
		?>
        <ul class="breadcrumb">
			<li><a href="../main/index.php">Home</a></li>
            <li><a href="../main/user_list.php">Users</a></li>
			<li><a href="#">Pre-defined (Routing)</a></li>
		</ul>
		<?php
		if  ($_GET['id'] != "")
		{
		$filteruser = new UserAccountFilter();
		$filteruser->addIdFilter($_GET['id']);
		$routinguser = $filteruser->getList();
		$displayname = $routinguser[0]->getUserName();	
		}
		
		// transfer form variables into local values (form variables come from parent)
		foreach ($this->form_vars as $key=>$val) {$$key = $val; }

		?>

        <h1 class="heading" style="float:left;">User Routing for <?php print($displayname) ?></h1>
        <br class="clear" />
        <div class="filter_nav">
                   
                   <ul>
                        <li> <a class="held" id='btnRoutingD' href='../main/client_file_default.php?id=<?php echo util_get_num("id"); ?>' class='btn_routing' onclick="return confirm('Are you sure to assign default routing?');"><span></span>Default</a></li>
              
              			<li><a class="held" id='btnRoutingM' href='../main/routing_add.php?id=<?php echo util_get_num("id"); ?>' class='btn_routing'><span></span>Manual</a></li>
                        <li><a class="held" id='btnRoutingP' href='../main/client_file.php?id=<?php echo util_get_num("id"); ?>' class='btn_routing'><span></span>Personalized</a></li>
                        <li><a class="held" id='btnRoutingV' href='../main/list_routing.php?id=<?php echo util_get_num("id"); ?>&country=a&action=view' class='btn_routing'><span></span>View</a></li>
                        <li><a class="held" id="btnCancel" href="../main/user_list.php" class='btn_cancel'><span></span>Cancel</a></li>
                   </ul>
                   <br class="clear" />
                   </div>
        <br class="clear" />
        
        <div id="page_head">
			<div class="filter_nav">
            	<table class="tableA" style ="width:100%; font-weight:bold;"><tr style="text-align:center;"><td> <h2  style="text-align:center;">One World Partner Services with Routing </h2></td></tr>
                <td align="center"> <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=a&action=view">A</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=b&action=view">B</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=c&action=view">C</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=d&action=view">D</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=e&action=view">E</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=f&action=view">F</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=g&action=view">G</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=h&action=view">H</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=i&action=view">I</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=j&action=view">J</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=k&action=view">K</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=l&action=view">L</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=m&action=view">M</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=n&action=view">N</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=o&action=view">O</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=p&action=view">P</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=q&action=view">Q</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=r&action=view">R</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=s&action=view">S</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=t&action=view">T</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=u&action=view">U</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=v&action=view">V</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=w&action=view">W</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=x&action=view">X</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=y&action=view">Y</a> | 
                <a href="list_routing.php?id=<?php echo util_get_num("id"); ?>&country=z&action=view">Z</a></td></tr>
              
                </table> 
            </div>
        </div>
		<?php errorList::getItem()->render(); ?>
        <div id="table_container" class="main_grid" style=" overflow-x: scroll;">
            <div style="float:left;">      
            <span id="fisrtshow">
            <?php
        
            $psr = new PartnerServicesRoutingFilter();
            $country	=	util_get("country");
            $psr->addCountryOrderByAlphabet($country);
			$psr->addNotSpecialServiceTypeFilter();
            $psr->addUserIdFilter( util_get_num("id") );
            $resultCountry 	=	$psr->getCountrytList();
        
            $display_stringDemo = "<table class='tableA'>";
            $display_stringDemo .= "<tr class='grid_header' style='font-weight:bold;'>";
            $display_stringDemo .= "<th>Options</th>";
            $display_stringDemo .= "<th>Countries</th>";
            $weightCount	=	0.25;
            while($weightCount<=30)
            {
                $display_stringDemo .= "<th>".$weightCount."</th>";
                if($weightCount<2)$weightCount+=0.25;
                //elseif($weightCount<10)$weightCount+=0.5;
                else $weightCount+=0.5;
            }
            $display_stringDemo .= "</tr>";
        /***********************************/
        /*				DATA FIELD*/
        /***********************************/
        if(isset($_GET["action"]) && $_GET["action"] == 'view')
		{
			
		
        foreach($resultCountry as $rowCountry){
            $display_stringDemo .= "<tr >";
            $display_stringDemo .= "<td><a href='routing_edit.php?id=".util_get_num("id")."&cont=".$rowCountry->getCountry()."'>Bluk Edit</a></td>";
            $display_stringDemo .= "<td>".$rowCountry->getCountry()."</td>";
            $weightCount	=	0.25;
            while($weightCount<=30)
            {
                $display_stringDemo .= "<td>".$this->getCellData($rowCountry->getCountry(),'',$weightCount)."</td>";
                if($weightCount<2)$weightCount+=0.25;
                //elseif($weightCount<10)$weightCount+=0.5;
                else $weightCount+=0.5;
            }
            $display_stringDemo .= "</tr>";
        }
		}
        $display_stringDemo .= "</table>";
        echo $display_stringDemo;
               ?>
               </span>
             <span id="txtHint1">
             
             </span>
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
		$menu = new Adminmenu(Adminmenu::CUSTOMERS);
		$menu->render();
	}

}

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();