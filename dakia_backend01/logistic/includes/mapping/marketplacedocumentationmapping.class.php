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
class MarketPlaceDocumentationMapping extends DbAccess3
{
    protected $Country;
    protected $County;
    protected $Weight;
    protected $Currency;
    protected $DateCreated;
    protected $ServiceType;
    protected $UserId;
    protected $Vat;
    protected $Account;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array(
            'id' => 'number',
            'marketplace_id' => 'number',
            'step_description' => 'string',
            'step_title' => 'string',
            'step_image' => 'string',
            'created_at' => 'datetime',
            'updated_by' => 'number',
            'added_by' => 'number',
            'updated_at' => 'datetime',
            'step_order' => 'number',
        );

        parent::__construct("market_place_documentation_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getMarketPlaceDocumentationMappingFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfMarketPlaceDocumentationMappingFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
