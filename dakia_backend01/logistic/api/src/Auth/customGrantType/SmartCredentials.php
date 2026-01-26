<?php

namespace Auth\customGrantType;

use Auth\customGrantType\ClientAssertionType\HttpSmartBasic;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\ResponseType\AccessTokenInterface;

class SmartCredentials extends HttpSmartBasic implements GrantTypeInterface
{
    /**
     * @var array
     */
    private $clientData;

    /**
     * @param ClientCredentialsInterface $storage
     * @param array $config
     */
    public function __construct(SmartCredentialsInterface $storage, array $config = array())
    {
//	    echo "inside smartcredential smarttrack mobile app"; die();
        /**
         * The client credentials grant type MUST only be used by confidential clients
         *
         * @see http://tools.ietf.org/html/rfc6749#section-4.4
         */
        $config['allow_public_clients'] = false;

        parent::__construct($storage, $config);
    }

    /**
     * Get query string identifier
     *
     * @return string
     */
    public function getQueryStringIdentifier()
    {
        return 'smarttrack_app';
    }

    /**
     * Get scope
     *
     * @return string|null
     */
    public function getScope()
    {
        $this->loadClientData();

        return isset($this->clientData['scope']) ? $this->clientData['scope'] : null;
    }

    /**
     * Get user id
     *
     * @return mixed
     */
    public function getUserId()
    {
        $this->loadClientData();

        return isset($this->clientData['user_id']) ? $this->clientData['user_id'] : null;
    }

    /**
     * Create access token
     *
     * @param AccessTokenInterface $accessToken
     * @param mixed                $client_id   - client identifier related to the access token.
     * @param mixed                $user_id     - user id associated with the access token
     * @param string               $scope       - scopes to be stored in space-separated string.
     * @return array
     */
    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        /**
         * Client Credentials Grant does NOT include a refresh token
         *
         * @see http://tools.ietf.org/html/rfc6749#section-4.4.3
         */
        $includeRefreshToken = true;

        return $accessToken->createAccessToken($client_id, $user_id, $scope, $includeRefreshToken);
    }

    private function loadClientData()
    {
        if (!$this->clientData) {
	        $clientData = $this->storage->getClientDetailsByUserName($this->getClientId());
	        $clientData['user_id'] = isset($clientData['id']) ? $clientData['id'] : "";
	        $this->clientData = $clientData;
        }
    }
}
