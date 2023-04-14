<?php

namespace App\ApplicationModul\AppManagement\Model;

use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku AmazonAdsAppManager amazon_ads_config
 */
class AmazonAdsConfigTable
{
    /**
     * Názov Tabuľky pre Spracovanie Uživateľa
     */
    const AMAZON_ADS_CONFIG_TABLE = 'amazon_ads_config';

    /**
     * Konštanty Databázy 'AppManagementManager'
     */
    const AMAZON_ADS_CONFIG_ID = 'amazon_ads_config_id';
    const USER_ID = UserTable::USER_ID;
    const CLIENT_ID = 'client_id';
    const CLIENT_SECRET = 'client_secret';
    const REFRESH_TOKEN = 'refresh_token';
    const AMAZON_ADS_REGION_ID = AmazonAdsRegionTable::AMAZON_ADS_REGION_ID;

    /**
     * @var array Kľuče
     */
    private $keys = [self::AMAZON_ADS_CONFIG_ID, self::USER_ID, self::CLIENT_ID, self::CLIENT_SECRET,self::REFRESH_TOKEN,self::AMAZON_ADS_REGION_ID];

    /**
     * Všetky hodnty triedy
     */
    private $amazonAdsConfigId = '';
    private $userId = '';
    private $clientId = '';
    private $clientSecret = '';
    private $refreshToken = '';
    private $amazonAdsRegionId = '';

    /**
     * @var null Pristupový token
     */
    private $accessToken = '';

    /**
     * Pole všetkých hodnôt triedy
     */
    private $arrayData;

    /**
     ** Konštruktor, načita vŠetky dáta z DB
     * @param string $userId Id uživateľa na sťahovanie údajov
     */
    public function __construct(string $userId)
    {
        $data =  Db::queryOneRow('SELECT * FROM '. self::AMAZON_ADS_CONFIG_TABLE. ' WHERE ' . self::USER_ID . ' = ?', array($userId));

        if($data)
        {
            // automaticke ulozenie hodnot do premenných atributov
            foreach ($data as $key => $dat)
            {
                $nameAtribute = StringUtilities::underlineToCamel($key);
                $this->$nameAtribute = $dat; //ulozi hodnotu atributu
            }
            $this->setArrayData();
        }
    }
    /**
     ** Vloži data do poľa
     * @return void
     */
    private function setArrayData(): void
    {
        $this->arrayData = [self::AMAZON_ADS_CONFIG_ID => $this->amazonAdsConfigId,self::USER_ID => $this->userId,
            self::CLIENT_ID => $this->clientId, self::CLIENT_SECRET => $this->clientSecret,
            self::REFRESH_TOKEN => $this->refreshToken,self::AMAZON_ADS_REGION_ID => $this->amazonAdsRegionId];
    }
    /**
     ** Ukladanie všetkych hodnôt do DB
     * @return string
     */
    public function save($updateOld = false)
    {
        $this->setArrayData();
        Db::insert(self::AMAZON_ADS_CONFIG_TABLE, $this->arrayData,$updateOld);
        return Db::returnLastId();
    }

    /**
     ** Setter pre všetky data
     * @param array $data Nové dáta
     * @return void
     */
    public function setData(array $data): void
    {
        foreach ($data as $key => $dat)
        {
            $nameAtribute = StringUtilities::underlineToCamel($key);
            $this->$nameAtribute = $dat; //ulozi hodnotu atributu
        }
        $this->setArrayData();
    }
    /**
     * @param mixed|null $refreshToken
     */
    public function setRefreshToken(mixed $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }
    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return null
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
    /**
     * @return mixed|null
     */
    public function getAmazonAdsConfigId(): mixed
    {
        return $this->amazonAdsConfigId;
    }
    /**
     * @return mixed|null
     */
    public function getClientId(): mixed
    {
        return $this->clientId;
    }
    /**
     * @return mixed|null
     */
    public function getClientSecret(): mixed
    {
        return $this->clientSecret;
    }
    /**
     * @return mixed|null
     */
    public function getRefreshToken(): mixed
    {
        return $this->refreshToken;
    }
    /**
     * @return mixed|null
     */
    public function getAmazonAdsRegionId(): mixed
    {
        return $this->amazonAdsRegionId;
    }
    /**
     * @return mixed|null
     */
    public function getUserId(): mixed
    {
        return $this->userId;
    }
    /**
     * @return array
     */
    public function getArrayData(): array
    {
        return $this->arrayData;
    }
}