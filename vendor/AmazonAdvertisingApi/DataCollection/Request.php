<?php

namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Report\ReportDictionary;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\Table;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

/**
 * Rozhranie pre Reporty na stahovenie údajov z amazon ads
 */
abstract class Request
{
    /**
     * Nastavenia
     */
    protected $className = null;
    protected $contentType = null;
    protected $accept = null;
    protected $prefer = null;
    protected $endPoint = null;
    protected $listMethod = null;
    protected $getMethod = null;
    protected $filter = null;
    protected $dataRaw = null;

    /**
     * @var Connection
     */
    protected Connection $connection;

    /**
     * @var string[] Hlavičky
     */
    protected array $headers;

    /**
     * @param Connection $connection Inštancia spojenia
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->buildHeader();
    }

    /**
     ** Zostavý hlavičku požiadavky
     * @return void
     */
    private function buildHeader()
    {
        $this->headers = array(
            'Content-Type: ' . $this->contentType,
            'Authorization: Bearer ' . $this->connection->amazonAdsConfigTable->getAccessToken(),
            'Amazon-Advertising-API-ClientId: ' . $this->connection->amazonAdsConfigTable->getClientId()
        );
        if (!empty($this->connection->profileId))
            array_push($this->headers, 'Amazon-Advertising-API-Scope: ' . $this->connection->profileId);
        if (!empty($this->accept))
            array_push($this->headers, 'Accept: ' . $this->accept);
        if (!empty($this->prefer))
            array_push($this->headers, 'Prefer: ' . $this->prefer);

        //print_r($this->headers);echo"<br><br>";
    }

    /**
     ** Načita profily
     * @return mixed
     */
    protected function getProfiles()
    {
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        return $amazonAdsProfileTable->get([AmazonAdsProfileTable::USER_ID => $this->connection->amazonAdsConfigTable->getUserId()]);
    }

    /**
     ** Stiahne a pripravy Dáta
     * @return array Pole upravených dát pripravených na uloženie do DB
     * @throws Exception
     */
    public function prepareData() : array
    {
        $profiles = $this->getProfiles();
        $full = array();
        foreach ($profiles as $profile)
        {
            $this->connection->profileId = $profile[Table::PROFILE_ID];

            $requestData = $this->list();

            $data = $this->setTableData($requestData);
            $full = array_merge($full, $data);
        }
        return $full;
    }

    /**
     ** preloži nazvy amazonu na nazvy premenných v databáze
     * @param array $requestData
     * @return array
     * @throws Exception
     */
    protected function setTableData(array $requestData) : array
    {
        // preloži nazvy amazonu na nazvy premenných v databáze
        if($requestData[Connection::SUCCESS])
        {
            $table = new ('AmazonAdvertisingApi\\Table\\AmazonAds' . $this->className . 'Table')();

            $requestData = json_decode($requestData[Connection::RESPONSE],true);

            // tie vo verzou 3 sa udajen nachadzaju este v poli z názvom napr['campaings'] preto ak je to V3 tak zoberiem prve pole inak enchavam povodne
            $requestData = $this->listMethod === 'POST' ? array_shift($requestData) : $requestData;

            foreach ($requestData as $i => $requestValue)
            {
                $data[$i] = $table->getArrayData();

                if (is_array($requestValue))
                    foreach ($requestValue as $key => $item)
                    {
                        if(is_array($item))
                            foreach ($item as $index => $value)
                            {
                                $key1 = $key . ($index == 0 ? '' : 'Array' . StringUtilities::firstBig($index));
                                if(is_array($value))
                                    foreach ($value as $in => $val)
                                    {
                                        $key2 = $key1 . ($in == 0 ? '' : 'Array' .  StringUtilities::firstBig($in));
                                        if(is_array($val))
                                            foreach ($val as $inn => $vall)
                                            {
                                                $key3 = $key2 . ($inn == 0 ? '' : 'Array' . StringUtilities::firstBig($inn));
                                                if(is_array($vall))
                                                    foreach ($vall as $innn => $valll)
                                                    {
                                                        $key4 = $key3 . ($innn == 0 ? '' : 'Array' . StringUtilities::firstBig($innn));
                                                        if (!is_array($valll))
                                                            $data[$i][StringUtilities::camelToUnderline($key4)] = $valll;
                                                    }
                                                else
                                                    $data[$i][StringUtilities::camelToUnderline($key3)] = $vall;
                                            }
                                        else
                                            $data[$i][StringUtilities::camelToUnderline($key2)] = $val;
                                    }
                                else
                                    $data[$i][StringUtilities::camelToUnderline($key1)] = $value;
                            }
                        else
                            $data[$i][StringUtilities::camelToUnderline($key)] = $item;
                    }
                $data[$i][Table::USER_ID] = $this->connection->amazonAdsConfigTable->getUserId();

                if($this->className === 'Profile')
                    $data[$i][AmazonAdsProfileTable::COUNTRY_NAME] = Profile::ABBREVIATIONS[$data[$i][AmazonAdsProfileTable::COUNTRY_CODE]];
                else
                    $data[$i][Table::PROFILE_ID] = $this->connection->profileId;
            }
        }
        else
            throw new Exception('Error occured. Code: ' . $requestData[Connection::CODE] . ' - ' .
                ReportDictionary::ERROR_CODES[$requestData[Connection::CODE]]['status'] . ' - ' .
                ReportDictionary::ERROR_CODES[$requestData[Connection::CODE]]['notes']);
        return $data;
    }

    /**
     ** Načita Zoznam z Amazon
     * @return array
     * @throws Exception
     */
    public function list(): array
    {
        $this->buildHeader();

        if ($this->className === 'Portfolio')
            return $this->request();

        return $this->loadAllPages();
    }

    /**
     ** NaČita dalšie strany kedŽe nemusi mvrátiť všetky data
     * @param string|null $nextToken Token dalšej strany dát
     * @return array|mixed
     * @throws Exception
     */
    protected function loadAllPages(string|null $nextToken = null)
    {
        // nenačitavalo vŠetky lebo zakladny limit je nastaveny na 1000
        $dataRaw = ['maxResults' => 10000, Connection::NEXT_TOKEN => $nextToken];// maximalne mozne nastavená hodnota

        $data =  $this->request($dataRaw);

        $responseData = json_decode($data[Connection::RESPONSE],true);//

        if (isset($responseData[Connection::NEXT_TOKEN])) // ak obsahuje token tak načitavam Dalšiu stranu
        {
            $keyName = array_key_first($responseData);

            $returnData = $this->loadAllPages($responseData[Connection::NEXT_TOKEN]);
            $returnData = json_decode($returnData[Connection::RESPONSE],true);

            $responseData[$keyName] = array_merge($responseData[$keyName], $returnData[$keyName]);
        }
        $data[Connection::RESPONSE] = json_encode($responseData);
        return $data;
    }

    /**
     * Načita podľa hodnoty Id
     * @param string $id Ktoreho detailne hodnoty chcem načitať z amazon
     * @return array
     * @throws Exception
     */
    public function get(string $id): array
    {
        if (empty($this->filter))
        {
            $this->endPoint .= '/' . $id; // tak je to ako Id ktore chem načitavať
            $dataRaw = array();
        }
        else
        {
            $dataRaw = [
                $this->filter . 'IdFilter' => [
                    'include' => [$id]
                ]
            ];
        }
        $this->buildHeader();
        return $this->request($dataRaw);
    }

    /**
     ** Zostavy a odošle žiadosť
     * @param array $dataRaw Data pre požiadavku
     * @return array|mixed
     * @throws Exception
     */
    protected function request(array $dataRaw = array())
    {
        $dataRaw = !empty($this->dataRaw) ? $this->dataRaw : $dataRaw;

        return $this->connection->_operation($this->connection->amazonAdsRegionTable->getHost(),
            $this->endPoint, $this->headers,$this->listMethod, $dataRaw);
    }
}