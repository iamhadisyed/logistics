<?php
ini_set('default_socket_timeout', 120);
header('Content-Type: text/html; charset=utf-8');

require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'importdataapis.class',
	]);
$connectCustom	=	mysql_connect(SETTING_DB_SERVER,SETTING_DB_USER,SETTING_DB_PASSWORD);
mysql_select_db(SETTING_DB_DATABASE,$connectCustom) or die(mysql_error());
	

$allFiles	=	array();
//Open directory to get names of all file.
if ($handle = opendir('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-weight-in/')) 
{
//	Reading name of all file in the directory
    while (false !== ($entry = readdir($handle))) 
	{
        if ($entry != "." && $entry != "..") 
		{
			$file	=	 $entry;
			// Reading File start
			$fileName			=	'/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-weight-in/'.$file;
			$resultArray		=	'';
			$resultArrayError	=	array();
			
			$skipFirstRow		=	true;
			$allInsertQuery		=	array();
			$allHawbNumbers		=	array();
			$allCarrier			=	array();

			$handleFile 			=	fopen($fileName, "r");
			// Reading column from CSV to import the data.
			$countNumber	=	0;
			while (($data = fgetcsv($handleFile, 1000, ",")) !== FALSE) 
			{
				// the condion skipvery first row of the csv. 
				if($skipFirstRow)
				{
					$skipFirstRow	=	false;
					continue;
				}
			//	echo '<br>me here 1<br>';
				
				$awbNumber				=	mysql_real_escape_string($data[0]);
				$weight					=	mysql_real_escape_string($data[1]);
				if(trim($awbNumber	) != '' && $weight > 0)
				{
					// Array to store the shipment information  and then it will use for query
					$updateQuery		=	"UPDATE `consignment` SET `weight` = '".$weight."' WHERE awb ='".$awbNumber."' AND `weight` <= '".$weight."' ";
					mysql_query($updateQuery,$connectCustom) or die(mysql_error());
					$allHawbNumbers[]	=	$awbNumber;
				}
			}
			mysql_close();
			fclose($handleFile);
			if(count($allHawbNumbers)>0)
			{
				$htmls	=	implode ( "<br />", $allHawbNumbers);
				$to = "ITSupport@oneworldexpress.com";
				$from = "ITSupport@oneworldexpress.com";
				$subject = "Weight updated via FTP file";
				
					//begin of HTML message
					$message = '<html>
				  <body bgcolor="#DCEEFC">
					
						<b> '.count($allHawbNumbers).' consignments has been uploaded to smart system.<br>
					 </body>
				</html>';
			   //end of message
				$headers  = "From: $from\r\n";
				$headers .= "Content-type: text/html\r\n";
			   // now lets send the email.
				mail($to, $subject, $message, $headers);
			}
			// Moving file to data-out folder
			if(copy("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-weight-in/".$file, "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-weight-out/".date('YmdHis',time())." - ".$file))
			{
				unlink("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-weight-in/".$file);
			}	
			// Redirectory End 
			break; 
		}
    }
    closedir($handle);
}
echo "Message has been sent....!"; 
print "Import done";

?>