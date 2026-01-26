<?php
////////////////////////////////////////////////////
//
// Base Class for Courier Services
//
// Each Courier Service can have a unique way of calculating
// volume or price.
// Also each Courier Service can it's own unique method for communicating
// booking to couirer.
// This class provides base methods for courier service specific
// tasks.  The class can be inherited to override methods.
//
////////////////////////////////////////////////////
class CourierBase
{
	const BOOKING_NO_ACTION = 0;
	const BOOKING_FAILED = 1;
	const BOOKING_INPROGRESS = 2;
	const BOOKING_SENT = 3;

	/**
	 * Gets a courier service class for a given courier service class code.
	 * If class cannot be found returns the default (base) class
	 *
	 * @param the courier service class code
	 */
	public static function getCourierClass ($theClassCode)
	{
		$theClassCode = trim(strtoLower($theClassCode));

		// Check the courier used to send this parcel group as been assigned a short name
		if ($theClassCode != "")
		{
			// Look for the courier clas file
			$class_name = "Courier$theClassCode";
			$class_file = "$class_name.class.php";

			// all scripts in sub folders from root - so path is
			$class_path = strToLower("../includes/autoload/$class_file");
			//
			if (file_exists($class_path))
			{
				require_once ($class_path);
				if(class_exists($class_name))
				{
					return (new $class_name);
				}
			}
		}
		return new CourierBase();
	}

	/*
	 * The booking prococess methods
	 */
	public function startBookingProcess() {}
    public function endBookingProcess() {}
	public function asynchrous () {}

	public function processParcelGroup(Basket $basket, ParcelGroup $parcel_group)
	{
		$parcel_group->setBookingStatus(ParcelGroup::BOOKING_STATUS_FAILED);
		$parcel_group->save();

		return CourierBase::BOOKING_NO_ACTION;
	}

	/**
	 * The tracking information for specified parcel group
	 *
	 * @ return array of TrackingEvents
	 */
	public function getTrackingEvents($parcelGroup)
	{
		$eventList = array();

		$t = new TrackingEvent();

		$t->setLocation("Dummy");
		$t->setEventDateTime(time());

		$eventList[] = $t;

		return $eventList;
	}

	/**
	 * Different documentation has to be provided depending on the courier.
	 * By passing the PDF object (object used to create documentation), to
	 * each courier class, it gives opportunity for additional documentation
	 * to be added.
	 *
	 * @param the (FPDF) Pdf Object
	 * @param the Parcel group object, uses the parcelgroupid if not set.
	 */
	public function addPdfDocumentation ($thePdfObj, $basketObject, $theParcelGroupObj = null) { }

	/**
	 * Calculates the volumetric weight for a parcel.
	 * Different couriers can have different volumetric weight formulas.
	 * This is default calculation, can be overridden for each courier
	 * if necessary.
	 *
	 * @param ParcelObject $theParcel
	 */
	public function getVolumetricWeight($theParcel, $theVolumetricWeightDenominator)
	{
		if ($theVolumetricWeightDenominator == 0) return $theParcel->getTotalWeight();
		//
		return floatval((floatval($theParcel->getLength())
								* floatval($theParcel->getWidth())
								* floatval($theParcel->getHeight())) / $theVolumetricWeightDenominator);

	}
}