<?php

//@session_start();
/**
 * Each parcel is identified by a unique licence plate number.
 * Licence plate number ranges are issued by DHL.  Ranges
 * are stored in DB and used as used as required.
 *
 */
class LicencePlate extends DbAccess3 {

    const STATIC_PREFIX = "JD0002210160";     //JD0002257356= "JD0002255030";  //const STATIC_PREFIX = "JD0002255030"; JD0002257357
    const STATIC_INTERNATIONAL_ROYALMAIL_TRACK_SIGN_PREFIX = "RS";
    const STATIC_INTERNATIONAL_ROYALMAIL_SIGN_PREFIX = "RU";
    const STATIC_INTERNATIONAL_ROYALMAIL_TRACK_PREFIX = "TT";
    const STATIC_SUFFIX = "GB";
    const STATIC_ROYALMAIL_SAFE_PREFIX = "JL";
    const STATIC_ROYALMAIL_SIGN_PREFIX = "FG";
    const DPD_PL_SLID = "5730";
    const DPD_SLID = "593"; //5662
    const DPD_SLID_1KG = "5929";
    const DPD_IDENTIFICATION = "0944";
    const DPD_SLID_NL = "8552"; // "5580" 5890;
    const DPD_IDENTIFICATION_NL = "0516";
    const DAC_PREFIX = "DAC";
    const DAC_SUFFIC = "LHR";
    const ANPOST_PREFIX = "CE";
    const ANPOST_SUFFIC = "IE";
    const REGPOST_PREFIX = "RE";
    const REGPOST_SUFFIC = "SE";
    const REGPOST_AUSTRALIA_PREFIX = "LX";
    const HUNGARY_PREFIX = "RR";
    const HUNGARY_SUFFIC = "HU";
    const ESTONIA_PREFIX = "RR";
    const ESTONIA_SUFFIX = "EE";
    const PARCELFORCE_PREFIX = "FF";
    const STATIC_PREFIX_CZECH = "98874";
    const STATIC_PREFIX_CTT = "4454";
    const CZECHINT_PREFIX = "RR";
    const CZECHINT_SUFIX = "CZ";
    const RMNETH_SUFIX = "3STWQL";
    const RMNETHBE_SUFIX = "3SVLYU";
    const RMNETHUNDER10_SUFIX = "3SRM";
    const TURKEYPOST_PREFIX = "RH";
    const TURKEYPOST_SUFIX = "TR";
    const DPD_SLID_DE_DIRECT = "5161"; //5124 5157 5160
    const DPD_IDENTIFICATION_DE_DIRECT = "0150";
    const CPOST_PREFIX = "RG";
    const CPOST_SUFIX = "CW";
    const DEUTSCHEPOST_PREFIX = "RR";
    const DEUTSCHEPOST_SUFIX = "DE";
    const YPL_PREFIX = "JD0002258700";
    const CPOST_UTR_PREFIX = "CPET";
    const CPOST_UTR_SUFIX = "GB";

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'range_name' => 'string',
            'range_start' => 'number',
            'range_end' => 'number',
            'next_number' => 'number',
            'increment_date' => 'datetime',
            'delivery_network' => 'string',
            'prefix' => 'string',
            'sufix' => 'string',
            'date_created' => 'datetime',
            'date_updated' => 'datetime',
            'addedby' => 'number',
            'updatedby' => 'number',
            'country_range' => 'number',
            'country_list'  => 'string',
            'range_reminder_limit' => 'number',
            'agent_id' => 'undefined',
            'service_id' => 'undefined',
        );
        //
        parent::__construct("licence_plate", 'id', $fieldList, $mixedCreator);
    }

    public static function getLicencePlateListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRangesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get the next licence plate number - an 11 digit string.
     *
     * @return string
     */
    public static function getLicencePlateNumberRevert($deliveryNetwork = Consignment::SERVICE_DOMESTIC, $invoice_number = 0) {
        $val = 0;
        $prefix = 0;


        ///////////////////////////////////// PLEASE ROLLBACK THE BELOW CODE IF ANY PROBLEM ///////////////////////////////////////

        $host = SETTING_DB_SERVER;
        $user = SETTING_DB_USER;
        $password = SETTING_DB_PASSWORD;
        $db = SETTING_DB_DATABASE;

        $mysqli = new mysqli($host, $user, $password, $db);
        if ($mysqli->connect_errno) {
            return "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        if (!($res = $mysqli->query("call licencePlateRevert('" . $deliveryNetwork . "', @rtn,'" . $invoice_number . "')"))) {
            return "ERROR||CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
        } else {
            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                if (trim($row['@rtn']) != '') {
                    $val = $row['@rtn'];
                } else {
                    return "ERROR||Tracking number range has finished. Please contact to administrator at itsupport@oneworldexpress.com.";
                }
            } else {
                return "ERROR||Tracking number range has finished. Please contact to administrator at itsupport@oneworldexpress.com.";
            }
        }

        $res->close();
        $mysqli->close();
        $num = $val;
        return $num;
    }

    public static function getLicencePlateNumber($licenceId, $countryid = '') {

        // $licencePlate = new LicencePlate($licenceId);


        $outputArray = array();
        $val = 0;
        $prefix = 0;
        ///////////////////////////////////// PLEASE ROLLBACK THE BELOW CODE IF ANY PROBLEM ///////////////////////////////////////
        $host = SETTING_DB_SERVER;
        $user = SETTING_DB_USER;
        $password = SETTING_DB_PASSWORD;
        $db = SETTING_DB_DATABASE;
        //echo $host . " " . $user . " " . $password . " " . $db;
        $mysqli = new mysqli($host, $user, $password, $db);
        if ($mysqli->connect_errno) {
            $outputArray["STATUS"] = "ERROR";
            $outputArray["ERROR"][] = "Failed to connect to database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            $outputArray["MESSAGE"] = "Failed to connect to database: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        } else if (!($res = $mysqli->query("call trackingNumber('" . $licenceId . "', '" . $countryid . "',@rtn,@pre_fix,@su_fix)"))) {
            $outputArray["STATUS"] = "ERROR";
            $outputArray["ERROR"][] = "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
            $outputArray["MESSAGE"] = "CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
        } else if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (trim($row['@rtn']) != '') {
                $prefix = trim($row['@pre_fix']); //$licencePlate->getPrefix();
                $sufix = trim($row['@su_fix']); //= $licencePlate->getsufix();
                $val = $row['@rtn'];
                $outputArray["STATUS"] = "SUCCESS";
                $outputArray["PREFIX"] = $prefix;
                $outputArray["RANGE"] = $val;
                $outputArray["SUFIX"] = $sufix;
            } else {
                $outputArray["STATUS"] = "ERROR";
                $outputArray["ERROR"][] = "Tracking number range has finished. Please contact to administrator at itsupport@oneworldexpress.com.";
                $outputArray["MESSAGE"] = "Tracking number range has finished. Please contact to administrator at itsupport@oneworldexpress.com.";
            }
        } else {
            $outputArray["STATUS"] = "ERROR";
            $outputArray["ERROR"][] = "Tracking number not available. Please contact to administrator at itsupport@oneworldexpress.com.";
            $outputArray["MESSAGE"] = "Tracking number not available. Please contact to administrator at itsupport@oneworldexpress.com.";
        }
//        $res->close();
        $mysqli->close();



        return $outputArray;
     
    }

    public static function modEuro($value) {
        $awbno = self::STATIC_PREFIX_CZECH . $value;
        $arr = str_split($awbno);
        $checkdigit = ($arr[0] * 1) + ($arr[1] * 8) + ($arr[2] * 6) + ($arr[3] * 4) + ($arr[4] * 2) + ($arr[5] * 3) + ($arr[6] * 5) + ($arr[7] * 9) + ($arr[8] * 7);

        $remainder = $checkdigit % 11;

        $checkdigit = 11 - $remainder;

        if ($checkdigit == 10) {
            $checkdigit = 0;
        } elseif ($checkdigit == 11) {
            $checkdigit = 5;
        }

        return $checkdigit;
    }

    public static function mod11($val) {

        $arr = str_split($val);
        $checkdigit = ($arr[0] * 8) + ($arr[1] * 6) + ($arr[2] * 4) + ($arr[3] * 2) + ($arr[4] * 3) + ($arr[5] * 5) + ($arr[6] * 9) + ($arr[7] * 7);
        $remainder = $checkdigit % 11;

        $checkdigit = 11 - $remainder;

        if ($checkdigit == 10) {
            $checkdigit = 0;
        } elseif ($checkdigit == 11) {
            $checkdigit = 5;
        }

        return $checkdigit;
    }

    public static function modParcelForce($val) {

        $arr = str_split($val);
        $checkdigit = ($arr[0] * 4) + ($arr[1] * 2) + ($arr[2] * 3) + ($arr[3] * 5) + ($arr[4] * 9) + ($arr[5] * 7);
        $remainder = $checkdigit % 11;

        $checkdigit = 11 - $remainder;

        if ($checkdigit == 10) {
            $checkdigit = 0;
        } elseif ($checkdigit == 11) {
            $checkdigit = 5;
        }

        return $checkdigit;
    }

    public static function mod10($val) {

        $sumOdd = "";
        $sumEven = "";
        $checkdigit = "";

        $arrayOfLicencePlate = str_split(trim($val));

        $sum = ($arrayOfLicencePlate[0] * 3) +
                $arrayOfLicencePlate[1] +
                $arrayOfLicencePlate[2] * 3 +
                $arrayOfLicencePlate[3] +
                $arrayOfLicencePlate[4] * 3 +
                $arrayOfLicencePlate[5] +
                $arrayOfLicencePlate[6] * 3 +
                $arrayOfLicencePlate[7] +
                $arrayOfLicencePlate[8] * 3 +
                $arrayOfLicencePlate[9] +
                $arrayOfLicencePlate[10] * 3 +
                $arrayOfLicencePlate[11] +
                $arrayOfLicencePlate[12] * 3 +
                $arrayOfLicencePlate[13] +
                $arrayOfLicencePlate[14] * 3 +
                @$arrayOfLicencePlate[15] +
                @$arrayOfLicencePlate[16] * 3 +
                @$arrayOfLicencePlate[17] 
        ;





        $remainder = $sum % 10;



        if ($remainder == 0)
            $checkdigit = 0;
        else
            $checkdigit = 10 - $remainder;

        return $checkdigit;
    }

    public static function getHandlingFilter($handling) {
        $sql = "SELECT * FROM licence_plate
				WHERE delivery_network= '" . DbAccess3::escape($handling) . "'";
        $list = DbAccess3::getListFromSql(__CLASS__, $sql);
        $list = $list[0];
        return $list;
    }

    public function getRange() {
        $sql = "SELECT * FROM licence_plate
				ORDER BY id";

        //t($sql, __METHOD__);
//echo $sql;
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    
    public static function commoditycode($val) {

        $checkdigit = rand(0,9);

        return $checkdigit;
    }
    

}
