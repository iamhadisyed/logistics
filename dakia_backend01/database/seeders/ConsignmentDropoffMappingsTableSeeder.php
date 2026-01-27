<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentDropoffMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_dropoff_mappings')->delete();
        
        \DB::table('consignment_dropoff_mappings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'dropoff_consignment_id' => 160151,
                'dispatch_consignment_id' => 160152,
                'dropoff_consignment_tracking' => '1Z3985RW6806068797',
                'dispatch_consignment_tracking' => 'JD0002210161165769',
                'parcel_tracking' => '{"1Z3985RW6806068797":"JD0002210161165769"}',
                'added_by' => 2191,
                'date_created' => '2020-02-20 12:50:49',
            ),
            1 => 
            array (
                'id' => 2,
                'dropoff_consignment_id' => 160188,
                'dispatch_consignment_id' => 160189,
                'dropoff_consignment_tracking' => '1Z3985RW6810874452',
                'dispatch_consignment_tracking' => 'JD0002210161165789',
                'parcel_tracking' => '{"1Z3985RW6810874452":"JD0002210161165789"}',
                'added_by' => 2253,
                'date_created' => '2020-02-24 13:01:58',
            ),
            2 => 
            array (
                'id' => 3,
                'dropoff_consignment_id' => 161898,
                'dispatch_consignment_id' => 161899,
                'dropoff_consignment_tracking' => '1Z3985RW6825860297',
                'dispatch_consignment_tracking' => 'JD0002210161166750',
                'parcel_tracking' => '{"1Z3985RW6825860297":"JD0002210161166750"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:32:27',
            ),
            3 => 
            array (
                'id' => 4,
                'dropoff_consignment_id' => 161902,
                'dispatch_consignment_id' => 161903,
                'dropoff_consignment_tracking' => '1Z3985RW6820134901',
                'dispatch_consignment_tracking' => 'JD0002210161166754',
                'parcel_tracking' => '{"1Z3985RW6820134901":"JD0002210161166754","1Z3985RW6815238354":"JD0002210161166755"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:40:17',
            ),
            4 => 
            array (
                'id' => 5,
                'dropoff_consignment_id' => 160118,
                'dispatch_consignment_id' => 161904,
                'dropoff_consignment_tracking' => '1Z3985RW6821558514',
                'dispatch_consignment_tracking' => 'JD0002210161166756',
                'parcel_tracking' => '{"1Z3985RW6821558514":"JD0002210161166756"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:41:44',
            ),
            5 => 
            array (
                'id' => 6,
                'dropoff_consignment_id' => 160096,
                'dispatch_consignment_id' => 161905,
                'dropoff_consignment_tracking' => '1Z3985RW6804886362',
                'dispatch_consignment_tracking' => 'JD0002210161166757',
                'parcel_tracking' => '{"1Z3985RW6804886362":"JD0002210161166757","1Z3985RW6824231129":"JD0002210161166758"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:42:56',
            ),
            6 => 
            array (
                'id' => 7,
                'dropoff_consignment_id' => 160095,
                'dispatch_consignment_id' => 161906,
                'dropoff_consignment_tracking' => '1Z3985RW6832252734',
                'dispatch_consignment_tracking' => 'JD0002210161166759',
                'parcel_tracking' => '{"1Z3985RW6832252734":"JD0002210161166759","1Z3985RW6812618976":"JD0002210161166760"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:43:23',
            ),
            7 => 
            array (
                'id' => 8,
                'dropoff_consignment_id' => 149128,
                'dispatch_consignment_id' => 161907,
                'dropoff_consignment_tracking' => '1Z3985RW6839723347',
                'dispatch_consignment_tracking' => 'JD0002210161166761',
                'parcel_tracking' => '{"1Z3985RW6839723347":"JD0002210161166761","1Z3985RW6830742959":"JD0002210161166762"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:44:57',
            ),
            8 => 
            array (
                'id' => 1,
                'dropoff_consignment_id' => 160151,
                'dispatch_consignment_id' => 160152,
                'dropoff_consignment_tracking' => '1Z3985RW6806068797',
                'dispatch_consignment_tracking' => 'JD0002210161165769',
                'parcel_tracking' => '{"1Z3985RW6806068797":"JD0002210161165769"}',
                'added_by' => 2191,
                'date_created' => '2020-02-20 12:50:49',
            ),
            9 => 
            array (
                'id' => 2,
                'dropoff_consignment_id' => 160188,
                'dispatch_consignment_id' => 160189,
                'dropoff_consignment_tracking' => '1Z3985RW6810874452',
                'dispatch_consignment_tracking' => 'JD0002210161165789',
                'parcel_tracking' => '{"1Z3985RW6810874452":"JD0002210161165789"}',
                'added_by' => 2253,
                'date_created' => '2020-02-24 13:01:58',
            ),
            10 => 
            array (
                'id' => 3,
                'dropoff_consignment_id' => 161898,
                'dispatch_consignment_id' => 161899,
                'dropoff_consignment_tracking' => '1Z3985RW6825860297',
                'dispatch_consignment_tracking' => 'JD0002210161166750',
                'parcel_tracking' => '{"1Z3985RW6825860297":"JD0002210161166750"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:32:27',
            ),
            11 => 
            array (
                'id' => 4,
                'dropoff_consignment_id' => 161902,
                'dispatch_consignment_id' => 161903,
                'dropoff_consignment_tracking' => '1Z3985RW6820134901',
                'dispatch_consignment_tracking' => 'JD0002210161166754',
                'parcel_tracking' => '{"1Z3985RW6820134901":"JD0002210161166754","1Z3985RW6815238354":"JD0002210161166755"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:40:17',
            ),
            12 => 
            array (
                'id' => 5,
                'dropoff_consignment_id' => 160118,
                'dispatch_consignment_id' => 161904,
                'dropoff_consignment_tracking' => '1Z3985RW6821558514',
                'dispatch_consignment_tracking' => 'JD0002210161166756',
                'parcel_tracking' => '{"1Z3985RW6821558514":"JD0002210161166756"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:41:44',
            ),
            13 => 
            array (
                'id' => 6,
                'dropoff_consignment_id' => 160096,
                'dispatch_consignment_id' => 161905,
                'dropoff_consignment_tracking' => '1Z3985RW6804886362',
                'dispatch_consignment_tracking' => 'JD0002210161166757',
                'parcel_tracking' => '{"1Z3985RW6804886362":"JD0002210161166757","1Z3985RW6824231129":"JD0002210161166758"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:42:56',
            ),
            14 => 
            array (
                'id' => 7,
                'dropoff_consignment_id' => 160095,
                'dispatch_consignment_id' => 161906,
                'dropoff_consignment_tracking' => '1Z3985RW6832252734',
                'dispatch_consignment_tracking' => 'JD0002210161166759',
                'parcel_tracking' => '{"1Z3985RW6832252734":"JD0002210161166759","1Z3985RW6812618976":"JD0002210161166760"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:43:23',
            ),
            15 => 
            array (
                'id' => 8,
                'dropoff_consignment_id' => 149128,
                'dispatch_consignment_id' => 161907,
                'dropoff_consignment_tracking' => '1Z3985RW6839723347',
                'dispatch_consignment_tracking' => 'JD0002210161166761',
                'parcel_tracking' => '{"1Z3985RW6839723347":"JD0002210161166761","1Z3985RW6830742959":"JD0002210161166762"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:44:57',
            ),
            16 => 
            array (
                'id' => 1,
                'dropoff_consignment_id' => 160151,
                'dispatch_consignment_id' => 160152,
                'dropoff_consignment_tracking' => '1Z3985RW6806068797',
                'dispatch_consignment_tracking' => 'JD0002210161165769',
                'parcel_tracking' => '{"1Z3985RW6806068797":"JD0002210161165769"}',
                'added_by' => 2191,
                'date_created' => '2020-02-20 12:50:49',
            ),
            17 => 
            array (
                'id' => 2,
                'dropoff_consignment_id' => 160188,
                'dispatch_consignment_id' => 160189,
                'dropoff_consignment_tracking' => '1Z3985RW6810874452',
                'dispatch_consignment_tracking' => 'JD0002210161165789',
                'parcel_tracking' => '{"1Z3985RW6810874452":"JD0002210161165789"}',
                'added_by' => 2253,
                'date_created' => '2020-02-24 13:01:58',
            ),
            18 => 
            array (
                'id' => 3,
                'dropoff_consignment_id' => 161898,
                'dispatch_consignment_id' => 161899,
                'dropoff_consignment_tracking' => '1Z3985RW6825860297',
                'dispatch_consignment_tracking' => 'JD0002210161166750',
                'parcel_tracking' => '{"1Z3985RW6825860297":"JD0002210161166750"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:32:27',
            ),
            19 => 
            array (
                'id' => 4,
                'dropoff_consignment_id' => 161902,
                'dispatch_consignment_id' => 161903,
                'dropoff_consignment_tracking' => '1Z3985RW6820134901',
                'dispatch_consignment_tracking' => 'JD0002210161166754',
                'parcel_tracking' => '{"1Z3985RW6820134901":"JD0002210161166754","1Z3985RW6815238354":"JD0002210161166755"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:40:17',
            ),
            20 => 
            array (
                'id' => 5,
                'dropoff_consignment_id' => 160118,
                'dispatch_consignment_id' => 161904,
                'dropoff_consignment_tracking' => '1Z3985RW6821558514',
                'dispatch_consignment_tracking' => 'JD0002210161166756',
                'parcel_tracking' => '{"1Z3985RW6821558514":"JD0002210161166756"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:41:44',
            ),
            21 => 
            array (
                'id' => 6,
                'dropoff_consignment_id' => 160096,
                'dispatch_consignment_id' => 161905,
                'dropoff_consignment_tracking' => '1Z3985RW6804886362',
                'dispatch_consignment_tracking' => 'JD0002210161166757',
                'parcel_tracking' => '{"1Z3985RW6804886362":"JD0002210161166757","1Z3985RW6824231129":"JD0002210161166758"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:42:56',
            ),
            22 => 
            array (
                'id' => 7,
                'dropoff_consignment_id' => 160095,
                'dispatch_consignment_id' => 161906,
                'dropoff_consignment_tracking' => '1Z3985RW6832252734',
                'dispatch_consignment_tracking' => 'JD0002210161166759',
                'parcel_tracking' => '{"1Z3985RW6832252734":"JD0002210161166759","1Z3985RW6812618976":"JD0002210161166760"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:43:23',
            ),
            23 => 
            array (
                'id' => 8,
                'dropoff_consignment_id' => 149128,
                'dispatch_consignment_id' => 161907,
                'dropoff_consignment_tracking' => '1Z3985RW6839723347',
                'dispatch_consignment_tracking' => 'JD0002210161166761',
                'parcel_tracking' => '{"1Z3985RW6839723347":"JD0002210161166761","1Z3985RW6830742959":"JD0002210161166762"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:44:57',
            ),
            24 => 
            array (
                'id' => 1,
                'dropoff_consignment_id' => 160151,
                'dispatch_consignment_id' => 160152,
                'dropoff_consignment_tracking' => '1Z3985RW6806068797',
                'dispatch_consignment_tracking' => 'JD0002210161165769',
                'parcel_tracking' => '{"1Z3985RW6806068797":"JD0002210161165769"}',
                'added_by' => 2191,
                'date_created' => '2020-02-20 12:50:49',
            ),
            25 => 
            array (
                'id' => 2,
                'dropoff_consignment_id' => 160188,
                'dispatch_consignment_id' => 160189,
                'dropoff_consignment_tracking' => '1Z3985RW6810874452',
                'dispatch_consignment_tracking' => 'JD0002210161165789',
                'parcel_tracking' => '{"1Z3985RW6810874452":"JD0002210161165789"}',
                'added_by' => 2253,
                'date_created' => '2020-02-24 13:01:58',
            ),
            26 => 
            array (
                'id' => 3,
                'dropoff_consignment_id' => 161898,
                'dispatch_consignment_id' => 161899,
                'dropoff_consignment_tracking' => '1Z3985RW6825860297',
                'dispatch_consignment_tracking' => 'JD0002210161166750',
                'parcel_tracking' => '{"1Z3985RW6825860297":"JD0002210161166750"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:32:27',
            ),
            27 => 
            array (
                'id' => 4,
                'dropoff_consignment_id' => 161902,
                'dispatch_consignment_id' => 161903,
                'dropoff_consignment_tracking' => '1Z3985RW6820134901',
                'dispatch_consignment_tracking' => 'JD0002210161166754',
                'parcel_tracking' => '{"1Z3985RW6820134901":"JD0002210161166754","1Z3985RW6815238354":"JD0002210161166755"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:40:17',
            ),
            28 => 
            array (
                'id' => 5,
                'dropoff_consignment_id' => 160118,
                'dispatch_consignment_id' => 161904,
                'dropoff_consignment_tracking' => '1Z3985RW6821558514',
                'dispatch_consignment_tracking' => 'JD0002210161166756',
                'parcel_tracking' => '{"1Z3985RW6821558514":"JD0002210161166756"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:41:44',
            ),
            29 => 
            array (
                'id' => 6,
                'dropoff_consignment_id' => 160096,
                'dispatch_consignment_id' => 161905,
                'dropoff_consignment_tracking' => '1Z3985RW6804886362',
                'dispatch_consignment_tracking' => 'JD0002210161166757',
                'parcel_tracking' => '{"1Z3985RW6804886362":"JD0002210161166757","1Z3985RW6824231129":"JD0002210161166758"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:42:56',
            ),
            30 => 
            array (
                'id' => 7,
                'dropoff_consignment_id' => 160095,
                'dispatch_consignment_id' => 161906,
                'dropoff_consignment_tracking' => '1Z3985RW6832252734',
                'dispatch_consignment_tracking' => 'JD0002210161166759',
                'parcel_tracking' => '{"1Z3985RW6832252734":"JD0002210161166759","1Z3985RW6812618976":"JD0002210161166760"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:43:23',
            ),
            31 => 
            array (
                'id' => 8,
                'dropoff_consignment_id' => 149128,
                'dispatch_consignment_id' => 161907,
                'dropoff_consignment_tracking' => '1Z3985RW6839723347',
                'dispatch_consignment_tracking' => 'JD0002210161166761',
                'parcel_tracking' => '{"1Z3985RW6839723347":"JD0002210161166761","1Z3985RW6830742959":"JD0002210161166762"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:44:57',
            ),
            32 => 
            array (
                'id' => 1,
                'dropoff_consignment_id' => 160151,
                'dispatch_consignment_id' => 160152,
                'dropoff_consignment_tracking' => '1Z3985RW6806068797',
                'dispatch_consignment_tracking' => 'JD0002210161165769',
                'parcel_tracking' => '{"1Z3985RW6806068797":"JD0002210161165769"}',
                'added_by' => 2191,
                'date_created' => '2020-02-20 12:50:49',
            ),
            33 => 
            array (
                'id' => 2,
                'dropoff_consignment_id' => 160188,
                'dispatch_consignment_id' => 160189,
                'dropoff_consignment_tracking' => '1Z3985RW6810874452',
                'dispatch_consignment_tracking' => 'JD0002210161165789',
                'parcel_tracking' => '{"1Z3985RW6810874452":"JD0002210161165789"}',
                'added_by' => 2253,
                'date_created' => '2020-02-24 13:01:58',
            ),
            34 => 
            array (
                'id' => 3,
                'dropoff_consignment_id' => 161898,
                'dispatch_consignment_id' => 161899,
                'dropoff_consignment_tracking' => '1Z3985RW6825860297',
                'dispatch_consignment_tracking' => 'JD0002210161166750',
                'parcel_tracking' => '{"1Z3985RW6825860297":"JD0002210161166750"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:32:27',
            ),
            35 => 
            array (
                'id' => 4,
                'dropoff_consignment_id' => 161902,
                'dispatch_consignment_id' => 161903,
                'dropoff_consignment_tracking' => '1Z3985RW6820134901',
                'dispatch_consignment_tracking' => 'JD0002210161166754',
                'parcel_tracking' => '{"1Z3985RW6820134901":"JD0002210161166754","1Z3985RW6815238354":"JD0002210161166755"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:40:17',
            ),
            36 => 
            array (
                'id' => 5,
                'dropoff_consignment_id' => 160118,
                'dispatch_consignment_id' => 161904,
                'dropoff_consignment_tracking' => '1Z3985RW6821558514',
                'dispatch_consignment_tracking' => 'JD0002210161166756',
                'parcel_tracking' => '{"1Z3985RW6821558514":"JD0002210161166756"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:41:44',
            ),
            37 => 
            array (
                'id' => 6,
                'dropoff_consignment_id' => 160096,
                'dispatch_consignment_id' => 161905,
                'dropoff_consignment_tracking' => '1Z3985RW6804886362',
                'dispatch_consignment_tracking' => 'JD0002210161166757',
                'parcel_tracking' => '{"1Z3985RW6804886362":"JD0002210161166757","1Z3985RW6824231129":"JD0002210161166758"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:42:56',
            ),
            38 => 
            array (
                'id' => 7,
                'dropoff_consignment_id' => 160095,
                'dispatch_consignment_id' => 161906,
                'dropoff_consignment_tracking' => '1Z3985RW6832252734',
                'dispatch_consignment_tracking' => 'JD0002210161166759',
                'parcel_tracking' => '{"1Z3985RW6832252734":"JD0002210161166759","1Z3985RW6812618976":"JD0002210161166760"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:43:23',
            ),
            39 => 
            array (
                'id' => 8,
                'dropoff_consignment_id' => 149128,
                'dispatch_consignment_id' => 161907,
                'dropoff_consignment_tracking' => '1Z3985RW6839723347',
                'dispatch_consignment_tracking' => 'JD0002210161166761',
                'parcel_tracking' => '{"1Z3985RW6839723347":"JD0002210161166761","1Z3985RW6830742959":"JD0002210161166762"}',
                'added_by' => 2253,
                'date_created' => '2020-03-03 13:44:57',
            ),
        ));
        
        
    }
}