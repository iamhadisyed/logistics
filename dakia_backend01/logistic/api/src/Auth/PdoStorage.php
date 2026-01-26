<?php

namespace Auth;

use Auth\customGrantType\SmartCredentialsInterface;
use OAuth2\Storage\Pdo;

class PdoStorage extends Pdo implements SmartCredentialsInterface {

    protected function checkPassword($user, $password) {
        return password_verify($password, $user['user_pass']);
    }

//    public function checkClientCredentials($client_id, $client_secret = null)
//    {
//        $stmt = $this->db->prepare(sprintf('SELECT * from %s
//            where client_id = :client_id', $this->config['client_table']));
//        $stmt->execute(compact('client_id'));
//        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
//
//        return $result && password_verify($client_secret, $result['client_secret']);
//    }
    public function checkClientCredentials($client_id, $client_secret = null) {
        $stmt = $this->db->prepare(sprintf('SELECT * from %s where api_key = :client_id AND status=1 ', $this->config['client_table']));
        $stmt->execute(compact('client_id'));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($result['user_id'])) {
            return $result && "" == $client_secret;
        }

        $stmtUsr = $this->db->prepare(sprintf('SELECT * from %s where id = ' . $result['user_id'] . ' AND active_flag=1 ', $this->config['user_table']));
        $stmtUsr->execute(compact('client_id'));
        $resultUsr = $stmtUsr->fetch(\PDO::FETCH_ASSOC);

        if ($resultUsr['id'] == $result['user_id']) {
            return $result && $result['api_secrete'] == $client_secret;
        } else {
            return $result && "" == $client_secret;
        }
        // make this extensible
    }

    public function checkSmartCredentials($client_id, $client_secret = null) {
        $stmt = $this->db->prepare(sprintf('SELECT * from %s where user_name = :client_id AND active_flag=1', $this->config['user_table']));
        $stmt->execute(compact('client_id'));
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        // make this extensible
        return $result && password_verify($client_secret, $result['user_pass']);
//		return $result && $result['api_secrete'] == $client_secret;
    }

    /**
     * @param string $client_id
     * @return array|mixed
     */
    public function getClientDetails($client_id) {
        $stmt = $this->db->prepare(sprintf('SELECT * from %s where api_key = :client_id', $this->config['client_table']));
        $stmt->execute(compact('client_id'));

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $client_id
     * @return array|mixed
     */
    public function getClientDetailsByUserName($user_name) {
        $stmt = $this->db->prepare(sprintf('SELECT * from %s where user_name = :user_name', $this->config['user_table']));
        $stmt->execute(compact('user_name'));

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function verifyPlatFrom($client_id, $client_secret, $platform) {
        if (!is_null($platform) && $platform == "smarttrack_app") {
            return true;
        }
        if (is_null($platform)) {
            $platform = "smarttrack";
        }

        $stmt = $this->db->prepare("SELECT * from " . $this->config['client_table'] . " as `usp` INNER JOIN " . $this->config['client_platform'] . " as `sp` ON `usp`.`shopping_platform_id`=`sp`.`id` WHERE `usp`.`api_key` = '" . $client_id . "' AND `usp`.`api_secrete` = '" . $client_secret . "' AND `sp`.`plugin_key` = '" . $platform . "'");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

//	public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
//	{
//		// convert expires to datestring
//		$expires = date('Y-m-d H:i:s', $expires);
//		//cheapyfor now ...fix later
////		$cheapyAccessTokaen = substr($access_token, 0, 40);
////		$access_token = substr(str_shuffle(str_repeat($access_token.$user_id, mt_rand(1,2))),1,40);
//		// if it exists, update it.
//		if ($this->getAccessToken($access_token)) {
//			$stmt = $this->db->prepare(sprintf('UPDATE %s SET client_id=:client_id, expires=:expires, user_id=:user_id, scope=:scope where access_token=:access_token', $this->config['access_token_table']));
//		} else {
//			$stmt = $this->db->prepare(sprintf('INSERT INTO %s (access_token, client_id, expires, user_id, scope) VALUES (:access_token, :client_id, :expires, :user_id, :scope)', $this->config['access_token_table']));
//		}
//
//		return $stmt->execute(compact('access_token', 'client_id', 'user_id', 'expires', 'scope'));
//	}
}
