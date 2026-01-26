<?php
/**
 * DHL day definite domestic bookings are sent to their links system.
 * Records name of files sent.
 *
 */
class PmpRoutine extends DbAccess3
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
					'storeid' => 'number',
					'store_name' => 'string',
					'is_active' => 'number',
					'country' => 'string',
					'address_line_1' => 'string',
					'address_line_2' => 'string',
					'city' => 'string',
					'postcode' => 'string',
					'telephone' => 'string',
					'depot_no' => 'number',
					'depot_description' => 'string',
					'round1' => 'number',
					'round2' => 'number',
					'drop1' => 'number',
					'drop2' => 'number',
					'date_created' => 'datetime'
					);
		//
		parent::__construct("pmp_routine", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	 public static function getPmpRoutineListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
