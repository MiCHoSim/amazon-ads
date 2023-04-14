<?php

namespace App\ApplicationModul\AppManagement\Model;

use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_region
 */
class AmazonAdsRegionTable
{
    /**
     * Názov Tabuľky pre Spracovanie Uživateľa
     */
    const AMAZON_ADS_REGION_TABLE = 'amazon_ads_region';

    /**
     * Konštanty Databázy 'AppManagementManager'
     */
    const AMAZON_ADS_REGION_ID = 'amazon_ads_region_id';
    const NAME = 'name';
    const TITLE = 'title';
    const HOST = 'host';
    const TOKEN_URL = 'token_url';
    const CODE_URL = 'code_url';

    /**
     * @var array Kľuče
     */
    private $keys = [self::AMAZON_ADS_REGION_ID,self::NAME, self::TITLE, self::HOST, self::TOKEN_URL, self::CODE_URL];

    private $amazonAdsRegionId = null;
    private $name = null;
    private $title = null;
    private $host = null;
    private $tokenUrl = null;
    private $codeUrl = null;

    private $arrayData;

    /**
     ** Konštruktor, načita vŠetky dáta z DB
     * @param string|bool $userId Id uživateľa na sťahovanie údajov
     */
    public function __construct(string|bool $regionId = false)
    {
        if ($regionId)
        {
            $data =  Db::queryOneRow('SELECT * FROM '. self::AMAZON_ADS_REGION_TABLE. ' WHERE ' . self::AMAZON_ADS_REGION_ID . ' = ?', array($regionId));

            // automaticke ulozenie hodnot do premenných atributov
            foreach ($data as $key => $dat)
            {
                $nameAtribute = StringUtilities::underlineToCamel($key);
                $this->$nameAtribute = $dat; //ulozi hodnotu atributu
            }
        }
        $this->setArrayData();
    }
    /**
     ** Vloži data do poľa
     * @return void
     */
    public function setArrayData(): void
    {
        $this->arrayData = [self::AMAZON_ADS_REGION_ID => $this->amazonAdsRegionId,self::NAME => $this->name,
            self::TITLE => $this->title, self::HOST => $this->host, self::TOKEN_URL => $this->tokenUrl,
            self::CODE_URL => $this->codeUrl];
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
    public function getName(): mixed
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getTitle(): mixed
    {
        return $this->title;
    }

    /**
     * @return mixed|null
     */
    public function getHost(): mixed
    {
        return $this->host;
    }

    /**
     * @return mixed|null
     */
    public function getTokenUrl(): mixed
    {
        return $this->tokenUrl;
    }

    /**
     * @return mixed|null
     */
    public function getCodeUrl(): mixed
    {
        return $this->codeUrl;
    }

    public function get(): mixed
    {
        $keys = [self::AMAZON_ADS_REGION_ID];
        return Db::queryAllRows('SELECT ' . implode(', ',$keys) . ',
                                                CONCAT(COALESCE(' . self::NAME . ', ""), " | ", COALESCE(' . self::TITLE . ', "")) AS region
                                            FROM ' . self::AMAZON_ADS_REGION_TABLE . '
        ');
    }

    /**
     ** Vráti páry regionov pre zopbrazenie v Select form
     * @return array|false
     */
    public function getPairs()
    {
        return ArrayUtilities::getPairs($this->get(),'region', self::AMAZON_ADS_REGION_ID);
    }

}