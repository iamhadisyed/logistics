<?php

////////////////////////////////////////////////////
//
// Class for dealing with Invoices
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class DeutschepostDhlStreetCode extends DbAccess3
{
   
  
    private $filter;
	
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array( 
            'id'                    => 'number',
            'street'                => 'string',
            'zipcode'               => 'string',
            'street_code'           => 'number'
            );

        parent::__construct("deutschepost_dhl_streetcode", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getDeutschePostDhlStreetCodeListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>
