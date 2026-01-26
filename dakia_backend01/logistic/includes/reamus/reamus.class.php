<?php

class Reamus {

    private $postcode = "";
    private $lookup_complete_flag = false;
    private $hub = "";
    private $station = "";
    private $feature_code = "";
    private $product_code = "";
    private $error_list = array();
    private $postcode_valid_flag = false;

    /*     * *
     * Create a reamus lookup, starting with postcode.
     */

    public function __construct($postcode, $product_code, $feature_code) {
        $this->postcode = str_replace(" ", "", $postcode);
        $this->feature_code = $feature_code;
        $this->product_code = $product_code;
    }

    /*     * *
     * Checks if this is a known postcode.
     */

    public function isPostcodeValid() {
        $this->performLookup();
        return $this->postcode_valid_flag;
    }

    /*     * *
     * Lookup service details
     */

    public function performLookup() {
        // already performed lookup
        if ($this->lookup_complete_flag) {
            return ($this->hub != "");
        }
        $this->lookup_complete_flag = true;
        //
        t("Lookup " . $this->postcode, __METHOD__);

        // Get the destination station and hub
        $filter = new ReamusDestinationStationFilter();
        $filter->AddPostcodeFilter($this->postcode);
        $filter->AddProductCodeFilter($this->product_code);
        //
        $list = ReamusDestinationStation::getFilteredList($filter);

        if (sizeof($list) == 0) {
            $this->error_list[] = "Unable to find postcode.";
            return;
        }
        $this->postcode_valid_flag = true; // known postcode
        $destinationStation = $list[0];

        // Check that the destination supports requested service
        $filter = new ReamusServiceProductFilter();
        $filter->addReamusIdFilter($destinationStation->getStationId());
        $filter->addProductCodeFilter($this->product_code);
        $filter->addFeatureCodeFilter($this->feature_code);
        //
        $list = ReamusServiceProduct::getFilteredList($filter);

        if (sizeof($list) == 0) {
            $this->error_list[] = "Service not available for this postcode.";
            return;
        }
        $serviceProduct = $list[0];

        // If there are exceptions for this service, check if service is available
        if ($serviceProduct->getException() == "E") {
            $filter = new ReamusExceptionFilter();
            $filter->AddPostcodeFilter($this->postcode);
            $filter->addProductCodeFilter($this->product_code);
            $filter->addFeatureCodeFilter($this->feature_code);
            //
            $list = ReamusException::getFilteredList($filter);
            if (sizeof($list) == 0) {
                $this->error_list[] = "Service not available for this postcode.";
                return;
            }
        }

        // Reached this point, service available to given postcode, set the station and hub
        $site = ReamusSite::getSiteFromReamusId($destinationStation->getStationId());
        if (count($site) > 0)
            $this->station = $site->getSite();
        else {
            $this->error_list[] = "Service not available for this postcode.";
            return;
        }


        $site = ReamusSite::getSiteFromReamusId($destinationStation->getHubId());
        if (count($site) > 0)
            $this->hub = $site->getSite();
        else {
            $this->error_list[] = "Service not available for this postcode.";
            return;
        }
    }

    /*     * *
     * Get the station associated with specified $postcode
     */

    public function getStation() {
        $this->performLookup();
        return $this->station;
    }

    /*     * *
     * Get the hub associated with specified postcode
     */

    public function getHub() {
        $this->performLookup();
        return $this->hub;
    }

    /*     * *
     * Gives error description if there is a problem.
     */

    public function getErrorList() {
        return $this->error_list;
    }

}
