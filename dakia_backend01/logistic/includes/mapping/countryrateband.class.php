<?php

////////////////////////////////////////////////////
//
// Class for dealing with Countries link ratebands class
//
////////////////////////////////////////////////////

/**
 * Countryrateband - Countries link ratebands class
 * @package Courier
 */
class Countryrateband extends DbAccess3
{

    protected $country_id;
    protected $rateband_id;
    protected $orderq;
    protected $active 				= true;
    protected $deletedq 			= false;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    protected $country_ratebands    = array();

    // Array for storing associate information
    private $associated_info;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList 		=	array(  
						'id'          => 'number',
						'country_id'          => 'number',
	                    'rateband_id'         => 'number',
	                    'orderq'              => 'number',
    	                'active'              => 'string',
        	            'deletedq'            => 'string',
		                'added_on'            => 'added_on',
        		        'added_by'            => 'added_by',
                	    'changed_on'          => 'changed_on',
                    	'changed_by'          => 'changed_by'
                    );

		parent::__construct("countries_link_ratebands", 'id', $fieldList, $mixedCreator);
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////


    public static function getList($where, $filterArray = NULL, $theOrderByFieldList = '', $limit = NULL, $offset = NULL) 
    {
    	$countryRateBand = new CountryRateBand();
    	return $countryRateBand->getAnyCountryrateband($where);
    }


/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getCountryRatebandListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


	public function getExpnge($courier_service_id = -1) {
		$rateBandSql 		= "SELECT id FROM  ratebands  WHERE courier_service_id='".DbAccess3::escape($courier_service_id)."'";
		$insertBackupSql 	= "INSERT INTO countries_link_ratebands_backup (SELECT id, country_id, rateband_id,orderq, 0, 1, added_on, added_by, changed_on, changed_by 
FROM countries_link_ratebands WHERE rateband_id in ($rateBandSql))";
					
		$deleteSql = "DELETE FROM   countries_link_ratebands 
						WHERE rateband_id in ($rateBandSql)";
		
		DbAccess3::runQuery($insertBackupSql);
		DbAccess3::runQuery($deleteSql);
		return;
	}
	
	/**
     * Gets a country rateband object for every active country.
     * (many objects will be unpopulated)
     */
    public function getFullCountryRatebandsList ($courier_service_id = -1, $countryId) {
		$activeOnly = true;
		$orderBy = "id";

		$rateBandSql = "SELECT id FROM ratebands r 
						WHERE r.courier_service_id='".DbAccess3::escape($courier_service_id)."'";

        $whereSql  = "WHERE c.deletedq <> 'Y' and c.country_id = '".$countryId."' and rateband_id in (".$rateBandSql.")";

        if ($activeOnly) $whereSql .= " AND c.active=1";
        
		 $sql = "SELECT * FROM countries_link_ratebands c $whereSql ";
        $result = DbAccess3::getListFromSql(__CLASS__, $sql);
        return $result;
    }

    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete($id=0)
    {
        if ($this->getId() > 0) {
        	$sql = "DELETE FROM countries_link_ratebands ".
        			"WHERE id=".$this->getId();

        	$result = DbAccess3::runQuery($sql);
        }

        return;

    }

}
?>
