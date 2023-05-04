<?php
namespace AmazonAdvertisingApi\Report;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\BidRecommendationsV2;
use AmazonAdvertisingApi\DataCollection\KeywordRecommendations;
use AmazonAdvertisingApi\DataCollection\ThemeBasedBidRecommendation;
use AmazonAdvertisingApi\Table\AmazonAdsBidRecommendationsV2Table;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordRecommendationsTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use AmazonAdvertisingApi\Table\AmazonAdsThemeBasedBidRecommendationTable;
use AmazonAdvertisingApi\Table\SelectDateTable;
use AmazonAdvertisingApi\Table\Table;
use AmazonAdvertisingApi\Table\TimeUnitTable;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesTable;
use App\ApplicationModul\Amazon\Model\RecomendationsBidsManager;
use App\BaseModul\System\Controller\Controller;
use http\Env\Response;
use Micho\Db;
use Micho\Exception\SettingException;
use Micho\Utilities\DateTimeUtilities;
use Micho\Utilities\StringUtilities;
use Exception;
use DateTime;

class Report
{
    /**
     * Nastavenia
     */
    const CONTENT_TYPE = 'application/vnd.createasyncreportrequest.v3+json';
    const ENDPOINT = '/reporting/reports';

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var Table Tabukla ukladania dát
     */
    private Table $savingTable;

    /**
     * @var string[] Hlavičky
     */
    private array $headers;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->buildHeaders();
    }

    /**
     ** Zostavy hlavičku
     * @return void
     */
    private function buildHeaders()
    {
        $this->headers = array(
            'Content-Type: ' . self::CONTENT_TYPE,
            'Authorization: Bearer ' . $this->connection->amazonAdsConfigTable->getAccessToken(),
            'Amazon-Advertising-API-ClientId: ' . $this->connection->amazonAdsConfigTable->getClientId(),
            'Amazon-Advertising-API-Scope: ' . $this->connection->profileId
        );
    }

    /**
     ** Odošle žiadosť na prípravu údajov na stiahnutie
     * @param array $data zakladného nastavenia
     * @return void
     * @throws Exception
     */
    public function request(array $data)
    {
        $request = $this->sendRequest($data);

        if($request [Connection::SUCCESS])
        {
            $data = json_decode($request [Connection::RESPONSE],true);
            $this->connection->reportId = $data[Connection::REPORT_ID];
        }
        else
            throw new Exception('Error occured. Code: ' . $request[Connection::CODE] . ' - ' .
                ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['status'] . ' - ' .
                ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['notes']);
    }

    /**
     ** Odošle žiadosť na prípravu údajov pre všetky dostupné profily
     * @param string $startDate začiatok stahovania dat reportu
     * @param string $endDate koniec stahovania dat reportu
     * @return string retazec url pre presmerovanie
     * @throws Exception
     */
    public function requestAllProfiles(string $startDate, string $endDate) : string
    {
        $requestSettings = [DataRaw::START_DATE => $startDate,DataRaw::END_DATE => $endDate,
            DataRaw::REPORT_TYPE_ID => ConstRawSpAdvertisedProduct::REPORT_TYPE_ID,DataRaw::TIME_UNIT => ConstRaw::TIME_UNIT_SUMMARY];

        // pošle žiedosť pre všetky profily
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        $profiles = $amazonAdsProfileTable->getPair([Table::USER_ID => $this->connection->amazonAdsConfigTable->getUserId()]);

        if(!$profiles)
            throw new SettingException('Basic settings are not created');

        $reportsIdUrl = '/';
        foreach ($profiles as $profile)
        {
            $this->connection->profileId = $profile;
            $this->buildHeaders();

            $this->request($requestSettings);
            $reportsIdUrl .= $this->connection->reportId . '.';
        }
        return trim($reportsIdUrl,'.');
    }

    /**
     ** Odošle žiadosť
     * @param array $data zakladného nastavenia
     * @return array
     * @throws Exception
     */
    private function sendRequest(array $data) : array
    {
        $dataRaw = new DataRaw($data);
        return $this->connection->_operation($this->connection->amazonAdsRegionTable->getHost(),self::ENDPOINT,
                                                 $this->headers,'POST', $dataRaw->getDataRaw());
    }

    /**
     ** Overí či su údaje pripravené na stiahnutie
     * @return array
     * @throws Exception
     */
    public function check() : array
    {
        $request = $this->sendCheck();

        if($request[Connection::SUCCESS])
        {
            $data = json_decode($request[Connection::RESPONSE],true);

            // vytiahnem niejake data na spatne preukladanie do formulára
            $keysResponse = [DataRaw::START_DATE, DataRaw::END_DATE, DataRaw::STATUS];
            $keysConfiguration = [DataRaw::TIME_UNIT,DataRaw::REPORT_TYPE_ID];

            return array_merge(array_intersect_key($data,array_flip($keysResponse)),
                array_intersect_key($data[DataRaw::CONFIGURATION],array_flip($keysConfiguration)));
        }
        else
            throw new Exception('The data is not ready for download');
    }

    /**
     ** Overí či su údaje pre všetky reporty pripravené na stiahnutie
     * @param string $reportsIdUrl Retaze Id reportov z url
     * @return bool Status ci je su vŠetky reporty pripravené
     * @throws Exception
     */
    public function checkAllReports(string $reportsIdUrl) : bool
    {
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        $profiles = $amazonAdsProfileTable->getPair([Table::USER_ID => $this->connection->amazonAdsConfigTable->getUserId()]);
        $profilesIdReportsId = array_combine($profiles, explode('.', $reportsIdUrl)); //spoji dva pola do ejdneho pricom jedno je ako kluc a dreuje ako hodnota

        $completed = true;

        foreach ($profilesIdReportsId as $profileId => $reportId)
        {
            $this->connection->profileId = $profileId;
            $this->connection->reportId = $reportId;
            $this->buildHeaders();

            $checkData = $this->check();

            if($checkData[Connection::STATUS] !== 'COMPLETED')
                $completed = false;
        }
        return $completed;
    }

    /**
     ** Odošle žiadosť
     * @return array
     * @throws Exception
     */
    private function sendCheck() : array
    {
        return $this->connection->_operation($this->connection->amazonAdsRegionTable->getHost(),
            self::ENDPOINT . '/' . $this->connection->reportId, $this->headers,'GET');
    }

    /**
     ** Stiahne vygenerované údaje
     * @return array|mixed
     */
    public function get()
    {
        $request = $this->sendCheck();

        if ($request[Connection::SUCCESS])
        {
            $json = json_decode($request[Connection::RESPONSE], true);
            if ($json[Connection::STATUS] == "COMPLETED")
            {
                $data['reports'] = $this->connection->_download($json[Connection::URL]);
                // data navyše pre rozlišovanie a ukladanie
                $data['other'][DataRaw::REPORT_TYPE_ID] = $json[DataRaw::CONFIGURATION][DataRaw::REPORT_TYPE_ID];
                $data['other'][SelectDateTable::SELECT_START_DATE] = $json[DataRaw::START_DATE];
                $data['other'][SelectDateTable::SELECT_END_DATE] = $json[DataRaw::END_DATE];
                $data['other'][TimeUnitTable::TIME_UNIT_NAME] = $json[DataRaw::CONFIGURATION][DataRaw::TIME_UNIT];
                return $data;
            }
        }
        else
            throw new Exception('The data is not ready for download');
    }

    /**
     ** Stiahne a pripravy Dáta
     * @return array Pole upravených dát pripravených na uloženie do DB
     * @throws Exception
     */
    public function prepareData(array $dataReports): array
    {
        $reports = $dataReports['reports'];
        $data = [];
        if($reports[Connection::SUCCESS])
        {
            foreach (json_decode($reports[Connection::RESPONSE],true) as $i => $report)
            {
                $data[$i] = $this->savingTable->getArrayData();
                array_shift($data[$i]); // odstranim prvú polozku co je id autoincrement
                if (is_array($report))
                {
                    foreach ($report as $key => $item)
                    {
                        $key = StringUtilities::camelToUnderline($key);

                        // kvoli tomu ze tie ktore su zecielene na ine produkty cez asin tak nemaju KEYWORD_ID svojich keywords v dalsej tabuľke a tym padom foreigner kluc piše chybu
                        // tak preto hodnotu KEYWORD_ID presuniem na KEYWORD_TARGETING_ID a KEYWORD_ID = NULL('')
                        // Ak maju data hodnotu keywordType nataven na TATGETING zanemna to ze keywordId sa nachadza v tabulke Target a preto to poprehadzujem
                        if(defined(get_class($this->savingTable) . '::KEYWORD_ID') && ($key === $this->savingTable::KEYWORD_ID || $key === $this->savingTable::TARGET_ID))
                        {
                            if(in_array($report[StringUtilities::underlineToCamel($this->savingTable::MATCH_TYPE)],KeywordRecommendations::MATCH_TYPE_OPTIONS))
                            {
                                $data[$i][$this->savingTable::KEYWORD_ID] = $item;
                                $data[$i][$this->savingTable::TARGET_ID] = '1';
                            }
                            else
                            {
                                $data[$i][$this->savingTable::KEYWORD_ID] = '1'; // nastavujem 1 kvoli tomu že null vŽdy rozisuje ako unikatnu aj ked je veic NULL uklada dupliitne data pri or update
                                $data[$i][$this->savingTable::TARGET_ID] = $item;
                            }
                        }
                        else
                            $data[$i][$key] = $item;
                    }
                }
                $data[$i][Table::USER_ID] = $this->connection->amazonAdsConfigTable->getUserId();
                $data[$i][Table::PROFILE_ID] = $this->connection->profileId;
                $data[$i][SelectDateTable::SELECT_DATE_ID] = $dataReports['other'][SelectDateTable::SELECT_DATE_ID];
                $data[$i][TimeUnitTable::TIME_UNIT_ID] = $dataReports['other'][TimeUnitTable::TIME_UNIT_ID];
            }

            if (empty($data))
                throw new Exception("No data in this date range");

            return $data;
        }
        else
            throw new Exception('Error occured. Code: ' . $reports[Connection::CODE] . ' - ' .
                ReportDictionary::ERROR_CODES[$reports[Connection::CODE]]['status'] . ' - ' .
                ReportDictionary::ERROR_CODES[$reports[Connection::CODE]]['notes']);
    }

    /**
     ** Uloži reporty do prislušnej tabuľky
     * @return string Id Spracovavaného Dátumu
     * @throws Exception
     */
    public function save()
    {
        //toto get keby som tocil a ulozil komplet celu tabulku v jednom kroku
        $dataReport = $this->get();

        $this->savingTable = new ('AmazonAdvertisingApi\\Table\\AmazonAds' . StringUtilities::firstBig($dataReport['other'][DataRaw::REPORT_TYPE_ID]) . 'Table')();

        // uloženie vybratého dátumu
        $selectDateTable = new SelectDateTable();
        $selectDateId = $selectDateTable->save($dataReport['other']);

        // načitanie vybratého Id time unit
        $timeUnitTable = new TimeUnitTable($dataReport['other'][TimeUnitTable::TIME_UNIT_NAME]);
        $timeUnitId = $timeUnitTable->getTimeUnitId();

        unset($dataReport['other']);

        $dataReport['other'] = [SelectDateTable::SELECT_DATE_ID => $selectDateId, TimeUnitTable::TIME_UNIT_ID => $timeUnitId];

        $data = $this->prepareData($dataReport);

        //print_r($data[0]);die;

        $this->savingTable->save($data);

        // ak Mam tabulku targeting tak k nej stahujem aj suggestion bid
        if ($this->savingTable instanceof AmazonAdsSpTargetingTable)
        {
            $where = [UserTable::USER_ID => $this->connection->amazonAdsConfigTable->getUserId(), AmazonAdsProfileTable::PROFILE_ID => $this->connection->profileId, SelectDateTable::SELECT_DATE_ID => $selectDateId];
            $recomendationsBidsManager = new RecomendationsBidsManager($this->connection);
            $recomendationsBidsManager->downloadBids($where);
        }
        $this->delete();
    }

    /**
     ** Uloži všetky reporty do prislušnej tabuľky
     * @param string $reportsIdUrl Retaze Id reportov z url
     * @return void
     * @throws Exception
     */
    public function saveAllReports(string $reportsIdUrl) :void
    {
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        $profiles = $amazonAdsProfileTable->getPair([Table::USER_ID => $this->connection->amazonAdsConfigTable->getUserId()]);

        if(empty($profiles))
            throw new SettingException('Basic settings are not created');

        $profilesIdReportsId = array_combine($profiles, explode('.', $reportsIdUrl)); //spoji dva pola do ejdneho pricom jedno je ako kluc a dreuje ako hodnota

        foreach ($profilesIdReportsId as $profileId => $reportId)
        {
            $this->connection->profileId = $profileId;// nastavenia profilu spojenia
            $this->connection->reportId = $reportId; // nastavenie Id reportu
            $this->buildHeaders();
            $this->save();
        }
    }

    /**
     ** Vymaža žiadaný report
     * @return void
     * @throws Exception
     */
    public function delete()
    {
        $request = $this->connection->_operation($this->connection->amazonAdsRegionTable->getHost(),
            self::ENDPOINT . '/' . $this->connection->reportId, $this->headers,'DELETE');
        // vymaze zaznam ak nie tak vyvolá chýbu
        if(!$request[Connection::SUCCESS])
            throw new Exception('Error occured. Code: ' . $request[Connection::CODE] . ' - ' .
                ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['status'] . ' - ' .
                ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['notes']);
    }

    /**
     * @return Table
     */
    public function getSavingTable(): Table
    {
        return $this->savingTable;
    }

    /**
     ** získa a uloži bidi
     * @param array $bidData Data potrebné pre stahovanie budov
     * @throws Exception
     */
    public function getBids(array $bidData) //: array
    {
        $amazonAdsSpTargetingId = $bidData[AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID];
        $keyword = $bidData[AmazonAdsSpTargetingTable::KEYWORD];
        $matchType = $bidData[AmazonAdsSpTargetingTable::MATCH_TYPE];
        $campaignId = $bidData[AmazonAdsSpTargetingTable::CAMPAIGN_ID];
        $adGroupId = $bidData[AmazonAdsSpTargetingTable::AD_GROUP_ID];
        $profileId = $bidData[AmazonAdsProfileTable::PROFILE_ID];

        // rozhodujem akym sposobom budem načitavaŤ bidy
        if(in_array($matchType, KeywordRecommendations::MATCH_TYPE_OPTIONS))
        {
            $amazonAdsKeywordRecommendationsTable = new AmazonAdsKeywordRecommendationsTable();
            $dataKeywordRecommendations = $this->connection->keywordRecommendations($campaignId,$adGroupId, $matchType,$keyword)->prepareData();
            $dataKeywordRecommendations[0][AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID] = $amazonAdsSpTargetingId;
            //AmazonAdsController::view($dataKeywordRecommendations);
            $amazonAdsKeywordRecommendationsTable->save($dataKeywordRecommendations);
            //return [AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID => $amazonAdsSpTargetingId,
                //AmazonAdsKeywordRecommendationsTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID => $amazonAdsKeywordRecommendationsId];
        }
        else
        {
            if(in_array($matchType, BidRecommendationsV2::MATCH_TYPE_OPTIONS))
            {
                $amazonAdsBidRecommendationsV2Table = new AmazonAdsBidRecommendationsV2Table();
                $keyword = explode('=', $keyword);
                $type = explode('-',$keyword[0])[0];
                $value = StringUtilities::returnStringBetween($keyword[1],'"','"');
                $dataBidRecommendationsV2 = $this->connection->BidRecommendationsV2($adGroupId, $value, $type)->prepareData();
                $dataBidRecommendationsV2[0][AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID] = $amazonAdsSpTargetingId;
                //AmazonAdsController::view($dataBidRecommendationsV2);
                $amazonAdsBidRecommendationsV2Table->save($dataBidRecommendationsV2);
                //return [AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID => $amazonAdsSpTargetingId,
                    //AmazonAdsBidRecommendationsV2Table::AMAZON_ADS_RECOMMENDATIONS_V2_ID => $amazonAdsBidRecommendationsV2Id];
            }
            elseif (in_array($matchType, ThemeBasedBidRecommendation::MATCH_TYPE_OPTIONS))
            {
                $countryCode = (new AmazonAdsProfileTable())->getCountryCode($profileId);

                if(in_array($countryCode,ThemeBasedBidRecommendation::ALLOWED_PROFILE))
                {
                    $amazonAdsThemeBasedBidRecommendationTable = new AmazonAdsThemeBasedBidRecommendationTable();
                    $targetingExpressionTypeKeyword = strtoupper(StringUtilities::changeChar($keyword,'-','_'));
                    $dataThemeBasedBidRecommendation = $this->connection->themeBasedBidRecommendation($campaignId, $adGroupId, $targetingExpressionTypeKeyword)->prepareData();
                    $dataThemeBasedBidRecommendation[0][AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID] = $amazonAdsSpTargetingId;
                    //AmazonAdsController::view($dataThemeBasedBidRecommendation);
                    $amazonAdsThemeBasedBidRecommendationTable->save($dataThemeBasedBidRecommendation, false);
                    //return [AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID => $amazonAdsSpTargetingId,
                       // AmazonAdsThemeBasedBidRecommendationTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID => $amazonAdsThemeBasedBidRecommendationId];
                }
            }
        }
    }

    public function deleteReport(string $reportTypeId, string $selectDateId, string $userId, string $profileId)
    {
        $table = new ('AmazonAdvertisingApi\\Table\\AmazonAds' . StringUtilities::firstBig($reportTypeId) . 'Table')();

        echo 'DELETE FROM ' . $table::TABLE . ' WHERE ' . $table::SELECT_DATE_ID . ' = ? AND ' . $table::USER_ID . ' = ? AND ' . $table::PROFILE_ID . ' = ?';echo "<hr>";

        //Db::queryAlone()

        //Db::query('DELETE FROM clanok WHERE url = ?', [$selectDateId,$userId,$profileId]);
        echo $table::TABLE;echo "<hr>";
        echo $selectDateId;echo "<hr>";
        echo $userId;echo "<hr>";
        echo $profileId;echo "<hr>";

        echo "dnu som celkom";
        //

    }


}