<?php

/**
 * RemoteareasGroups Object
 *
 */
class FlightInfo extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'flight_number' => 'string',
//            'destination' => 'string',
            'country_id' => 'number',
            'date_created' => 'datetime',
            'current_status' => ['enum' => ['in_tranist','arrived_lhr','clearance_in_process','collection_in_process'],'default' => 'in_tranist'],
            'status' => ['enum' => ['not_assigned','assigned','in_warehouse'],'default' => 'not_assigned'],
//            'flight_type' => 'string',
            'address_line_1' => 'string',
            'address_line_2' => 'string',
//            'address_line_3' => 'string',
//           'address_line_4' => 'string',
            'city' => 'string',
            'postcode' => 'string',
            'signature' => 'string',
            'carrier' => 'string',
            'carriage_value' => 'decimal',
            'custom_value' => 'decimal',
            'insurance_amount' => 'decimal',
            'currency' => 'string',
//            'reference_number' => 'string',
            'connecting_flight_number' => 'string',
//            'billing_reference' => 'string',
            'weight_type' => 'string',
            'rate_charge' => 'string',
            'iata_code' => 'string',
            'departure_airport' => 'string',
            'phone_number' => 'string',
            'shipper_co' => 'string',
            'consignee_co' => 'string',
            'arrival_airport' => 'string',
            'shippers_name' => 'string',
            'shippers_addressline1' => 'string',
            'shippers_addressline2' => 'string',
            'hscodes' => 'string',
            'account_id' => 'number',
//            'arrived_date' => 'datetime',
            'etd' => 'string',
            'eta' => 'string',
            'shed' => 'string',
            'files_lv' => 'varchar',
            'files_hv' => 'varchar',
            'files' => 'varchar',
//            'uploadby' => 'string',
//            'type' => 'string',
            'cleared' => 'string',
            'comments' => 'string',
            'weight' => 'string',
            'pieces' => 'string',
            'is_delete' => 'tinyint',
            'created_by' => 'number',
            'invoice' => 'string',
            'mawb'=>'undefined',
            'user_account' => 'undefined',
            'is_closed' => 'tinyint',
            'company' => 'string',
//            'shipper_addressline2' => 'text',
//            'shipper_addressline1' => 'text',
            'airway_bill' => 'string',
            'account_number' => 'string',
            'rate_change' => 'string',
            'reference' => 'string',
            'destination_country_id' => 'number',
            'accounting_reference' => 'string',
            'currancy' => 'string',
            'low_value_manifest' => 'string',
            'high_value_manifest' => 'string',
            'destination_company' => 'string',
            'destination_phone_number' => 'string',
            'transport_type' => 'string',
            'transport_id' => 'number',
            'hawb'=>'undefined',
            'awb'=>'undefined',
            'origin_country'=>'undefined',
            'foreign_value'=>'undefined',
            'description'=>'undefined',
            'sender_name'=>'undefined',
            'contact'=>'undefined',
            'tracking_number'=>'undefined',
            'consignment_id' => 'undefined',
            'address_line_3' => 'undefined',
            'state' => 'undefined',
            'sender_address_line_1' => 'undefined',
            'sender_address_line_2' => 'undefined',
            'sender_address_line_3' => 'undefined',
            'sender_city' => 'undefined',
            'sender_state' => 'undefined',
            'sender_postcode' => 'undefined',
        );
        parent::__construct("flight_info", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getFlightInfoListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfFlightInfoFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
