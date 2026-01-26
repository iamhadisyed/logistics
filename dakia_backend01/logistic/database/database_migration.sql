
INSERT INTO `smarttrack_relation`.`country`
(`id`,
`iso`,
`name`,
`region`,
`postcode_required`,
`type`,
`region_collection`,
`numcode`,
`allow_express`,
`allow_classic`,
`eu_country`,
`shipping_advice`,
`is_vatable`,
`vat_rate`,
`printable_name`,
`iso3`,
`export_flag`,
`timezone_difference`,
`has_postcodeq`,
`has_subzonesq`,
`orderq`,
`active`,
`deletedq`,
`added_on`,
`added_by`,
`changed_on`,
`changed_by`,
`vat_charged_flag`,
`customs_flag`,
`description`,
`country_image`,
`metakeywords`,
`metadescription`,
`pagetitle`,
`countrybanner`,
`opcode`,
`iso_three`,
`german_name` ) SELECT select `id`,
`iso`,
`name`,
`region`,
`postcode_required`,
`type`,
`region_collection`,
`numcode`,
`allow_express`,
`allow_classic`,
`eu_country`,
`shipping_advice`,
`is_vatable`,
`vat_rate`,
`printable_name`,
`iso3`,
`export_flag`,
`timezone_difference`,
`has_postcodeq`,
`has_subzonesq`,
`orderq`,
`active`,
`deletedq`,
`added_on`,
`added_by`,
`changed_on`,
`changed_by`,
`vat_charged_flag`,
`customs_flag`,
`description`,
`country_image`,
`metakeywords`,
`metadescription`,
`pagetitle`,
`countrybanner`,
`opcode`,
`iso_three`,
`german_name`  FROM rumba19.`country`;

ALTER TABLE `smarttrack_relation`.`country` 
ADD INDEX `by name` (`name` ASC)  COMMENT '';


ALTER TABLE `smarttrack_relation`.`remoteareas_groups` 
ADD INDEX `by_group` (`group_name` ASC)  COMMENT '';


INSERT INTO `smarttrack_relation`.`customer_account`
(`id`,
`user_account`,
`active_flag`,
`company`,
`full_name`,
`return_address`,
`sms_dpd`,
`user_service_type`,
`parentid`,
`phone`,
`logo`,
`instant_label`,
`country`,
`country_id`,
`tracking_api_access`,
`import_data_csv`,
`proforma`,
`add_tracking`,
`collection`,
`default_description`,
`default_notes`,
`default_weight`,
`payment_term`,
`query_term`,
`vat_number`,
`billing_currency`,
`vat_chargable`,
`vat_value`,
`allow_remote_area`,
`telephone`,
`billing_address`,
`date_dispatch`,
`is_product`,
`profile_image`,
`send_courier_data`,
`archive_server`,
`credit_check`,
`tariff_agreed`,
`sales_person`,
`scan_document`,
`data_entry`,
`bank_account_title`,
`bank_sortcode`,
`bank_account_number`,
`bank_branch_address`,
`trade_name_i`,
`trade_address_i`,
`trade_email_i`,
`trade_phone_i`,
`trade_name_ii`,
`trade_address_ii`,
`trade_email_ii`,
`trade_phone_ii`,
`reg_number`,
`reg_address`,
`reg_postcode`,
`reg_country`,
`sale_agent`,
`sale_date`,
`fuel_charges`,
`warehouse_id`,
`user_signature`,
`is_fuelcharges_include`,
`is_prepaid`,
`return_label`,
`finalmile_over_label`,
`request_manifest_collection`,
`create_pre_alert`,
`is_employee`,
`invoice_bank_details_id`,
`check_list_account_form`,
`check_list_credit_check`,
`check_list_t_cs`,
`check_list_tariff_agreed`,
`check_list_sales_pot`,
`sales_pot_time_period`,
`sales_pot_percentage`,
`last_login_date`,
`invalid_login_count`,
`token`,
`token_updated`,
`lock_time`,
`opearation_manifest`,
`own_tariff`,
`user_warehouse`,
`api_key`,
`api_secert`,
`api_date`,
`bagging`,
`retail_customer`,
`show_price`,
`sales_rate`,
`collection_add_line_1`,
`collection_add_line_2`,
`collection_add_line_3`,
`collection_city`,
`collection_postcode`,
`collection_country`,
`theme_id`,
`user_code`,
`website_link`,
`allow_pmp_email`,
`default_lang`,
`credit_limit`,
`invoice_period`,
`label_price`,
`discount`,
`account_code`,
`paypal_email`,
`paypal_currency`,
`email`,
`alternative_email`,
`billing_email`,
`date_created`) SELECT
NULL,
`user_account`,
`active_flag`,
`company`,
`full_name`,
`return_address`,
`sms_dpd`,
`user_service_type`,
`parentid`,
`phone`,
`logo`,
`instant_label`,
`country`,
c.id  'country_id',
`tracking_api_access`,
`import_data_csv`,
`proforma`,
`add_tracking`,
'' collection,
'' default_description,
'' default_notes,
'' default_weight,
'' payment_term,
'' query_term,
`vat_number`,
`billing_currency`,
`vat_chargable`,
`vat_value`,
`allow_remote_area`,
`telephone`,
`billing_address`,
`date_dispatch`,
`is_product`,
`profile_image`,
'' send_courier_data,
`archive_server`,
`credit_check`,
`tariff_agreed`,
`sales_person`,
`scan_document`,
`data_entry`,
`bank_account_title`,
`bank_sortcode`,
`bank_account_number`,
`bank_branch_address`,
`trade_name_i`,
`trade_address_i`,
`trade_email_i`,
`trade_phone_i`,
`trade_name_ii`,
`trade_address_ii`,
`trade_email_ii`,
`trade_phone_ii`,
`reg_number`,
`reg_address`,
`reg_postcode`,
`reg_country`,
`sale_agent`,
`sale_date`,
`fuel_charges`,
`warehouse_id`,
`user_signature`,
`is_fuelcharges_include`,
`is_prepaid`,
`return_label`,
`finalmile_over_label`,
`request_manifest_collection`,
`create_pre_alert`,
`is_employee`,
'' `invoice_bank_details_id`,
`check_list_account_form`,
`check_list_credit_check`,
`check_list_t_cs`,
`check_list_tariff_agreed`,
`check_list_sales_pot`,
`sales_pot_time_period`,
`sales_pot_percentage`,
`last_login_date`,
`invalid_login_count`,
`token`,
`token_updated`,
`lock_time`,
`opearation_manifest`,
`own_tariff`,
`user_warehouse`,
`api_key`,
`api_secert`,
`api_date`,
`bagging`,
`retail_customer`,
`show_price`,
`sales_rate`,
`collection_add_line_1`,
`collection_add_line_2`,
`collection_add_line_3`,
`collection_city`,
`collection_postcode`,
`collection_country`,
`theme_id`,
`user_code`,
`website_link`,
`allow_pmp_email`,
`default_lang`,
'' `credit_limit`,
'' `invoice_period`,
'' `label_price`,
'' `discount`,
user_code `account_code`,
'' `paypal_email`,
'GBP' `paypal_currency`,
`email`,
`alternative_email`,
`billing_email`, '' FROM `rumba19`.`user` u LEFT JOIN `rumba19`.`country` c ON u.country = c.iso GROUP BY u.user_account;


INSERT INTO `smarttrack_relation`.`warehouse`
(`id`,
`countryid`,
`warehouse_name`,
`addressline1`,
`addressline2`,
`stateregion`,
`citytown`,
`postzipcode`,
`phone`,
`description`,
`is_active`,
`is_deleted`,
`added_date`,
`added_by`,
`updated_date`,
`updated_by`,
`hub`,
`email`,
`warehouse_code`) SELECT id,
`countryid`,
`warehouse_name`,
`addressline1`,
`addressline2`,
`stateregion`,
`citytown`,
`postzipcode`,
`phone`,
`description`,
`is_active`,
`is_deleted`,
`added_date`,
`added_by`,
`updated_date`,
`updated_by`,
`hub`,
`email`,
description FROM rumba19.warehouse;

/*
*	update parent id
*/
  UPDATE 
      smarttrack_relation.customer_account ua
  INNER JOIN
  rumba19.user u
        ON u.user_account = ua.user_account
        INNER JOIN
    rumba19.user pu ON pu.parentid = u.id
    INNER JOIN 
    smarttrack_relation.customer_account pua  ON  pua.user_account = pu.user_account
    
    SET pua.parentid = ua.id;
	
	
	

INSERT INTO `smarttrack_relation`.`user`
(`id`,
`user_account_id`,
`user_type`,
`user_name`,
`user_pass`,
`active_flag`,
`first_name`,
`last_name`,
`address`,
`email`,
`phone`,
`country_id`,
`api_key`,
`api_secert`,
`api_date`,
`profile_image`,
`is_employee`,
`warehouse_id`,
`dashboard`,
`invalid_login_count`,
`last_login_date`,
`added_by`,
`added_date`,
`updated_by`,
`updated_date`,
`is_deleted`,
`archive_server`,
`carrier_setup_agreement`,
`receive_email`,
`tc_agreed_date`,
`is_tc_agreed`) SELECT
    u.id,
    ua.id,
    CASE
        WHEN user_type = 'corporateclient' THEN 'corporate'
        WHEN user_type = 'customerservice' THEN 'client'
        WHEN user_type = 'admin' THEN 'admin'
        WHEN user_type = 'finance' THEN 'corporate'
        WHEN user_type = 'manager' THEN 'corporate'
        ELSE 'client'
    END,
    user_name,
    user_pass,
    active_flag,
    full_name,
    '',
    return_address,
    email,
    phone,
    c.id,
    api_key,
    api_secert,
    api_date,
    profile_image,
    is_employee,
    CASE 
		WHEN warehouse_id = 0 THEN NULL
        WHEN warehouse_id = 19 THEN NULL
        WHEN warehouse_id = 18 THEN NULL
        ELSE warehouse_id
        END ,
    CASE
        WHEN user_type = 'corporateclient' THEN 'corporate'
        WHEN user_type = 'customerservice' THEN 'client'
        WHEN user_type = 'admin' THEN 'admin'
        WHEN user_type = 'finance' THEN 'corporate'
        WHEN user_type = 'manager' THEN 'corporate'
        ELSE 'client'
    END,
    0,
    NOW(),
    '54',
    NOW(),
    '54',
    NOW(),
    active_flag,
    archive_server,
    0,
    'n',
    NOW(),
    is_tc_agreed
FROM
   ( rumba19.user u
    INNER JOIN
    (SELECT id, user_account FROM smarttrack_relation.customer_account GROUP BY user_account) ua
    ON u.user_account = ua.user_account
    )
        LEFT JOIN
    rumba19.country c ON u.country = c.iso GROUP BY u.id;
	
	
	INSERT INTO `smarttrack_relation`.`carrier`
(`id`,
`country_id`,
`carrier`,
`logo`,
`cut_off_time`,
`carrier_display_name`,
`status`,
`carrier_id`,
`currency_code`,
`remotearea_check`,
`zone_base`,
`on_contract`) SELECT 
	null,
	225,
	s.`carrier`,
	cl.`logo`,
	`cut_off_time`,
	s.carrier,
	active,
	NULL,
	`uploaded_currency`,
	's',
	'1',
	0
FROM 
 	`rumba19`.`services` s inner join `rumba19`.`carrier_logo` cl ON  s.carrier = cl.carrier group by s.carrier


	
	
	
	
	INSERT INTO `smarttrack_relation`.`services`
(`id`,
`name`,
`code`,
`carrier_id`,
`account_number`,
`type`,
`from_weight`,
`to_weight`,
`wieght_type`,
`supplier`,
`service_type`,
`description`,
`fuel_surcharge_cost`,
`fuel_surcharge`,
`fuel_surcharge_type`,
`max_length`,
`max_width`,
`max_height`,
`max_volumetric_weight`,
`volumetric_denominator`,
`send_data_courier`,
`is_document`,
`friday_only_flag`,
`saturday_only_flag`,
`sunday_only_flag`,
`active`,
`deletedq`,
`added_on`,
`added_by`,
`changed_on`,
`changed_by`,
`uploaded_currency`,
`uploaded_currency_value`,
`registration_fee`,
`weight_after`,
`aditional_charge`,
`origin_country`,
`is_untrack`,
`account_owner`,
`remotearea`,
`carrier_address_limit`,
`label_class_name`,
`transit_time`,
`required_email`,
`required_telephone`,
`shipment_type`,
`pre_sort`,
`proforma_invoice`,
`agent_dispatch`,
`brief_manifest`,
`delivery_mode`,
`insurance_available`,
`vol_wgt_formula`,
`is_remotearea`,
`is_customized`,
`pre_advise`,
`pre_alert`,
`pre_alert_email`,
`cut_off_time`,
`label_charges`)
SELECT null,
`name`,
`code`,
c.id 'carrier_id',
`account_number`,
`type`,
`from_weight`,
`to_weight`,
`wieght_type`,
`supplier`,
`service_type`,
`description`,
`fuel_surcharge_cost`,
`fuel_surcharge`,
`fuel_surcharge_type`,
`max_length`,
`max_width`,
`max_height`,
`max_volumetric_weight`,
`volumetric_denominator`,
`send_data_courier`,
0,
0,
0,
0,
`active`,
`deletedq`,
`added_on`,
`added_by`,
`changed_on`,
`changed_by`,
`uploaded_currency`,
`uploaded_currency_value`,
`registration_fee`,
`weight_after`,
`aditional_charge`,
`origin_country`,
`is_untrack`,
`account_owner`,
`remotearea`,
`carrier_address_limit`,
`label_class_name`,
`transit_time`,
`required_email`,
`required_telephone`,
`shipment_type`,
`pre_sort`,
'' `proforma_invoice`,
`agent_dispatch`,
NULL `brief_manifest`,
NULL `delivery_mode`,
NULL `insurance_available`,
'L*W*W / 5000' `vol_wgt_formula`,
NULL `is_remotearea`,
0 `is_customized`,
'N' `pre_advise`,
'N' `pre_alert`,
NULL `pre_alert_email`,
s.`cut_off_time`,
NULL `label_charges` from rumba19.services s INNER JOIN smarttrack_relation.carrier  c ON s.carrier=c.carrier 


/*products into services */

INSERT INTO `smarttrack_relation`.`services`
(`id`,
`name`,
`code`,
`carrier_id`,
`account_number`,
`type`,
`from_weight`,
`to_weight`,
`wieght_type`,
`supplier`,
`service_type`,
`description`,
`fuel_surcharge_cost`,
`fuel_surcharge`,
`fuel_surcharge_type`,
`max_length`,
`max_width`,
`max_height`,
`max_volumetric_weight`,
`volumetric_denominator`,
`send_data_courier`,
`is_document`,
`friday_only_flag`,
`saturday_only_flag`,
`sunday_only_flag`,
`active`,
`deletedq`,
`added_on`,
`added_by`,
`changed_on`,
`changed_by`,
`uploaded_currency`,
`uploaded_currency_value`,
`registration_fee`,
`weight_after`,
`aditional_charge`,
`origin_country`,
`is_untrack`,
`account_owner`,
`remotearea`,
`carrier_address_limit`,
`label_class_name`,
`transit_time`,
`required_email`,
`required_telephone`,
`shipment_type`,
`pre_sort`,
`proforma_invoice`,
`agent_dispatch`,
`brief_manifest`,
`delivery_mode`,
`insurance_available`,
`vol_wgt_formula`,
`is_remotearea`,
`is_customized`,
`pre_advise`,
`pre_alert`,
`pre_alert_email`,
`cut_off_time`,
`label_charges`)
SELECT null,
routing_name `name`,
routing_name `code`,
32 'carrier_id',
NULL `account_number`,
`type`,
MIN(c.`from_weight`),
MAX(c.`to_weight`),
s.`wieght_type`,
s.`supplier`,
'D' `service_type`,
s.`description`,
s.`fuel_surcharge_cost`,
s.`fuel_surcharge`,
s.`fuel_surcharge_type`,
s.`max_length`,
s.`max_width`,
s.`max_height`,
s.`max_volumetric_weight`,
s.`volumetric_denominator`,
s.`send_data_courier`,
0,
0,
0,
0,
s.`active`,
s.`deletedq`,
s.`added_on`,
s.`added_by`,
s.`changed_on`,
s.`changed_by`,
s.`uploaded_currency`,
s.`uploaded_currency_value`,
s.`registration_fee`,
s.`weight_after`,
s.`aditional_charge`,
s.`origin_country`,
s.`is_untrack`,
s.`account_owner`,
s.`remotearea`,
s.`carrier_address_limit`,
s.`label_class_name`,
s.`transit_time`,
s.`required_email`,
s.`required_telephone`,
s.`shipment_type`,
`pre_sort`,
'' `proforma_invoice`,
s.`agent_dispatch`,
NULL `brief_manifest`,
NULL `delivery_mode`,
NULL `insurance_available`,
'L*W*W / 5000' `vol_wgt_formula`,
NULL `is_remotearea`,
1 `is_customized`,
'N' `pre_advise`,
'N' `pre_alert`,
NULL `pre_alert_email`,
s.`cut_off_time`,
NULL `label_charges` from rumba19.partnerservicesrouting c JOIN smarttrack_relation.services  s ON s.code = c.service_name WHERE routing_name <> ''  group by routing_name ;

/*product routing entry */
INSERT INTO `smarttrack_relation`.`customized_services_routing`
(`id`,
`country_id`,
`service_id`,
`from_weight`,
`to_weight`,
`status`,
`customize_service_id`)
SELECT 
    NULL,
    c.id,
    pr.id,
    p.`from_weight`,
    p.`to_weight`,
    IF(p.status = 'active', 1, 0),
    s.id
FROM
    rumba19.partnerservicesrouting p
        INNER JOIN
    smarttrack_relation.services s ON p.routing_name = s.code
        AND p.routing_name <> ''
        INNER JOIN
    smarttrack_relation.services pr ON p.service_name = pr.code AND  p.country IN (SELECT 
            name
        FROM
            smarttrack_relation.country)
        LEFT JOIN
    smarttrack_relation.country c ON UPPER(TRIM(c.name)) = UPPER(TRIM(p.country))
        AND c.id IS NOT NULL; 
        
		
		
	INSERT INTO `smarttrack_relation`.`customized_user_services_routing`
(`id`,
`service_id`,
`user_account_id`,
`routing_added_date`)
		select 
		null,
		s.id,
		ua.id,
		now()
		from rumba19.routing_user_mapping rum
		inner JOIN smarttrack_relation.services s ON rum.routing_name = s.code
		INNER JOIN smarttrack_relation.customer_account ua ON rum.user_account = ua.user_account;
		
		
		ALTER TABLE `smarttrack_relation`.`customer_account`
		ADD INDEX `user_account` (`user_account` ASC)  COMMENT '';


		/*
		*	USER SERVICES ASSIGN
		*/
		INSERT INTO `smarttrack_relation`.`user_services_routing`
(`id`,
`user_account_id`,
`country_id`,
`from_weight`,
`to_weight`,
`status`,
`service_id`,
`is_remotearea`,
`added_by`,
`is_agreed`,
`label_charges`)
		SELECT 
			NULL,
			ua.id,
			c.id,
			p.`from_weight`,
			p.`to_weight`,
			IF(p.status = 'active', 1, 0),
			s.id,
			NULL,
			58,
			1,
			0.00
		FROM
			rumba19.partnerservicesrouting p
				INNER JOIN
			smarttrack_relation.services s ON p.service_name = s.code
				AND p.routing_name = ''
				INNER JOIN
			smarttrack_relation.customer_account ua ON ua.user_account = p.account_number
				INNER JOIN
			smarttrack_relation.country c ON UPPER(TRIM(c.name)) = UPPER(TRIM(p.country))
				AND c.id IS NOT NULL;  
        
        /*
		*	AGENT DATA 
		*/
		
		INSERT INTO `smarttrack_relation`.`agent_data`
(`id`,
`agent_code`,
`agent_name`,
`active`,
`contact_name`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
`country_id`,
`county`,
`city`,
`postcode`,
`telephone`,
`mobile`,
`fax`,
`email`,
`alternative_contact_1`,
`alternative1_telephone`,
`alternative1_mobile`,
`alternative1_fax`,
`alternative1_email`,
`alternative_contact_2`,
`alternative2_telephone`,
`alternative2_mobile`,
`alternative2_fax`,
`alternative2_email`,
`remarks`,
`date_created`,
`user_id`,
`is_deleted`,
`logo`,
`agent_type`)
SELECT NULL,
`agent_code`,
`agent_name`,
ad.`active`,
`contact_name`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
c.id,
`county`,
`city`,
`postcode`,
`telephone`,
`mobile`,
`fax`,
`email`,
`alternative_contact_1`,
`alternative1_telephone`,
`alternative1_mobile`,
`alternative1_fax`,
`alternative1_email`,
`alternative_contact_2`,
`alternative2_telephone`,
`alternative2_mobile`,
`alternative2_fax`,
`alternative2_email`,
`remarks`,
`date_created`,
`user_id`,
if(ad.active = 0,0,1) `is_deleted`,
NULL `logo`,
'carrier' `agent_type` FROM rumba19.`agent_data` ad inner join country c ON ad.country = c.name;


/*
*	Agent Service mapping 
*/

INSERT INTO `smarttrack_relation`.`carrier_service_default_rules`
(`id`,
`serviceid`,
`agentid`,
`from_weight`,
`to_weight`,
`is_default`)
SELECT 
    NULL, srs.id, ad.id, s.from_weight, s.to_weight, 1
FROM
    rumba19.`services` s
        INNER JOIN
    smarttrack_relation.services srs ON s.code = srs.code 
        INNER JOIN
    rumba19.agent_data a ON s.agentid = a.id
        INNER JOIN
    smarttrack_relation.agent_data ad ON a.agent_code = ad.agent_code
        
		
ALTER TABLE `smarttrack_relation`.`address` 
CHANGE COLUMN `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '' ;



INSERT INTO `smarttrack_relation`.`address`
(`id`,
`user_id`,
`phone_number`,
`company`,
`contact`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
`city`,
`country`,
`postcode`,
`state`)
SELECT NULL,
u.id,
ad.`phone_number`,
ad.`company`,
ad.`contact`,
ad.`address_line_1`,
ad.`address_line_2`,
ad.`address_line_3`,
ad.`city`,
ad.`country`,
ad.`postcode`,
' ' `state`
FROM rumba19.`address` ad
INNER JOIN smarttrack_relation.customer_account ua on ad.account   = ua.user_account COLLATE utf8_unicode_ci
INNER JOIN smarttrack_relation.user u on u.user_account_id = ua.id;




/*
*	Service country 
*/

INSERT INTO `smarttrack_relation`.`service_country_ttime`
(`id`,
`id_country`,
`id_service`,
`transit_time`)
SELECT 
    NULL,  n.id, ss.id, ss.transit_time
FROM
    (rumba19.services s
    INNER JOIN 
		smarttrack_relation.country n ON CHAR_LENGTH(s.service_country) - CHAR_LENGTH(REPLACE(s.service_country, ',', '')) >= n.id - 1)
	INNER JOIN
		smarttrack_relation.services ss ON s.code = ss.code;
		
		
/*
*	Carrier Zones  
*/
		
INSERT INTO `smarttrack_relation`.`carrier_zones`
(`id`,
`carrier_id`,
`service_id`,
`name`,
`sort_order`,
`status`,
`deleted`,
`date_added`,
`added_by`,
`date_updated`,
`updated_by`)
SELECT 
r.id,
s.`carrier_id`,
courier_service_id `service_id`,
r.`name`,
`orderq`,
r.`active`,
IF(r.`deletedq`= 'N',0,1),
r.`added_on`,
'188' `added_by`,
r.`changed_on`,
'188' `changed_by`
FROM rumba19.ratebands r
inner join smarttrack_relation.services s on r.courier_service_id =  s.id ;

/*
*	Countries link Carrier Zones  
*/
INSERT INTO `smarttrack_relation`.`carrier_zones_countries`
(`id`,
`country_id`,
`carrier_zone_id`)
SELECT 
    r.id,cc.id,r.rateband_id
FROM
    rumba19.countries_link_ratebands r
        INNER JOIN
    rumba19.country c ON c.id = r.country_id
        INNER JOIN
    smarttrack_relation.country cc ON cc.iso = c.iso AND r.rateband_id <> 0 group by r.id;

	
	

INSERT INTO smarttrack_relation.currency select * from rumba19.currency;

    
	
	/*
	*	service tariffs 
	*/
	
	
	INSERT INTO `smarttrack_relation`.`tariffs` (
`id`,
`user_account_id`,
`carrier_id`,
`service_id`,
`currency_id`,
`tariffs_pricing_rule_id`,
`name`,
`status`,
`tariff_type`,
`start_date`,
`end_date`,
`description`,
`date_added`,
`added_by`,
`date_updated`,
`updated_by`)
SELECT 
    NULL,
    '503' `user_account_id`,
    ss.`carrier_id`,
    ss.id `service_id`,
    '2' `currency_id`,
    NULL `tariffs_pricing_rule_id`,
    customer_id `name`,
    t.active `status`,
    'customer' `tariff_type`,
    '2018-06-01' `start_date`,
    '2018-12-31' `end_date`,
    '' `description`,
    NOW() `date_added`,
    58 `added_by`,
    NOW() `date_updated`,
    58 `updated_by`
FROM
    rumba19.tariffs t
        INNER JOIN
    rumba19.services s ON s.id = t.courier_service_id
        INNER JOIN
    smarttrack_relation.services ss ON s.code = ss.code
WHERE
    t.customer_id NOT IN (SELECT DISTINCT
            routing_name
        FROM
            rumba19.partnerservicesrouting
        WHERE
            routing_name <> '')
            
GROUP BY t.customer_id;



	
	/*
	*	product tariffs 
	*/

	INSERT INTO `smarttrack_relation`.`tariffs` (
`id`,
`user_account_id`,
`carrier_id`,
`service_id`,
`currency_id`,
`tariffs_pricing_rule_id`,
`name`,
`status`,
`tariff_type`,
`start_date`,
`end_date`,
`description`,
`date_added`,
`added_by`,
`date_updated`,
`updated_by`)
SELECT 
    NULL,
    '503' `user_account_id`,
    ss.`carrier_id`,
    ss.id `service_id`,
    '2' `currency_id`,
    NULL `tariffs_pricing_rule_id`,
    customer_id `name`,
    t.active `status`,
    'customer' `tariff_type`,
    '2018-06-01' `start_date`,
    '2018-12-31' `end_date`,
    '' `description`,
    NOW() `date_added`,
    58 `added_by`,
    NOW() `date_updated`,
    58 `updated_by`
FROM
    rumba19.tariffs t
        INNER JOIN
    smarttrack_relation.services ss ON t.customer_id = ss.code AND ss.is_customized = 1
GROUP BY t.customer_id;


/*
*	Tariffs for the services
*/
INSERT INTO `smarttrack_relation`.`tariffs_details`
(`id`,
`tariffs_id`,
`from_zone_id`,
`to_zone_id`,
`weight_from`,
`weight_to`,
`weight_cost`,
`piece_cost`,
`formula`)
SELECT 
    NULL,
    st.id `tariffs_id`,
    rt.collection_rateband_id `from_zone_id`,
    rt.destination_rateband_id `to_zone_id`,
    rt.`weight_from`,
    rt.`weight_to`,
    rt.tariff `weight_cost`,
    rt.add_unit_cost `piece_cost`,
    rt.`formula`
FROM
    rumba19.tariffs rt
        INNER JOIN
    smarttrack_relation.tariffs st ON rt.customer_id = st.name       


/*
*	User assign tariff 
*/

INSERT INTO `smarttrack_relation`.`tariffs_account_mapping`
(`id`,
`tariff_id`,
`user_account_id`,
`added_date`,
`added_by`)
select  null,
t.id`tariff_id`,
ua.id `user_account_id`,
tum.tariff_added_date `added_date`,
'148' `added_by` from rumba19.tariff_user_mapping tum
inner join smarttrack_relation.tariffs t ON tum.tariff_name = t.name
inner join smarttrack_relation.customer_account ua on tum.user_account = ua.user_account

/*
*	Remote area group mapping
*/

INSERT INTO `smarttrack_relation`.`remoteareas_groups`
(`id`,
`carrier_id`,
`group_name`,
`is_deleted`,
`added_by`,
`added_date`,
`updated_by`,
`updated_date`)
select 
NULL ,
s.`carrier_id`,
rum.postcode_name `group_name`,
0 `is_deleted`,
58 `added_by`,
now() `added_date`,
58 `updated_by`,
now() `updated_date`
	FROM
	rumba19.`postcode_user_service_charges` pusc
	INNER JOIN 
    rumba19.`remotearea_user_mapping` rum  ON pusc.postcode_name = rum.postcode_name
    INNER JOIN smarttrack_relation.services s ON rum.service_code = s.code
group by trim(pusc.postcode_name), s.carrier_id ;

	
/*
*	Remote area postcodes group 
*/
	
	INSERT INTO `smarttrack_relation`.`remoteareas`
(`id`,
`remoteareas_groups_id`,
`country_id`,
`from_postcode`,
`to_postcode`,
`city`,
`is_deleted`,
`added_by`,
`added_date`,
`updated_by`,
`updated_date`)
SELECT 
    NULL `id`,
    rg.id `remoteareas_groups_id`,
    c.id `country_id`,
    `from_postcode`,
    `to_postcode`,
    pusc.city_name `city`,
    0 `is_deleted`,
    58 `added_by`,
    NOW() `added_date`,
    58 `updated_by`,
    NOW() `updated_date`
FROM
    rumba19.`remotearea_user_mapping` rum
        INNER JOIN
    `rumba19`.`postcode_user_service_charges` pusc ON rum.postcode_name = pusc.postcode_name
        INNER JOIN
    `smarttrack_relation`.`remoteareas_groups` rg ON rg.group_name = rum.postcode_name
        INNER JOIN
    `smarttrack_relation`.`country` c ON pusc.country_iso = c.iso
GROUP BY from_postcode , rg.id;

		
/* remotearea general charges for customers */
		
INSERT INTO `smarttrack_relation`.`remotearea_charges_services`
(`id`,
`remotearea_group_id`,
`service_id`,
`remotearea_charges`,
`from_weight`,
`to_weight`,
`formulla`,
`is_deleted`,
`added_by`,
`added_date`,
`updated_by`,
`updated_date`)
SELECT
 NULL,
rg.id `remotearea_group_id`,
s.id `service_id`,
charges `remotearea_charges`,
s.from_weight `from_weight`,
s.to_weight `to_weight`,
'' `formulla`,
'N' `is_deleted`,
58 `added_by`,
NOW() `added_date`,
58 `updated_by`,
NOW()`updated_date` FROM `rumba19`.`remotearea_user_mapping` rum
INNER JOIN `smarttrack_relation`.`remoteareas_groups` rg ON  rum.postcode_name = rg.group_name
INNER JOIN `smarttrack_relation`.`services` s ON s.code = rum.service_code
group by rg.id,s.id;



/* remotearea general charges for customers */

		
INSERT INTO `smarttrack_relation`.`remotearea_charges_carrier`
(`id`,
`remotearea_group_id`,
`remotearea_charges`,
`is_deleted`,
`added_by`,
`added_date`,
`updated_by`,
`updated_date`)
SELECT
 NULL,
rg.id `remotearea_group_id`,
charges `remotearea_charges`,
'N' `is_deleted`,
58 `added_by`,
NOW() `added_date`,
58 `updated_by`,
NOW()`updated_date` FROM `rumba19`.`remotearea_user_mapping` rum
INNER JOIN `smarttrack_relation`.`remoteareas_groups` rg ON  rum.postcode_name = rg.group_name
group by rg.id;


/* Consignment data m */



INSERT INTO `smarttrack_relation`.`consignment`
(`id`,
`agent_id`,
`user_id`,
`service_id`,
`customized_service_id`,
`warehouse_user_id`,
`warehouse_id`,
`sales_pot_id`,
`invoice_id`,
`credit_id`,
`is_invoiced`,
`invoice_type`,
`shipment_status`,
`shipment_type`,
`awb`,
`consignment_status`,
`return_awb`,
`hawb`,
`mawb`,
`service_name`,
`reference`,
`date_created`,
`date_label_created`,
`date_booked`,
`date_delivered`,
`is_customer_manifested`,
`booked_file_id`,
`company`,
`contact`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
`city`,
`state`,
`postcode`,
`country_id`,
`telephone`,
`number_pieces`,
`weight_type`,
`weight`,
`update_weight`,
`fake_weight`,
`charge_weight`,
`vol_weight`,
`vol_demonimator`,
`hv_lv`,
`description`,
`notes`,
`value`,
`currency`,
`sender_name`,
`username`,
`sender_checked`,
`message`,
`sorter_image`,
`label_file`,
`is_doc`,
`email`,
`itemtype`,
`routing_code`,
`routing_code_eur`,
`other_routing_code`,
`billing_hold`,
`send_courier_data`,
`remote_charges`,
`reinvoices`,
`optimus_sorter`,
`full_pallet`,
`half_pallet`,
`quarter_pallet`,
`date_scanned`,
`consignment_type`)
SELECT 
    c.`id`,
(SELECT id from smarttrack_relation.agent_data where id = c.agentid ) `agent_id`,
(SELECT 
            u.id
        FROM
            `smarttrack_relation`.`customer_account` ua
                INNER JOIN
            `smarttrack_relation`.`user` u ON u.user_account_id = ua.id
        WHERE
            c.account = ua.customer_account
        LIMIT 1)  `user_id`,
 s.id `service_id`,
    (SELECT 
            p.id
        FROM
            smarttrack_relation.services p
        WHERE
            p.code = c.type
                AND c.type NOT IN ('D' , 'C', '') limit 1) `customized_service_id`,
'' `warehouse_user_id`,
(SELECT id from smarttrack_relation.warehouse where id = c.warehouse_id) `warehouse_id`,
'' `sales_pot_id`,
'' `invoice_id`,
'' `credit_id`,
isinvoiced `is_invoiced`,
`invoice_type`,
(CASE  
		WHEN consignment_status ="valid"  THEN '12'
		WHEN consignment_status ="warehouse received"  THEN '14'
		WHEN consignment_status ="relabel"  THEN '26'
		WHEN consignment_status ="recycled"  THEN '22'
		WHEN consignment_status ="recyced"  THEN '22'
		WHEN consignment_status ="received"  THEN '13'
		WHEN consignment_status ="hold"  THEN '24'
		WHEN consignment_status ="discrepancy"  THEN '28'
		WHEN consignment_status ="delivered"  THEN  '19'
		WHEN consignment_status ="data ready"  THEN  '14'
		WHEN consignment_status ="closed"  THEN  '21'
		WHEN consignment_status ="booked"  THEN  '16'
		WHEN consignment_status ="dataready supplier"  THEN  '14'
	END )  `shipment_status`,
`shipment_type`,
`awb`,
(CASE  
		WHEN consignment_status = "valid"  THEN 'ready to print'
		WHEN consignment_status ="warehouse received"  THEN  'received'
		WHEN consignment_status ="relabel"  THEN  'relabel'
		WHEN consignment_status ="recycled"  THEN  'recycled'
		WHEN consignment_status ="recyced"  THEN 'recycled'
		WHEN consignment_status ="received"  THEN  'label created'
		WHEN consignment_status ="hold"  THEN  'hold'
		WHEN consignment_status ="discrepancy"  THEN  'discrepancy'
		WHEN consignment_status ="delivered"  THEN  'delivered'
		WHEN consignment_status ="data ready"  THEN  'received'
		WHEN consignment_status ="closed"  THEN  'closed'
		WHEN consignment_status ="booked"  THEN  'dispatched'
		WHEN consignment_status ="dataready supplier"  THEN 'received'
	END ) consignment_status,
`return_awb`,
`hawb`,
`mawb`,
    s.name `service_name`,
    `reference`,
    date_submitted `date_created`,
    UNIX_TIMESTAMP(date_received) `date_label_created`,
    UNIX_TIMESTAMP(date_booked) `date_booked`,
    UNIX_TIMESTAMP(date_delivered) `date_delivered`,
0 `is_customer_manifested`,
`booked_file_id`,
`company`,
`contact`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
`city`,
`state`,
`postcode`,
 (SELECT 
            con.id
        FROM
            smarttrack_relation.country con
        WHERE
            c.country = con.name limit 1)  `country_id`,
`telephone`,
`number_pieces`,
`weight_type`,
`weight`,
`update_weight`,
`fake_weight`,
`charge_weight`,
`vol_weight`,
`vol_demonimator`,
`hv_lv`,
c.`description`,
`notes`,
`value`,
`currency`,
`sender_name`,
`username`,
`sender_checked`,
`message`,
'' `sorter_image`,
single_label `label_file`,
`is_doc`,
`email`,
`itemtype`,
`routing_code`,
`routing_code_eur`,
`other_routing_code`,
`billing_hold`,
1 `send_courier_data`,
`remote_charges`,
`reinvoices`,
`optimus_sorter`,
`full_pallet`,
`half_pallet`,
`quarter_pallet`,
`date_scanned`,
'outbound' `consignment_type`
FROM
    rumba19.consignment c
        INNER JOIN
    smarttrack_relation.services s ON c.handling = s.code
        AND c.isinvoiced <> 'Y'
        AND c.date_received >= '2018-07-01'

		
		
		
/* Parcel Data mapping */
		
		
		INSERT INTO `smarttrack_relation`.`parcel`
(`id`,
`consignment_id`,
`tracking_number`,
`length`,
`width`,
`height`,
`weight`,
`description`,
`parcel_message`,
`qty`,
`commoditycode`,
`grossweight`,
`pweight`,
`itemvalue`,
`number_item`,
`tarrif_no`,
`update_weight`,
`owe_status_code`,
`chute_sorted`,
`parcel_status_code`,
`routing_code`,
`last_tracking_update`)
SELECT 
p.`id`,
p.`consignment_id`,
p.licence_plate `tracking_number`,
p.`length`,
p.`width`,
p.`height`,
p.`weight`,
p.`description`,
 '' `parcel_message`,
p.`qty`,
p.`commoditycode`,
p.`grossweight`,
p.`pweight`,
p.`itemvalue`,
p.`number_item`,
p.`tarrif_no`,
p.`update_weight`,
c.consignment_status `owe_status_code`,
p.`chute_sorted`,
c.shipment_status `parcel_status_code`,
c.routing_code `routing_code`,
p.date_delivered `last_tracking_update` from rumba19.parcel p
INNER JOIN  `smarttrack_relation`.`consignment` c  ON c.id = p.consignment_id;


UPDATE  `smarttrack_relation`.user SET country_id = 225 WHERE country_id IS NULL ;

UPDATE customer_account u INNER JOIN rumba19.user ru ON ru.user_account = u.user_account SET u.date_created = ru.update_date ;


UPDATE `smarttrack_staging`.carrier c INNER JOIN `smarttrack_relation`.carrier cr  ON c.carrier = cr.carrier
SET cr.logo = c.logo;



UPDATE `smarttrack_relation`.`carrier` SET carrier_id = '0' WHERE carrier_id IS NULL ;





		INSERT INTO `smarttrack_relation`.`agent_data`
(`id`,
`agent_code`,
`agent_name`,
`active`,
`contact_name`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
`country_id`,
`county`,
`city`,
`postcode`,
`telephone`,
`mobile`,
`fax`,
`email`,
`alternative_contact_1`,
`alternative1_telephone`,
`alternative1_mobile`,
`alternative1_fax`,
`alternative1_email`,
`alternative_contact_2`,
`alternative2_telephone`,
`alternative2_mobile`,
`alternative2_fax`,
`alternative2_email`,
`remarks`,
`date_created`,
`user_id`,
`is_deleted`,
`logo`,
`agent_type`)
SELECT NULL,
`agent_code`,
`agent_name`,
`active`,
`contact_name`,
`address_line_1`,
`address_line_2`,
`address_line_3`,
country_id,
`county`,
`city`,
`postcode`,
`telephone`,
`mobile`,
`fax`,
`email`,
`alternative_contact_1`,
`alternative1_telephone`,
`alternative1_mobile`,
`alternative1_fax`,
`alternative1_email`,
`alternative_contact_2`,
`alternative2_telephone`,
`alternative2_mobile`,
`alternative2_fax`,
`alternative2_email`,
`remarks`,
`date_created`,
`user_id`,
`is_deleted`,
NULL `logo`,
'carrier' `agent_type` FROM smarttrack_staging.`agent_data` where  agent_code not in (select agent_code from smarttrack_relation.agent_data );




SELECT 
  scv.`constant_value`,
  sr.`id` AS service_id,
  sr.name,
  adr.id AS agent_id,
  adr.agent_code,
  scr.`id` AS constant_id,
  NOW(),
  '188',
  NOW(),
  '188' 
FROM
  `smarttrack_staging`.`service_constant_value` scv 
  JOIN `smarttrack_staging`.`service_constant` sc 
    ON sc.`id` = scv.`constant_id` 
  JOIN `smarttrack_relation`.`service_constant` scr 
    ON scr.`constant` = sc.`constant` 
  JOIN `smarttrack_staging`.`services` s 
    ON s.`id` = scv.`service_id` 
  JOIN `smarttrack_staging`.`agent_data` ad 
    ON ad.`id` = scv.`agent_id` 
  JOIN `smarttrack_relation`.`services` sr 
    ON sr.code = s.code 
  JOIN `smarttrack_relation`.`agent_data` adr 
    ON adr.`agent_code` = ad.`agent_code` ;
	
	
	
	ALTER TABLE `smarttrack_relation`.`agent_data` 
ADD INDEX `acode` (`agent_code` ASC)  COMMENT '',
ADD INDEX `aname` (`agent_name` ASC)  COMMENT '',
ADD INDEX `acountry` (`country_id` ASC)  COMMENT '',
ADD INDEX `auserid` (`user_id` ASC)  COMMENT '',
ADD INDEX `adeleted` (`is_deleted` ASC)  COMMENT '',
ADD INDEX `atype` (`agent_type` ASC)  COMMENT '';



ALTER TABLE `smarttrack_relation`.`customer_account`
ADD INDEX `parentid` (`parentid` ASC)  COMMENT '',
ADD INDEX `active` (`active_flag` ASC)  COMMENT '';

	
	
	