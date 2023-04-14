<?php

namespace AmazonAdvertisingApi\Table;

use AmazonAdvertisingApi\AmazonAdvertisingApi;
use AmazonAdvertisingApi\ClientV3;
use App\AccountModul\Model\UserTable;
use Micho\Db;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\StringUtilities;
use Exception;

/**
$a = ((json_decode($this->connection->listPortfolios()[ClientV3::RESPONSE],true)[0]));
echo "const AMAZON_ADS_" . strtoupper(StringUtilities::camelOnUnderline(array_keys($a)[0])) . " = 'amazon_ads_" . StringUtilities::camelOnUnderline(array_keys($a)[0]) . "';";
echo "<br>";
echo "const USER_ID = UserManager::USER_ID;";
echo "<br>";
foreach ($a as $key => $item)
{
echo "const " . strtoupper(StringUtilities::camelOnUnderline($key)) . " = '" . StringUtilities::camelOnUnderline($key) . "';";
echo "<br>";
if(is_array($item))
{
foreach ($item as $k => $it)
{
echo "const " . strtoupper(StringUtilities::camelOnUnderline($k)) . " = '" . StringUtilities::camelOnUnderline($k) . "';";
echo "<br>";
}
}
}
echo"<br>";
foreach ($a as $key => $item)
{
echo "const RES_" . strtoupper(StringUtilities::camelOnUnderline($key)) . " = '" . $key . "';";
echo "<br>";
if(is_array($item))
{
foreach ($item as $k => $it)
{
echo "const RES_" . strtoupper(StringUtilities::camelOnUnderline($k)) . " = '" . $k . "';";
echo "<br>";
}
}
}
echo"<br>";

$keys = "private \$keys = [self::AMAZON_ADS_" . strtoupper(StringUtilities::camelOnUnderline(array_keys($a)[0])) . ",self::USER_ID,";

foreach ($a as $key => $item)
{
$keys .= "self::" . strtoupper(StringUtilities::camelOnUnderline($key)) . ",";
if(is_array($item))
{
foreach ($item as $k => $it)
{
$keys .= "self::" . strtoupper(StringUtilities::camelOnUnderline($k)) . ",";                        echo "<br>";
}
}
}
$keys .= "];";
echo $keys;echo"<br>";echo"<br>";

echo "private \$amazonAds" . StringUtilities::firstBig(array_keys($a)[0]) . " = null;";
echo "<br>";
echo "private \$userId = null;";
echo "<br>";
foreach ($a as $key => $item)
{
echo "private \$" . $key . " = null;";
echo "<br>";
if(is_array($item))
{
foreach ($item as $k => $it)
{
echo "private \$" . $k . " = null;";
echo "<br>";
}
}
}
echo"<br>";

 *
 */
/**
 * Trieda pre tabuľku amazon_ads_portfolio
 */
class AmazonAdsPortfolioTable extends Table
{
    /**
     * Názov Tabuľky
     */
    const AMAZON_ADS_PORTFOLIO_TABLE = 'amazon_ads_portfolio';

    /**
     * Konštanty Databázy
     */
    const AMAZON_ADS_PORTFOLIO_ID = 'amazon_ads_portfolio_id';
    const PORTFOLIO_ID = 'portfolio_id';
    const NAME = 'name';
    const IN_BUDGET = 'in_budget';
    const STATE = 'state';

    /**
     * @var array Kľuče databázi
     */
    protected $keys = [self::AMAZON_ADS_PORTFOLIO_ID,self::PORTFOLIO_ID,self::USER_ID,self::PROFILE_ID,self::NAME,self::IN_BUDGET,self::STATE];

    /**
     * @var null Atributy
     */
    protected $amazonAdsPortfolioId = null;
    protected $portfolioId = null;
    protected $name = null;
    protected $inBudget = null;
    protected $state = null;

    /**
     * @var Data pre abstraktnú triedu
     */
    protected $table = self::AMAZON_ADS_PORTFOLIO_TABLE;
    protected $id = self::AMAZON_ADS_PORTFOLIO_ID;
    protected $whereId = self::PORTFOLIO_ID;

    protected $getPairKeyKey = self::NAME;
    protected $getPairKeyValue = self::PORTFOLIO_ID;

    protected $getKeys = [self::PORTFOLIO_ID, self::NAME];





}