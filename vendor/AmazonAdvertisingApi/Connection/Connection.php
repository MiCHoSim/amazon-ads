<?php
namespace AmazonAdvertisingApi\Connection;

use AmazonAdvertisingApi\DataCollection\AdGroup;
use AmazonAdvertisingApi\DataCollection\BidRecommendationsV2;
use AmazonAdvertisingApi\DataCollection\KeywordRecommendations;
use AmazonAdvertisingApi\DataCollection\ThemeBasedBidRecommendation;
use AmazonAdvertisingApi\DataCollection\Campaign;
use AmazonAdvertisingApi\DataCollection\Keyword;
use AmazonAdvertisingApi\DataCollection\Portfolio;
use AmazonAdvertisingApi\DataCollection\ProductAds;
use AmazonAdvertisingApi\DataCollection\Profile;
use AmazonAdvertisingApi\DataCollection\Target;
use AmazonAdvertisingApi\PostFields\PostFields;
use AmazonAdvertisingApi\Report\Report;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Model\AmazonAdsConfigTable;
use App\ApplicationModul\AppManagement\Model\AmazonAdsRegionTable;
use AmazonAdvertisingApi\Token\GenerateTokens;
use Exception;


/**
 ** Vytvorenie spojenie a metody pre načitavenie údajov
 */
class Connection
{

    /**
     * Konštanty žiadosti
     */
    const SUCCESS = 'success';
    const CODE = 'code';
    const RESPONSE = 'response';
        const NEXT_TOKEN = 'nextToken';
        const TOTAL_RESULTS = 'totalResults';
        const REQUEST_ID = 'requestId';



    const REPORT_ID = 'reportId';
    const STATUS = 'status';
    const START_DATE = 'startDate';
    const END_DATE = 'endDate';

    const CREATED_AT = 'createdAt';
    const FAIULURE_REASON = 'failureReason';
    const FILE_SIZE = 'fileSize';
    const GENERATED_AT = 'generatedAt';
    const NAME = 'name';
    const UPDATED_AT = 'updatedAt';
    const URL = 'url';
    const URL_EXPIRED_AT = 'urlExpiresAt';

    /**
     ** Parametre pre starú a novu verziu Api
     */
    const CONTEN_TYPE_V2 = 'application/json';
    const CONTEN_TYPE_V3 = 'application/vnd.createasyncreportrequest.v3+json';

    /**
     ** Inštancie Tried
     */
    public AmazonAdsConfigTable $amazonAdsConfigTable;
    public AmazonAdsRegionTable $amazonAdsRegionTable;
    public GenerateTokens $generateTokens;

    /**
     * @var null Id aktivneho profilu
     */
    public $profileId = null;
    /**
     * @var null Id vytvorenej správy/údajov
     */
    public $reportId = null;

    /**
     * @param string $userId Id uživateľa
     * @throws Exception
     */
    public function __construct(string $userId)
    {
        $this->amazonAdsConfigTable = new AmazonAdsConfigTable($userId);

        $this->amazonAdsRegionTable = new AmazonAdsRegionTable($this->amazonAdsConfigTable->getAmazonAdsRegionId());

        $this->generateTokens = new GenerateTokens($this);

        if($this->amazonAdsConfigTable->getRefreshToken())
            $this->generateTokens()->refreshAccessToken();
    }

    /**
     * @return GenerateTokens Inštancia GenerateTokens
     */
    public function generateTokens() : GenerateTokens
    {
        return $this->generateTokens;
    }
    /**
     * @return Profile Inštancia Profile
     */
    public function profile() : Profile
    {
        return (new Profile($this));
    }
    /**
     * @return Portfolio Inštancia Portfolio
     */
    public function portfolio() : Portfolio
    {
        return (new Portfolio($this));
    }
    /**
     * @return Campaign Inštancia Campaign
     */
    public function campaign() : Campaign
    {
        return (new Campaign($this));
    }
    /**
     * @return AdGroup Inštancia AdGroup
     */
    public function adGroup() : AdGroup
    {
        return (new AdGroup($this));
    }
    /**
     * @return ProductAds Inštancia AdGroup
     */
    public function productAds() : ProductAds
    {
        return (new ProductAds($this));
    }
    /**
     * @return Keyword Inštancia AdGroup
     */
    public function keyword() : Keyword
    {
        return (new Keyword($this));
    }
    /**
     * @return Target Inštancia AdGroup
     */
    public function target() : Target
    {
        return (new Target($this));
    }

    /**
     * @return Report Inštancia Report
     */
    public function report() : Report
    {
        return (new Report($this));
    }

    /**
     * @param string $campaignId Id Kampane
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $targetingExpressionTypeKeyword Typ zamiereného Kľučového slová / Kľučové slovo
     * @return ThemeBasedBidRecommendation Inštancia BidRecommendation
     */
    public function themeBasedBidRecommendation(string $campaignId, string $adGroupId, string $targetingExpressionTypeKeyword) : ThemeBasedBidRecommendation
    {
        return (new ThemeBasedBidRecommendation($this,$campaignId, $adGroupId, $targetingExpressionTypeKeyword));
    }

    /**
     * @param string $campaignId Id Kampane
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $matchType Match Type sklucového slová
     * @param string $keyword Kľučové slovo
     * @return KeywordRecommendations Inštancia KeywordRecommendations
     */
    public function keywordRecommendations(string $campaignId, string $adGroupId, string $matchType, string $keyword) : KeywordRecommendations
    {
        return (new KeywordRecommendations($this,$campaignId,$adGroupId,$matchType,$keyword));
    }

    /**
     * @param string $adGroupId Id reklamenj skupiny
     * @param string $value Hodnota Asin/category
     * @param string $type Typ = asin/category
     * @return BidRecommendationsV2 Inštancia BidRecommendationsV2
     */
    public function BidRecommendationsV2(string $adGroupId, string $value, string $type) : BidRecommendationsV2
    {
        return (new BidRecommendationsV2($this,$adGroupId,$value, $type));
    }


    /**
     ** Vymaža žiadaný report
     * @param string $reportId Id vytvorenej správy/údajov
     * @return array|mixed
     * @throws \Exception
     */
    public function deleteReport(string $reportId)
    {
        return $this->_operation("reporting/reports/{$reportId}", self::CONTEN_TYPE_V3, array(), "DELETE");
    }


    /**
     ** Vykoná žiadost o Data
     * @param string $host Url pristupu
     * @param string $endpoint Koncový bod
     * @param array $headers Hlavičky
     * @param string $method Metoda GET/POST...
     * @param array $params Parametre
     * @return array|mixed
     * @throws Exception
     */
    public function _operation(string $host, string $endpoint, array $headers, string $method, array $params = array())
    {
        $request = new CurlRequest();
        $url = 'https://' . $host . $endpoint;
        $data = "";
        switch (strtolower($method))
        {
            case "get":
                if (!empty($params))
                {
                    $url .= "?";
                    foreach ($params as $k => $v)
                    {
                        $url .= "{$k}=".rawurlencode($v)."&";
                    }
                    $url = rtrim($url, "&");
                }
                break;
            case "put":case "post":case "delete":
                if (!empty($params))
                {
                    $data = json_encode($params);
                    $request->setOption(CURLOPT_POST, true);
                    $request->setOption(CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                throw new Exception("Unknown verb {$method}.");
        }
        //echo $url;
        //print_r($data);
        //AmazonAdsController::view($data);

        $request->setOption(CURLOPT_URL, $url);
        $request->setOption(CURLOPT_HTTPHEADER, $headers);
        $request->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        return $this->_executeRequest($request);
    }

    /**
     ** Stiahne Json z url
     * @param string $url url adresa na stiahnutie
     * @return array|mixed
     */
    public function _download(string $url)
    {
        $request = new CurlRequest();
        $request->setOption(CURLOPT_URL, $url);

        $response = $this->_executeRequest($request);
        $response[self::RESPONSE] = gzdecode($response[self::RESPONSE]);
        return $response;
    }

    /**
     ** Vykoná žiadosť
     * @param CurlRequest $request Žiadosť
     * @return array|mixed Pole parametrov
     */
    public function _executeRequest(CurlRequest $request)
    {
        $response = $request->execute();
        $this->requestId = $request->requestId;
        $response_info = $request->getInfo();
        $request->close();
        if (!preg_match("/^(2|3)\d{2}$/", $response_info["http_code"]))
        {
            $requestId = 0;

            $json = json_decode($response, true);
            if (!is_null($json))
                if (array_key_exists(self::REQUEST_ID, $json))
                    $requestId = json_decode($response, true)[self::REQUEST_ID];

            return array(self::SUCCESS => false,
                self::CODE => $response_info["http_code"],
                self::RESPONSE => $response,
                self::REQUEST_ID => $requestId);
        }
        else
            return array(self::SUCCESS => true,
                self::CODE => $response_info["http_code"],
                self::RESPONSE => $response,
                self::REQUEST_ID => $this->requestId);
    }

    /**
     ** Nastaví konfiguračné parametre
     * @param array $config Konfiguračné paramatre
     * @return bool
     * @throws \Exception
     */
    private function _setConfig(array $config) : bool
    {
        if (is_null($config))
            $this->_logAndThrow("'config' cannot be null.");

        foreach ($config as $param => $val)
        {
            if (array_key_exists($param, $this->amazonAdsConfigTable))
                $this->amazonAdsConfigTable[$param] = $val;
            else
                $this->_logAndThrow("Unknown parameter '{$param}' in config.");
        }
        return true;
    }
}

/**
 * MiCHo
 */