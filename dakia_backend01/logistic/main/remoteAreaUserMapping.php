<?php 
/*$userAccountOweChina	=	array('BFE', 'MECHK',  'LTB', 'DX', 'QUANT', 'FPRICE', 'CSCM',  'SELEAD', 'BFEPORTS', 'WINIT', 'KINGFLYING', 'ZJS',  'YPOST',  'MXBNO', 'ETOP', 'GOLDEND', 'ROYAL', 'SYNERW', 'BLUELANS', 'HAOHAN', 'UEB', 'TEST-CN', 'UEO', 'SINOTE', 'SELDHL', 'TOMTOP', 'TAKESEND', 'ZEHUI',  'TESTCN',  '5POST',  'MILESEEY',  'YOUZZON',  'ZHUTING',  'MGM',  'MAILWOLF', 'LNGJIANG',  'KITTY',  'RUSTON',  'YWSZ',  'OW00043',  'LEIYAN',  'HUANOU',  'SES',  'BABY', 'BOQIAN', 'CORNPO','HONGD',  'LANHAI',  'SAIWEI', 'ZDRESS', 'JIEJIN', 'BEIYI', 'LONGYU', 'XINYI', 'ZIDIE', 'ZXT', 'FHR', 'BESSKY', 'FLOWERS', 'JINLONG', 'GALILEO', 'LTJS', 'BMLL', 'HBING', 'SUNVARY', 'CBYI', 'HAPPYS', 'ASIAPEAK', 'LALANG', 'FEIB', 'OUMEI', 'SED(UK)',  'CDPOST',  'YINY', 'MHENG', 'LINA', 'LANSI', 'ANPING', 'TMSK','SAIC', 'GINDART', 'WEIJU', 'VALUE', 'WOJIN', 'IMPERIAL', 'XPOST', 'BRAIN', 'SFC', 'MAIGU', 'THLAN', 'YDHN', 'NAWOUK', 'FUMAN');*/
require_once("../includes/settings/config.inc.php");
define("SETTING_DB_SERVER",   "213.246.108.71");
define("SETTING_DB_USER",     "usrRumba19");
define("SETTING_DB_PASSWORD", "Q1DoUzrs4z");
define("SETTING_DB_DATABASE", "rumba19");

	$connection	=	mysql_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD );
	mysql_select_db(SETTING_DB_DATABASE,$connection);

$userAccountOweChina	=	array('BFE', 'MICHELLE', 'MECHK', 'LTB', 'DX', 'QUANT', 'FPRICE', 'CSCM', 'SELEAD', 'BFEPORTS', 'WINIT', 'KINGFLYING', 'ZJS', 'YPOST', 'MXBNO', 'ETOP', 'GOLDEND', 'ROYAL', 'SYNERW', 'BLUELANS', 'HAOHAN', 'UEB', 'TEST-CN', 'UEO', 'SINOTE', 'SELDHL', 'TOMTOP', 'TAKESEND',  'ZEHUI',  'TESTCN',  '5POST',  'MILESEEY', 'YOUZZON', 'ZHUTING', 'MGM', 'MAILWOLF', 'LNGJIANG', 'KITTY', 'RUSTON', 'YWSZ', 'OW00043', 'LEIYAN', 'HUANOU', 'SES', 'BABY', 'BOQIAN', 'CORNPO', 'HONGD', 'LANHAI', 'SAIWEI', 'ZDRESS', 'JIEJIN', 'BEIYI', 'LONGYU', 'XINYI', 'ZIDIE', 'ZXT', 'FHR', 'BESSKY', 'FLOWERS', 'JINLONG', 'GALILEO', 'LTJS', 'BMLL', 'HBING', 'SUNVARY', 'CBYI', 'HAPPYS', 'ASIAPEAK', 'LALANG', 'FEIB',  'OUMEI',  'SED(UK)', 'CDPOST',  'YINY', 'MHENG', 'LINA',  'LANSI', 'ANPING', 'TMSK', 'SAIC',  'GINDART', 'WEIJU',  'VALUE', 'WOJIN', 'IMPERIAL', 'XPOST', 'BRAIN', 'SFCSLTD', 'MAIGU', 'THLAN', 'YDHN', 'NAWOUK', 'FUMAN', 'MAIGC', 'JMY', 'DDB');


foreach($userAccountOweChina as $account)
{
	$htmlQuery	=	'insert into tariff_user_mapping (tariff_name, user_account, tariff_added_date) values ';
	$ChinaTariff	=	array( 'DHL_OWECN_DOM', 'DHL_OWECN_DOX', 'DHL_OWECN_ECX', 'DHL_OWECN_ESU', 'DHL_OWECN_WPX', 'DHLC2YCU', 'DHLC2YUK', 'DHLWPX', 'DPDDE', 'EB2C-DAC-IT', 'EEURCORREOUSOWECH', 'EEURCORREOUSSELEAD', 'EUROB2C', 'HERMESUK','HUNUTREURUK', 'HUNUTRINTUK', 'PRIORITYEURUTR', 'REGPOSTHUNEUR', 'REGPOSTHUNINT', 'REGPOSTSEDEUR', 'REGPOSTSEDINT', 'REGPOSTSEDUTREUR', 'REGPOSTSEDUTRINT', 'RM24UTRUK', 'RM48UTRUK', 'RMEURTSUK', 'RMINTTSUK', 'RMTP2OWECHINA', 'RMTP2SOWECHINA', 'tp2', 'WHISTL', 'YODEL 24', 'YODEL1HOWECHINA',  'YODEL24M', 'YODEL3HOWECHINA', 'YODELMINIOWECHINA');

	$tariffAssigned			=	array();
	$queryDefaultTariff		=	"SELECT * FROM tariff_user_mapping WHERE user_account = '".$account."'";
	$tarrifAssignedRecord	=	mysql_query($queryDefaultTariff);
	if(mysql_num_rows($tarrifAssignedRecord) > 0)
	{
		while($tariffAssignedRow	=	mysql_fetch_array($tarrifAssignedRecord))
		{
			$tariffAssigned[]		=	$tariffAssignedRow['tariff_name'];
		}
	}
	$queryDe	=	false;
	$subqueryArray	=	array();
	foreach($ChinaTariff as $tariffName)
	{
		if(!in_array($tariffName,$tariffAssigned	))
		{
			$subqueryArray[]	=	"('".$tariffName."', '".$account."', '2015-07-07 17:00:00')";
			$queryDe;
		}
	}
	
	if(count($subqueryArray)>0)
	{
		echo $finalQuery 	= $htmlQuery.implode(',',$subqueryArray);
		echo "<br><br><br><br>";
		if(mysql_query($finalQuery))
			echo "SUCCESS<br>";
		else
			echo "FAILED<br>";
		
	}
}




?>
 