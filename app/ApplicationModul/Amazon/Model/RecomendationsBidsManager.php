<?php

namespace App\ApplicationModul\Amazon\Model;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
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
        $keys = [AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID,AmazonAdsSpTargetingTable::KEYWORD, AmazonAdsSpTargetingTable::MATCH_TYPE, AmazonAdsSpTargetingTable::CAMPAIGN_ID,AmazonAdsSpTargetingTable::AD_GROUP_ID,AmazonAdsProfileTable::PROFILE_ID];

        $where[AmazonAdsSpTargetingTable::BID] = 0;

        $dataReportSuggestions = $this->getDataForSuggestionBids($where, $keys);

        //print_r($dataReportSuggestions);die;

        set_time_limit(1000);
        foreach ($dataReportSuggestions as $suggestion)
        {
            $suggestionData[] = $this->connection->report()->getBids($suggestion);
        }

        if (isset($suggestionData))
            ( new AmazonAdsSpTargetingTable())->save($suggestionData);

    }

    /**
     ** Načíta data pre potrebi stiahnuťia bidu z Amazon
     * @param array $where Pole podmienky
     * @param array $keys Kluice ktore chem stahovať
     * @return mixed
     */
    public function getDataForSuggestionBids(array $where, array $keys) : mixed
    {

        $whereKeys = array_keys($where);
        $whereValues = array_values($where);

        $selectQuery = 'SELECT ' . implode(', ', $keys);
        $fromQuery = ' FROM ' . AmazonAdsSpTargetingTable::TABLE;

        $whereQuery = ' WHERE ' . implode(' = ? AND ',$whereKeys) . ' = ? ';

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