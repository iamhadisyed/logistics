<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OauthAccessTokensTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_access_tokens')->delete();
        
        \DB::table('oauth_access_tokens')->insert(array (
            0 => 
            array (
                'access_token' => 'fe73060e430030efe5ea3d8496b1b3c4d6c46c52',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 20:08:16',
                'scope' => NULL,
            ),
            1 => 
            array (
                'access_token' => 'fe735159264e9d59c0f280938157f7660739efc8',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:33:14',
                'scope' => NULL,
            ),
            2 => 
            array (
                'access_token' => 'fe742317306d4f271a5ddf1725d66a74041596ea',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 20:51:26',
                'scope' => NULL,
            ),
            3 => 
            array (
                'access_token' => 'fe7495c070b79225e48c884d7b1aa8bd8b2e08d3',
                'client_id' => 'tahirfinance',
                'user_id' => 2191,
                'expires' => '2019-11-29 20:40:51',
                'scope' => NULL,
            ),
            4 => 
            array (
                'access_token' => 'fe76012196c034b38ceeb4f758b5efef611eac5f',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-27 19:24:49',
                'scope' => NULL,
            ),
            5 => 
            array (
                'access_token' => 'fe763039b6fce9be7aae6a6a619cacb739dcf407',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 21:24:24',
                'scope' => NULL,
            ),
            6 => 
            array (
                'access_token' => 'fe76361f1a9a6bcd8d03fa87bf84118b11eb7436',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 17:04:36',
                'scope' => NULL,
            ),
            7 => 
            array (
                'access_token' => 'fe764919e16c1bfeb8e94d8985cda86f28c1258d',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:02:51',
                'scope' => NULL,
            ),
            8 => 
            array (
                'access_token' => 'fe76a7289fbcf2006c31fdac4e35f0c87f62b988',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:09:13',
                'scope' => NULL,
            ),
            9 => 
            array (
                'access_token' => 'fe774edfdafb2bfede3ec3a1f400bfadbf197101',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:18:31',
                'scope' => NULL,
            ),
            10 => 
            array (
                'access_token' => 'fe77fdfbe8406d794cc2fdbf25f2dac4af94a4a3',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:10:38',
                'scope' => NULL,
            ),
            11 => 
            array (
                'access_token' => 'fe78c4a2a44b06f5773e0a8f6f4b5c0f80c99094',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 23:21:46',
                'scope' => NULL,
            ),
            12 => 
            array (
                'access_token' => 'fe73060e430030efe5ea3d8496b1b3c4d6c46c52',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 20:08:16',
                'scope' => NULL,
            ),
            13 => 
            array (
                'access_token' => 'fe735159264e9d59c0f280938157f7660739efc8',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:33:14',
                'scope' => NULL,
            ),
            14 => 
            array (
                'access_token' => 'fe742317306d4f271a5ddf1725d66a74041596ea',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 20:51:26',
                'scope' => NULL,
            ),
            15 => 
            array (
                'access_token' => 'fe7495c070b79225e48c884d7b1aa8bd8b2e08d3',
                'client_id' => 'tahirfinance',
                'user_id' => 2191,
                'expires' => '2019-11-29 20:40:51',
                'scope' => NULL,
            ),
            16 => 
            array (
                'access_token' => 'fe76012196c034b38ceeb4f758b5efef611eac5f',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-27 19:24:49',
                'scope' => NULL,
            ),
            17 => 
            array (
                'access_token' => 'fe763039b6fce9be7aae6a6a619cacb739dcf407',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 21:24:24',
                'scope' => NULL,
            ),
            18 => 
            array (
                'access_token' => 'fe76361f1a9a6bcd8d03fa87bf84118b11eb7436',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 17:04:36',
                'scope' => NULL,
            ),
            19 => 
            array (
                'access_token' => 'fe764919e16c1bfeb8e94d8985cda86f28c1258d',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:02:51',
                'scope' => NULL,
            ),
            20 => 
            array (
                'access_token' => 'fe76a7289fbcf2006c31fdac4e35f0c87f62b988',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:09:13',
                'scope' => NULL,
            ),
            21 => 
            array (
                'access_token' => 'fe774edfdafb2bfede3ec3a1f400bfadbf197101',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:18:31',
                'scope' => NULL,
            ),
            22 => 
            array (
                'access_token' => 'fe77fdfbe8406d794cc2fdbf25f2dac4af94a4a3',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:10:38',
                'scope' => NULL,
            ),
            23 => 
            array (
                'access_token' => 'fe78c4a2a44b06f5773e0a8f6f4b5c0f80c99094',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 23:21:46',
                'scope' => NULL,
            ),
            24 => 
            array (
                'access_token' => 'fe73060e430030efe5ea3d8496b1b3c4d6c46c52',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 20:08:16',
                'scope' => NULL,
            ),
            25 => 
            array (
                'access_token' => 'fe735159264e9d59c0f280938157f7660739efc8',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:33:14',
                'scope' => NULL,
            ),
            26 => 
            array (
                'access_token' => 'fe742317306d4f271a5ddf1725d66a74041596ea',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 20:51:26',
                'scope' => NULL,
            ),
            27 => 
            array (
                'access_token' => 'fe7495c070b79225e48c884d7b1aa8bd8b2e08d3',
                'client_id' => 'tahirfinance',
                'user_id' => 2191,
                'expires' => '2019-11-29 20:40:51',
                'scope' => NULL,
            ),
            28 => 
            array (
                'access_token' => 'fe76012196c034b38ceeb4f758b5efef611eac5f',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-27 19:24:49',
                'scope' => NULL,
            ),
            29 => 
            array (
                'access_token' => 'fe763039b6fce9be7aae6a6a619cacb739dcf407',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 21:24:24',
                'scope' => NULL,
            ),
            30 => 
            array (
                'access_token' => 'fe76361f1a9a6bcd8d03fa87bf84118b11eb7436',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 17:04:36',
                'scope' => NULL,
            ),
            31 => 
            array (
                'access_token' => 'fe764919e16c1bfeb8e94d8985cda86f28c1258d',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:02:51',
                'scope' => NULL,
            ),
            32 => 
            array (
                'access_token' => 'fe76a7289fbcf2006c31fdac4e35f0c87f62b988',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:09:13',
                'scope' => NULL,
            ),
            33 => 
            array (
                'access_token' => 'fe774edfdafb2bfede3ec3a1f400bfadbf197101',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:18:31',
                'scope' => NULL,
            ),
            34 => 
            array (
                'access_token' => 'fe77fdfbe8406d794cc2fdbf25f2dac4af94a4a3',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:10:38',
                'scope' => NULL,
            ),
            35 => 
            array (
                'access_token' => 'fe78c4a2a44b06f5773e0a8f6f4b5c0f80c99094',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 23:21:46',
                'scope' => NULL,
            ),
            36 => 
            array (
                'access_token' => 'fe73060e430030efe5ea3d8496b1b3c4d6c46c52',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 20:08:16',
                'scope' => NULL,
            ),
            37 => 
            array (
                'access_token' => 'fe735159264e9d59c0f280938157f7660739efc8',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:33:14',
                'scope' => NULL,
            ),
            38 => 
            array (
                'access_token' => 'fe742317306d4f271a5ddf1725d66a74041596ea',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 20:51:26',
                'scope' => NULL,
            ),
            39 => 
            array (
                'access_token' => 'fe7495c070b79225e48c884d7b1aa8bd8b2e08d3',
                'client_id' => 'tahirfinance',
                'user_id' => 2191,
                'expires' => '2019-11-29 20:40:51',
                'scope' => NULL,
            ),
            40 => 
            array (
                'access_token' => 'fe76012196c034b38ceeb4f758b5efef611eac5f',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-27 19:24:49',
                'scope' => NULL,
            ),
            41 => 
            array (
                'access_token' => 'fe763039b6fce9be7aae6a6a619cacb739dcf407',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 21:24:24',
                'scope' => NULL,
            ),
            42 => 
            array (
                'access_token' => 'fe76361f1a9a6bcd8d03fa87bf84118b11eb7436',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 17:04:36',
                'scope' => NULL,
            ),
            43 => 
            array (
                'access_token' => 'fe764919e16c1bfeb8e94d8985cda86f28c1258d',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:02:51',
                'scope' => NULL,
            ),
            44 => 
            array (
                'access_token' => 'fe76a7289fbcf2006c31fdac4e35f0c87f62b988',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:09:13',
                'scope' => NULL,
            ),
            45 => 
            array (
                'access_token' => 'fe774edfdafb2bfede3ec3a1f400bfadbf197101',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:18:31',
                'scope' => NULL,
            ),
            46 => 
            array (
                'access_token' => 'fe77fdfbe8406d794cc2fdbf25f2dac4af94a4a3',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:10:38',
                'scope' => NULL,
            ),
            47 => 
            array (
                'access_token' => 'fe78c4a2a44b06f5773e0a8f6f4b5c0f80c99094',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 23:21:46',
                'scope' => NULL,
            ),
            48 => 
            array (
                'access_token' => 'fe73060e430030efe5ea3d8496b1b3c4d6c46c52',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 20:08:16',
                'scope' => NULL,
            ),
            49 => 
            array (
                'access_token' => 'fe735159264e9d59c0f280938157f7660739efc8',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:33:14',
                'scope' => NULL,
            ),
            50 => 
            array (
                'access_token' => 'fe742317306d4f271a5ddf1725d66a74041596ea',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 20:51:26',
                'scope' => NULL,
            ),
            51 => 
            array (
                'access_token' => 'fe7495c070b79225e48c884d7b1aa8bd8b2e08d3',
                'client_id' => 'tahirfinance',
                'user_id' => 2191,
                'expires' => '2019-11-29 20:40:51',
                'scope' => NULL,
            ),
            52 => 
            array (
                'access_token' => 'fe76012196c034b38ceeb4f758b5efef611eac5f',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-27 19:24:49',
                'scope' => NULL,
            ),
            53 => 
            array (
                'access_token' => 'fe763039b6fce9be7aae6a6a619cacb739dcf407',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 21:24:24',
                'scope' => NULL,
            ),
            54 => 
            array (
                'access_token' => 'fe76361f1a9a6bcd8d03fa87bf84118b11eb7436',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 17:04:36',
                'scope' => NULL,
            ),
            55 => 
            array (
                'access_token' => 'fe764919e16c1bfeb8e94d8985cda86f28c1258d',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:02:51',
                'scope' => NULL,
            ),
            56 => 
            array (
                'access_token' => 'fe76a7289fbcf2006c31fdac4e35f0c87f62b988',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:09:13',
                'scope' => NULL,
            ),
            57 => 
            array (
                'access_token' => 'fe774edfdafb2bfede3ec3a1f400bfadbf197101',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2019-11-18 16:18:31',
                'scope' => NULL,
            ),
            58 => 
            array (
                'access_token' => 'fe77fdfbe8406d794cc2fdbf25f2dac4af94a4a3',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 22:10:38',
                'scope' => NULL,
            ),
            59 => 
            array (
                'access_token' => 'fe78c4a2a44b06f5773e0a8f6f4b5c0f80c99094',
                'client_id' => 'developer',
                'user_id' => 58,
                'expires' => '2020-03-23 23:21:46',
                'scope' => NULL,
            ),
        ));
        
        
    }
}