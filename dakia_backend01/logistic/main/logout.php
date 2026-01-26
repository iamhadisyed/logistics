<?php

////////////////////////////////////////////////////
// 
// Controller for Admin - logout
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

/*------------------------------------------------------------------------------*/
// logout
$AurObj = new Adminuser();
$AurObj->logout();

?>
