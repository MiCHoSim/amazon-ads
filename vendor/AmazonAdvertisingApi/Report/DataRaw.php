<?php

namespace AmazonAdvertisingApi\Report;

use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;

/**
 * Abstraktná trieda na prípravu údajov na odoslanie cez HTTP CURLOPT_POSTFIELDS
 */
class DataRaw
{
    /**
     * Konštanty
     */
    const NAME = 'name';
    const START_DATE = 'startDate';
    const END_DATE = 'endDate';
    const CONFIGURATION = 'configuration';
        const AD_PRODUCT = 'adProduct';
        const REPORT_TYPE_ID = 'reportTypeId';
        const GROUP_BY = 'groupBy';
        const TIME_UNIT = 'timeUnit';
        const COLUMNS = 'columns';
        const FORMAT = 'format';

    const STATUS = 'status';

    /**
     * @var array Kľuče
     */
    private $keys = [self::NAME,self::START_DATE,self::END_DATE,self::CONFIGURATION,self::AD_PRODUCT,self::REPORT_TYPE_ID,
        self::GROUP_BY,self::TIME_UNIT,self::COLUMNS,self::FORMAT];

    /**
     * @var mixed Parametre a konšatntý potrebné k žiadosti
     */
    private string $name;
    private string $startDate;
    private string $endDate;
        private string $adProduct;
        private string $reportTypeId;
        private string $timeUnit;
        private array $groupBy;
        private string $format = 'GZIP_JSON';
        private array $columns;

    /**
     * @var array Výsledné pole žiadosti a parametrov
     */
    private array $dataRaw;

    /**
     * @param array $data Data hlavného nastavenie Z fomulára [self::START_DATE,self::END_DATE,self::REPORT_TYPE_ID,self::TIME_UNIT];
     */
    public function __construct(array $data)
    {
        $data = ArrayUtilities::filterKeys($data, $this->keys);

        $this->name = 'Report-' . $data[self::REPORT_TYPE_ID];

        // automaticke ulozenie hodnot do premenných atributov
        foreach ($data as $key => $dat)
        {
            $nameAtribute = $key;
            $this->$nameAtribute = $dat; //ulozi hodnotu atributu
        }

        $classConstRaw = new ('AmazonAdvertisingApi\\Report\\ConstRaw' . StringUtilities::firstBig($this->reportTypeId));

        $this->adProduct = ConstRaw::AD_PRODUCT_SPONSORED_PRODUCTS;
        $this->groupBy = $classConstRaw::GRPUP_BY;

        $this->columns =  array_merge($classConstRaw::BASE_METRICS,$classConstRaw::ADDITIONAL_METRIX,
            ConstRaw::TIME_UNIT_MATRIX[$this->timeUnit]);

        $this->packDataRaw();
    }

    /**
     ** Zostavi parametre na požadovanú formu pre vloženie do CURLOPT_POSTFIELDS
     * @return void
     */
    private function packDataRaw()
    {
        $this->dataRaw = [
            self::NAME => $this->name,
            self::START_DATE => $this->startDate,
            self::END_DATE => $this->endDate,
            self::CONFIGURATION => [
                self::AD_PRODUCT => $this->adProduct,
                self::REPORT_TYPE_ID => $this->reportTypeId,
                self::GROUP_BY => $this->groupBy,
                self::TIME_UNIT => $this->timeUnit,
                self::COLUMNS => $this->columns,
                self::FORMAT => $this->format
            ]];
    }

    /**
     * @return array
     */
    public function getDataRaw(): array
    {
        return $this->dataRaw;
    }
}