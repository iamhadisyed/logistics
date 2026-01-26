<?php

require __DIR__ . '/../includes/settings/config.inc.php';
require __DIR__ . '/../includes/general/carrierservice.class.php';
require __DIR__ . '/../includes/3rdparty/tcpdf/tcpdf.php';
require __DIR__ . '/../includes/mapping/iaddress.class.php';
foreach (glob(__DIR__ . '/../includes/mapping/*.php') as $filename){
    include_once $filename;
}
foreach (glob(__DIR__ . '/../includes/labels/*.php') as $labelFilename){
    include_once $labelFilename;
}


//ini_set('max_execution_time', '0'); // for infinite time of execution 
//ini_set('memory_limit','-1');
//phpinfo();