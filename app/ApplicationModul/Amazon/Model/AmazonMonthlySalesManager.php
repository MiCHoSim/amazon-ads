<?php

namespace App\ApplicationModul\Amazon\Model;


use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpAdvertisedProductTable;
use AmazonAdvertisingApi\Table\SelectDateTable;
use App\ApplicationModul\AppManagement\Model\AmazonProductDataTable;
use Micho\Exception\SettingException;
use Micho\Exception\UserException;
use Micho\Files\FileXlsx;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\DateTimeUtilities;
use DateTime;
use Micho\Utilities\StringUtilities;

/**
 * Trieda spracujuca požiadavky AmazonMonthlySalesManager
 */
class AmazonMonthlySalesManager
{
    const START_YEAR = 2023;
    /**
     * Konštanty Excelu
     */
    const ASIN = 'ASIN';
    const SKU = 'SKU';
    const MARKETPLACE = 'Marketplace';
    const START_DATE = 'Start Date';
    const END_DATE = 'End Date';
    const GROSS_REVENUE = 'Gross Revenue';
    const EXPENSES = 'Expenses';
    const NET_PROFIT = 'Net Profit';
    const MARGIN = 'Margin %';
    const ROI = 'ROI %';
    const REFUNDS = 'Refunds';
    const UNIT_SOLD = 'Units Sold';
    const PAGE_VIEWS = 'Page Views';
    const SESSIONS = 'Sessions';
    const UNIT_SESSION = 'Unit Session %';

    /**
     * data ktore použijem z data Raw
     */
    const KEYS_XLSX = ['asin' => self::ASIN,'sku' => self::SKU,'marketplace' => self::MARKETPLACE,
        'start_date' => self::START_DATE,'end_date' => self::END_DATE,'gross_revenue' => self::GROSS_REVENUE,
        'expenses' => self::EXPENSES,'net_profit' => self::NET_PROFIT,'margin' => self::MARGIN,'roi' => self::ROI,
        'refunds' => self::REFUNDS,'units_sold' => self::UNIT_SOLD,'page_views' => self::PAGE_VIEWS,
        'sessions' => self::SESSIONS,'unit_session' => self::UNIT_SESSION
        ];



    /**
     ** spracuje načitané Dáta excelu na spracovanie pre PHP prisom kluce su ako názvy hlavičiek a bunky sú ako ich hodnoty
     * @param string $pathToFile Cesta k uloženému súboru
     * @return array Načitané a upravené Dátu na uloženie do DB
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function prepareDataXlsx(string $pathToFile) : array
    {
        $xlsx = new FileXlsx();
        $xlsxData = $xlsx->getTitleOtherRow($pathToFile);

        $rowsData = array();
        foreach ($xlsxData['otherRow'] as $row) // priradenie nazvu k hodnote
        {
            $row = array_combine($xlsxData['titleRow'], $row);

            if(isset($row[self::START_DATE]))
            {
                $startDate = StringUtilities::changeChar($row[self::START_DATE],'/','.');
                $endDate =  StringUtilities::changeChar($row[self::END_DATE],'/','.');
                $row[self::START_DATE] = (new DateTime($startDate))->format(DateTimeUtilities::DB_DATE_FORMAT);
                $row[self::END_DATE] = (new DateTime($endDate))->format(DateTimeUtilities::DB_DATE_FORMAT);
                $rowsData[] = ArrayUtilities::filterKeys($row, self::KEYS_XLSX);// priradí hodnotam kluča prida len tie ktore pozadujem
            }
            else
                throw new UserException('The imported table does not have the necessary data');

        }
        return $rowsData;
    }

    /**
     ** Pripravý a uloži datá do DB
     * @param array $xlsxData Pole dát z excelu
     * @param string $userId Id uživateľa
     * @return void
     */
    public function prepareAndSave(array $xlsxData, string $userId)
    {
        $amazonMonthlySalesTable = new AmazonMonthlySalesTable();
        $selectDate = new SelectDateTable();
        $selectDateId = $selectDate->selectDateId($xlsxData[0][self::START_DATE],$xlsxData[0][self::END_DATE]);

        $amazonProductDataTable = new AmazonProductDataTable();
        $amazonAdsProfileTable = new AmazonAdsProfileTable();

        // prejde pole a púodava do neho potrebné hodnoty
        foreach ($xlsxData as $i => $values)
        {
            $monthlySalesData[$i] = $amazonMonthlySalesTable->getArrayData(); // data ktore potrebujem získať
            array_shift($monthlySalesData[$i]); // odstranenie id

            // Priradenie profilu a teda štátu
            $profileId = $amazonAdsProfileTable->profileIdFromMarketplace($values[self::MARKETPLACE], $userId);

            if(!$profileId) // nacitavalo BE belgocko ktore nemajú v profiloch
            {
                unset($monthlySalesData[$i]); // nieje v profiloch BE tak odstranim cely riadok
                continue;
            }
            $monthlySalesData[$i][AmazonMonthlySalesTable::USER_ID] = $userId;
            $monthlySalesData[$i][AmazonMonthlySalesTable::PROFILE_ID] = $profileId;

            $monthlySalesData[$i][AmazonMonthlySalesTable::SELECT_DATE_ID] = $selectDateId;//priradenie select_date_id pre start a end date

            // prejdenie všetkých položiek ktore sa len pretiahnú z xlsx... preklad názvu klučov
            foreach (AmazonMonthlySalesTable::KEYS_XLSX as $key)
            {
                $monthlySalesData[$i][$key] = $values[self::KEYS_XLSX[$key]];
            }

            // načitam potrebné údaje z Advertised
            $amazonAdsSpAdvertisedProductTable= new AmazonAdsSpAdvertisedProductTable();
            $where = [AmazonAdsSpAdvertisedProductTable::ADVERTISED_SKU => $values[self::SKU],
                AmazonAdsSpAdvertisedProductTable::PROFILE_ID => $monthlySalesData[$i][AmazonMonthlySalesTable::PROFILE_ID],
                AmazonAdsSpAdvertisedProductTable::SELECT_DATE_ID => $monthlySalesData[$i][AmazonMonthlySalesTable::SELECT_DATE_ID]];

            $keys = [AmazonAdsSpAdvertisedProductTable::PORTFOLIO_ID, ' 
                    AVG(' . AmazonAdsSpAdvertisedProductTable::ACOS_CLICKS14D.') as ' . AmazonMonthlySalesTable::ACOS,
                ' SUM(' . AmazonAdsSpAdvertisedProductTable::COST.') as ' . AmazonMonthlySalesTable::AD_COST  ,' 
                SUM(' . AmazonAdsSpAdvertisedProductTable::UNITS_SOLD_CLICKS7D.') as ' . AmazonMonthlySalesTable::UNITS_SOLD_FROM_AD_SALES];

            $advertiserData = $amazonAdsSpAdvertisedProductTable->get($where,$keys);

            if(empty($advertiserData[0][AmazonAdsSpAdvertisedProductTable::PORTFOLIO_ID])) // ked nenajde zaznam v advertised tak ho odstrani
            {
                unset($monthlySalesData[$i]); // nieje v profiloch napr BE tak odstranim cely riadok
                continue;
            }
            $monthlySalesData[$i] = array_merge($monthlySalesData[$i],$advertiserData[0]);

            //Natiahnuť tie co ma fraz dodať nejake_co_da -> toto dorobiť
            $productData = $amazonProductDataTable->selectProductData($profileId, $monthlySalesData[$i][AmazonMonthlySalesTable::SKU]);
            if(!$productData) // ked nenajde zaznam v Detailoch produktu tak neviem hotnoty ako FBA Fees, Landing Cost, Breakk-Even ... preto odstranim zaznam a preskocim iteraciu
            {
                unset($monthlySalesData[$i]);
                continue;
            }
            $monthlySalesData[$i][AmazonMonthlySalesTable::AMAZON_PRODUCT_DATA_ID] = $productData[AmazonProductDataTable::AMAZON_PRODUCT_DATA_ID];

            // vypočet ostatńých
            $monthlySalesData[$i][AmazonMonthlySalesTable::TACOS] = $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] == 0 ? 0 :
                ($monthlySalesData[$i][AmazonMonthlySalesTable::AD_COST] / $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE]) * 100;

            $monthlySalesData[$i][AmazonMonthlySalesTable::AD_SALES] = $monthlySalesData[$i][AmazonMonthlySalesTable::UNITS_SOLD] == 0 ? 0 :
                ($monthlySalesData[$i][AmazonMonthlySalesTable::UNITS_SOLD_FROM_AD_SALES] / $monthlySalesData[$i][AmazonMonthlySalesTable::UNITS_SOLD]) * 100;

            $monthlySalesData[$i][AmazonMonthlySalesTable::VAT] =
                $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] - ($monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] / 1.2);

            $monthlySalesData[$i][AmazonMonthlySalesTable::COGS] =
                $monthlySalesData[$i][AmazonMonthlySalesTable::UNITS_SOLD] * $productData[AmazonProductDataTable::LANDING_COST];

            $monthlySalesData[$i][AmazonMonthlySalesTable::ADJUSTED_NET_PROFIT] =
                $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] - (($productData[AmazonProductDataTable::FBA_FEES] * $monthlySalesData[$i][AmazonMonthlySalesTable::UNITS_SOLD]) +
                    $monthlySalesData[$i][AmazonMonthlySalesTable::COGS] + $monthlySalesData[$i][AmazonMonthlySalesTable::AD_COST] +
                    $monthlySalesData[$i][AmazonMonthlySalesTable::VAT]);

            $monthlySalesData[$i][AmazonMonthlySalesTable::ADJUSTED_NET] = $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] == 0 ? 0 :
                ($monthlySalesData[$i][AmazonMonthlySalesTable::ADJUSTED_NET_PROFIT] / $monthlySalesData[$i][AmazonMonthlySalesTable::GROSS_REVENUE] * 100);
        }

        if(empty($monthlySalesData))
            throw new SettingException('Basic settings or Product Data are not created');

        $amazonMonthlySalesTable->save($monthlySalesData);
    }

    /**
     ** vytvory pole rokov od začiatok programu po teraz
     * @return array
     */
    public function getYears() : array
    {
        $yearNow = DateTimeUtilities::yearNow();

        $years = range(self::START_YEAR, $yearNow);

        foreach ($years as $year)
        {
            $yearYear[$year] = $year;
        }

        return $yearYear ;
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