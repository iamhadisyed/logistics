<?php

class LabelFormatting
{
	/**
	 * Formats a licence plate number in human readable format.
	 *
	 * @param string $lp
	 * @return string
	 */
	public static function humanReadableLicncePlate ($lp)
	{

            $lp = substr($lp, 0, 2) . " " .
            substr($lp, 2, 3) . " " .
            substr($lp, 5, 3) . " " .
            substr($lp, 8, 2) . " " .
            substr($lp, 10, 4) . " " .
            substr($lp, 14, 4) . " " ;
            return $lp;
	}

	public static function createCode39BarcodeUsingPearLib ($barcode, $fileName)
	{
		require_once '../Image/Barcode/code39.php';
		//
		$bc_folder = "../_assets/barcodes";

		$absoluteFilePathName = $_SERVER["SCRIPT_FILENAME"];
		$pos = strrpos($absoluteFilePathName, "\\");
		$absoluteFilePathName = substr($absoluteFilePathName, 0, $pos);
		$absoluteFilePathName .= "/" . $bc_folder;
		$absoluteFilePathName = realpath($absoluteFilePathName);
		$absoluteFilePathName .= "\\" . $fileName . ".png";
		//
		$relativePath = ($bc_folder) . "\\" . $fileName . ".png";

		t("Barcode file, " . $absoluteFilePathName, __CLASS__);

		if (!file_exists ($absoluteFilePathName))
		{
			$bc = new Image_Barcode_Code39($barcode);
			// Get barcode and save to file
			$rs = $bc->plot(true, 65);
			if (!imagepng ($rs, $absoluteFilePathName))
			{
				//EmailSend::EmailError("Unable to create barcode \n" . $absoluteFilePathName);
				//EmailSend::EmailError("Unable to create barcode \n" . $absoluteFilePathName);
				return "";
			}
		}
		// turn error reporting back on
		
		return $absoluteFilePathName;
	}

	/**
	 * Generator barcode using pear library.
	 *
	 * @param string $barcode
	 * @param string $fileName
	 */
	public static function createBarcodeUsingPearLib ($barcode, $fileName, $type="code39")
	{
	//
		require_once "../includes/Image/Barcode.php";
		//
		$bc_folder = "../_assets/barcodes";

		$absoluteFilePathName = $_SERVER["SCRIPT_FILENAME"];
		$pos = strrpos($absoluteFilePathName, "\\");
		$absoluteFilePathName = substr($absoluteFilePathName, 0, $pos);
		$absoluteFilePathName .= "/" . $bc_folder;
		$absoluteFilePathName = realpath($absoluteFilePathName);
		$absoluteFilePathName .= "\\" . $fileName . ".png";
		//
		$relativePath = ($bc_folder) . "\\" . $fileName . ".png";

		t("Barcode file, " . $absoluteFilePathName, __CLASS__);

		if (!file_exists ($absoluteFilePathName))
		{
			// Get barcode and save to file
			$Image_Barcode	=	new Image_Barcode();
			$rs = $Image_Barcode->draw($barcode, $type, "png", false);

			if (!imagepng ($rs, $absoluteFilePathName))
			{
				//EmailSend::EmailError("Unable to create barcode \n" . $absoluteFilePathName);
				return "";
			}
		}
		// turn error reporting back on

		return $absoluteFilePathName;
	}
        
        public function recycledShipment($consignment) {
            $output["STATUS"]   =   "SUCCESS";
            return $output;
        }
}