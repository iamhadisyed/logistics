<?php
$this->user = SessionManager::getUser();
$driversData = new UserFilter();
$driversData->addFieldFilter('user_type', 'driver');
$userAccountArray = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
$driversData->addFilterIn('user_account_id', $userAccountArray);
$driversData = $driversData->getListLimit(4);
$dashboardData = [];
foreach ($driversData as $driverDatum) {
    $driverParcels = new AssignVehicleFilter();
    $driverParcels->addFilter(['driver_id' => $driverDatum->getId()], '=');
    $totalParcels = $driverParcels->getCount(false);
    $driverParcels = new AssignVehicleFilter();
    $driverParcels->addFilter(['driver_id' => $driverDatum->getId(), 'is_active' => 1, 'p.parcel_status_code' => 19], '=');
    $driverParcels->innerJoin('parcel p', "p.id=vpm.parcel_id");
    $totalDeliveredParcels = $driverParcels->getCount(false);
    $driverParcels = new AssignVehicleFilter();
    $driverParcels->addFilter(['driver_id' => $driverDatum->getId(), 'is_active' => 0], '=');
    $totalUndeliveredParcels = $driverParcels->getCount(false);
    $dashboardData[] = [
        'driver_name' => $driverDatum->getFirstName(),
        'driver_id' => $driverDatum->getId(),
        'progress' => ($totalDeliveredParcels * 100) / $totalParcels,
        'driver_data' => [
            'total_parcel' => $totalParcels,
            'parcel_delivered' => $totalDeliveredParcels,
            'parcel_undelivered' => $totalParcels - $totalDeliveredParcels,
        ]
    ];
}
$numItems = count($dashboardData);
$i = 0;

?>
<style>
    .dashboard-stat2, .dashboard-stat2 .display {
        margin-bottom: 4px;
    }
</style>
<div class="portlet light">
    <div class="row">
        <?php if (!empty($dashboardData)): ?>
            <?php foreach ($dashboardData as $driver): ?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="dashboard-stat2 bordered">
                        <div class="display">
                            <h5 class="font-blue-sharp"><b><?php echo $driver['driver_name']; ?></b></h5>
                            <div class="number">
                                <small>Total</small>
                            </div>
                            <div class="float-right">
                                <span><?php echo $driver['driver_data']['total_parcel']; ?></span>
                                <!--                                    <small class="font-green-sharp">$</small>-->
                            </div>
                        </div>

                        <div class="display">
                            <div class="number">
                                <small>Delivered</small>
                            </div>
                            <div class="float-right">
                                <span><?php echo $driver['driver_data']['parcel_delivered']; ?></span>
                                <!--<small class="font-green-sharp">$</small>-->
                            </div>
                        </div>

                        <div class="display">
                            <div class="number">
                                <small>Undelivered</small>
                            </div>
                            <div class="float-right">
                                <span><?php echo $driver['driver_data']['parcel_undelivered']; ?></span>
                                <!--<small class="font-green-sharp">$</small>-->
                            </div>
                        </div>
                        <div class="progress-info">
                            <div class="progress">
                                <span style="width: <?php echo $driver['progress']; ?>%;"
                                      class="progress-bar progress-bar-success blue-sharp">
                                    <span class="sr-only">100% progress</span>
                                </span>
                            </div>
                            <div class="status">

                                <div class="status-title">
                                    <?php
                                    if (++$i === $numItems && $i == 4) { ?>
                                        <div class="status-number float-left">
                                            <a href="all_drivers_data.php"
                                               data-original-title="" class="float-left"
                                               title="">View more</a></div>
                                    <?php }
                                    ?>
                                </div>
                                <div class="status-number"><a
                                            href="list_assign_parcels_to_vehicle.php?driver_id=<?php echo $driver['driver_id']; ?>"
                                            data-original-title=""
                                            title="">View Details</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>