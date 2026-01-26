<?php
/**
 * Uploads the tarrif values from a file
 *
 */
class TariffUpload
{
	private $dataFile = "";
	private $perc = 0;
	private $debug_mode = false;
	private $update_chargeable_tariff = true;
	private $update_chargeable_tariff_new = '';
	private $action_count = 0;
	// There is an option to use price breaks rather than load all values.
	private $load_all_tariffs = true;

	/**
	 * Pass the path to the location of data file
	 * holding prices
	 *
	 * @param string $priceFilePath
	 */
	public function __construct ($priceFilePath)
	{
		$this->dataFile = $priceFilePath;
	}

	/**
	 * All the upload prices to be varied by given percentage
	 *
	 * @param unknown_type $perc
	 */
	public function setPriceVariation ($perc)
	{
		$this->perc = $perc;
	 	if ($this->perc < -100) $this->perc = -100;
	 	if ($this->perc > 100) $this->perc = 100;
	}

	public function setToUpdateCostTariffs ($flag)
	{
		if(trim($flag) == 'costunit')
		{
			$this->update_chargeable_tariff = 'costunit';
		}
		elseif(trim($flag) == 'chargeableunit')
		{
			$this->update_chargeable_tariff = 'chargeableunit';
		}
		else
			$this->update_chargeable_tariff = !$flag;
			
	}

	/**
	 * Extends the page time out if the number of actions is too great.
	 *
	 */
	private function checkActionCount()
	{
 		// This is a long process, allow extra time
 		if (($this->action_count % 100) == 0)
 		{
 			echo "<br />" . " Tarrif has been updated Successfully";
			echo "<br />" . $this->action_count . " tariffs checked/updated. Please wait...";
 			echo str_pad(" ", 4096); // this is rubbish!! but buffer only flushed is longer than 4096, so fill!!
 			flush();
 			set_time_limit(30);
 		}
		
 		++$this->action_count;
	}

	public function setDebugMode($debugOn)
	{
		$this->debug_mode = $debugOn;
	}


	private function msg($msg)
	{
		if ($this->debug_mode) echo $msg;
	}

	/**
	 * Enter description here...
	 *
	 */
	public function updateAll($serviceId, $coll_rateband_id, $reloadPrices=false, $coll_postcode_group_id = 0, $dest_postcode_group_id = 0, $type="", $customer_id = "", $formula = "")
	{
            // Get the list of ratebands
            $ratebandFilter = new RatebandFilter();
            $ratebandFilter->addFieldFilter("courier_service_id", $serviceId);
            $ratebandList = $ratebandFilter->getList();
		
            if($type!="add_new") 
                {
                    $this->uploadFromFile($serviceId, $ratebandList, $coll_rateband_id, $coll_postcode_group_id, $dest_postcode_group_id, $reloadPrices=false,$val=true,$customer_id,$formula ); 
                }

            else
                {
                   $this->upload($serviceId, $ratebandList, $coll_rateband_id, $coll_postcode_group_id, $dest_postcode_group_id, $reloadPrices=false,$val=true,$customer_id,$formula); 
                }
	}

	private function uploadFromFile($serviceId, $dest_rateband_array, $coll_rateband_id, $coll_postcode_group_id, $dest_postcode_group_id, $reloadPrices, $val,$customer_id="",$formula="")
	{
		// Check file exists
		if (!file_exists($this->dataFile))
		{
			return false;
		}
	 	$firstLine = true;
	 	$firstDataLine = true;
	 	$dataColumnFound = false;
	 	$gotRateBandRow = false;
	 	$newWeight = 0;
	 	// New values
	 	$newUnitSize = array();
	 	$newPrice = array();
	 	$newUnitCost = array();
	 	// last values
	 	$lastWeight = array();
	 	$lastUnitSize = array();
	 	$lastPrice = array();
	 	$lastUnitCost = array();
	 	// from values
	 	$fromWeight = array();
	 	$fromPrice = array();
	 	//
	 	$rateband_array = array();
	 	//
 		$this->msg("<p>Price variation: " . $this->perc . "</p>");
 		//
	 	$hndl = fopen($this->dataFile, "r");
		$rows = file($this->dataFile);
		$last_row = array_pop($rows);
		 $totalRowCsv = str_getcsv($last_row);
		
		while ($line = fgets($hndl))
	 	{
                    $lineArray = split(",", $line);
                    // Look for "Kgs", so we know we're at rate band header row
                    if (!$gotRateBandRow)
                    {
                        if (sizeof ($lineArray) > 0)
                        {
                            if (trim(strtolower($lineArray[0])) == "kgs")
                            {
                                $gotRateBandRow = true;
                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                    // First line contains rate band details
                    if ($firstLine)
                    {
                        $this->msg ("<table border=1>");
                        //
                        for ($i=0; $i < sizeof ($lineArray); $i++)
                        {
                            $this->msg("<tr><td>Column</td><td>$i</td><td>" . $lineArray[$i] . "</td>");
                            // collumn array holding rate band for the column (if match found)
                            $rateband_array[] = null;
                            //
                            $newPrice [] = 0;
                            $newUnitSize [] = 0;
                            $newUnitCost [] = 0;
                            //
                            $lastWeight [] = 0;
                            $lastUnitSize [] = 0;
                            $lastPrice [] = 0;
                            $lastUnitCost [] = 0;
                            //
                            $fromWeight[] = 0;
                            $fromPrice[] = 0;
                            //
                            foreach ($dest_rateband_array as $rateband)
                            {
                                    if (strtolower(trim($lineArray[$i])) == strtolower($rateband->getName()))
                                    {
                                            $this->msg("<td>Rateband Found</td>");
                                            $rateband_array[sizeof($rateband_array) - 1] = $rateband;
                                            $dataColumnFound = true;
                                            break;
                                    }
                            }
                            $this->msg ("</tr>");
                        }
                        $firstLine = false;
                        $this->msg ("</table><br><table border=1>");

                        // data column not found
                        if (!$dataColumnFound) return false;
                        continue;
                    }
                    // line containing data found
                    if ($firstDataLine)
                    {
                        // Clear current values ready to load new values
                        // delete all tariffs for this courier service
                        if ($reloadPrices)
                        {
                            foreach ($rateband_array as $rateband)
                            {
                                if ($rateband == null) continue;

                                $TarDelObj = new Tariff;
                                $TarDelObj->setCourierServiceId($serviceId);
                                $TarDelObj->setCollectionRatebandId($coll_rateband_id);
                                $TarDelObj->setDestinationRatebandId($rateband->getId());
                                //$TarDelObj->setCollectionPostcodeGroupId($coll_postcode_group_id);
                                //$TarDelObj->setDestinationPostcodeGroupId($dest_postcode_group_id);
                                $TarDelObj->setCustomerId( $customer_id );
                                $TarDelObj->setFormula( $formula );
                                $TarDelObj->expunge();
                            }
                        }
                        //
                        $firstDataLine = false;
                    }

                    // Weight in first column - if not greater than zero, then at end of list.
                    $newWeight = doubleval($lineArray[0]);
                    if ($newWeight <= 0) break;
                    $this->msg("<tr>");
                    $this->msg("<td>$newWeight Kg</td> ");
                    // Check if a column has title corrosponding to a rate band
                    for ($col_idx=0; $col_idx < sizeof($rateband_array); $col_idx++)
                    {
                        if ($rateband_array[$col_idx] == null) continue;
                        $newUnitSize[$col_idx] = doubleval($lineArray[0]) - $lastWeight[$col_idx];
                        $newPrice[$col_idx] = doubleval($lineArray[$col_idx]);
                        $newUnitCost[$col_idx] = '0';//round($newPrice[$col_idx] - $lastPrice[$col_idx], 3);
                        $this->msg("<td>&nbsp;-&nbsp;</td> ");
                        $this->msg("<td><b>" . $rateband_array[$col_idx]->getName()  . "</b></td>");
                        $this->msg("<td>" . $newUnitSize[$col_idx] . " Kg</td> ");
                        if ($reloadPrices)
                        {
                                if (
                                        ($lastUnitSize[$col_idx] != $newUnitSize[$col_idx]) 
                                        || ($lastUnitCost[$col_idx] != $newUnitCost[$col_idx]) 
                                        || ($this->load_all_tariffs)
                                    )
                                {
                                    $this->msg("<td>" . $newPrice[$col_idx] . "</td> ");
                                    $this->msg("<td>" . $newUnitCost[$col_idx] . "</td> ");
                                    //
                                    $tariff = new Tariff();
                                    $tariff->setCustomerId( $customer_id );
                                    $tariff->setFormula( $formula );
                                    $tariff->setCourierServiceId($serviceId);
                                    $tariff->setCollectionRatebandId($coll_rateband_id);
                                    $tariff->setDestinationRatebandId($rateband_array[$col_idx]->getId());
                                    //$tariff->setCollectionPostcodeGroupId($coll_postcode_group_id);
                                    //$tariff->setDestinationPostcodeGroupId($dest_postcode_group_id);
                                    //
                                    $tariff->setUnitSize($lastUnitSize[$col_idx]);
                                    $tariff->setWeightFrom($fromWeight[$col_idx]);
                                    $tariff->setWeightTo($newWeight);
                                    if ($this->update_chargeable_tariff)
                                    {
                                        $tariff->setAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
                                        $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                                    }
                                    else
                                    {
                                        $tariff->setExtraAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
                                        $tariff->setExtraTariff($this->calcPrice((($fromPrice[$col_idx] == 0) ? $newPrice[$col_idx] : $fromPrice[$col_idx])));
                                    }
                                    //
                                    $tariff->setActive(1);
                                    $tariff->setDeletedq('N');
                                    $tariff->setChangedOn(date('Y-m-d h:i:s',time()));
                                    $tariff->setChangedBy($_SESSION['admin']["user_account"]);
                                    $tariff->setAddedOn(date('Y-m-d h:i:s',time()));
                                    $tariff->setAddedBy($_SESSION['admin']["user_account"]);	

                                    $tariff->save();
                                    $this->checkActionCount();

                                    // Only update the from values when saved
                                    $fromWeight[$col_idx] = $newWeight;
                                    $fromPrice [$col_idx] = $newPrice[$col_idx];
                                }

                        }
                        //*** Update existing prices rather than reload
                        else
                        {
                            $TarObjFilter = new TariffFilter();
                            $TarObjFilter->addFieldFilter('courier_service_id',$serviceId);
                            $TarObjFilter->addFieldFilter('collection_rateband_id',$coll_rateband_id );
                            $TarObjFilter->addFieldFilter('destination_rateband_id',$rateband_array[$col_idx]->getId());
                           //$TarObjFilter->addFieldFilter('collection_postcode_group_id',$coll_postcode_group_id );
                            //$TarObjFilter->addFieldFilter('destination_postcode_group_id', $dest_postcode_group_id);
                            $TarObjFilter->addFieldFilter('weight_from', $lastWeight[$col_idx]);
                            $TarObjFilter->addFieldFilter('customer_id', $customer_id);
                            $tariff_list = $TarObjFilter->getList();
                            $this->checkActionCount();
                            if (sizeof($tariff_list) > 0)
                            {
                                $this->msg("<td>" . $newPrice[$col_idx] . "</td> ");
                                $this->msg("<td>" . $newUnitCost[$col_idx] . "</td> ");
                                $tariff = $tariff_list[0];
                                $update_flag = false;
                                $tariff_val = $this->calcPrice($newPrice[$col_idx]);
                                $unit_cost = $this->calcPrice($newUnitCost[$col_idx]);
                                if (trim($this->update_chargeable_tariff)	==	'costunit')
                                {
                                    if ($tariff->getExtraAddUnitCost() != $tariff_val) 
                                    {
                                        $update_flag = true;
                                        $tariff->setExtraAddUnitCost($tariff_val);
                                    }
                                }
                                elseif (trim($this->update_chargeable_tariff)	==	'chargeableunit')
                                {
                                    if ($tariff->getAddUnitCost() != $tariff_val) 
                                    {
                                        $update_flag = true;
                                        $tariff->setAddUnitCost($tariff_val);
                                    }
                                }
                                else  if ($this->update_chargeable_tariff)
                                {
                                    if ($tariff->getTariff() != $tariff_val) 
                                    {
                                        $update_flag = true;
                                        $tariff->setTariff($tariff_val);
                                    }
                                    if ($tariff->getAddUnitCost() != $tariff_val) 
                                    {
                                        $update_flag = true;
                                        $tariff->setAddUnitCost($unit_cost);
                                    }
                                }
                                else
                                {
                                    if ($tariff->getTariff() != $tariff_val) 
                                    {
                                        $update_flag = true;
                                        $tariff->setExtraTariff($tariff_val);
                                    }
                                    if ($tariff->getAddUnitCost() != $tariff_val)
                                    {
                                        $update_flag = true;
                                        $tariff->setExtraAddUnitCost($unit_cost);
                                    }
                                }
                                if ($update_flag)
                                {
                                    $tariff->setCustomerId( $customer_id );
                                    $tariff->setFormula( $formula );
                                    $tariff->setActive(1);
                                    $tariff->setDeletedq('N');
                                    $tariff->setChangedOn(date('Y-m-d h:i:s',time()));
                                    $tariff->setChangedBy($_SESSION['admin']["user_account"]);
                                    $tariff->setAddedOn(date('Y-m-d h:i:s',time()));
                                    $tariff->setAddedBy($_SESSION['admin']["user_account"]);	
                                    $tariff->save();
                                    $this->checkActionCount();
                                }
                                // There is an additional line at the beginning
                                // of tarrif table that captures the 0-(initial weight) case.
                                // If the last weight was zero, then at start of tariffs,
                                // find and update this addtional line
                                if ($lastWeight == 0)
                                {
                                    // Find the corrosponding tariff
                                    $TarObjFilter = new TariffFilter();
                                    $TarObjFilter->addFieldFilter('courier_service_id',$serviceId);
                                    $TarObjFilter->addFieldFilter('collection_rateband_id',$coll_rateband_id );
                                    $TarObjFilter->addFieldFilter('weight_from', 0);
                                    $TarObjFilter->addFieldFilter('customer_id', $customer_id);
                                    $tariff_list = $TarObjFilter->getList();
                                    if (sizeof($tariff_list) > 0)
                                    {
                                        $tariff = $tariff_list[0];
                                        $tariff->setCustomerId( $customer_id );
                                        $tariff->setFormula( $formula );
                                        if (trim($this->update_chargeable_tariff)	==	'costunit')
                                        {
                                            if ($tariff->getExtraAddUnitCost() != $tariff_val) $update_flag = true;
                                            $tariff->setExtraAddUnitCost($tariff_val);
                                        }
                                        elseif (trim($this->update_chargeable_tariff)	==	'chargeableunit')
                                        {
                                            if ($tariff->getAddUnitCost() != $tariff_val) $update_flag = true;
                                            $tariff->setAddUnitCost($tariff_val);
                                        }
                                        else  if ($this->update_chargeable_tariff)
                                        {
                                            $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                                        }
                                        else
                                        {
                                            $tariff->setExtraTariff($this->calcPrice($newPrice[$col_idx]));
                                        }
                                        $tariff->setActive(1);
                                        $tariff->setDeletedq('N');
                                        $tariff->setChangedOn(date('Y-m-d h:i:s',time()));
                                        $tariff->setChangedBy($_SESSION['admin']["user_account"]);
                                        $tariff->setAddedOn(date('Y-m-d h:i:s',time()));
                                        $tariff->setAddedBy($_SESSION['admin']["user_account"]);
                                        $tariff->save();
                                    }
                                }
                            }
                            else
                            {
                                    $this->msg("<td colspan=2>Not found</td> ");
                            }
                        }
                        //
                        $lastWeight[$col_idx] = $newWeight;
                        $lastUnitCost[$col_idx] = $newUnitCost[$col_idx];
                        $lastPrice[$col_idx] = $newPrice[$col_idx];
                        $lastUnitSize[$col_idx] = $newUnitSize[$col_idx];
                    }
                    $this->msg("</tr>");
	 	}
		
	 	fclose($hndl);
		
	 	if ($reloadPrices)
	 	{
                    // add the final tariff
                    for ($col_idx=0; $col_idx < sizeof($rateband_array); $col_idx++)
                    {
                        if ($rateband_array[$col_idx] == null) continue;
                        $tariff = new Tariff();
                        $tariff->setCustomerId( $customer_id );
                        $tariff->setFormula( $formula );
                        $tariff->setCourierServiceId($serviceId);
                        $tariff->setCollectionRatebandId($coll_rateband_id);
                        $tariff->setDestinationRatebandId($rateband_array[$col_idx]->getId());
                        $tariff->setCollectionPostcodeGroupId($coll_postcode_group_id);
                        $tariff->setDestinationPostcodeGroupId($dest_postcode_group_id);
                        $tariff->setUnitSize($newUnitSize);
                        $tariff->setWeightFrom($lastWeight[$col_idx]);
                        $tariff->setWeightTo(999999);
                        $tariff->setActive(1);
                        $tariff->setDeletedq('N');
                        $tariff->setChangedOn(date('Y-m-d h:i:s',time()));
                        $tariff->setChangedBy($_SESSION['admin']["user_account"]);
                        $tariff->setAddedOn(date('Y-m-d h:i:s',time()));
                        $tariff->setAddedBy($_SESSION['admin']["user_account"]);	
                        if ($this->update_chargeable_tariff)
                        {
                                $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                                $tariff->setAddUnitCost($this->calcPrice($newUnitCost[$col_idx]));
                        }
                        else
                        {
                                $tariff->setExtraTariff($this->calcPrice($newPrice[$col_idx]));
                                $tariff->setExtraAddUnitCost($this->calcPrice($newUnitCost[$col_idx]));
                        }
                        //
                        $tariff->save();
                    }
	 	}
	 	if ($this->debug_mode) echo "</table>";
                    return true;
	}
    
    
    private function upload($serviceId, $dest_rateband_array, $coll_rateband_id, $coll_postcode_group_id, $dest_postcode_group_id, $reloadPrices,$addnew,$customer_id="", $formula="")
    {
		// Check file exists
        if (!file_exists($this->dataFile))
        {
            return false;
        }

         $firstLine = true;
         $firstDataLine = true;
         $dataColumnFound = false;
         $gotRateBandRow = false;

         //
         $newWeight = 0;
         // New values
         $newUnitSize = array();
         $newPrice = array();
         $newUnitCost = array();
         // last values
         $lastWeight = array();
         $lastUnitSize = array();
         $lastPrice = array();
         $lastUnitCost = array();
         // from values
         $fromWeight = array();
         $fromPrice = array();
         //
         $rateband_array = array();
         //
         $this->msg("<p>Price variation: " . $this->perc . "</p>");
         //
         $hndl = fopen($this->dataFile, "r");
         while ($line = fgets($hndl))
         {
			
             $lineArray = split(",", $line);
			
             // Look for "Kgs", so we know we're at rate band header row
             if (!$gotRateBandRow)
             {
                 //
                 if (sizeof ($lineArray) > 0)
                 {
                     if (trim(strtolower($lineArray[0])) == "kgs")
                     {
                         $gotRateBandRow = true;
                     }
                     else
                     {
                         continue;
                     }
                 }
             }

             // First line contains rate band details
			
             if ($firstLine)
             {
                 $this->msg ("<table border=1>");
                 //
                 for ($i=0; $i < sizeof ($lineArray); $i++)
                 {
                     $this->msg("<tr><td>Column</td><td>$i</td><td>" . $lineArray[$i] . "</td>");

                     // collumn array holding rate band for the column (if match found)
                     $rateband_array[] = null;
                     //
                     $newPrice [] = 0;
                     $newUnitSize [] = 0;
                     $newUnitCost [] = 0;
                     //
                     $lastWeight [] = 0;
                     $lastUnitSize [] = 0;
                     $lastPrice [] = 0;
                     $lastUnitCost [] = 0;
                     //
                     $fromWeight[] = 0;
                     $fromPrice[] = 0;
                     //
                     foreach ($dest_rateband_array as $rateband)
                     {
                         if (strtolower(trim($lineArray[$i])) == strtolower($rateband->getName()))
                         {
							$this->msg("<td>Rateband Found</td>");
							$rateband_array[sizeof($rateband_array) - 1] = $rateband;
							$dataColumnFound = true;
							break;
                         }
                     }
                     $this->msg ("</tr>");
                 }
				 
                 $firstLine = false;
                 $this->msg ("</table><br><table border=1>");
			

                 // data column not found
                 if (!$dataColumnFound) 
				 	return false;
                 continue;
             }
             // line containing data found
             if ($firstDataLine)
             {
                 // Clear current values ready to load new values
                // delete all tariffs for this courier service
                 if ($reloadPrices)
                 {
                     foreach ($rateband_array as $rateband)
                     {
                         if ($rateband == null) continue;

                        $TarDelObj = new Tariff;
                        $TarDelObj->setCourierServiceId($serviceId);
                        $TarDelObj->setCollectionRatebandId($coll_rateband_id);
                        $TarDelObj->setDestinationRatebandId($rateband->getId());
                        $TarDelObj->setCollectionPostcodeGroupId($coll_postcode_group_id);
                        $TarDelObj->setDestinationPostcodeGroupId($dest_postcode_group_id);
                        $TarDelObj->setCustomerId( $customer_id );
                        $TarDelObj->expunge();
                     }
                 }
                 //
                 $firstDataLine = false;
             }

             // Weight in first column - if not greater than zero, then at end of list.
             $newWeight = doubleval($lineArray[0]);
             if ($newWeight <= 0) break;
             //
            $this->msg("<tr>");
            $this->msg("<td>$newWeight Kg</td> ");
			//echo sizeof($rateband_array). ' -- '. $lineArray[0] ;
			//echo "<br>";
			
            // Check if a column has title corrosponding to a rate band
            for ($col_idx=0; $col_idx < sizeof($rateband_array); $col_idx++)
            {
                if ($rateband_array[$col_idx] == null) continue;
                //
                $newUnitSize[$col_idx] 	= doubleval($lineArray[0]) - $lastWeight[$col_idx];
                $newPrice[$col_idx] 	= doubleval($lineArray[$col_idx]);
                $newUnitCost[$col_idx] 	= '0';//round($newPrice[$col_idx] - $lastPrice[$col_idx], 3);
                 //
                 $this->msg("<td>&nbsp;-&nbsp;</td> ");
                 $this->msg("<td><b>" . $rateband_array[$col_idx]->getName()  . "</b></td>");
                 $this->msg("<td>" . $newUnitSize[$col_idx] . " Kg</td> ");
                //
                if ($reloadPrices)
                {
                     // has it changed
                     if (($lastUnitSize[$col_idx] != $newUnitSize[$col_idx]) || ($lastUnitCost[$col_idx] != $newUnitCost[$col_idx]) || ($this->load_all_tariffs))
                     {
                         $this->msg("<td>" . $newPrice[$col_idx] . "</td> ");
                         $this->msg("<td>" . $newUnitCost[$col_idx] . "</td> ");
                         //
                         $tariff = new Tariff();
						 $tariff->setCustomerId( $customer_id );
						 $tariff->setFormula( $formula );
                         $tariff->setCourierServiceId($serviceId);
                         $tariff->setCollectionRatebandId($coll_rateband_id);
                         $tariff->setDestinationRatebandId($rateband_array[$col_idx]->getId());
                         $tariff->setCollectionPostcodeGroupId($coll_postcode_group_id);
                         $tariff->setDestinationPostcodeGroupId($dest_postcode_group_id);
                         //
                         $tariff->setUnitSize($lastUnitSize[$col_idx]);
                         $tariff->setWeightFrom($fromWeight[$col_idx]);
                         $tariff->setWeightTo($newWeight);
                         //
                         if ($this->update_chargeable_tariff)
                         {
                             $tariff->setAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
							 //$this->calcPrice((($fromPrice[$col_idx] == 0) ? $newPrice[$col_idx] : $fromPrice[$col_idx]))
                             $tariff->setTariff($this->calcPrice( $newPrice[$col_idx]));
                         }
                         else
                         {
                             $tariff->setExtraAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
                             $tariff->setExtraTariff($this->calcPrice((($fromPrice[$col_idx] == 0) ? $newPrice[$col_idx] : $fromPrice[$col_idx])));
                         }
                         //
						 $tariff->setActive(1);
						$tariff->setDeletedq('N');
						$tariff->setChangedOn(date('Y-m-d h:i:s',time()));
						$tariff->setChangedBy($_SESSION['admin']["user_account"]);
						$tariff->setAddedOn(date('Y-m-d h:i:s',time()));
						$tariff->setAddedBy($_SESSION['admin']["user_account"]);
                         $tariff->save();
                         $this->checkActionCount();

                         // Only update the from values when saved
                         $fromWeight[$col_idx] = $newWeight;
                         $fromPrice [$col_idx] = $newPrice[$col_idx];
                     }
                }
                
        		if ($addnew)
                {
					
                     // has it changed
                     if (($lastUnitSize[$col_idx] != $newUnitSize[$col_idx]) || ($lastUnitCost[$col_idx] != $newUnitCost[$col_idx]) || ($this->load_all_tariffs))
                     {//echo "<br> >>".$line.">> <br>";
                         $this->msg("<td>" . $newPrice[$col_idx] . "</td> ");
                         $this->msg("<td>" . $newUnitCost[$col_idx] . "</td> ");
                         //
                         $tariff = new Tariff();
						 $tariff->setCustomerId( $customer_id );
						 $tariff->setFormula( $formula );
                         $tariff->setCourierServiceId($serviceId);
                         $tariff->setCollectionRatebandId($coll_rateband_id);
                         $tariff->setDestinationRatebandId($rateband_array[$col_idx]->getId());
                         $tariff->setCollectionPostcodeGroupId($coll_postcode_group_id);
                         $tariff->setDestinationPostcodeGroupId($dest_postcode_group_id);
                         //
                         $tariff->setUnitSize($lastUnitSize[$col_idx]);
                         $tariff->setWeightFrom($fromWeight[$col_idx]);
                         $tariff->setWeightTo($newWeight);
                         //
                         if ($this->update_chargeable_tariff)
                         {
                             $tariff->setAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
							 //(($fromPrice[$col_idx] == 0) ? $newPrice[$col_idx] : $fromPrice[$col_idx]))
                             $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                         }
                         else
                         {
                            $tariff->setExtraAddUnitCost($this->calcPrice($lastUnitCost[$col_idx]));
							$tariff->setExtraTariff($this->calcPrice($newPrice[$col_idx]));
                         }
						 $tariff->setActive(1);
						$tariff->setDeletedq('N');
						$tariff->setChangedOn(date('Y-m-d h:i:s',time()));
						$tariff->setChangedBy($_SESSION['admin']["user_account"]);
						$tariff->setAddedOn(date('Y-m-d h:i:s',time()));
						$tariff->setAddedBy($_SESSION['admin']["user_account"]);
                         $tariff->save();
                         $this->checkActionCount();

                         // Only update the from values when saved
                         $fromWeight[$col_idx] = $newWeight;
                         $fromPrice [$col_idx] = $newPrice[$col_idx];
                     }
					// else
					 	// echo "<br> --".$line."-- <br>";
                }
                
                
                //*** Update existing prices rather than reload
                else
                {
					//echo "<br> <<".$line."<< <br>";
					
                    // Find the corrosponding tariff
             /*       $where = "courier_service_id=$serviceId " .
                            " AND collection_rateband_id=" . $coll_rateband_id .
                            " AND destination_rateband_id=" . $rateband_array[$col_idx]->getId()  .
                            " AND collection_postcode_group_id=" . $coll_postcode_group_id  .
                            " AND destination_rateband_id=" . $rateband_array[$col_idx]->getId()  .
                            " AND destination_postcode_group_id=" . $dest_postcode_group_id  .
                            " AND weight_from= " .$newWeight;
                    //
                    $tariff_list = Tariff::listFactory($where);*/
					
					$TarObjFilter = new TariffFilter();
					$TarObjFilter->addFieldFilter('courier_service_id',$serviceId);
					$TarObjFilter->addFieldFilter('collection_rateband_id',$coll_rateband_id );
					$TarObjFilter->addFieldFilter('destination_rateband_id',$rateband_array[$col_idx]->getId());
					$TarObjFilter->addFieldFilter('collection_postcode_group_id',$coll_postcode_group_id );
					$TarObjFilter->addFieldFilter('destination_postcode_group_id', $dest_postcode_group_id);
					$TarObjFilter->addFieldFilter('weight_from', $newWeight);
					$TarObjFilter->addFieldFilter('customer_id', $customer_id);
					$tariff_list = $TarObjFilter->getList();
					
                    $this->checkActionCount();

                    if (sizeof($tariff_list) > 0)
                    {
                         $this->msg("<td>" . $newPrice[$col_idx] . "</td> ");
                         $this->msg("<td>" . $newUnitCost[$col_idx] . "</td> ");
                        //
                        $tariff = $tariff_list[0];
                        //
                        $update_flag = false;
                         $tariff_val = $this->calcPrice($newPrice[$col_idx]);
                         $unit_cost = $this->calcPrice($newUnitCost[$col_idx]);
                        //
                       if (trim($this->update_chargeable_tariff)	==	'costunit')
						{
							if ($tariff->getExtraAddUnitCost() != $tariff_val) $update_flag = true;
							$tariff->setExtraAddUnitCost($tariff_val);
						}
						elseif (trim($this->update_chargeable_tariff)	==	'chargeableunit')
						{
							if ($tariff->getAddUnitCost() != $tariff_val) $update_flag = true;
							$tariff->setAddUnitCost($tariff_val);
						}
						else  if ($this->update_chargeable_tariff)
                         {
                             if ($tariff->getTariff() != $tariff_val) $update_flag = true;
                             if ($tariff->getAddUnitCost() != $tariff_val) $update_flag = true;
                             $tariff->setTariff($tariff_val);
                             $tariff->setAddUnitCost($unit_cost);
                         }
                         else
                         {
                             if ($tariff->getCourierTariff() != $tariff_val) $update_flag = true;
                             if ($tariff->getCourierUnitCost() != $tariff_val) $update_flag = true;
                             $tariff->setExtraTariff($tariff_val);
                             $tariff->setExtraAddUnitCost($unit_cost);
                         }
                         if ($update_flag)
                         {
							 $tariff->setCustomerId( $customer_id );
							 $tariff->setFormula( $formula );
							$tariff->setActive(1);
							$tariff->setDeletedq('N');
							$tariff->setChangedOn(date('Y-m-d h:i:s',time()));
							$tariff->setChangedBy($_SESSION['admin']["user_account"]);
							$tariff->setAddedOn(date('Y-m-d h:i:s',time()));
							$tariff->setAddedBy($_SESSION['admin']["user_account"]);
                             $tariff->save();
                             $this->checkActionCount();
                         }
                        // There is an additional line at the beginning
                        // of tarrif table that captures the 0-(initial weight) case.
                        // If the last weight was zero, then at start of tariffs,
                        // find and update this addtional line
                        if ($lastWeight == 0)
                        {
                            // Find the corrosponding tariff
                           // $where = "courier_service_id=$serviceId " .
                           //         " AND weight_from=0 ";
                            //
                         //   $tariff_list = Tariff::listFactory($where);
							
								$TarObjFilter = new TariffFilter();
								$TarObjFilter->addFieldFilter('courier_service_id',$serviceId);
								$TarObjFilter->addFieldFilter('weight_from', 0);
								$TarObjFilter->addFieldFilter('customer_id', $customer_id);
								$tariff_list = $TarObjFilter->getList();
					
					
					
                            if (sizeof($tariff_list) > 0)
                            {
                                $tariff = $tariff_list[0];
								$tariff->setCustomerId( $customer_id );
								$tariff->setFormula( $formula );
                                if (trim($this->update_chargeable_tariff)	==	'costunit')
								{
									if ($tariff->getExtraAddUnitCost() != $tariff_val) $update_flag = true;
									$tariff->setExtraAddUnitCost($tariff_val);
								}
								elseif (trim($this->update_chargeable_tariff)	==	'chargeableunit')
								{
									if ($tariff->getAddUnitCost() != $tariff_val) $update_flag = true;
									$tariff->setAddUnitCost($tariff_val);
								}
								else  if ($this->update_chargeable_tariff)
                                 {
                                     $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                                 }
                                 else
                                 {
                                     $tariff->setCourierTariff($this->calcPrice($newPrice[$col_idx]));
                                 }
								$tariff->setActive(1);
								$tariff->setDeletedq('N');
								$tariff->setChangedOn(date('Y-m-d h:i:s',time()));
								$tariff->setChangedBy($_SESSION['admin']["user_account"]);
								$tariff->setAddedOn(date('Y-m-d h:i:s',time()));
								$tariff->setAddedBy($_SESSION['admin']["user_account"]);
                                $tariff->save();
                            }
                        }
                    }
                    else
                    {
                        $this->msg("<td colspan=2>Not found</td> ");
                    }
                }
                //
                $lastWeight[$col_idx] = $newWeight;
                 $lastUnitCost[$col_idx] = $newUnitCost[$col_idx];
                 $lastPrice[$col_idx] = $newPrice[$col_idx];
                 $lastUnitSize[$col_idx] = $newUnitSize[$col_idx];
            }
            //
             $this->msg("</tr>");
         }
		
         fclose($hndl);
         if ($reloadPrices)
         {
			
             // add the final tariff
            for ($col_idx=0; $col_idx < sizeof($rateband_array); $col_idx++)
            {
                if ($rateband_array[$col_idx] == null) continue;


                 $tariff = new Tariff();
				 $tariff->setCustomerId( $customer_id );
				 $tariff->setFormula( $formula );
                 $tariff->setCourierServiceId($serviceId);
                 $tariff->setCollectionRatebandId($coll_rateband_id);
                 $tariff->setDestinationRatebandId($rateband_array[$col_idx]->getId());
                 $tariff->setCollectionPostcodeGroupId($coll_postcode_group_id);
                 $tariff->setDestinationPostcodeGroupId($dest_postcode_group_id);
                 //
                 $tariff->setUnitSize($newUnitSize);
                 $tariff->setWeightFrom($lastWeight[$col_idx]);
                 $tariff->setWeightTo(999999);
				 
                 //
                 if ($this->update_chargeable_tariff)
                 {
                     $tariff->setTariff($this->calcPrice($newPrice[$col_idx]));
                     $tariff->setAddUnitCost($this->calcPrice($newUnitCost[$col_idx]));
                 }
                 else
                 {
                     $tariff->setExtraTariff($this->calcPrice($newPrice[$col_idx]));
                     $tariff->setExtraAddUnitCost($this->calcPrice($newUnitCost[$col_idx]));
                 }
                 $tariff->setActive(1);
				$tariff->setDeletedq('N');
				$tariff->setChangedOn(date('Y-m-d h:i:s',time()));
				$tariff->setChangedBy($_SESSION['admin']["user_account"]);
				$tariff->setAddedOn(date('Y-m-d h:i:s',time()));
				$tariff->setAddedBy($_SESSION['admin']["user_account"]);
                 $tariff->save();
             }
         }
         if ($this->debug_mode) echo "</table>";
         return true;
    }

	/**
	 * Update the tariffs
	 *
	 * @param int $service_id
	 * @param int $coll_rateband_id
	 * @param int $dest_rateband_id
	 * @param int $reloadPrices
	 * @param int $coll_postcode_group_id
	 * @param int $dest_postcode_group_id
	 */
	public function update ($serviceId, $coll_rateband_id, $dest_rateband_id,
		$reloadPrices=false, $coll_postcode_group_id = 0, $dest_postcode_group_id=0 )
	{
		$destRateBand = new RateBand($dest_rateband_id);
		$rateband_array = array($destRateBand);
		//
		$this->uploadFromFile($serviceId, $rateband_array, $coll_rateband_id, $coll_postcode_group_id, $dest_postcode_group_id, $reloadPrices);
	}

	private function calcPrice ($price)
	{
		// New price = $price + ($perc * $price) / 100
		return round ($price + (($price * $this->perc) / 100), 2);
	}	
}