<?php

namespace AmazonAdvertisingApi\Table;

use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Trieda pre tabuľku amazon_ads_keyword
 */
class AmazonAdsTargetTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_TARGET_TABLE = 'amazon_ads_target';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_TARGET_ID = 'amazon_ads_Target_id';
    const TARGET_ID = 'target_id';
    const CAMPAIGN_ID = AmazonAdsCampaignTable::CAMPAIGN_ID;
    const AD_GROUP_ID = AmazonAdsAdGroupTable::AD_GROUP_ID;
    const EXPRESION_ARRAY_TYPE = 'expression_array_type';
    const EXPRESION_ARRAY_VALUE = 'expression_array_value';
    const EXPRESION_TYPE = 'expression_type';
    const RESOLVED_EXPRESION_ARRAY_TYPE = 'resolved_expression_array_type';
    const RESOLVED_EXPRESION_ARRAY_VALUE = 'resolved_expression_array_value';
    const STATE = 'state';
    const BID = 'bid';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_TARGET_ID,self::TARGET_ID,self::USER_ID,self::PROFILE_ID,self::CAMPAIGN_ID,
        self::AD_GROUP_ID,self::EXPRESION_ARRAY_TYPE,self::EXPRESION_ARRAY_VALUE,self::EXPRESION_TYPE,
        self::RESOLVED_EXPRESION_ARRAY_TYPE,self::RESOLVED_EXPRESION_ARRAY_VALUE,self::STATE,self::BID];

    /**
     * @var null Atributy
     */
    protected $amazonAdsTargetId = null;
    protected $targetId = null;
    protected $campaignId = null;
    protected $adGroupId = null;
    protected $expressionArrayType = null;
    protected $expressionArrayValue = null;
    protected $expressionType = null;
    protected $resolvedExpressionArrayType = null;
    protected $resolvedExpressionArrayValue = null;
    protected $state = null;
    protected $bid = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_TARGET_TABLE;
    protected $id = self::AMAZON_ADS_TARGET_ID;
    protected $whereId = self::TARGET_ID;

    protected $getPairKeyKey = 'name';
    protected $getPairKeyValue = self::TARGET_ID;

    protected $getKeys =[self::PROFILE_ID,
        'CONCAT(COALESCE(' . self::EXPRESION_TYPE . ', ""), " | ", COALESCE(' . self::EXPRESION_ARRAY_VALUE . ', "")) AS name'
    ];
}