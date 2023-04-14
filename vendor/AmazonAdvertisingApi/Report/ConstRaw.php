<?php

namespace AmazonAdvertisingApi\Report;

/**
 * Trieda na prípravu údajov na odoslanie cez HTTP CURLOPT_POSTFIELDS
 */
class ConstRaw
{
    const AD_PRODUCT_SPONSORED_PRODUCTS = 'SPONSORED_PRODUCTS';
    //const AD_PRODUCT_SPONSORED_BRANDS = 'SPONSORED_BRANDS';

    const AD_PRODUCT = ['Sponsored Products' => self::AD_PRODUCT_SPONSORED_PRODUCTS];//,
                        //'Sponsored Brands' => self::AD_PRODUCT_SPONSORED_BRANDS];
    const TIME_UNIT_SUMMARY = 'SUMMARY';
    const TIME_UNIT_DAILY = 'DAILY';
    const TIME_UNIT_MATRIX = [self::TIME_UNIT_SUMMARY => ['startDate','endDate'],
                              self::TIME_UNIT_DAILY => ['date']];

    const REPORT_TYPE_ID = ['Sponsored Products - Targeting reports' => ConstRawSpTargeting::REPORT_TYPE_ID,
                            'Sponsored Products - Search Term reports' => ConstRawSpSearchTerm::REPORT_TYPE_ID];
}