<?php
//
// get settings
require_once("../includes/settings/config.inc.php");
$sessionUser = SessionManager::getUser();

$q = $_GET['q'];
if( isset($_GET['t']) && trim($_GET['t']) != '')
	$SerTySent	=	$_GET['t'];
else
	$SerTySent	=	'';
$db = SETTING_DB_DATABASE;
$mysql_access = mysql_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
mysql_select_db($db) or die(mysql_error());

if(isset($_GET['u']) && trim($_GET['u'])!= '')
{
	
		$userData		=	new	UserFilter();
		$userData->addIdFilter($_GET['u'] );
		$userlists		=	$userData->getList();
		$srtServicOp	=	array();
		
		foreach($userlists as $useroption)
		{
			$rtn	=	false;
			if(trim($useroption->getdomesticType())== 'Y' && $SerTySent == 'DBP' )
			{
				$srtServicOp[]	=	'DBP';
				$rtn	=	true;
			}
			if(trim($useroption->getinternationalType())== 'Y' && $SerTySent == 'INT')
			{
				$srtServicOp[]	=	'INT';
			}
			if(trim($useroption->geteuropeType())== 'Y'&& $SerTySent == 'R1')
			{
				$srtServicOp[]	=	'R1';
				$rtn	=	true;
			}
			if($rtn === true )
				$srtServicOp[]	=	'RTN';
		}
		$setServiceType	=	 "'".implode("','",$srtServicOp)."'";
}
else
	$setServiceType	=	"''";
/***************************
*	Check access for cooperate client
****************************/
if($sessionUser->getUserType() == User::USER_TYPE_CORPORATE_CLIENT)
{
	//$service_filter->addAccountNumberFilter($sessionUser->getUserAccount());
	$sql	 	=	"SELECT ser.name, ser.code  FROM services ser INNER JOIN partnerservicesrouting psr ON ser.carrier = psr.carrier WHERE ser.carrier = '".DbAccess3::escape($q)."' AND ser.type IN (".$setServiceType.") AND psr.account_number = '".DbAccess3::escape($sessionUser->getUserAccount())."' GROUP BY ser.carrier  ";
}
else
{
	$sql		=	"SELECT name,code FROM services WHERE carrier = '".DbAccess3::escape($q)."' AND type IN (".$setServiceType.")";
}


echo '<label></label>';

$result = mysql_query($sql) or die(mysql_error());
//echo $sql;
//exit;
if($sessionUser->getUserType() == User::USER_TYPE_CORPORATE_CLIENT)
{
	$function 	=	'return allowWeight(this);';
}
else
{
	$function 	=	'';
}
if(isset($_GET['n']))
	echo '<select  name="'.$_GET['n'].'"  size="1" id="'.$_GET['i'].'" class="form-control">';
else
	echo "<select name='services' id='services' class='form-control' onchange='".$function."'>";

	echo "<option value=''>Select Service</option>";
	while($row = mysql_fetch_array($result))
	{
	 	 if(isset($_GET['sel']) && trim($_GET['sel']) ==  trim($row['code'])  )
			echo "<option value='" . $row['code']. "' selected='selected'>" . $row['name'] . "</option>";
		else
			echo "<option value='" . $row['code']. "' >" . $row['name'] . "</option>";
	}
echo "</select>";

 mysql_close($mysql_access);

?>