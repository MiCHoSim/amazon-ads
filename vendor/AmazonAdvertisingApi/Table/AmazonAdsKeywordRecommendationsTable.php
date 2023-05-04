<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\AmazonAdvertisingApi;
use AmazonAdvertisingApi\ClientV3;
use AmazonAdvertisingApi\DataCollection\Keyword;
use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_campaign
 */
class AmazonAdsKeywordRecommendationsTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_KEYWORD_RECOMMENDATIONS_TABLE = 'amazon_ads_keyword_recommendations';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID = 'amazon_ads_keyword_recommendations_id';
    const AMAZON_ADS_SP_TARGETING_ID = AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID;
    const KEYWORD = 'keyword';
    const TRANSLATION = 'translation';
    const USER_SELECTED_KEYWORD = 'user_selected_keyword';
    const SEARCH_TERM_IMPRESSION_RANK = 'search_term_impression_rank';
    const SEARCH_TERM_IMPRESSION_SHARE = 'search_term_impression_share';
    const REC_ID = 'rec_id';
    const BID_INFO_ARRAY_MATCH_TYPE = 'bid_info_array_match_type';
    const BID_INFO_ARRAY_RANK = 'bid_info_array_rank';
    const BID_INFO_ARRAY_BID = 'bid_info_array_bid';
    const BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START ='bid_info_array_suggested_bid_array_range_start';
    const BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_MEDIAN ='bid_info_array_suggested_bid_array_range_median';
    const BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END ='bid_info_array_suggested_bid_array_range_end';
    const BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_BID_REC_ID ='bid_info_array_suggested_bid_array_bid_rec_id';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID, self::AMAZON_ADS_SP_TARGETING_ID,self::KEYWORD,self::TRANSLATION,
        self::USER_SELECTED_KEYWORD,self::SEARCH_TERM_IMPRESSION_RANK,self::SEARCH_TERM_IMPRESSION_SHARE,self::REC_ID,
        self::BID_INFO_ARRAY_MATCH_TYPE,self::BID_INFO_ARRAY_RANK,self::BID_INFO_ARRAY_BID,
        self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START,self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_MEDIAN,
        self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END,self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_BID_REC_ID
        ];

    /**
     * Slovnik na prechadzanie medzi viacerimi typmi bidov
     */
    const DICTIONARY_BID = [
        'sugested_bid_low' => self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_START,
        'sugested_bid_median' => self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_MEDIAN,
        'sugested_bid_high' => self::BID_INFO_ARRAY_SUGGESTED_BID_ARRAY_RANGE_END
    ];

    /**
     * @var null Atributy
     */
    protected $amazonAdsKeywordRecommendationsId = null;
    protected $amazonAdsSpTargetingId = null;
    protected $keyword = null;
    protected $translation = null;
    protected $userSelectedKeyword = null;
    protected $searchTermImpressionRank = null;
    protected $searchTermImpressionShare = null;
    protected $recId = null;
    protected $bidInfoArrayMatchType = null;
    protected $bidInfoArrayRank = null;
    protected $bidInfoArrayBid = null;
    protected $bidInfoArraySuggestedBidArrayRangeStart = null;
    protected $bidInfoArraySuggestedBidArrayRangeMedian = null;
    protected $bidInfoArraySuggestedBidArrayRangeEnd = null;
    protected $bidInfoArraySuggestedBidArrayBidRecId = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_TABLE;
    protected $id = self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID;
    protected $whereId = self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID;

    protected $getPairKeyKey = self::KEYWORD;
    protected $getPairKeyValue = self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID;

    protected $getKeys = [self::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID, self::KEYWORD];
}