<?php
namespace App\ApplicationModul\AppManagement\Model;

use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\Table;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesManager;
use Micho\Db;
use Micho\Exception\UserException;
use Micho\Files\FileXlsx;

/**
 * Trieda pre tabuľku amazon_product_data
 */
class AmazonProductDataTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_PRODUCT_DATA_TABLE = 'amazon_product_data';

    /**
     * Konštanty Databázy
     */
    const AMAZON_PRODUCT_DATA_ID = 'amazon_product_data_id';
    const ADDITON_DATE = 'addition_date';
    const PROFILE_ID = AmazonAdsProfileTable::PROFILE_ID;
    const SKU = 'sku';
    const FBA_FEES = 'fba_fees';
    const LANDING_COST = 'landing_cost';
    const BREAK_EVEN = 'break_even';

    /**
     * kluče
     */
    const KEYS = [
        self::AMAZON_PRODUCT_DATA_ID,self::ADDITON_DATE, self::PROFILE_ID,
        'SKU' => self::SKU, 'FBA Fees' => self::FBA_FEES, 'Landing Cost' => self::LANDING_COST, 'Break-Even %' => self::BREAK_EVEN
    ];

    //vŠetky kluče
    public $keys = self::KEYS;

    /**
     * @var null Atributy
     */
    protected $amazonProductDataId = null;
    protected $additionDate = null;
    protected $profileId = null;
    protected $sku = null;
    protected $fbaFees = null;
    protected $landingCost = null;
    protected $breakEven = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_PRODUCT_DATA_TABLE;
    protected $id = self::AMAZON_PRODUCT_DATA_ID;
    protected $whereId = self::AMAZON_PRODUCT_DATA_ID;

    protected $getPairKeyKey = null;
    protected $getPairKeyValue = null;

    /**
     ** spracuje uloženie dát do DB
     * @param string $pathToFile Cesta k uloženému súboru
     * @param string $userId Id uzivateľa
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function prepareAndSaveProductData(string $pathToFile, string $userId)
    {
        $xlsx = new FileXlsx();
        $xlsxData = $xlsx->getTitleOtherRow($pathToFile);

        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        $amazonProductDataTable = new AmazonProductDataTable();

        $rowData = array();
        foreach ($xlsxData['otherRow'] as $i => $row) // priradenie nazvu k hodnote
        {
            $row = array_combine($xlsxData['titleRow'], $row);
            $rowData[$i] = $amazonProductDataTable->getArrayData(); // data ktore potrebujem získať
            array_shift($rowData[$i]); // odstranenie id
            array_shift($rowData[$i]); // odstranenie addition_date
            array_shift($rowData[$i]); // odstranenie profile_id

            $rowData[$i][AmazonAdsProfileTable::PROFILE_ID] = $amazonAdsProfileTable->profileIdFromMarketplace($row[AmazonMonthlySalesManager::MARKETPLACE], $userId);

            //preklad názvu klučov
            foreach (array_slice(AmazonProductDataTable::KEYS, 3) as $key) // premenuje nazvy
            {
                if(isset($row[(array_flip(AmazonProductDataTable::KEYS))[$key]]))
                    $rowData[$i][$key] = $row[(array_flip(AmazonProductDataTable::KEYS))[$key]];
                else
                    throw new UserException('The imported table does not have the necessary data');
            }
        }
        $amazonProductDataTable->save($rowData,false);
    }

    /**
     ** Načita data záznamu ...ale iba posledné pridané cize ak ich je viac tak iba to co bolo naposledy pridane
     * @param string $profileId Id profilu
     * @param string $sku identifikacnny retazeec produktu cez SKU
     * @return array|false konkretné záznam produktu
     */
    public function selectProductData(string $profileId, string $sku) : array|false
    {
        $keys = [self::AMAZON_PRODUCT_DATA_ID, self::FBA_FEES, self::LANDING_COST,self::BREAK_EVEN];

        $productDataId = Db::queryOneRow('SELECT ' . implode(', ', $keys) . '  
                            FROM ' . self::AMAZON_PRODUCT_DATA_TABLE . ' 
                            WHERE ' . self::PROFILE_ID . ' = ? AND ' . self::SKU . ' = ? ORDER BY ' . self::ADDITON_DATE . ' DESC LIMIT 1', [$profileId,$sku]);

        return $productDataId ? $productDataId : false;
    }


    /**
     ** Ziska všetky hodnoty product data pre daného uživateľa
     * @param string $userId
     * @return array|false|null
     */
    public function getAllProductDataUserId(string $userId) : ?array
    {
        $keys = array_merge(self::KEYS,[AmazonAdsProfileTable::COUNTRY_CODE]);
        $data = Db::queryAllRows('SELECT ' . implode(', ', $keys) . '
                    FROM ' . self::AMAZON_PRODUCT_DATA_TABLE . '
		            JOIN  ' . AmazonAdsProfileTable::AMAZON_ADS_PROFILE_TABLE . ' USING( ' . self::PROFILE_ID . ')
		            WHERE  ' . AmazonAdsProfileTable::USER_ID . ' = ?
		            ORDER BY  ' . self::AMAZON_PRODUCT_DATA_ID , [$userId]);

        return $data ? ['tableHeader' => [self::SKU,AmazonAdsProfileTable::COUNTRY_CODE,self::FBA_FEES,self::LANDING_COST,self::BREAK_EVEN], 'data' => $data] : false;
    }

}