<?php
// Create an Crystal Object Factory.
$o_CrObjectFactory = new COM('CrystalReports11.ObjectFactory.1');

// Create the Crystal Reports Runtime Application.
$o_CrApplication =
$o_CrObjectFactory->CreateObject("CrystalRunTime.Application.11");

// Register the typelibrary.
com_load_typelib('CrystalDesignRunTime.Application');

// Load the report.
$o_CrReport = $o_CrApplication->OpenReport('C:\Report.rpt', 1); // 1
//== crOpenReportByTempCopy.

// Logon to the database.
$o_CrReport->Database->LogOnServer
	(
	'odbc',
	'Accounts',
	registryDatabaseLocations::Database('Accounts'),
	registryDatabaseLocations::Username('Accounts'),
	registryDatabaseLocations::Password('Accounts')
	);

// Don't tell anyone what is going on when running live.
$o_CrReport->DisplayProgressDialog = False;

$s_ExportedReport = 'C:\Report.pdf';

// Run the report and save the PDF to disk.
$o_CrReport->ExportOptions->DiskFileName = $s_ExportedReport;
$o_CrReport->ExportOptions->PDFExportAllPages = True;
$o_CrReport->ExportOptions->DestinationType = 1; // Export to File
$o_CrReport->ExportOptions->FormatType = 31; // 31 = PDF, 36 = XLS, 14 = DOC

// Assign the parameters to the report.
$m_Stuff = new Variant();

$o_CrPeriodsParam =
$o_CrReport->ParameterFields->GetItemByName('PeriodIDs', $m_Stuff);
$o_CrPeriodsParam->ClearCurrentValueAndRange();

foreach($_SESSION['tabRG_PeriodIDs'] as $i_Period)
	{
	$o_CrPeriodsParam->AddCurrentValue(intval($i_Period));
	}

$o_CrReport->ReadRecords();
$o_CrReport->Export(False);
?>
