<?php

namespace App\ApplicationModul\Amazon\Model;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use Exception;
use Micho\Db;

/**
 * Trieda spracujuca požiadavky kontroléra pre RecomendationsBidsManager
 */
class RecomendationsBidsManager
{
    /**
     * @var Connection
     */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     ** Stahovanie a ukladanie bidou reportu
     * @param array $where Pole podmienky
     * @return void
     * @throws Exception
     */
    public function downloadBids(array $where) : void
    {

        $dataReportSuggestions = $this->getDataForSuggestionBids($where);

        set_time_limit(1000);
        foreach ($dataReportSuggestions as $suggestion)
        {
            $suggestionData[] = $this->connection->report()->getBids($suggestion, $where[AmazonAdsSpTargetingTable::CAMPAIGN_ID],$where[AmazonAdsSpTargetingTable::AD_GROUP_ID], $where[AmazonAdsProfileTable::PROFILE_ID]);
        }

        if (isset($suggestionData))
            ( new AmazonAdsSpTargetingTable())->save($suggestionData);
    }

    /**
     ** Načíta data pre potrebi stiahnuťia bidu z Amazon
     * @param array $where Pole podmienky
     * @return mixed
     */
    public function getDataForSuggestionBids(array $where) : mixed
    {
        $keys = [AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID,AmazonAdsSpTargetingTable::KEYWORD, AmazonAdsSpTargetingTable::MATCH_TYPE];

        $whereKeys = array_keys($where);
        $whereValues = array_values($where);

        $selectQuery = 'SELECT ' . implode(', ', $keys);
        $fromQuery = ' FROM ' . AmazonAdsSpTargetingTable::TABLE;
tfdf
        $whereQuery = ' WHERE ' . implode(' = ? AND ',$whereKeys) . ' = ? 
                AND ' . AmazonAdsSpTargetingTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID . ' IS NULL 
                AND ' . AmazonAdsSpTargetingTable::AMAZON_ADS_RECOMMENDATIONS_V2_ID . ' IS NULL 
                AND ' . AmazonAdsSpTargetingTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID . ' IS NULL';

        return Db::queryAllRows($selectQuery . $fromQuery . $whereQuery,$whereValues);
    }


    private function view($data)
    {
        foreach ($data as $key => $d)
        {
            echo $key . ' -> ';
            print_r($d);echo "<br><br>";
        }
    }

}