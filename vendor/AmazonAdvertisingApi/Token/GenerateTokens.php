<?php

namespace AmazonAdvertisingApi\Token;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Connection\CurlRequest;
use App\ApplicationModul\AppManagement\Model\AmazonAdsConfigTable;
use Exception;

/**
 * Práca z generovaným tokenov
 */
class GenerateTokens
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var string Presmerovanie stránky pri generovani Code a Refresh Token
     */
    private $redirectUri;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        // na webe to treba nastaviť na stranku na ktorej to bude vysieť a presne to nasmerovat na tuto metodu ::NASTAV
        $this->redirectUri = 'https://adsportal.metalo.sk/app-management/generate-refresh-token';//'https://www.cim-fit.eu/testy';
    }

    /**
     ** Vygeneruje Url na generovanie codu pre Refresh Token
     * @return string vygenerovaná url
     */
    public function generateRefreshUrl()
    {
        $url = 'https://' . $this->connection->amazonAdsRegionTable->getCodeUrl() . '?';

        $params = [
            'client_id' => $this->connection->amazonAdsConfigTable->getClientId(),
            'scope' => 'advertising::campaign_management',
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri
        ];

        foreach ($params as $k => $v)
        {
            $url .= "{$k}=".($v)."&";
        }
        return rtrim($url, "&");
    }

    /**
     ** Spusti obnovu Access Token
     * @return void
     * @throws Exception
     */
    public function refreshAccessToken()
    {
        $params = [
            "grant_type" => "refresh_token",
            "refresh_token" => $this->connection->amazonAdsConfigTable->getRefreshToken(),
            "client_id" => $this->connection->amazonAdsConfigTable->getClientId(),
            "client_secret" => $this->connection->amazonAdsConfigTable->getClientSecret()
        ];
        $response = $this->_operationToken($params);

        if($response[Connection::SUCCESS])
        {
            $response_array = json_decode($response[Connection::RESPONSE], true);

            if (array_key_exists("access_token", $response_array))
                $this->connection->amazonAdsConfigTable->setAccessToken($response_array["access_token"]);
        }
        else
            throw new Exception('Error occured. Code: ' . $response[Connection::CODE] . ' -> ' . print_r($response));
    }

    /**
     ** Vygeneruje Refresh Token
     * @return void
     * @throws Exception
     */
    public function generateRefreshToken()
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
            'redirect_uri'   => $this->redirectUri,
            'client_id' => $this->connection->amazonAdsConfigTable->getClientId(),
            'client_secret' => $this->connection->amazonAdsConfigTable->getClientSecret(),
        ];
        $response = $this->_operationToken($params);

        if($response[Connection::SUCCESS])
        {
            $response_array = json_decode($response[Connection::RESPONSE], true);

            if (array_key_exists(AmazonAdsConfigTable::REFRESH_TOKEN, $response_array))
                $this->connection->amazonAdsConfigTable->setRefreshToken($response_array[AmazonAdsConfigTable::REFRESH_TOKEN]);
        }
        else
            throw new Exception('Error occured. Code: ' . $response[Connection::CODE] . ' -> ' . print_r($response));
    }

    /**
     ** Vykoná žiadosť
     * @param array $params Parametre
     * @return array|mixed
     */
    private function _operationToken(array $params)
    {
        $url = "https://{$this->connection->amazonAdsRegionTable->getTokenUrl()}";

        $request = new CurlRequest();
        $request->setOption(CURLOPT_URL, $url);
        $request->setOption(CURLOPT_POST, true);
        $request->setOption(CURLOPT_POSTFIELDS, http_build_query($params));

        return $this->connection->_executeRequest($request);
    }
}