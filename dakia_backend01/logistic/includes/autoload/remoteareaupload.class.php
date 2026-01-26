<?php
/**
 * Uploads the tarrif values from a file
 *
 */
class RemoteAreaUpload
{
	private $dataFile = "";
	private $perc = 0;
	private $debug_mode = false;
	private $update_chargeable_tariff = true;
	private $update_chargeable_tariff_new = '';
	private $action_count = 1;
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
	 * Extends the page time out if the number of actions is too great.
	 *
	 */
	private function checkActionCount()
	{
 		// This is a long process, allow extra time
 		if (($this->action_count % 100) == 0)
 		{
 			echo "<br />" . " Remote Area postcode has been updated Successfully";
			echo "<br />" . $this->action_count . "  Remote Area postcode  checked/updated. Please wait...";
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

	/**
	 * Enter description here...
	 *
	 */
	public function uploadAll()
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
         $hndl = fopen($this->dataFile, "r");
         while ($line = fgets($hndl))
         {
			
			$lineArray = split(",", $line);
			// Look for "Kgs", so we know we're at rate band header row
			if (!$gotRateBandRow)
			{
				if (sizeof ($lineArray) > 0)
				{
					$gotRateBandRow = true;
				}
			}
			$tariff = new PostcodeUserServiceCharges();
			$tariff->setFromPostcode( $lineArray[0] );
			$tariff->setToPostcode( $lineArray[1]);
			$tariff->setPostcodeName( $lineArray[2] );
			$tariff->setCityName( $lineArray[3] );
			$tariff->setCountryIso( $lineArray[4] );
			$tariff->save();

			$this->checkActionCount();
			
         }
		
         fclose($hndl);

         if ($this->debug_mode) 
		 	echo "</table>";
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
}