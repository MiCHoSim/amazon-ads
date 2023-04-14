<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\ClientV3;
use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\Profile;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesManager;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use Exception;

/**
 * Trieda pre tabuľku amazon_ads_profile
 */
class AmazonAdsProfileTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_PROFILE_TABLE = 'amazon_ads_profile';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_PROFILE_ID = 'amazon_ads_profile_id';
    const COUNTRY_CODE = 'country_code';
    const COUNTRY_NAME = 'country_name';
    const CURRENCY_CODE = 'currency_code';
    const DAILY_BUDGET = 'daily_budget';
    const TIMEZONE= 'timezone';

    const ACCOUNT_INFO_ARRAY_MARKETPLACE_STRING_ID = 'account_info_array_marketplace_string_id';
    const ACCOUNT_INFO_ARRAY_ID = 'account_info_array_id';
    const ACCOUNT_INFO_ARRAY_TYPE = 'account_info_array_type';
    const ACCOUNT_INFO_ARRAY_NAME = 'account_info_array_name';
    const ACCOUNT_INFO_ARRAY_VALID_PAYMENT_METHOD = 'account_info_array_valid_payment_method';

    /**
     * @var array Kľuče
     */
    protected $keys = [self::AMAZON_ADS_PROFILE_ID,self::PROFILE_ID,self::USER_ID,self::COUNTRY_CODE,self::COUNTRY_NAME,
        self::CURRENCY_CODE,self::DAILY_BUDGET,self::TIMEZONE,self::ACCOUNT_INFO_ARRAY_MARKETPLACE_STRING_ID,self::ACCOUNT_INFO_ARRAY_ID,self::ACCOUNT_INFO_ARRAY_TYPE,
        self::ACCOUNT_INFO_ARRAY_NAME,self::ACCOUNT_INFO_ARRAY_VALID_PAYMENT_METHOD];

    /**
     * @var null Atributy
     */
    protected $amazonAdsProfileId = null;
    protected $countryCode = null;
    protected $countryName = null;
    protected $currencyCode = null;
    protected $dailyBudget = null;
    protected $timezone = null;

    protected $accountInfoArrayMarketplaceStringId = null;
    protected $accountInfoArrayId = null;
    protected $accountInfoArrayType = null;
    protected $accountInfoArrayName = null;
    protected $accountInfoArrayValidPaymentMethod = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_PROFILE_TABLE;
    protected $id = self::AMAZON_ADS_PROFILE_ID;
    protected $whereId = self::PROFILE_ID;

    protected $getPairKeyKey = 'country';
    protected $getPairKeyValue = self::PROFILE_ID;

    protected $getKeys =[self::PROFILE_ID,
        'CONCAT(COALESCE(' . self::COUNTRY_CODE . ', ""), " | ", COALESCE(' . self::COUNTRY_NAME . ', "")) AS country '
        ];


    /**
     ** Vráti Id profili z hodnotý marketplace, kde sa verie domená posledna cast .uk ...
     * @param string $marketplace Názov marketlace napr.: amazon.co.uk
     * @param string $userId id uživateľa
     * @return string|false Id uživateľa alebo false
     */
    public function profileIdFromMarketplace(string $marketplace, string $userId) : string|false
    {
        $separatedMarketplace = explode('.', $marketplace);
        $countryCode = strtoupper(end($separatedMarketplace));

        $profileId = Db::queryOneRow('SELECT ' . self::PROFILE_ID . '  
                            FROM ' . self::AMAZON_ADS_PROFILE_TABLE . ' 
                            WHERE ' . self::USER_ID . ' = ? AND ' . self::COUNTRY_CODE . ' = ?', [$userId,$countryCode]);

        return $profileId ? $profileId[self::PROFILE_ID] : false;
    }

    // požiadavky ktoré che zákaznik kombinovať
    const COMBINE_PROFILE = ['UK' => 'uk','EU' => 'eu', 'UK+EU' => 'uk+eu'];
    const FULL_COMBINE_PROFILE = ['UK' => 'UK', 'EU' => ['DE','FR','ES','IT','NL','SE','PL'], 'UK+EU' => ['UK','DE','FR','ES','IT','NL','SE','PL']];
    const CUSTOM_SORT = ['UK','DE','FR','ES','IT','NL','SE','PL'];

    /**
     ** Vrýti combine profile const bez UK
     * @return string[]
     */
    public static function combineProfileWithouUk()
    {
        $combineProfile = self::COMBINE_PROFILE;
        array_shift($combineProfile);
        return $combineProfile;
    }

    /**
     ** zoradí krajiny podľapožiadavky zákaznika ... casom mozno dorobiť podľa db
     * @param array $profiles Profili na ulozenie Do db z Request
     * @return void
     */
    public function sortByCustomer(array $profiles) : array
    {
        $customerRequest = self::CUSTOM_SORT;

        foreach ($profiles as $profile)
        {
            $key = array_search($profile[self::COUNTRY_CODE], $customerRequest);
            $customerRequest[$key] = $profile;
        }
        AppManagementController::view($customerRequest);

       return $customerRequest;
    }

    /**
     ** vybere Id krajín podla kombinovaného profilu eu uk+eu
     * @param $countryCodes
     * @return array|false
     */
    public function getByCountry($countryCodes)
    {
        $whereQuery = ' WHERE ';
        foreach ($countries = self::FULL_COMBINE_PROFILE[$countryCodes] as $key => $code)
        {
            $whereQuery .= self::COUNTRY_CODE . ' = ? ';
            if ($key !== array_key_last($countries))
                $whereQuery .= ' OR ';
        }

        $profilesId = Db::queryAllRows('SELECT ' . self::PROFILE_ID . '  
                            FROM ' . self::AMAZON_ADS_PROFILE_TABLE . $whereQuery, self::FULL_COMBINE_PROFILE[$countryCodes]);

        return $profilesId ? array_column($profilesId, self::PROFILE_ID) : false;
    }
}