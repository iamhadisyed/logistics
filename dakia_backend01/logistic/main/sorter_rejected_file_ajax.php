<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

$getfilename = $_GET['filename'];
$ftp_server = "164.39.218.210";
$tracking_number = array();
$matchfileid = array();

// set up basic connection
$conn_id = ftp_connect($ftp_server, 2100);
if (!$conn_id) {
    echo "connection failed";
} else {
    //echo "success";
    // login with username and password
    $login_result = ftp_login($conn_id, "optimus", "Optimus123@");
    ftp_pasv($conn_id, true);
    // get contents of the current directory
    $arrfile = ftp_nlist($conn_id, "Error/");
    if (sizeof($arrfile) > 0) {
        foreach ($arrfile as $filename) {
            $file_start = "SEFE";
            if (strpos($filename, $file_start) !== false) {
                $matchfileid[] = str_replace("Error/", "", $filename);
            }
        }
    }
}
//print_r($matchfileid);
// get report

$resolvefile_name = array_search($getfilename, $matchfileid);
//echo $matchfileid[$resolvefile_name];
if ($resolvefile_name >= 0) {
    $fp = fopen('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/optimus_sorter/Error/' . $matchfileid[$resolvefile_name], 'w'); //exit;
    $ret = ftp_nb_fget($conn_id, $fp, "Error/" . $matchfileid[$resolvefile_name], FTP_BINARY);

    while ($ret == FTP_MOREDATA) {

        // Do whatever you want
        // echo ".";
        // Continue downloading...
        $ret = ftp_nb_continue($conn_id);
    }

    if ($ret != FTP_FINISHED) {
        echo "error";
        // mail("mruga@oneworldexpress.com","DX","There was an error downloading the file FROM DX...");
        exit(1);
    }

    $handle = fopen('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/optimus_sorter/Error/' . $matchfileid[$resolvefile_name], "r");
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;

        for ($c = 0; $c < $num; $c++) {
            //print_r($data[$c]);
            $sorter_data = $data[$c];
            if ($sorter_data != "") {
                $s_data = explode(";", $sorter_data);
                if ($s_data[0] == "H") {
                    $res = $s_data[1];
                    $reject_error = $s_data[4];

                    if ($s_data[4] == "STNS" || $s_data[4] == "CNI" || $s_data[4] == "PCZI" || $s_data[4] == "CCI" || $s_data[4] = "TNB" || $s_data[4] == "TCAK" || $s_data[4] == "UCS") {

                        if ($reject_error == "TCAK") {
                            $error_message = "Tracking Already exits";
                        } else if ($reject_error == "STNS") {
                            $error_message = "Sort Type field is empty";
                        } else if ($reject_error == "CNI") {
                            $error_message = "CHUTE is not assigned";
                        } else if ($reject_error == "PCZI") {
                            $error_message = "Post Code Zone is not defined";
                        } else if ($reject_error == "CCI") {
                            $error_message = "Country code is empty";
                        } else if ($reject_error == "TNB") {
                            $error_message = "Tracking Number is empty";
                        } else if ($reject_error == "UCS") {
                            $error_message = "UNKNOW SERVICE CODE OR SERVICE NAME";
                        }

                        $tracking_number['error_data'][$error_message][] = $res;
                    }
                }
            }
        }
    }
}
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title">Tracking Numbers</h4>    
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr>            
                    <th>Error List </th>
                    <td><?php echo $getfilename; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tracking Number</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
<?php
$sr = 1;
foreach ($tracking_number['error_data'] as $index => $data) {
    ?>
                        <tr>
                            <td><?php echo $index; ?></td>
                            <td><?php echo implode("<br>", $data); ?></td>
                        </tr>   
    <?php
}
?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="sorter_rejected_file_list.php?csv=1&file=<?php echo $getfilename; ?>" class="btn default" title="View">RESOLVE</a>&nbsp;
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>

<?


?> 
