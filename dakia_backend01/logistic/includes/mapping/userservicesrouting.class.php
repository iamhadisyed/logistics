<?php 
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class UserServicesRouting extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'user_account_id' => 'number',
            'country_id' => 'number',
            'from_weight' => 'number',
            'to_weight' => 'number',
            'status' => 'bit',
            'service_id' => 'number',
            'is_remotearea' => 'bit',
            'is_over_size' => 'bit',
            'is_over_label' => 'bit',
            'added_by' => 'string',
            'is_agreed' => 'bit',
            'label_charges' => 'number',
            'is_dead_weight' => 'bit',
            'logo' => 'undefined',
            'carrier' => 'undefined',
            'service_name' => 'undefined',
            'service_from_weight' => 'undefined',
            'service_to_weight' => 'undefined',
            'carrier_country' => 'undefined',
            'proforma_invoice' => 'undefined',
            'carrier_id' => 'undefined',
            'remotearea_check' => 'undefined',
            'is_customized' => 'undefined',
            'name' => 'undefined',
            'code' => 'undefined',
            'description' => 'undefined',
            'origin_country_name' => 'undefined',
            'origin_country_iso' => 'undefined',
            'service_transit_time' => 'undefined',
            'service_delivery_countries' => 'undefined',
            'delivery_country_name' => 'undefined',
            'delivery_country_iso' => 'undefined',
            'validation_type' => 'undefined',
            'max_length' => 'undefined',
            'max_width' => 'undefined',
            'max_height' => 'undefined',
            'maximum_dim_formula' => 'undefined',
            'maximum_allowed_dimension' => 'undefined',
            'volumetric_denominator' => 'undefined',
            'max_volumetric_weight' => 'undefined',
            'girth_formula' => 'undefined',
            'girth' => 'undefined',
//                    'is_customized'     =>	'undefined'
        );

        //
        parent::__construct("user_services_routing", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of Consignmnet Piece objects, using sql given
     *
     * @param string $sql
     */
    public static function getPartnerServicesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function getUserServices($accountnumber) {
        //this was the main query but now we are using this as count. using getuserservicespaged for the correct return with paging.
        $Query = "SELECT country, s.name service_name,min(from_weight) from_weight, max(to_weight) to_weight from services s, user_services_routing p where account_number = '$accountnumber' and status = 'active' and p.service_name = s.code group by country, service_name";
        DbAccess3::runQuery($Query);
        return UserServicesRouting::getPartnerServicesListFromSql($Query);
    }

    public static function deleteServicesByUserId($userId, $serviceId = 0) {
        $sql = "DELETE FROM user_services_routing WHERE user_account_id = '" . DbAccess3::escape($userId) . "' " . ($serviceId > 0 ? "AND service_id = '" . DbAccess3::escape($serviceId) . "'" : "" ) . "";
        if (!empty($userId) && $userId > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    public static function updateUserAgreementForServices($carrier_id, $user_id) {

        $sql = "UPDATE user_services_routing usr 
                SET 
                is_agreed = 1 
                WHERE
                usr.user_account_id = '" . DbAccess3::escape($user_id) . "'
                AND
                service_id IN (SELECT 
                id
                FROM
                services s
                WHERE
                s.carrier_id = '" . DbAccess3::escape($carrier_id) . "')
                ";
        $user = SessionManager::getUser();
        $carrierData = new Carrier($carrier_id);
        $old_data = json_encode(['is_agreed' => 0]);
        $new_data = json_encode(['is_agreed' => 1]);
        $userAudit = new UserAudit();
        $table_key = $user->getId();
        $userAudit->allowAdd = true;
        $userData = new User($user);
        $userAudit->insertAuditData('user', 'insert', $user->getFirstName() . ' ' . $user->getLastName(), $table_key, $new_data, $table_key, $old_data, $user->getFirstName() . ' ' . $user->getLastName() . ' has agreed for carrier ' . $carrierData->getCarrier());
        if (!empty($user_id) && $user_id > 0) {
            return DbAccess3::runQuery($sql);
        }
    }

    public function getAllUserServices($accountnumber, $serviceId = '') {
        $where = '';
        if (!empty($serviceId))
            $where = " AND usr.service_id = '" . DbAccess3::escape($serviceId) . "'";
        $Query = "SELECT
                    srvs.id,
                    srvs.name,
                    srvs.code,
                    srvs.description,
                    country.name AS origin_country_name,
                    country.iso AS origin_country_iso,
                    srvs.from_weight,
                    srvs.to_weight,
                    srvs.validation_type,
                    srvs.max_length,
                    srvs.max_width,
                    srvs.max_height,
                    srvs.maximum_dim_formula,
                    srvs.maximum_allowed_dimension,
                    srvs.maximum_dim_formula,
                    srvs.volumetric_denominator,
                    srvs.max_volumetric_weight,
                    srvs.girth_formula,
                    srvs.girth,
                    srvs.maximum_allowed_dimension
                    FROM
                     user_services_routing usr
                    INNER JOIN services AS srvs
                       ON srvs.id = usr.service_id
                    INNER JOIN country
                       ON country.id = srvs.`origin_country`
                    INNER JOIN carrier
                       ON carrier.id = srvs.`carrier_id`
                    WHERE usr.user_account_id = '" . DbAccess3::escape($accountnumber) . "'
                    AND srvs.`active` = 1 AND srvs.`deletedq` = 0 and carrier.status = 1
                    AND usr.status = 1 and usr.is_agreed = 1
                          AND 
                          CASE WHEN srvs.`is_customized` = 1
                            THEN 1 = 1
                            ELSE usr.country_id <> ''
                          END 
                     " . $where . "
              GROUP By srvs.id
                            ";
//		AND usr.is_agreed = 1
        DbAccess3::runQuery($Query);
        return UserServicesRouting::getPartnerServicesListFromSql($Query);
    }

    public function getServiceDeliveryCountries($serviceId) {
        $Query = "SELECT
				 sctt.id_service,country.name AS delivery_country_name,country.iso AS delivery_country_iso,
				  sctt.transit_time AS service_transit_time
				FROM
				  service_country_ttime AS sctt
				  INNER JOIN country
				    ON country.id = sctt.id_country
				    WHERE sctt.id_service = $serviceId
					";
        DbAccess3::runQuery($Query);
        return UserServicesRouting::getPartnerServicesListFromSql($Query);
    }
}
