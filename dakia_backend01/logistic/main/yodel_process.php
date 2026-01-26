<?php

//$directory = "../_assets/supplier invoices/surcharges/*"; // CSV Files Diectory Path
//$masterCSVFile = fopen('../_assets/supplier invoices/surcharges_consolidate.csv', "w+");

$directory = "/var/www/vhosts/smarttrack.co/httpdocs/_assets/supplier invoices/combined/*"; // CSV Files Diectory Path

ini_set("memory_limit", "-1");
ini_set("max_execution_time", 0);


//$directory = "../_assets/supplier invoices/parcel_invoices/*"; // CSV Files Diectory Path
//$masterCSVFile = fopen('../_assets/supplier invoices/parcel_invoice_consolidate.csv', "w+");


//$directory = "../_assets/supplier invoices/surcharges/*"; // CSV Files Diectory Path
//$masterCSVFile = fopen('../_assets/supplier invoices/surcharges_consolidate.csv', "w+");


$dataHeader 	= []; // Empty Data
$dataHeader 	= [
					'Hawb', 
					'Tracking Number', 
					'Charges Reference', 
					'Basic Charges', 
					'Fuel Charges', 
					'Additional Charges', 
					'DDP', 
					'Remote Area Charges', 
					'Handling Charges', 
					'Linehaul', 
					'Address Correction', 
					'Airline Handling', 
					'Clearance', 
					'Collections', 
					'DDP Admin Fee', 
					'DDP Charge', 
					'Delivery', 
					'Dispatch', 
					'Labour', 
					'Other', 
					'Out of gauge', 
					'Over Weight', 
					'RAS', 
					'Redelivery', 
					'Label Charges', 
					'VAT', 
					'Discount', 
					'Processing Fee'
				];
				
// Process each CSV file inside root directory
$trackingNumber = [];
$data	=	[];
$trackingCount = 1;
foreach(glob($directory) as $keyFile=>$file) {

	$purchaseInvoiceFile = true;
    echo $keyFile;
	if($keyFile == 0)  
    	$data[]	=	$dataHeader;
 	// Allow only CSV files
    if (strpos($file, '.csv') !== false) {
	
	echo $file;
        // Open and Read individual CSV file
        if (($handle = fopen($file, 'r')) !== false) {
            // Collect CSV each row records
			$countRowData = 1;
			$fuelChargespercentage = 0.00;
            while (($dataValue = fgetcsv($handle, 1000000)) !== false) {
                
				if(trim(@$dataValue[1])=='Fuel Surcharge'){
					$fuelChargespercentage = (float)trim(str_replace('%','',trim(@$dataValue[3])));
					}
				if(trim(@$dataValue[14])=='Fuel Surcharge'){
					$fuelChargespercentage = (float)trim(str_replace('%','',trim(@$dataValue[16])));
					}
				

				if(count($dataValue)>= 44 && trim(@$dataValue[21])==''){
					$countRowData++;
					continue;
				} else if( trim(@$dataValue[10])==''){
					$countRowData++;
					continue;
				}
				
				if(array_search('Surchrge Service Code', $dataValue)!==false){
					$purchaseInvoiceFile = false;
				}
				if(array_search('Service Code', $dataValue)!==false){//(trim(@$dataValue[13])=='Service Code'){
					$purchaseInvoiceFile = true;
				}
				echo "<pre>";
				if(array_search('Parcel Id (Lp, Upi Or Common Label)', $dataValue)!==false){//if(trim(@$dataValue[10])=='Parcel Id (Lp, Upi Or Common Label)'){
					$headerData =	[]; 
					foreach($dataValue as $dataKeyIndex=>$dataValueIndex){
							if(trim($dataValueIndex) == '')
								break;
							$headerData[trim(str_replace("£","",str_replace(' �',"", utf8_encode($dataValueIndex))))] = $dataKeyIndex;
						}
						
					$countRowData++;
					continue;
				}

				
				
				//print_r($dataValue);
				//print_r($headerData);
				//die;
				//$data[] = $dataValue;
				//print_r($headerData);
				//die;
				$trackingRangesCheck = substr(str_replace(","," ",$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]),0,12);
				if($trackingRangesCheck != 'JD0002210164')
					continue;
				if(!isset($trackingNumber[$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]])){
					$trackingNumber[$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]] = $trackingCount ;
					$trackingIndexCount = $trackingCount ;
					$trackingCount++;
					
					$chargeReference		=	''; // charge reference
					$basicCharge			=	0; // basic charge
					$fuelCharge				=	0; // fuel charge
					$additionalCharge		=	0; // aditionalcharge
					
				}else{
					$trackingIndexCount = $trackingNumber[$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]];
					$chargeReference		=	$data[$trackingIndexCount][2]; // charge reference
					$basicCharge			=	$data[$trackingIndexCount][3]; // basic charge
					$fuelCharge				=	$data[$trackingIndexCount][4]; // fuel charge
					$additionalCharge		=	$data[$trackingIndexCount][5]; // aditionalcharge
					
				}
				$chargeReference .= str_replace(","," ",$dataValue[$headerData['Invoice Number']])."-";
				 
				
				
				if($purchaseInvoiceFile){
					if(trim(str_replace(","," ",$dataValue[@$headerData['Net Amount']]))!='')
					{
						$bamount			=	str_replace(","," ",$dataValue[@$headerData['Net Amount']]); // basic charge
					} else 
					{
						$bamount			=	(float)str_replace(","," ",$dataValue[@$headerData['Rate']]) * (int)str_replace(","," ",$dataValue[@$headerData['Instance']]); // basic charge
					}
					$basicCharge			+=	$bamount; // basic charge
					
					} else {
					
					if(trim(str_replace(","," ",$dataValue[@$headerData['Net Amount']]))!='')
					{
						$bamount			=	str_replace(","," ",$dataValue[@$headerData['Net Amount']]); // basic charge
					} else 
					{
						$bamount			=	(float)str_replace(","," ",$dataValue[@$headerData['Surcharge Rate']]) * (int)str_replace(","," ",$dataValue[@$headerData['Instance']]); // basic charge
					}
					$additionalCharge		+=	$bamount; // aditionalcharge
				}
				
				$fuelCharge				+=	(($fuelChargespercentage * $bamount)/100); // fuel charge
				$vatAmount = '';
				if(isset($dataValue[@$headerData['VAT Rate']]) && $dataValue[@$headerData['VAT Rate']] > 0)
				{
					$vatAmount = (($fuelCharge + $basicCharge + $additionalCharge)  * ( $dataValue[@$headerData['VAT Rate']] / 100  ));
				}
				$data[$trackingIndexCount] = [
					str_replace(","," ",$dataValue[$headerData['Customer Reference']]), //order reference
					str_replace(","," ",$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]),//tracking Number 
					rtrim($chargeReference,"-"), //invoice reference number 
					$basicCharge,// Basic Charges 
					$fuelCharge, //Fuel Charges, 
					$additionalCharge, //Additional Charges, 
					'',//'DDP', 
					'',//'Remote Area Charges', 
					'',//'Handling Charges', 
					'',//'Linehaul', 
					'',//'Address Correction', 
					'',//'Airline Handling', 
					'',//'Clearance', 
					'',//'Collections', 
					'',//'DDP Admin Fee', 
					'',//'DDP Charge', 
					'',//'Delivery', 
					'',//'Dispatch', 
					'',//'Labour', 
					'',//'Other', 
					'',//'Out of gauge', 
					'',//'Over Weight', 
					'',//'RAS', 
					'',//'Redelivery', 
					'',//'Label Charges', 
					$vatAmount,//'VAT', 
					'',//'Discount', 
					'',//'Processing Fee'
				];/*[
					str_replace(","," ",$dataValue[$headerData['Invoice Number']]),
					str_replace(","," ",$dataValue[$headerData['Invoice Date']]),
					str_replace(","," ",$dataValue[$headerData['Mars Account No']]),
					str_replace(","," ",$dataValue[$headerData['Contract No']]),
					str_replace(","," ",$dataValue[$headerData['Parcel Id (Lp, Upi Or Common Label)']]),
					str_replace(","," ",$dataValue[$headerData['Instance']]),
					str_replace(","," ",$dataValue[$headerData['Manifest']]),
					str_replace(","," ",$dataValue[$headerData['Surchrge Service Code']]),
					str_replace(","," ",$dataValue[$headerData['Surcharge Name']]),
					str_replace(","," ",$dataValue[$headerData['Original Service Code']]),
					str_replace(","," ",$dataValue[$headerData['Customer Order No']]),
					str_replace(","," ",$dataValue[$headerData['Customer Reference']]),
					str_replace(","," ",$dataValue[$headerData['Collection Date']]),
					str_replace(","," ",$dataValue[$headerData['Delivery Name']]),
					str_replace(","," ",$dataValue[$headerData['Delivery Postcode']]),
					str_replace(","," ",$dataValue[$headerData['IT Solution (PAN)']]),
					str_replace(","," ",$dataValue[$headerData['PAN FTP Transmission Date/Time']]),
					str_replace(","," ",$dataValue[$headerData['1st Physival Scan Date/Time']]),
					str_replace(","," ",$dataValue[$headerData['Actual Length']]),
					str_replace(","," ",$dataValue[$headerData['Actual Height']]),					
					str_replace(","," ",$dataValue[$headerData['Actual Width']]),					
					str_replace(","," ",$dataValue[$headerData['Actual Volume']]),					
					str_replace(","," ",$dataValue[$headerData['Actual Weight']]),					
					str_replace(","," ",$dataValue[$headerData['Declared Weight']]),					
					str_replace(","," ",$dataValue[@$headerData['Surcharge Rate']]),					
					str_replace(","," ",$dataValue[@$headerData['Net Amount']]),
					str_replace(","," ",$dataValue[@$headerData['VAT Rate']]),
					str_replace(","," ",$dataValue[@$headerData['VAT Amount']]),
					str_replace(","," ",$dataValue[@$headerData['Total Amount']])				
				];*/
				
				//if($countRowData>50) break;
				
				$countRowData++;

            }

        }
        fclose($handle); // Close individual CSV file 
    } else {
        echo "[$file] is not a CSV file.";
    }
}

$dataHeader 	= [
					'Hawb', 
					'Tracking Number', 
					'Charges Reference', 
					'Basic Charges', 
					'Fuel Charges', 
					'Additional Charges', 
					'DDP', 
					'Remote Area Charges', 
					'Handling Charges', 
					'Linehaul', 
					'Address Correction', 
					'Airline Handling', 
					'Clearance', 
					'Collections', 
					'DDP Admin Fee', 
					'DDP Charge', 
					'Delivery', 
					'Dispatch', 
					'Labour', 
					'Other', 
					'Out of gauge', 
					'Over Weight', 
					'RAS', 
					'Redelivery', 
					'Label Charges', 
					'VAT', 
					'Discount', 
					'Processing Fee'
				];
$finaldatafileIndex = 1;
$masterCSVFile = fopen('/var/www/vhosts/smarttrack.co/httpdocs/_assets/supplier invoices/final_data-'.$finaldatafileIndex.'.csv', "w+");
if(count($data) > 0) {

            foreach ($data as $keyDataIndex=>$value) {
                try {
                // Insert record into master CSV file
                $val =  fputcsv($masterCSVFile, $value);
				if($keyDataIndex>0 && ($keyDataIndex % 5000) == 0){
					fclose($masterCSVFile);
					$finaldatafileIndex++;
					$masterCSVFile = fopen('/var/www/vhosts/smarttrack.co/httpdocs/_assets/supplier invoices/final_data-'.$finaldatafileIndex.'.csv', "w+");
					fputcsv($masterCSVFile, $dataHeader);
				
				}
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            
            }

        } else {
            echo "[$file] file contains no record to process.";
        }

// Close master CSV file 
fclose($masterCSVFile);


die;


$dir    = '../_assets/supplier invoices/surcharges';
$dirSurcharges    = '../_assets/supplier invoices/surcharges_consolidate.csv';
$files1 = scan_dir($dir);
//echo "<pre>";
//print_r($files1);

foreach($files1 as $filesname){
	$fileCsvRead = $dir."/".$filesname;
	$row = 1;
	if (($handle = fopen($fileCsvRead, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$num = count($data);
			echo "<p> $num fields in line $row: <br /></p>\n";
			$row++;
			for ($c=0; $c < $num; $c++) {
				echo $data[$c] . "<br />\n";
			}
		}
		fclose($handle);
	}
}
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();    
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}
die;

?>