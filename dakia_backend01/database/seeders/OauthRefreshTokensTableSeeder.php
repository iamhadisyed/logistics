<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthRefreshTokensTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_refresh_tokens')->delete();
        
        \DB::table('oauth_refresh_tokens')->insert(array (
            0 => 
            array (
                'refresh_token' => 'fe9944ab1578dd1529d1eef7af4a90728bddf291',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 23:16:13',
                'scope' => NULL,
            ),
            1 => 
            array (
                'refresh_token' => 'fe994f0b25add1dca17f0e35a7275f483fac280a',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:04:48',
                'scope' => NULL,
            ),
            2 => 
            array (
                'refresh_token' => 'fe9a34af9e3e17c5648115e83e109e1495036813',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 20:24:59',
                'scope' => NULL,
            ),
            3 => 
            array (
                'refresh_token' => 'fe9a59f8d4d20b6314c17d2c841459ac459fa11e',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:14:35',
                'scope' => NULL,
            ),
            4 => 
            array (
                'refresh_token' => 'fe9b0aa458e8b8aedc8fad5d0a55219ebb51545b',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 21:15:31',
                'scope' => NULL,
            ),
            5 => 
            array (
                'refresh_token' => 'fe9b4fadba8a2d54c8aa693f2e7943b3e92196d9',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 22:31:33',
                'scope' => NULL,
            ),
            6 => 
            array (
                'refresh_token' => 'fe9c087cc1a46e06df2ef246395b40ae0d186adb',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 18:58:49',
                'scope' => NULL,
            ),
            7 => 
            array (
                'refresh_token' => 'fe9c364bc92d1cc33f257d771c40b742701c3788',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 17:41:26',
                'scope' => NULL,
            ),
            8 => 
            array (
                'refresh_token' => 'fe9cce004c2204dd915511935b4d9fce693ae95c',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:02:17',
                'scope' => NULL,
            ),
            9 => 
            array (
                'refresh_token' => 'fe9d7bdd441500c9f8c556b452afba75e489e4e0',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 18:03:03',
                'scope' => NULL,
            ),
            10 => 
            array (
                'refresh_token' => 'fe9944ab1578dd1529d1eef7af4a90728bddf291',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 23:16:13',
                'scope' => NULL,
            ),
            11 => 
            array (
                'refresh_token' => 'fe994f0b25add1dca17f0e35a7275f483fac280a',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:04:48',
                'scope' => NULL,
            ),
            12 => 
            array (
                'refresh_token' => 'fe9a34af9e3e17c5648115e83e109e1495036813',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 20:24:59',
                'scope' => NULL,
            ),
            13 => 
            array (
                'refresh_token' => 'fe9a59f8d4d20b6314c17d2c841459ac459fa11e',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:14:35',
                'scope' => NULL,
            ),
            14 => 
            array (
                'refresh_token' => 'fe9b0aa458e8b8aedc8fad5d0a55219ebb51545b',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 21:15:31',
                'scope' => NULL,
            ),
            15 => 
            array (
                'refresh_token' => 'fe9b4fadba8a2d54c8aa693f2e7943b3e92196d9',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 22:31:33',
                'scope' => NULL,
            ),
            16 => 
            array (
                'refresh_token' => 'fe9c087cc1a46e06df2ef246395b40ae0d186adb',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 18:58:49',
                'scope' => NULL,
            ),
            17 => 
            array (
                'refresh_token' => 'fe9c364bc92d1cc33f257d771c40b742701c3788',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 17:41:26',
                'scope' => NULL,
            ),
            18 => 
            array (
                'refresh_token' => 'fe9cce004c2204dd915511935b4d9fce693ae95c',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:02:17',
                'scope' => NULL,
            ),
            19 => 
            array (
                'refresh_token' => 'fe9d7bdd441500c9f8c556b452afba75e489e4e0',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 18:03:03',
                'scope' => NULL,
            ),
            20 => 
            array (
                'refresh_token' => 'fe9944ab1578dd1529d1eef7af4a90728bddf291',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 23:16:13',
                'scope' => NULL,
            ),
            21 => 
            array (
                'refresh_token' => 'fe994f0b25add1dca17f0e35a7275f483fac280a',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:04:48',
                'scope' => NULL,
            ),
            22 => 
            array (
                'refresh_token' => 'fe9a34af9e3e17c5648115e83e109e1495036813',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 20:24:59',
                'scope' => NULL,
            ),
            23 => 
            array (
                'refresh_token' => 'fe9a59f8d4d20b6314c17d2c841459ac459fa11e',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:14:35',
                'scope' => NULL,
            ),
            24 => 
            array (
                'refresh_token' => 'fe9b0aa458e8b8aedc8fad5d0a55219ebb51545b',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 21:15:31',
                'scope' => NULL,
            ),
            25 => 
            array (
                'refresh_token' => 'fe9b4fadba8a2d54c8aa693f2e7943b3e92196d9',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 22:31:33',
                'scope' => NULL,
            ),
            26 => 
            array (
                'refresh_token' => 'fe9c087cc1a46e06df2ef246395b40ae0d186adb',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 18:58:49',
                'scope' => NULL,
            ),
            27 => 
            array (
                'refresh_token' => 'fe9c364bc92d1cc33f257d771c40b742701c3788',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 17:41:26',
                'scope' => NULL,
            ),
            28 => 
            array (
                'refresh_token' => 'fe9cce004c2204dd915511935b4d9fce693ae95c',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:02:17',
                'scope' => NULL,
            ),
            29 => 
            array (
                'refresh_token' => 'fe9d7bdd441500c9f8c556b452afba75e489e4e0',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 18:03:03',
                'scope' => NULL,
            ),
            30 => 
            array (
                'refresh_token' => 'fe9944ab1578dd1529d1eef7af4a90728bddf291',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 23:16:13',
                'scope' => NULL,
            ),
            31 => 
            array (
                'refresh_token' => 'fe994f0b25add1dca17f0e35a7275f483fac280a',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:04:48',
                'scope' => NULL,
            ),
            32 => 
            array (
                'refresh_token' => 'fe9a34af9e3e17c5648115e83e109e1495036813',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 20:24:59',
                'scope' => NULL,
            ),
            33 => 
            array (
                'refresh_token' => 'fe9a59f8d4d20b6314c17d2c841459ac459fa11e',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:14:35',
                'scope' => NULL,
            ),
            34 => 
            array (
                'refresh_token' => 'fe9b0aa458e8b8aedc8fad5d0a55219ebb51545b',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 21:15:31',
                'scope' => NULL,
            ),
            35 => 
            array (
                'refresh_token' => 'fe9b4fadba8a2d54c8aa693f2e7943b3e92196d9',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 22:31:33',
                'scope' => NULL,
            ),
            36 => 
            array (
                'refresh_token' => 'fe9c087cc1a46e06df2ef246395b40ae0d186adb',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 18:58:49',
                'scope' => NULL,
            ),
            37 => 
            array (
                'refresh_token' => 'fe9c364bc92d1cc33f257d771c40b742701c3788',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 17:41:26',
                'scope' => NULL,
            ),
            38 => 
            array (
                'refresh_token' => 'fe9cce004c2204dd915511935b4d9fce693ae95c',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:02:17',
                'scope' => NULL,
            ),
            39 => 
            array (
                'refresh_token' => 'fe9d7bdd441500c9f8c556b452afba75e489e4e0',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 18:03:03',
                'scope' => NULL,
            ),
            40 => 
            array (
                'refresh_token' => 'fe9944ab1578dd1529d1eef7af4a90728bddf291',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 23:16:13',
                'scope' => NULL,
            ),
            41 => 
            array (
                'refresh_token' => 'fe994f0b25add1dca17f0e35a7275f483fac280a',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:04:48',
                'scope' => NULL,
            ),
            42 => 
            array (
                'refresh_token' => 'fe9a34af9e3e17c5648115e83e109e1495036813',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 20:24:59',
                'scope' => NULL,
            ),
            43 => 
            array (
                'refresh_token' => 'fe9a59f8d4d20b6314c17d2c841459ac459fa11e',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:14:35',
                'scope' => NULL,
            ),
            44 => 
            array (
                'refresh_token' => 'fe9b0aa458e8b8aedc8fad5d0a55219ebb51545b',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 21:15:31',
                'scope' => NULL,
            ),
            45 => 
            array (
                'refresh_token' => 'fe9b4fadba8a2d54c8aa693f2e7943b3e92196d9',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-04-06 22:31:33',
                'scope' => NULL,
            ),
            46 => 
            array (
                'refresh_token' => 'fe9c087cc1a46e06df2ef246395b40ae0d186adb',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 18:58:49',
                'scope' => NULL,
            ),
            47 => 
            array (
                'refresh_token' => 'fe9c364bc92d1cc33f257d771c40b742701c3788',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 17:41:26',
                'scope' => NULL,
            ),
            48 => 
            array (
                'refresh_token' => 'fe9cce004c2204dd915511935b4d9fce693ae95c',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-02 16:02:17',
                'scope' => NULL,
            ),
            49 => 
            array (
                'refresh_token' => 'fe9d7bdd441500c9f8c556b452afba75e489e4e0',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-12-11 18:03:03',
                'scope' => NULL,
            ),
        ));
        
        
    }
}