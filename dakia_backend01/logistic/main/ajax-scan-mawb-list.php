<?php
require_once("../includes/settings/config.inc.php");
@session_start();
$conMawbFilter	=	new	ConsignmentFilter();
	$getConsignmentMawbData	= $conMawbFilter->getTodayMawb();
	$htmlCode =	"";
//	$getConsignmentMawbData	= $conMawbFilter->getColumnList('id, hawb');
	$htmlCode .=	'<table width="100%" border="1" style="border-right:1px solid hsl(239, 100%, 20%);" >';
	$htmlCode .=	'<tr>
  	  <th colspan="2"  class="title-mawb" style="font-size:20px !important;">Mawb Currently being Scanned</th>
	  </tr>';
	if(count($getConsignmentMawbData)>0)
	{
		$countMawb	= 0;
		
		foreach($getConsignmentMawbData as $mawbData)
		{
			if($countMawb%2 == 0)
				$macbFontColor	=	'color:#F00 !important;';
			else
				$macbFontColor	=	'';
			$htmlCode .=	 '		<tr>';
			$htmlCode .=	'        	<td width="50%" style="font-size:18px !important; '.$macbFontColor	.' border:1px solid hsl(239, 100%, 20%)  !important;" align="center">'.$mawbData->getMawb().'</td>';
			$htmlCode .=	'          <td  align="center" style=" font-size:18px !important;'.$macbFontColor	.' border:1px solid hsl(239, 100%, 20%)  !important;">'.date('d - m - Y', time()).'</td>';
			$htmlCode .=	'       </tr>';
			
			
			//////////////// UPDATE PRE ALERT STATUS /////////
			
			updatePreAlert($mawbData->getMawb());
			
			$countMawb++;
		}
	}
	else
	{
			$htmlCode .=	'		<tr>';
			$htmlCode .=	'        	<td colspan="2"   align="center">No mawb number available</td>';
			$htmlCode .=	'       </tr>';		
	}
	$htmlCode .=	'</table>';
?>
	
<?php 

echo $htmlCode;
/*?><table width="100%" border="1">
  
  <tr id="ajax-loader-mawblist">
    <td  style="text-align:center; vertical-align:middle;" colspan="2">
     <?php ?>
    </td>
  </tr>
</table><?php */?>

<?php 
$mawbNumber		=	array();	
$conMawbFilter	=	new	ConsignmentFilter();
if(count($conMawbFilter->getTodayMawb()) <= count($_SESSION['MAWB_NUMBER']))
{
	unset($_SESSION['MAWB_NUMBER']);
}
if(!isset($_SESSION['MAWB_NUMBER']))
{
	$getConsignmentMawbData	= $conMawbFilter->getTodayMawb();
	if(count($getConsignmentMawbData)>0)
		$_SESSION['MAWB_NUMBER'][]	= $getConsignmentMawbData[0]->getMawb();
			
}
else
{
	$getConsignmentMawbData	= $conMawbFilter->getTodayMawbWithSession($_SESSION['MAWB_NUMBER']);
	if(count($getConsignmentMawbData)>0)
		$_SESSION['MAWB_NUMBER'][]	= $getConsignmentMawbData[0]->getMawb();
}


function updatePreAlert($mawb)
{
	$preAlterFilter = new PreAlertFilter();
	$preAlterFilter->updateFlightStatus($mawb);
}
?>