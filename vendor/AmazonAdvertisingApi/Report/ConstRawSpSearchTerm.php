<?php

namespace AmazonAdvertisingApi\Report;

/**
 * Trieda na prípravu údajov pre spSearchTerm
 */
class ConstRawSpSearchTerm
{
    const REPORT_TYPE_ID = 'spSearchTerm';

    const GRPUP_BY = ['searchTerm'];

    const BASE_METRICS = ['impressions', 'clicks', 'costPerClick', 'clickThroughRate', 'cost', 'purchases1d', 'purchases7d',
                'purchases14d', 'purchases30d', 'purchasesSameSku1d', 'purchasesSameSku7d', 'purchasesSameSku14d',
                'purchasesSameSku30d', 'unitsSoldClicks1d', 'unitsSoldClicks7d', 'unitsSoldClicks14d', 'unitsSoldClicks30d',
                'sales1d', 'sales7d', 'sales14d', 'sales30d', 'attributedSalesSameSku1d', 'attributedSalesSameSku7d',
                'attributedSalesSameSku14d', 'attributedSalesSameSku30d', 'unitsSoldSameSku1d', 'unitsSoldSameSku7d',
                'unitsSoldSameSku14d', 'unitsSoldSameSku30d', 'kindleEditionNormalizedPagesRead14d',
                'kindleEditionNormalizedPagesRoyalties14d', 'salesOtherSku7d', 'unitsSoldOtherSku7d', 'acosClicks7d',
                'acosClicks14d', 'roasClicks7d', 'roasClicks14d', 'keywordId', 'keyword', 'campaignBudgetCurrencyCode',
                'portfolioId', 'searchTerm', 'campaignName', 'campaignId', 'campaignBudgetType', 'campaignBudgetAmount',
                'campaignStatus', 'keywordBid', 'adGroupName', 'adGroupId', 'keywordType', 'matchType', 'targeting'];

    const ADDITIONAL_METRIX = ['adKeywordStatus'];
}