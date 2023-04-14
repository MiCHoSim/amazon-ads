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
class AmazonAdsBidRecommendationsV2Table extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_RECOMMENDATIONS_V2_TABLE = 'amazon_ads_bid_recommendations_v2';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_RECOMMENDATIONS_V2_ID = 'amazon_ads_bid_recommendations_v2_id';
    const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_SUGGESTED = 'recommendations_array_suggested_bid_array_suggested';
    const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START = 'recommendations_array_suggested_bid_array_range_start';
    const RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END ='recommendations_array_suggested_bid_array_range_end';
    const RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_VALUE ='recommendations_array_expression_array_value';
    const RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_TYPE ='recommendations_array_expression_array_type';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_RECOMMENDATIONS_V2_ID,self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_SUGGESTED,
        self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START,self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END,
        self::RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_VALUE,self::RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_TYPE
    ];

    /**
     * Slovnik na prechadzanie medzi viacerimi typmi bidov
     */
    const DICTIONARY_BID = [
        'sugested_bid_low' => self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START,
        'sugested_bid_median' => self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_SUGGESTED,
        'sugested_bid_high' => self::RECOMMENDATIONS_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END
    ];

    /**
     * @var null Atributy
     */
    protected $amazonAdsBidRecommendationsV2Id = null;
    protected $recommendationsArraySuggestedBidArraySuggested = null;
    protected $recommendationsArraySuggestedBidArrayRangeStart = null;
    protected $recommendationsArraySuggestedBidArrayRangeEnd = null;
    protected $recommendationsArrayExpressionArrayValue = null;
    protected $recommendationsArrayExpressionArrayType = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_RECOMMENDATIONS_V2_TABLE;
    protected $id = self::AMAZON_ADS_RECOMMENDATIONS_V2_ID;
    protected $whereId = self::AMAZON_ADS_RECOMMENDATIONS_V2_ID;

    protected $getPairKeyKey = self::RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_VALUE;
    protected $getPairKeyValue = self::AMAZON_ADS_RECOMMENDATIONS_V2_ID;

    protected $getKeys = [self::AMAZON_ADS_RECOMMENDATIONS_V2_ID, self::RECOMMENDATIONS_ARRAY_EXPRESSION_ARRAY_VALUE];
}