<?php

//get settings
require_once("../includes/settings/config.inc.php");

$mawb = @$_POST['mawb'];
$flight = @$_POST['flight'];
$pieces = @$_POST['pieces'];
$weight = @$_POST['weight'];
$etd = @$_POST['etd'];
$eta = @$_POST['eta'];
$id = @$_POST['id'];
$val = @$_POST['value'];
$field = @$_POST['field'];
$accoun = @$_POST['account'];
$stat = @$_POST['oweStatus'];
$fStatus = @$_POST['flightStatus'];
//$clear			= @$_POST['clear'];
$shed = @$_POST['shed'];
$mawb_array = @$_POST['mawb_check'];
//echo $mawb_array; exit;
$error_arr = array();
$consign_res;

//echo $_POST["action"]; exit;
if (@$_POST["action"] == "ServicePopUp") {
    $consignFlr = new ConsignmentFilter();
//echo $mawb_array;
    $summrayReport = $consignFlr->getServicesSummary($mawb_array);

    echo '<table>';
    if (sizeof($summrayReport) > 0) {
        foreach ($summrayReport as $summary) {
            echo '<tr>';
            echo '<td>' . $summary->getServiceType() . '</td>';
            echo '<td>' . $summary->getNumberPieces() . '</td>';
            echo '<td>' . $summary->getWeight() . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td>Please Update Mawb Number</td></tr>';
    }
    echo '</table>';
}


if (@$_POST["action"] == "UpdateField") {
    $pAlertFilter = new PreAlertFilter();
    $updateData = $pAlertFilter->addIdFilter($id);
    $fun = 'set' . $field;
    $updateData[0]->$fun($val);
    $updateData[0]->save();
}

if (@$_POST["action"] == 'savePreAlert') {
    if ($id <= 0) {
        $pAlert = new PreAlert();
        $pAlert->setMawb($mawb);
        $pAlert->setFlightNumber($flight);
        $pAlert->setPieces($pieces);
        $pAlert->setWeight($weight);
        $pAlert->setEtd($etd);
        $pAlert->setEta($eta);
        $pAlert->setCurrentStatus("IN TRANSIT");
        $pAlert->setDateTime('Please Set Date');
        //$pAlert->setCleared('NO');
        $pAlert->setStatus('Not Assigned');
        $pAlert->setShed($shed);
        $pAlert->save();
        $id = $pAlert->getId();
    } else {
        $pAlertFilter = new PreAlertFilter();
        $updateData = $pAlertFilter->addIdFilter($id);
        $updateData[0]->setMawb($mawb);
        $updateData[0]->setFlightNumber($flight);
        $updateData[0]->setPieces($pieces);
        $updateData[0]->setWeight($weight);
        $updateData[0]->setEtd($etd);
        $updateData[0]->setEta($eta);
        $updateData[0]->setCurrentStatus("IN TRANSIT");
        $updateData[0]->setDateTime("Please Set Date");
        //$updateData[0]->setCleared('NO');
        $updateData[0]->setShed($shed);
        $updateData[0]->setStatus('Not Assigned');
        $updateData[0]->save();
    }
    ?>
<? 
echo '<tr id="'.$id.'">
<td>'.$mawb.'</td>
<td>'.$flight.'</td>
<td>'.$pieces.'</td>
<td>'.$weight.'</td>
<td>'.$etd.'</td>
<td>'.$eta.'</td>
<td style="border:none; padding-right:0px;"><input type="button" name="ed" value="Edit" id="ed"
onclick="populateValues('.$id.','.$mawb.','.$flight.','.$pieces.','.$weight.',\''.$etd.'\',\''.$eta.'\')"
></td>
</tr>';	
}	

if(@$_POST["action"]=='AccountPreAlert')
{
$user = new UserAccountFilter();
$account = $user->getColumnList('user_account');
$accountList = array();
echo '<select id="shipper'.$id.'" name="shipper'.$id.'" style="width:60px; color: #000000 !important;">'; 
foreach($account as $acc)
{
if($acc->getUserAccount()==$accoun)
{
$selected = "selected='selected'";
$dbAccountNumber	=	$acc->getUserAccount();
}
else if($acc->getUserAccount()== 'MICHELLE' && $accoun == 'OWE China')
{
$selected = "selected='selected'";
$dbAccountNumber	=	'OWE China';
}
else
{
$selected = "";
$dbAccountNumber	=	$acc->getUserAccount();
}
echo '<option '.$selected.' value="'.$acc->getUserAccount().'">'.@$dbAccountNumber.'</option>';
}
echo '</select>';

}


if(@$_POST["action"]=='CurrentStatus')
{
echo '<select id="flight_status_drop'.$id.'" name="flight_status_drop'.$id.'" style="width:80px; color: #000000 !important;">'; 
$flightStatus = array("IN TRANSIT","ARRIVED LHR","CLEARANCE IN PROCESS","COLLECTION IN PROCESS");

for($i=0; $i<4; $i++)
{
if($flightStatus[$i]==$fStatus)
{
$selected = "selected='selected'";
}
else
{
$selected = "";
}
echo '<option '.$selected.' value="'.$flightStatus[$i].'">'.$flightStatus[$i].'</option>';
}
echo '</select>';

}


if(@$_POST["action"]=='OWEStatus')
{
if($stat=='M')
{
$stat = 'ASSIGNED';
}
echo '<select id="status_drop'.$id.'" name="status_drop'.$id.'" style="width:80px; color: #000000 !important;">'; 
$status = array("NOT ASSIGNED", "ASSIGNED", "IN WAREHOUSE");

for($i=0; $i<=2; $i++)
{
if($status[$i]==$stat)
{
$selected = "selected='selected'";
}
else
{
$selected = "";
}
echo '<option '.$selected.' value="'.$status[$i].'">'.$status[$i].'</option>';
}
echo '</select>';
}

/*if(@$_POST["action"]=='Clear')
{
echo '<select id="clear_drop'.$id.'" name="clear_drop'.$id.'" style="width:60px; color: #000000 !important;">'; 
$clear = array("YES", "NO");

for($i=0; $i<=1; $i++)
{
if($clear[$i]==$clear)
{
$selected = "selected='selected'";
}
else
{
$selected = "";
}
echo '<option '.$selected.' value="'.$clear[$i].'">'.$clear[$i].'</option>';
}
echo '</select>';
}*/

?> 