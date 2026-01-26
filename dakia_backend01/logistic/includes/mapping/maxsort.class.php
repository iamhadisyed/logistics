<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class MaxSort extends DbAccess3
{
    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null)
    {
            $fieldList = array(
                                    'id' => 'number',
                                    'version' => 'string',
                                    'iscode' => 'string',
                                    'service' => 'string',
                                    'ro_code' => 'string',
                                    'format' => 'string',
                                    'country' => 'string',
                                    'label_country' => 'string',
                                    'label_destination' => 'string',
                                    'pcs_master' => 'string',
                                    'postcode' => 'string',
                                    'areas' => 'string',
                                    'bill_to' => 'string',
                
                                    );
            parent::__construct("max_sort", 'id', $fieldList, $mixedCreator);
    }
    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId()
    {
            return $this->valArray["id"];
    }

    public static function getMaxSortListFromSql($sql)
    {
            return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
