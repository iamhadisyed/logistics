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
class PostItaliaRouting extends DbAccess3
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
            'zip_code'              => 'string',
            'routing_file'          => 'string',
            'province'              => 'string',
            'province_iso_code'     => 'string',
            'sortation_name'        => 'string',
            'sortation_id'          => 'string',
            'sortation_name_on_bag' => 'string'
            );

        parent::__construct("post_italia_routing", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getPostItaliaRoutingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
?>
