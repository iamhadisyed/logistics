<?php
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	/***
	 * Controller logic goes here
	 */
	public function init()
	{
		invoicetemplate::buildPDFDocuments();
	}
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
