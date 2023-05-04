<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\AmazonAdvertisingApi;
use AmazonAdvertisingApi\ClientV3;
use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_campaign
 */
class AmazonAdsThemeBasedBidRecommendationTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_TABLE = 'amazon_ads_theme_based_bid_recommendation';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID = 'amazon_ads_theme_based_bid_recommendation_id';
    const AMAZON_ADS_SP_TARGETING_ID = AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID;
    const TARGETING_EXPRESION_ARRAY_TYPE = 'targeting_expression_array_type';
    const TARGETING_EXPRESION_ARRAY_VALUE = 'targeting_expression_array_value';
    const BID_VALUES_ARRAY_SUGGESTED_BID_LOW ='bid_values_array_low_array_suggested_bid';
    const BID_VALUES_ARRAY_SUGGESTED_BID_MEDIAN ='bid_values_array_median_array_suggested_bid';
    const BID_VALUES_ARRAY_SUGGESTED_BID_HIGH ='bid_values_array_high_array_suggested_bid';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID, self::AMAZON_ADS_SP_TARGETING_ID,
        self::TARGETING_EXPRESION_ARRAY_TYPE,self::TARGETING_EXPRESION_ARRAY_VALUE,
        self::BID_VALUES_ARRAY_SUGGESTED_BID_LOW,self::BID_VALUES_ARRAY_SUGGESTED_BID_MEDIAN,self::BID_VALUES_ARRAY_SUGGESTED_BID_HIGH];

    /**
     * Slovnik na prechadzanie medzi viacerimi typmi bidov
     */
    const DICTIONARY_BID = [
        'sugested_bid_low' => self::BID_VALUES_ARRAY_SUGGESTED_BID_LOW,
        'sugested_bid_median' => self::BID_VALUES_ARRAY_SUGGESTED_BID_MEDIAN,
        'sugested_bid_high' => self::BID_VALUES_ARRAY_SUGGESTED_BID_HIGH
    ];


    /**
     * @var null Atributy
     */
    protected $amazonAdsThemeBasedBidRecommendationId = null;
    protected $amazonAdsSpTargetingId = null;
    protected $targetingExpressionArrayType = null;
    protected $targetingExpressionArrayValue = null;
    protected $bidValuesArrayLowArraySuggestedBid = null;
    protected $bidValuesArrayMedianArraySuggestedBid = null;
    protected $bidValuesArrayHighArraySuggestedBid = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_TABLE;
    protected $id = self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID;
    protected $whereId = self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID;

    protected $getPairKeyKey = self::TARGETING_EXPRESION_ARRAY_TYPE;
    protected $getPairKeyValue = self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID;

    protected $getKeys = [self::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID, self::TARGETING_EXPRESION_ARRAY_TYPE];
}