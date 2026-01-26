<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'api2cart.class',
]);


$api2cart = new Api2cart();
$api2cart->fetchApi2cartFields();