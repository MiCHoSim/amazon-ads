<?php
namespace AmazonAdvertisingApi\Currency;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\Request;
use AmazonAdvertisingApi\Report\ReportDictionary;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use Exception;

class Currency
{
    /**
     * Nastavenia
     */
    protected $className = 'Currency';

    protected $host = 'api.apilayer.com';
    protected $endPoint = '/exchangerates_data/convert';
    protected array $headers =  array(
        "Content-Type: text/plain",
        "apikey: TPrdOiwORFyS31yKn6O7hLO5D1eEpALd"
    );
    protected $method = 'GET';

    /**
     * Parametre
     */
    const TO = 'to';
    const FROM = 'from';
    const AMOUNT = 'amount';
    protected $params = [self::TO => 'EUR', self::FROM => 'PLN', self::AMOUNT => '1'];

    /**
     * Skratky
     */
    const ABBREVIATIONS_CURRENCY = ['SE' => 'SEK','PL' => 'PLN'];
    const DATE = 'date';
    const RESULT = 'result';

    /**
     * @param string $to na meny
     * @param string $from z meny
     * @param int $amount hodnota ktoru menim
     */
    public function prepareData(string $to, array $froms, int $amount = 1) : array
    {
        foreach ($froms as $key => $from)
        {
            $this->params = [self::TO => $to, self::FROM => $from, self::AMOUNT => $amount];

            $connection = new Connection('0');
            $request = $connection->_operation($this->host, $this->endPoint, $this->headers, $this->method, $this->params);

            if($request[Connection::SUCCESS])
            {
                $requestData = json_decode($request[Connection::RESPONSE],true);
            }
            else
                throw new Exception('Error occured. Code: ' . $request[Connection::CODE] . ' - ' .
                    ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['status'] . ' - ' .
                    ReportDictionary::ERROR_CODES[$request[Connection::CODE]]['notes']);

            $currencyData[$key][CurrencyTable::DOWNLOAD_DATE] = $requestData[self::DATE];
            $currencyData[$key][CurrencyTable::FROM_CURRENCY] = $from;
            $currencyData[$key][CurrencyTable::TO_CURRENCY] = $to;
            $currencyData[$key][CurrencyTable::RATE] = $requestData[self::RESULT];
        }
        return $currencyData;
    }
}