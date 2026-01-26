<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Index page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
$master_number = $_GET['mawb'];
$bag_number = $_GET['bn'];
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
                    <th>Master Number</th>
                    <td><?php echo $master_number; ?></td>
                    <th>Bag Number</th>
                    <td><?php echo $bag_number; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $report_filter = new reportingfilter();

            $trackingNoDataArr = $report_filter->getConsignmentBagTrackingNo($master_number, $bag_number);
            if (count($trackingNoDataArr) > 0) {
                ?>
                <table class="table table-bordered">
                    <thead
                        <tr>
                            <th>Sr.</th>
                            <th>Tracking Number</th>
                            <th>Sender</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sr = 1;
                        foreach ($trackingNoDataArr as $trackingNoData) {
                            ?>
                            <tr>
                                <td><?php echo $sr; ?></td>
                                <td><?php echo $trackingNoData['awb']; ?></td>
                                <td><?php echo $trackingNoData['sender_name']; ?></td>
                                <td><?php echo $trackingNoData['consignment_status']; ?></td>
                            </tr>   
                            <?php
                            $sr++;
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>
 