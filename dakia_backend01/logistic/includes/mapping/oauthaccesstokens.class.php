<?php

class OauthAccessTokens extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
        (
            'access_token' => 'string',
            'client_id' => 'string',
            'user_id' => 'number',
            'expires' => 'datetime',
            'scope' => 'string'
        );
        //
        parent::__construct("oauth_access_tokens", 'access_token', $fieldList, $mixedCreator);
    }

    public static function getOauthAccessTokensListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getAccessToken($token) {
        $sql = "SELECT * FROM oauth_access_tokens WHERE access_token = '". DbAccess3::escape($token)."'";
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data;
    }
}

// class
