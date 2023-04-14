<?php

namespace App\ApplicationModul\Amazon\Model;

use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpAdvertisedProductTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use AmazonAdvertisingApi\Table\SelectDateTable;
use AmazonAdvertisingApi\Table\Table;
use App\ApplicationModul\Amazon\Controller\AmazonMonthlySalesController;
use App\ApplicationModul\AppManagement\Model\AmazonProductDataTable;
use Micho\Db;
use DateTime;
use Micho\Utilities\DateTimeUtilities;

/**
 * Trieda pre tabuľku amazon_monthly_sales
 */
class AmazonMonthlySalesTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_MONTHLY_SALES_TABLE = 'amazon_monthly_sales';

    /**
     * Konštanty Databázy
     */
    const AMAZON_MONTHLY_SALES_ID = 'amazon_monthly_sales_id';
    const ASIN = 'asin'; // excel
    const SKU = 'sku'; // excel
    const PORTFOLIO_ID = AmazonAdsPortfolioTable::PORTFOLIO_ID; // Advertised cez portfolio_id ziskam -> 'product'
    const PROFILE_ID = AmazonAdsProfileTable::PROFILE_ID; // z excel -> 'marketplace' a s toho najdem profile_id
    const SELECT_DATE_ID = SelectDateTable::SELECT_DATE_ID; // z excel -> 'start_date','end_date' z toho najdeme select_date_id
    const PAGE_VIEWS = 'page_views'; // excel
    const SESSIONS = 'sessions'; // excel
    const UNIT_SESSION = 'unit_session'; // excel
    const AMAZON_PRODUCT_DATA_ID = AmazonProductDataTable::AMAZON_PRODUCT_DATA_ID;// tieto sa nahraju a budu sa tahat s toho tie tri   //'fba_fees';'landing_cost'; 'break_even';
    const ACOS = 'acos'; // Advertised -> acosClicks14d
    const TACOS = 'tacos'; // počita sa
    const ROI = 'roi'; // excel
    const UNIT_SOLD = 'units_sold'; // excel
    const ORGANIC_SALES = 'organic_sales'; // pocita sa pri nacitani nacitani z tabulky
    const UNITS_SOLD_FROM_AD_SALES = 'units_sold_from_ad_sales'; // Advertised -> unitsSoldClicks7d
    const AD_SALES = 'ad_sales'; // počita sa
    const REFUNDS = 'refunds'; // excel
    const GROSS_REVENUE = 'gross_revenue'; // excel
    const EXPENSES = 'expenses'; // excel
    const AD_COST = 'ad_cost'; // Advertised -> cost
    const VAT = 'vat'; // počita sa
    const COGS = 'cogs'; // počita sa
    const NET_PROFIT = 'net_profit'; // excel
    const ADJUSTED_NET_PROFIT = 'adjusted_net_profit'; // počita sa
    const MARGIN = 'margin'; // excel
    const ADJUSTED_NET = 'adjusted_net'; // počita sa

    /**
     * KluČe pre načitavanie z rozných tabuľeik Databázi
     */
    const VIEW_KEYS = [
        self::ASIN, self::SKU,
        self::PORTFOLIO_ID =>
            [AmazonAdsPortfolioTable::AMAZON_ADS_PORTFOLIO_TABLE,AmazonAdsPortfolioTable::NAME],
        self::PROFILE_ID =>
            [AmazonAdsProfileTable::AMAZON_ADS_PROFILE_TABLE,AmazonAdsProfileTable::COUNTRY_CODE],
        self::SELECT_DATE_ID =>
            [SelectDateTable::SELECT_DATE_TABLE,SelectDateTable::SELECT_START_DATE,SelectDateTable::SELECT_END_DATE],
        self::PAGE_VIEWS,self::SESSIONS,self::UNIT_SESSION,
        self::AMAZON_PRODUCT_DATA_ID =>
            [AmazonProductDataTable::AMAZON_PRODUCT_DATA_TABLE,
                AmazonProductDataTable::FBA_FEES,AmazonProductDataTable::LANDING_COST,AmazonProductDataTable::BREAK_EVEN],
        self::ACOS,self::TACOS,self::ROI,
        self::UNIT_SOLD,
        self::ORGANIC_SALES => ['as', self::UNIT_SOLD . ' - ' . self::UNITS_SOLD_FROM_AD_SALES],
        self::UNITS_SOLD_FROM_AD_SALES,
        self::AD_SALES,self::REFUNDS,
        self::GROSS_REVENUE,self::EXPENSES,self::AD_COST,self::VAT,self::COGS,self::NET_PROFIT,self::ADJUSTED_NET_PROFIT,
        self::MARGIN,self::ADJUSTED_NET
    ];

    /**
     * preklad pre pekný naźov hlavičky tabuľky
     */
    const DICTIONARY = [
        self::ASIN => ['title' => 'ASIN', 'description' => 'ASIN'],
        self::SKU => ['title' => 'SKU', 'description' => 'SKU'],
        AmazonAdsPortfolioTable::NAME => ['title' => 'Product', 'description' => 'Product name'],
        AmazonAdsProfileTable::COUNTRY_CODE => ['title' => 'C.M', 'description' => 'Country, Marketplace'],
        SelectDateTable::SELECT_START_DATE => ['title' => 'Start D.', 'description' => 'Start Date'],
        SelectDateTable::SELECT_END_DATE => ['title' => 'End D.', 'description' => 'End Date'],
        self::PAGE_VIEWS => ['title' => 'P.V.', 'description' => 'Page Views'],
        self::SESSIONS => ['title' => 'Ses.', 'description' => 'Sessions'],
        self::UNIT_SESSION => ['title' => 'U.S.%', 'description' => 'Unit Session %'],
        AmazonProductDataTable::FBA_FEES => ['title' => 'FBA F.', 'description' => 'FBA Fees'],
        AmazonProductDataTable::LANDING_COST => ['title' => 'L. C.', 'description' => 'Landing Cost'],
        AmazonProductDataTable::BREAK_EVEN => ['title' => 'B.E. %', 'description' => 'Break Even %'],
        self::ACOS => ['title' => 'ACOS', 'description' => 'ACOS'],
        self::TACOS => ['title' => 'TACOS', 'description' => 'TACOS'],
        self::ROI => ['title' => 'ROI %', 'description' => 'ROI %'],
        self::UNIT_SOLD => ['title' => 'U.S.', 'description' => 'Unit Sold'],
        self::ORGANIC_SALES => ['title' => 'Organic S', 'description' => 'Organic Sales'],
        self::UNITS_SOLD_FROM_AD_SALES => ['title' => 'U.S. Sal.', 'description' => 'Units sold from ad Sales'],
        self::AD_SALES => ['title' => 'A. Sal. %', 'description' => 'AD sales %'],
        self::REFUNDS => ['title' => 'RF', 'description' => 'Refund'],
        self::GROSS_REVENUE => ['title' => 'G. Rev.', 'description' => 'Gross Revenue'],
        self::EXPENSES => ['title' => 'Exp.', 'description' => 'Expenses'],
        self::AD_COST => ['title' => 'A. Cost', 'description' => 'Ad cost'],
        self::VAT => ['title' => 'VAT', 'description' => 'VAT'],
        self::COGS => ['title' => 'COGS', 'description' => 'COGS'],
        self::NET_PROFIT => ['title' => 'N. Prof.', 'description' => 'Net Profit'],
        self::ADJUSTED_NET_PROFIT => ['title' => 'A.N. Prof.', 'description' => 'Adjusted Net Profit'],
        self::MARGIN => ['title' => 'Marg. %', 'description' => 'Margin %'],
        self::ADJUSTED_NET => ['title' => 'A. Net %', 'description' => 'Adjusted Net %'],
    ];

    /**
     * Ropzhodnotaveni ktore su priemer a ktore suČet
     */
    const SUM_TOTAL = [self::PAGE_VIEWS,self::SESSIONS,self::UNIT_SOLD,self::UNITS_SOLD_FROM_AD_SALES,self::REFUNDS,self::GROSS_REVENUE,
        self::EXPENSES,self::AD_COST,self::VAT,self::COGS,self::NET_PROFIT,self::ADJUSTED_NET_PROFIT];
    const AVG_TOTAL = [self::UNIT_SESSION,AmazonProductDataTable::FBA_FEES,AmazonProductDataTable::LANDING_COST,AmazonProductDataTable::BREAK_EVEN,
        self::ACOS,self::TACOS,self::ROI,self::AD_SALES,self::MARGIN,self::ADJUSTED_NET];


    /**
     * kluče
     */
    const KEYS_OTHER = [self::AMAZON_MONTHLY_SALES_ID,self::USER_ID,self::PORTFOLIO_ID,self::PROFILE_ID,self::SELECT_DATE_ID,self::AMAZON_PRODUCT_DATA_ID
    ];
    const KEYS_XLSX = [
        self::ASIN,self::SKU,self::PAGE_VIEWS,self::SESSIONS,self::UNIT_SESSION,
        self::ROI,self::UNIT_SOLD,self::REFUNDS,self::GROSS_REVENUE,self::EXPENSES,self::NET_PROFIT,self::MARGIN
    ];
    const KEYS_CALCULATE = [ // scos ešte upresnyt lebo buď sa pocita alebo ťahá
        self::ACOS,self::TACOS,self::AD_SALES,self::VAT,self::COGS,self::ADJUSTED_NET_PROFIT,self::ADJUSTED_NET
    ];
    const KEYS_FROM_TABLE = [
        self::UNITS_SOLD_FROM_AD_SALES,self::AD_COST,
    ];
    //vŠetky kluče
    public $keys;

    public function __construct(bool|string $id = false)
    {
        $this->keys = array_merge(self::KEYS_OTHER,self::KEYS_XLSX,self::KEYS_CALCULATE,self::KEYS_FROM_TABLE);
        parent::__construct($id);
    }

    /**
     * @var null Atributy
     */
    protected $amazonMonthlySalesId = null;
    protected $asin = null;
    protected $sku = null;
    protected $portfolioId = null;
    protected $profileId = null;
    protected $selectDateId = null;
    protected $pageViews = null;
    protected $sessions = null;
    protected $unitSession = null;
    protected $amazonProductDataId = null;
    protected $acos = null;
    protected $tacos = null;
    protected $roi = null;
    protected $unitsSold = null;
    protected $unitsSoldFromAdSales = null;
    protected $adSales = null;
    protected $refunds = null;
    protected $grossRevenue = null;
    protected $expenses = null;
    protected $adCost = null;
    protected $vat = null;
    protected $cogs = null;
    protected $netProfit = null;
    protected $adjustedNetProfit = null;
    protected $margin = null;
    protected $adjustedNet = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_MONTHLY_SALES_TABLE;
    protected $id = self::AMAZON_MONTHLY_SALES_ID;
    protected $whereId = self::AMAZON_MONTHLY_SALES_ID;

    protected $getPairKeyKey = null;
    protected $getPairKeyValue = null;


    /**
     ** Zostavenie Query a načitanie dát z DB
     * @param string $profileId Id profilu
     * @param string|null $monthNumbers Cisla mesiaca odelené pomlčkami
     * @param string $total Či chem zobraziť iba Totaly
     * @param string $product Nazov produktu ktore zobrazujem podla sku alebo name
     * @return array Pole hlavičky a dát
     * @throws \Exception
     */
    public function getMonthlySales(string $profileId,string $monthNumbers, string $total, string|null $product) : array
    {
        // ak je zadane eu alebo eu+uk tvitvaram odlisne SELECT a WHERE query prezakladne dáta
        $combine = array_search($profileId, AmazonAdsProfileTable::COMBINE_PROFILE);
        $groupBy = ' ';
        $whereKeysCombine = ' ';
        $profilesId = [];
        if ($combine !== false)
        {
            $groupBy = ' GROUP BY ' . $this->table .'.sku ';
            $amazonAdsProfileTable = new AmazonAdsProfileTable();

            $profilesId = $amazonAdsProfileTable->getByCountry($combine);


            $whereKeysCombine = ' AND (';
            foreach ($profilesId as $key => $profileId)
            {
                $whereKeysCombine .= $this->table . '.' . AmazonAdsProfileTable::PROFILE_ID . ' = ? ';

                if ($key !== array_key_last($profilesId))
                    $whereKeysCombine .= ' OR ';
            }
            $whereKeysCombine .= ')';

            //jednotiky preto ze mi zo vytvoir 1=1 a nepise chzbu ze neznamy whereKeys
            $whereKeys = ['1'];
            $whereValues = ['1'];
        }
        else
        {
            $whereKeys = [$this->table . '.' . AmazonAdsProfileTable::PROFILE_ID];
            $whereValues = [$profileId];
        }

        $keys = [];
        $keysTotals = [];
        $joinQuery = '';
        foreach (self::VIEW_KEYS as $k => $tableKey)
        {
            if(is_array($tableKey))
            {
                $table = array_shift($tableKey);
                if($table === 'as') // vytvaranie moznosti Scitavania odcitavania ako AS
                {
                    $oragnicSalesAs = array_shift($tableKey);
                    $keys[] = $combine !== false ? ' SUM(' . $oragnicSalesAs .') as ' . $k : $oragnicSalesAs . ' as ' . $k; // vybere nieco +- nieco as nioeco
                    $keysTotals[] = ' SUM(' . $oragnicSalesAs .') as ' . $k;
                }
                else
                {
                    $joinQuery .= ' JOIN ' . $table . ' ON ' .  $this->table . '.' . $k . '=' . $table . '.' . $k;
                    foreach ($tableKey as $key)
                    {
                        $keys[] = $combine !== false
                            ? (in_array($key,self::SUM_TOTAL) ? ' SUM(' . $key .') as ' . $key : (in_array($key,self::AVG_TOTAL) ? ' AVG(' . $key .') as ' . $key : $key)) : $key;
                        // zostavenie pola pre nacitavanie AVG A SUM tabulky
                        $keysTotals[] = in_array($key,self::SUM_TOTAL) ? ' SUM(' . $key .') as ' . $key : (in_array($key,self::AVG_TOTAL) ? ' AVG(' . $key .') as ' . $key: '0') ;
                    }
                }
            }
            else
            {
                $keys[] = $combine !== false
                    ? (in_array($tableKey,self::SUM_TOTAL) ? ' SUM(' . $this->table . '.' . $tableKey .') as ' . $tableKey : (in_array($tableKey,self::AVG_TOTAL) ? ' AVG(' . $this->table . '.' . $tableKey .') as ' . $tableKey: $this->table . '.' . $tableKey)) : $this->table . '.' . $tableKey;
                // zostavenie pola pre nacitavanie AVG A SUM tabulky
                $keysTotals[] = in_array($tableKey,self::SUM_TOTAL) ? ' SUM(' . $this->table . '.' . $tableKey .') as ' . $tableKey : (in_array($tableKey,self::AVG_TOTAL) ? ' AVG(' . $this->table . '.' . $tableKey .') as ' . $tableKey: '0');
            }
        }
        $selectQueryTotals = 'SELECT ' . implode(', ', $keysTotals);
        $selectQuery = 'SELECT ' . implode(', ', $keys);

        $fromQuery = ' FROM ' . $this->table;
        $orderQuery = ' ORDER BY ' . SelectDateTable::SELECT_START_DATE . ',' . SelectDateTable::SELECT_END_DATE;

        if ($product)
        {
            $filterProduct = ['sku' => $this->table . '.' . self::SKU ,'name' => AmazonAdsPortfolioTable::NAME];
            $explodeProduct = explode('=', $product);
            $keyProduct = $explodeProduct[0];
            $valueProduct = $explodeProduct[1];
            $whereKeys = array_merge($whereKeys,[$filterProduct[$keyProduct]]);
            $whereValues = array_merge($whereValues,[rawurldecode($valueProduct)]);
        }

        $whereOtherKeys = implode(' = ? AND ',$whereKeys) . ' = ?' . $whereKeysCombine;
        $whereValues = array_merge($whereValues, $profilesId);
        $whereValuesTotal = $whereValues;

        //prejdenie vŠetkých vybraných mesiacov
        $monthNumbers = $monthNumbers === 'all' ? DateTimeUtilities::$month : explode('-', $monthNumbers);
        $whereDateKeysTotal = ' AND (';
        foreach ($monthNumbers as $key => $monthNumber)
        {
            $date = new DateTime('2023-' . $monthNumber . '-1');
            $startDate = $date->format(DateTimeUtilities::DB_DATE_FORMAT);
            $endDate = DateTimeUtilities::lastDayOfMonth($date)->format(DateTimeUtilities::DB_DATE_FORMAT);

            $whereDateKeys = ' AND ' . SelectDateTable::SELECT_START_DATE . ' = ? AND ' . SelectDateTable::SELECT_END_DATE . ' = ? ';
            $whereQuery = ' WHERE ' . $whereOtherKeys . $whereDateKeys;

            // ak nieje totl nacitavam aj jednotlive produkty
            $monthData[$key]['allData'] = $total === 'false' ? Db::queryAllRows($selectQuery . $fromQuery . $joinQuery . $whereQuery . $groupBy . $orderQuery, array_merge($whereValues,[$startDate,$endDate])) : [];

            // pridanie datumu k total samostatnym total datam
            $dateForTotal = $total === 'true' ? [SelectDateTable::SELECT_START_DATE => $startDate, SelectDateTable::SELECT_END_DATE => $endDate] : [];

            $monthData[$key]['totalData'] = $dateForTotal + Db::queryOneRow($selectQueryTotals . $fromQuery . $joinQuery . $whereQuery, array_merge($whereValues,[$startDate,$endDate]));

            // Query pre  total
            $whereDateKeysTotal .= SelectDateTable::SELECT_START_DATE . ' = ? AND ' . SelectDateTable::SELECT_END_DATE . ' = ? ';

            if ($key !== array_key_last($monthNumbers))
                $whereDateKeysTotal .= ' OR ';

            $whereValuesTotal = array_merge($whereValuesTotal,[$startDate,$endDate]);
        }
        $whereDateKeysTotal .= ')';

        $whereQueryTotal = ' WHERE ' . $whereOtherKeys . $whereDateKeysTotal;
        $allTotalData = Db::queryOneRow($selectQueryTotals . $fromQuery . $joinQuery . $whereQueryTotal , $whereValuesTotal);

        return ['tableHeader'=> array_keys(self::DICTIONARY), 'monthData' => $monthData, 'allTotalData' => $allTotalData];
    }


}