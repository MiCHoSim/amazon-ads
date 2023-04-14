<?php

namespace AmazonAdvertisingApi\Report;

/**
 * Trieda na prípravu údajov pre SpAdvertisedProduct
 */
class ConstRawSpAdvertisedProduct
{
    const REPORT_TYPE_ID = 'spAdvertisedProduct';

    const GRPUP_BY = ['advertiser'];

    const BASE_METRICS = ['campaignName', 'campaignId', 'adGroupName', 'adGroupId', 'adId', 'portfolioId', 'impressions',
        'clicks', 'costPerClick', 'clickThroughRate', 'cost', 'spend', 'campaignBudgetCurrencyCode', 'campaignBudgetAmount',
        'campaignBudgetType', 'campaignStatus', 'advertisedAsin', 'advertisedSku', 'purchases1d', 'purchases7d',
        'purchases14d', 'purchases30d', 'purchasesSameSku1d', 'purchasesSameSku7d', 'purchasesSameSku14d',
        'purchasesSameSku30d', 'unitsSoldClicks1d', 'unitsSoldClicks7d', 'unitsSoldClicks14d', 'unitsSoldClicks30d',
        'sales1d', 'sales7d', 'sales14d', 'sales30d', 'attributedSalesSameSku1d', 'attributedSalesSameSku7d',
        'attributedSalesSameSku14d', 'attributedSalesSameSku30d', 'salesOtherSku7d', 'unitsSoldSameSku1d',
        'unitsSoldSameSku7d', 'unitsSoldSameSku14d', 'unitsSoldSameSku30d', 'unitsSoldOtherSku7d',
        'kindleEditionNormalizedPagesRead14d', 'kindleEditionNormalizedPagesRoyalties14d', 'acosClicks7d', 'acosClicks14d',
        'roasClicks7d', 'roasClicks14d'];

    const ADDITIONAL_METRIX = [];
}