<?php

namespace AmazonAdvertisingApi\Report;

use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use AmazonAdvertisingApi\Table\SelectDateTable;

/**
 ** Slovník pre významy report premenných
 */
class ReportDictionary
{

    const ERROR_CODES = [
        '400' => ['status' => 'Bad Request' , 'notes' => 'General request error that does not fall into any other category'],
        '401' => ['status' => 'Unauthorized' , 'notes' => 'Request failed because user is not authenticated'],
        '403' => ['status' => 'Forbidden' , 'notes' => 'Request failed because user does not have access to a specified resource'],
        '404' => ['Not Found' => 'Bad Request' , 'notes' => 'Requested resource does not exist or is not visible for the authenticated user'],
        '422' => ['Not Found' => 'Unprocessable Entity' , 'notes' => 'Request was understood, but contained incorrect parameters'],
        '429' => ['status' => 'Too Many Requests' , 'notes' => 'Request was rate-limited. Retry later.'],
        '500' => ['status' => 'Internal Error' , 'notes' => 'Something went wrong on the server. Retry later and report an error if unresolved.']
    ];

    const DICTIONARY = [

        SelectDateTable::SELECT_START_DATE => ['title' => 'Start Date', 'description' => 'Selected report date from'],
        SelectDateTable::SELECT_END_DATE => ['title' => 'End Date', 'description' => 'Selected report date to'],

        AmazonAdsSpTargetingTable::SUGEST_BID_LOW => ['title' => 'Bid L', 'description' => 'Suggested bid (low)'],
        AmazonAdsSpTargetingTable::SUGEST_BID_MEDIAN => ['title' => 'Bid M', 'description' => 'Suggested bid (median)'],
        AmazonAdsSpTargetingTable::SUGEST_BID_HIGH => ['title' => 'Bid H', 'description' => 'Suggested bid (high)'],
        AmazonAdsSpTargetingTable::SEARCH_TERM_IMPRESSION_SHARE => ['title' => 'TOS', 'description' => 'Top of search'],
        AmazonAdsSpTargetingTable::TRANSLATION => ['title' => 'Translation', 'description' => 'Translation of the key word if available'],


        'acos_clicks14d' => ['title' => 'A.Click14', 'description' => 'Advertising cost of sales based on purchases made within 14 days of an ad click.'],
        'acos_clicks7d' => ['title' => 'A.Click7', 'description' => 'Advertising cost of sales based on purchases made within 7 days of an ad click.'],
        'ad_group_id' => ['title' => 'Ad Group Id', 'description' => 'Unique numerical ID of the ad group.'],
        'ad_group_name' => ['title' => 'A.G.Name', 'description' => 'The name of the ad group as entered by the advertiser.'],
        'ad_id' => ['title' => 'Ad Id', 'description' => 'Unique numerical ID of the ad.'],
        'ad_keyword_status' => ['title' => 'A.K.Status', 'description' => 'Current status of a keyword.'],
        'ad_status' => ['title' => 'Ad.Status', 'description' => 'Status of the ad group.'],
        'advertised_asin' => ['title' => 'A.ASIN', 'description' => 'The ASIN associated to an advertised product.'],
        'advertised_sku' => ['title' => 'A.SKU', 'description' => 'The SKU being advertised. Not available for vendors.'],
        'attributed_sales_same_sku14d' => ['title' => 'A.S.S.Sku14', 'description' => 'Total value of sales occurring within 14 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'attributed_sales_same_sku1d' => ['title' => 'A.S.S.Sku1', 'description' => 'Total value of sales occurring within 1 day of ad click where the purchased SKU was the same as the SKU advertised.'],
        'attributed_sales_same_sku30d' => ['title' => 'A.S.S.Sku30', 'description' => 'Total value of sales occurring within 30 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'attributed_sales_same_sku7d' => ['title' => 'A.S.S.Sku7', 'description' => 'Total value of sales occurring within 7 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'attribution_type' => ['title' => 'A.Type', 'description' => 'Describes whether a purchase is attributed to a promoted product or brand-halo effect.'],
        'campaign_applicable_budget_rule_id' => ['title' => 'C.A.B.Rule Id', 'description' => 'The ID associated to the active budget rule for a campaign.'],
        'campaign_applicable_budget_rule_name' => ['title' => 'C.A.B.RuleName', 'description' => 'The name associated to the active budget rule for a campaign.'],
        'campaign_bidding_strategy' => ['title' => 'C.B.Strategy', 'description' => 'The bidding strategy associated with a campaign.'],
        'campaign_budget_amount' => ['title' => 'C.B.Amount', 'description' => 'Total budget allocated to the campaign.'],
        'campaign_budget_currency_code' => ['title' => 'C.B.C.Code', 'description' => 'The currency code associated with the campaign.'],
        'campaign_budget_type' => ['title' => 'C.B.Type', 'description' => 'One of daily or lifetime.'],
        'campaign_id' => ['title' => 'Campain Id', 'description' => 'The ID associated with a campaign.'],
        'campaign_name' => ['title' => 'Campain Name', 'description' => 'The name associated with a campaign.'],
        'campaign_rule_based_budget_amount' => ['title' => 'C.R.B.B.Amount', 'description' => 'The value of the rule-based budget for a campaign.'],
        'campaign_status' => ['title' => 'C.Status', 'description' => 'The status of a campaign.'],
        'clicks' => ['title' => 'Clicks', 'description' => 'Total number of ad clicks.'],
        'click_through_rate' => ['title' => 'C.T.Rate', 'description' => 'Clicks divided by impressions.'],
        'cost' => ['title' => 'Cost', 'description' => 'Total cost of ad clicks. Same as spend.'],
        'cost_per_click' => ['title' => 'C.P.Click', 'description' => 'Total cost divided by total number of clicks.'],
        'date' => ['title' => 'Date', 'description' => 'Date when the ad activity ocurred in the format YYYY-MM-DD.'],
        'end_date' => ['title' => 'End Date', 'description' => 'End date of summary period for a report in the format YYYY-MM-DD.'],
        'impressions' => ['title' => 'Imp.', 'description' => 'Total number of ad impressions.'],
        'keyword' => ['title' => 'Keyword', 'description' => 'Text of the keyword or a representation of the targeting expression. For targets, the same value is returned in the targeting metric.'],
        'keyword_bid' => ['title' => 'Bid', 'description' => 'Bid associated with a keyword or targeting expression.'],
        'keyword_id' => ['title' => 'Keyword Id', 'description' => 'ID associated with a keyword or targeting expression.'],
        'keyword_type' => ['title' => 'Keyword Type', 'description' => 'Type of matching for the keyword used in bid. For keywords, one of: BROAD, PHRASE, or EXACT. For targeting expressions, one of TARGETING_EXPRESSION or TARGETING_EXPRESSION_PREDEFINED. Same as matchType'],
        'kindle_edition_normalized_pages_read14d' => ['title' => 'Read14', 'description' => 'Number of attributed Kindle edition normalized pages read within 14 days of ad click.'],
        'kindle_edition_normalized_pages_royalties14d' => ['title' => 'Royaltis14', 'description' => 'The estimated royalties of attributed estimated Kindle edition normalized pages within 14 days of ad click.'],
        'match_type' => ['title' => 'M. Type', 'description' => 'Type of matching for the keyword used in bid. For keywords, one of: BROAD, PHRASE, or EXACT. For targeting expressions, one of TARGETING_EXPRESSION or TARGETING_EXPRESSION_PREDEFINED. Same as keywordType.'],
        'new_to_brand_purchases14d' => ['title' => 'Purchases14', 'description' => 'The number of first-time orders for brand products over a one-year lookback window. Not available for book vendors.'],
        'new_to_brand_purchases_percentage14d' => ['title' => 'Purch%14', 'description' => 'The percentage of total orders that are new-to-brand orders. Not available for book vendors.'],
        'new_to_brand_sales14d' => ['title' => 'Sales14', 'description' => 'Total value of new-to-brand sales occurring within 14 days of an ad click. Not available for book vendors.'],
        'new_to_brand_sales_percentage14d' => ['title' => 'Sal%14', 'description' => 'Percentage of total sales made up of new-to-brand purchases. Not available for book vendors.'],
        'new_to_brand_units_sold14d' => ['title' => 'Sold14', 'description' => 'Total number of attributed units ordered as part of new-to-brand sales occurring within 14 days of an ad click. Not available for book vendors.'],
        'new_to_brand_units_sold_percentage14d' => ['title' => 'Sold%14', 'description' => 'Percentage of total attributed units ordered within 14 days of an ad click that are part of a new-to-brand purchase. Not available for book vendors.'],
        'orders14d' => ['title' => 'Orders14', 'description' => 'Orders within the last 14 days.'],
        'placement_classification' => ['title' => 'P.Classfication', 'description' => 'The page location where an ad appeared.'],
        'portfolio_id' => ['title' => 'Portfolio Id', 'description' => 'The portfolio the campaign is associated with.'],
        'product_category' => ['title' => 'Prod.Category', 'description' => 'The category the product is associated with on Amazon.'],
        'product_name' => ['title' => 'Prod.Name', 'description' => 'The name of the product.'],
        'purchased_asin' => ['title' => 'Asin', 'description' => 'The ASIN of the product that was purchased.'],
        'purchases14d' => ['title' => 'Purchases14', 'description' => 'Number of attributed conversion events occurring within 14 days of an ad click.'],
        'purchases1d' => ['title' => 'Purchases1', 'description' => 'Number of attributed conversion events occurring within 1 day of an ad click.'],
        'purchases30d' => ['title' => 'Purchases30', 'description' => 'Number of attributed conversion events occurring within 30 days of an ad click.'],
        'purchases7d' => ['title' => 'Purch7', 'description' => 'Number of attributed conversion events occurring within 7 days of an ad click.'],
        'purchases_other_sku14d' => ['title' => 'O.Sku14', 'description' => 'Number of attributed conversion events occurring within 14 days of an ad click where the SKU purchased was different that the advertised SKU.'],
        'purchases_other_sku1d' => ['title' => 'O.Sku1', 'description' => 'Number of attributed conversion events occurring within 1 day of an ad click where the SKU purchased was different that the advertised SKU.'],
        'purchases_other_sku30d' => ['title' => 'O.Sku30', 'description' => 'Number of attributed conversion events occurring within 30 days of an ad click where the SKU purchased was different that the advertised SKU.'],
        'purchases_other_sku7d' => ['title' => 'O.Sku7', 'description' => 'Number of attributed conversion events occurring within 7 days of an ad click where the SKU purchased was different that the advertised SKU.'],
        'purchases_same_sku14d' => ['title' => 'O.Sku14', 'description' => 'Number of attributed conversion events occurring within 14 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'purchases_same_sku1d' => ['title' => 'O.Sku1', 'description' => 'Number of attributed conversion events occurring within 1 day of ad click where the purchased SKU was the same as the SKU advertised.'],
        'purchases_same_sku30d' => ['title' => 'O.Sku30', 'description' => 'Number of attributed conversion events occurring within 30 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'purchases_same_sku7d' => ['title' => 'O.Sku7', 'description' => 'Number of attributed conversion events occurring within 7 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'roas_clicks14d' => ['title' => 'R.Click14', 'description' => 'Return on ad spend based on purchases made within 14 days of an ad click.'],
        'roas_clicks7d' => ['title' => 'R.Click7', 'description' => 'Return on ad spend based on purchases made within 7 days of an ad click.'],
        'sales14d' => ['title' => 'Sales14', 'description' => 'Total value of sales occurring within 14 days of an ad click.'],
        'sales1d' => ['title' => 'Sales1', 'description' => 'Total value of sales occurring within 1 day of an ad click.'],
        'sales30d' => ['title' => 'Sales30', 'description' => 'Total value of sales occurring within 30 days of an ad click.'],
        'sales7d' => ['title' => 'Sales7', 'description' => 'Total value of sales occurring within 7 days of an ad click.'],
        'sales_other_sku14d' => ['title' => 'S.O.Sku14', 'description' => 'Total value of sales occurring within 14 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'sales_other_sku1d' => ['title' => 'S.O.Sku1', 'description' => 'Total value of sales occurring within 1 day of an ad click where the purchased SKU was different from the SKU advertised.'],
        'sales_other_sku30d' => ['title' => 'S.O.Sku30', 'description' => 'Total value of sales occurring within 30 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'sales_other_sku7d' => ['title' => 'S.O.Sku7', 'description' => 'Total value of sales occurring within 7 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'search_term' => ['title' => 'Search Term', 'description' => 'The search term used by the customer.'],
        'spend' => ['title' => 'Spend', 'description' => 'Total cost of ad clicks. Same as cost.'],
        'start_date' => ['title' => 'Start Date', 'description' => 'Start date of summary period for a report in the format YYYY-MM-DD.'],
        'targeting' => ['title' => 'Targeting', 'description' => 'A string representation of the expression object used in the targeting clause. The targeting expression is also returned in keyword.'],
        'units_sold14d' => ['title' => 'U.Sold14', 'description' => 'Number of attributed units sold within 14 days of click on an ad. Same as unitsSoldClicks14d. Only valid for Sponsored Brands version 3 campaigns, not Sponsored Brands video or multi-ad group (version 4) campaigns.'],
        'units_sold_clicks14d' => ['title' => 'U.S.Click14', 'description' => 'Total number of units ordered within 14 days of an ad click. Same as unitsSold14d'],
        'units_sold_clicks1d' => ['title' => 'U.S.Click1', 'description' => 'Total number of units ordered within 1 day of an ad click.'],
        'units_sold_clicks30d' => ['title' => 'U.S.Click30', 'description' => 'Total number of units ordered within 30 days of an ad click.'],
        'units_sold_clicks7d' => ['title' => 'U.S.Click7', 'description' => 'Total number of units ordered within 7 days of an ad click.'],
        'units_sold_other_sku14d' => ['title' => 'U.S.O.Sku14', 'description' => 'Total number of units ordered within 14 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'units_sold_other_sku1d' => ['title' => 'U.S.O.Sku1', 'description' => 'Total number of units ordered within 1 day of an ad click where the purchased SKU was different from the SKU advertised.'],
        'units_sold_other_sku30d' => ['title' => 'U.S.O.Sku30', 'description' => 'Total number of units ordered within 30 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'units_sold_other_sku7d' => ['title' => 'U.S.O.Sku7', 'description' => 'Total number of units ordered within 7 days of an ad click where the purchased SKU was different from the SKU advertised.'],
        'units_sold_same_sku14d' => ['title' => 'U.S.S.Sku14', 'description' => 'Total number of units ordered within 14 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'units_sold_same_sku1d' => ['title' => 'U.S.S.Sku1', 'description' => 'Total number of units ordered within 1 day of ad click where the purchased SKU was the same as the SKU advertised.'],
        'units_sold_same_sku30d' => ['title' => 'U.S.S.Sku30', 'description' => 'Total number of units ordered within 30 days of ad click where the purchased SKU was the same as the SKU advertised.'],
        'units_sold_same_sku7d' => ['title' => 'U.S.S.Sku7', 'description' => 'Total number of units ordered within 7 days of ad click where the purchased SKU was the same as the SKU advertised.']
    ];
}