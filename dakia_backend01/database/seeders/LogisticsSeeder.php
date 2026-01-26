<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Carrier;
use App\Models\Service;
use App\Models\Address;
use App\Models\Consignment;

class LogisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample countries
        $countries = [
            [
                'iso' => 'GB',
                'name' => 'United Kingdom',
                'region' => 'Europe',
                'postcode_required' => 'YES',
                'printable_name' => 'United Kingdom',
                'iso3' => 'GBR',
                'has_postcodeq' => 'Y',
                'has_subzonesq' => 'N',
                'orderq' => 1,
                'active' => 1,
                'deletedq' => 'N',
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
            [
                'iso' => 'US',
                'name' => 'United States',
                'region' => 'North America',
                'postcode_required' => 'YES',
                'printable_name' => 'United States',
                'iso3' => 'USA',
                'has_postcodeq' => 'Y',
                'has_subzonesq' => 'N',
                'orderq' => 2,
                'active' => 1,
                'deletedq' => 'N',
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
            [
                'iso' => 'DE',
                'name' => 'Germany',
                'region' => 'Europe',
                'postcode_required' => 'YES',
                'printable_name' => 'Germany',
                'iso3' => 'DEU',
                'has_postcodeq' => 'Y',
                'has_subzonesq' => 'N',
                'orderq' => 3,
                'active' => 1,
                'deletedq' => 'N',
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
        ];

        foreach ($countries as $countryData) {
            Country::create($countryData);
        }

        // Create sample carriers
        $carriers = [
            [
                'carrier' => 'DHL',
                'carrier_display_name' => 'DHL Express',
                'status' => 1,
                'country_id' => 1,
                'currency_code' => 'GBP',
                'remotearea_check' => 'c',
                'zone_base' => false,
                'zone_type' => 'country',
                'on_contract' => true,
                'is_gazetteer' => false,
                'is_reconcile' => 0,
            ],
            [
                'carrier' => 'UPS',
                'carrier_display_name' => 'UPS',
                'status' => 1,
                'country_id' => 2,
                'currency_code' => 'USD',
                'remotearea_check' => 'c',
                'zone_base' => false,
                'zone_type' => 'country',
                'on_contract' => true,
                'is_gazetteer' => false,
                'is_reconcile' => 0,
            ],
            [
                'carrier' => 'FedEx',
                'carrier_display_name' => 'FedEx',
                'status' => 1,
                'country_id' => 2,
                'currency_code' => 'USD',
                'remotearea_check' => 'c',
                'zone_base' => false,
                'zone_type' => 'country',
                'on_contract' => true,
                'is_gazetteer' => false,
                'is_reconcile' => 0,
            ],
        ];

        foreach ($carriers as $carrierData) {
            Carrier::create($carrierData);
        }

        // Create sample services
        $services = [
            [
                'name' => 'DHL Express Worldwide',
                'code' => 'DHL-EW',
                'carrier_id' => 1,
                'service_type' => 'D',
                'description' => 'DHL Express Worldwide service',
                'fuel_surcharge' => 15.50,
                'fuel_surcharge_type' => '%',
                'max_length' => 120.00,
                'max_width' => 80.00,
                'max_height' => 80.00,
                'volumetric_denominator' => 5000,
                'active' => true,
                'deletedq' => false,
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
            [
                'name' => 'UPS Standard',
                'code' => 'UPS-STD',
                'carrier_id' => 2,
                'service_type' => 'D',
                'description' => 'UPS Standard service',
                'fuel_surcharge' => 12.00,
                'fuel_surcharge_type' => '%',
                'max_length' => 120.00,
                'max_width' => 80.00,
                'max_height' => 80.00,
                'volumetric_denominator' => 5000,
                'active' => true,
                'deletedq' => false,
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
            [
                'name' => 'FedEx International Priority',
                'code' => 'FEDEX-IP',
                'carrier_id' => 3,
                'service_type' => 'D',
                'description' => 'FedEx International Priority service',
                'fuel_surcharge' => 18.00,
                'fuel_surcharge_type' => '%',
                'max_length' => 120.00,
                'max_width' => 80.00,
                'max_height' => 80.00,
                'volumetric_denominator' => 5000,
                'active' => true,
                'deletedq' => false,
                'added_on' => now(),
                'added_by' => 'seeder',
                'changed_on' => now(),
                'changed_by' => 'seeder',
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }

        // Create sample addresses
        $addresses = [
            [
                'phone_number' => '+44 20 7123 4567',
                'company' => 'Tech Solutions Ltd',
                'contact' => 'John Smith',
                'email' => 'john@techsolutions.com',
                'address_line_1' => '123 Business Street',
                'address_line_2' => 'Suite 100',
                'city' => 'London',
                'country' => 'GB',
                'postcode' => 'SW1A 1AA',
                'state' => 'England',
            ],
            [
                'phone_number' => '+1 555 123 4567',
                'company' => 'Global Corp',
                'contact' => 'Jane Doe',
                'email' => 'jane@globalcorp.com',
                'address_line_1' => '456 Corporate Avenue',
                'address_line_2' => 'Floor 25',
                'city' => 'New York',
                'country' => 'US',
                'postcode' => '10001',
                'state' => 'NY',
            ],
        ];

        foreach ($addresses as $addressData) {
            Address::create($addressData);
        }

        // Create sample consignments
        $consignments = [
            [
                'service_id' => 1,
                'hawb' => 'DHL001',
                'reference' => 'REF001',
                'company' => 'Tech Solutions Ltd',
                'contact' => 'John Smith',
                'address_line_1' => '123 Business Street',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'country_id' => 1,
                'telephone' => '+44 20 7123 4567',
                'email' => 'john@techsolutions.com',
                'weight' => 2.5,
                'value' => 150.00,
                'currency' => 'GBP',
                'description' => 'Electronics shipment',
                'sender_name' => 'Sender Company',
                'sender_company' => 'Sender Corp',
                'sender_email' => 'sender@sender.com',
                'sender_telephone' => '+44 20 7654 3210',
                'sender_address_line_1' => '456 Sender Street',
                'sender_city' => 'Manchester',
                'sender_postcode' => 'M1 1AA',
                'sender_country_id' => 1,
                'consignment_status' => 'pending',
                'shipment_type' => 'D',
            ],
            [
                'service_id' => 2,
                'hawb' => 'UPS002',
                'reference' => 'REF002',
                'company' => 'Global Corp',
                'contact' => 'Jane Doe',
                'address_line_1' => '456 Corporate Avenue',
                'city' => 'New York',
                'postcode' => '10001',
                'country_id' => 2,
                'telephone' => '+1 555 123 4567',
                'email' => 'jane@globalcorp.com',
                'weight' => 1.8,
                'value' => 200.00,
                'currency' => 'USD',
                'description' => 'Document shipment',
                'sender_name' => 'UK Sender',
                'sender_company' => 'UK Sender Ltd',
                'sender_email' => 'uk@uk.com',
                'sender_telephone' => '+44 20 1111 2222',
                'sender_address_line_1' => '789 UK Street',
                'sender_city' => 'Birmingham',
                'sender_postcode' => 'B1 1AA',
                'sender_country_id' => 1,
                'consignment_status' => 'in_transit',
                'shipment_type' => 'D',
            ],
        ];

        foreach ($consignments as $consignmentData) {
            Consignment::create($consignmentData);
        }
    }
}
