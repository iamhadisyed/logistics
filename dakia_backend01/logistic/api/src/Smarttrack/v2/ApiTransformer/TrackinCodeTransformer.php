<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class TrackinCodeTransformer {

    public function transformCollection($trackingStatusCode) {
        $hal = new Hal();
        $count = 0;
        if (count($trackingStatusCode) > 0) {
            $resource = new Hal(null, $trackingStatusCode);
            $hal->addResource('tacking_status_code', $resource);
            $hal->setData(['status' => 'success', 'status_code' => 200, 'count' => $count, 'message' => "Records Found"]);
        } else {
            $hal->setData(['status' => 'error', 'status_code' => 400, 'count' => $count, 'message' => "No Record Found!"]);
        }

        return $hal;
    }

}
