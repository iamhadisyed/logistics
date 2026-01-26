<?php

////////////////////////////////////////////////////
//
// Controller for viewing documentation of order details
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");


/*------------------------------------------------------------------------------*/
// init vars
$id					 = NULL;

/*------------------------------------------------------------------------------*/

// get vars
(int)$id             = (isset($_GET['id'])           ? strip_tags($_GET['id']) : '');

/*------------------------------------------------------------------------------*/
// Build and create PDF
OrderPdf::buildPDFDocuments($id);
 