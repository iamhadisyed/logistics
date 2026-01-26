<?php
require_once("../includes/settings/config.inc.php");
if($_REQUEST['manifest'] == 'selected')
{
    Manifest::ManifestSelected();
}
elseif($_REQUEST['manifest'] == 'manifestall')
{
    Manifest::ManifestAll();
    
    
}
elseif($_REQUEST['manifest'] == 'viewallmanifest')
{
    util_redirect("../main/client_list.php?show=manifest_selected");
}
?>