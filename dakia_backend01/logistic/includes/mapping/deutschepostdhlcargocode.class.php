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
class DeutschepostDhlCargoCode extends DbAccess3
{
	
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array( 
            'id'                    => 'number',
            'start_postcode'                => 'string',
            'end_postcode'               => 'string',
            'cargo_code'           => 'number'
            );

        parent::__construct("deutschepostdhl_cargo_code", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getDeutschePostDhlCargoCodeListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>
