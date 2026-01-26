<?php

/**
 * MAWB CLASS
 *
 */
class CostTariffs extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' 				=> 'id',
			'courier_service_id' 				=> 'courier_service_id',
            'collection_rateband_id' 			=> 'collection_rateband_id',
            'destination_rateband_id' 			=> 'destination_rateband_id',
            'collection_postcode_group_id' 		=> 'collection_postcode_group_id',
            'destination_postcode_group_id' 	=> 'destination_postcode_group_id',
            'weight_from' 						=> 'weight_from',
            'weight_to' 						=> 'weight_to',
            'tariff_cost' 						=> 'tariff_cost',
            'unit_cost' 						=> 'unit_cost',
            'unit_size' 						=> 'unit_size',
            'active' 							=> 'active',
            'deletedq' 							=> 'deletedq',
            'added_on' 							=> 'added_on',
            'added_by'							=> 'added_by',
            'changed_on' 						=> 'changed_on',
            'changed_by' 						=> 'changed_by',
            'tariff_name' 						=> 'tariff_name',
			'formula' 							=> 'formula'
        );

        //
        parent::__construct("cost_tariffs", 'id', $fieldList, $mixedCreator);
    }
	public function getCollectionRateband()			{
														$RbnObj = new Rateband($this->getCollectionRatebandId());
														return $RbnObj->getName();
													}

	public function getDestinationRateband()		{
														$RbnObj = new Rateband($this->getDestinationRatebandId());
														return $RbnObj->getName();
													}

	

	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getTariffListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


		
}
