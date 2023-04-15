<?php

namespace App\ApplicationModul\Amazon\Controller;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\DataCollection\Profile;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Model\AmazonManager;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesManager;
use App\ApplicationModul\Amazon\Model\AmazonMonthlySalesTable;
use App\BaseModul\System\Controller\Controller;
use App\BaseModul\System\Controller\RouterController;
use Micho\Exception\SettingException;
use Micho\Exception\UserException;
use Micho\Exception\ValidationException;
use Micho\Form\File;
use Micho\Form\Form;
use Exception;
use Micho\Utilities\DateTimeUtilities;
use PDOException;
use DateTime;

/**
 * Trieda spracujúca Amazon Ads
 */
class AmazonMonthlySalesController extends Controller
{
    const NAME = 'App\ApplicationModul\Amazon\Controller\AmazonMonthlySalesController';
    /**
     * @var Connection
     */
    public Connection $connection;
    /**
     * @var AmazonManager
     */
    private AmazonManager $amazonManager;

    /**
     * @var string $userId Id uživateľa
     */
    public $userId;

    private $portfolioId = null;
    private $campaignId = null;
    private $adGroupId = null;
    private $report = null;
    private $reportTable = null;

    public function __construct($vytvorilApi = false)
    {
        $this->userId = UserTable::$user[UserTable::USER_ID];
        $this->connection = new Connection($this->userId);

        $this->userVerification();

        parent::__construct($vytvorilApi);

        $this->amazonManager = new AmazonManager($this);
    }

    /**
     ** Spracuje Upload excelu a nasledne spracuje odoslanie a overenie pripravy údajov
     * @param string|null $reportsIdUrl url adresy
     * @return void
     * @Action
     */
    public function download(string|null $reportsIdUrl = null)
    {
        // nastavenia pre tlacidla
        if (!empty($reportsIdUrl)) // je zadaný report Id tak
        {
            $disabledCheck = false;
            $disabledDownload = true;
            try
            {
                $completed = $this->connection->report()->checkAllReports($reportsIdUrl);
            }
            catch (Exception $error)
            {
                $this->addMessage($error->getMessage(),self::MSG_ERROR);
                $this->redirect('amazon-monthly-sales/download');
            }
            if ($completed)
            {
                // nastavenia pre tlacidla
                $disabledCheck = true;
                $disabledDownload = false;

                $this->addMessage('The data is ready for download.', self::MSG_SUCCESS);
            }
            else
                $this->addMessage('Data is being prepared for download.', self::MSG_INFO);

            $this->data['disabledCheck'] = $disabledCheck;
            $this->data['disabledDownload'] = $disabledDownload;
            $this->data['reportsIdUrl'] = $reportsIdUrl;
            $this->data['currentUrl'] = self::$currentUrl;
        }
        else
        {
            $form = new Form('upload');
            $form->addFile('Raw Data', 'month-data', 'upload', '', '', $required = true, false, File::XLXS);
            $form->addSubmit('Upload','upload-button','upload','btn btn-success');
            if($form->dataProcesing())
            {
                try
                {
                    $formData = $form->getData('month-data');

                    $form->validate($formData);

                    $amazonMonthlySalesManager = new AmazonMonthlySalesManager();
                    $xlsxData = $amazonMonthlySalesManager->prepareDataXlsx($formData['month-data'][File::TMP_NAME]);

                    $_SESSION['xlsxData'] = $xlsxData;

                    //toto bude autoamticky brrat z  tabulky  kedŽe to uz predtym da na DB fromat ...
                    $startDate = $xlsxData[0][AmazonMonthlySalesManager::START_DATE];
                    $endDate = $xlsxData[0][AmazonMonthlySalesManager::END_DATE];

                    $reportsIdUrl = $this->connection->report()->requestAllProfiles($startDate, $endDate);

                    $this->addMessage('The request to generate data for all profiles has been sent.', self::MSG_SUCCESS);
                    $this->redirect('amazon-monthly-sales/download' . $reportsIdUrl);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
                catch (SettingException $error)
                {
                    $this->addMessage($error->getMessage(),self::MSG_ERROR);
                    $this->redirect('app-management/settings');
                }
                catch (UserException $error)
                {
                    $this->addMessage($error->getMessage(),self::MSG_ERROR);
                    $this->redirect();
                }
                catch (Exception $error)
                {
                    $this->addMessage($error->getMessage(),self::MSG_ERROR);
                }
            }
            $this->data['form'] = $form->createForm();
        }

        RouterController::$subPageControllerArray['title'] = 'Amazon monthly sales Download Data'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Upload excel Data, Download and save Advertised to Database and Create monthly sales statistics'; // pridanie doplnujúceho description k hlavnému

        $this->data['method'] = 'download';
        $this->view = 'download';
    }

    /**
     ** Stiahne a uloži Report/Dáta Amazon Ads do DB
     * @param string $profileId Id profilu
     * @param string $reportId Id reportu na stiahnutie
     * @return void
     * @Action
     */
    public function downloadReports($reportsIdUrl)
    {
        try
        {
            $this->connection->report()->saveAllReports($reportsIdUrl);
            $this->addMessage('All reports data have been saved in the Database',self::MSG_SUCCESS);
            // presmeruje na vypracovanie tých mesačných predajov
            $this->redirect('amazon-monthly-sales/create-monthly-sales');
        }
        catch (SettingException $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
            $this->redirect('app-management/settings');
        }
        catch (PDOException $error)
        {
            //$this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
            $this->addMessage('Error saving to database.: probably need to restore all the basic settings',self::MSG_ERROR);
            $this->redirect('app-management/settings');
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }

        $this->redirect('amazon-monthly-sales/download');
    }

    /**
     ** Vytvorý mesačne Štatistky predaja
     * @return void
     * @Action
     */
    public function createMonthlySales()
    {
        $xlsxData = isset($_SESSION['xlsxData']) ? $_SESSION['xlsxData'] : false;
        if($xlsxData)
        {
            $amazonMonthlySalesManager = new AmazonMonthlySalesManager();
            try
            {
                $amazonMonthlySalesManager->prepareAndSave($xlsxData, $this->connection->amazonAdsConfigTable->getUserId());
                $this->addMessage('Monthly sales data have been saved in the Database',self::MSG_SUCCESS);
            }
            catch (SettingException $error)
            {
                $this->addMessage($error->getMessage(),self::MSG_ERROR);
                $this->redirect('app-management/settings');
            }
            catch (PDOException $error)
            {
                $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
            }
            unset($_SESSION['xlsxData']);
        }
        $this->redirect('amazon-monthly-sales/download');
    }

    /**
     * @param string $profileId Id profilu
     * @param string $monthNumbers Cislo mesiaca v roku
     * @return void
     * @throws \ErrorException
     * @Action
     */
    public function monthlySales($profileId = null, $year = null, $monthNumbers = 'all', $total = 'false', $product = null)
    {
        $this->amazonManager->basicTemplateSetings($profileId, true);

        $selectYear = !empty($year) ? $year : DateTimeUtilities::yearNow();

        $url = 'amazon-monthly-sales/monthly-sales/' . $this->connection->profileId . '/';
        //$monthNumber = !empty($monthNumber) ? $monthNumber : $datetime->format('n');

        $amazonMonthlySalesManager = new AmazonMonthlySalesManager();

        $formDate = new Form('dates');
        $formDate->addSelect('Years','year',$amazonMonthlySalesManager->getYears(), $selectYear,false, 'dates','','font-weight-bolder');
        $formDate->addSelect('Month','month',DateTimeUtilities::getMonthFull(), explode('-', $monthNumbers),true, 'dates','mul-select form-control','font-weight-bolder', true,false);
        $formDate->addSubmit('dates','dates-button','dates','sr-only');
        if($formDate->dataProcesing())
        {
            try
            {
                $formData = $formDate->getData();
                $formDate->validate($formData);
                // zakliknutie aj all aj mesiacov
                $months = $formData['month'][0] === 'all' ? 'all' : implode('-', $formData['month']);

                //print_r($months);die;
                $this->redirect($url . $formData['year'] . '/' . $months . '/' . $total . '/' . $product);
            }
            catch (ValidationException $error)
            {
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }
        if(!empty($product) || $total === 'true')
            $this->data['back'] = $url . $selectYear . '/' . $monthNumbers . '/false';
        else
        {
            $this->data['productUrl'] = $url . $selectYear . '/' . $monthNumbers . '/' . $total;
            $this->data['totalUrl'] = $url . $selectYear . '/' . $monthNumbers . '/true';
        }

        $this->data['formMonths'] = $formDate->createForm();

        $amazonMonthlySalesTable = new AmazonMonthlySalesTable();

        $this->data['monthlySalesData'] = $amazonMonthlySalesTable->getMonthlySales($this->connection->profileId, $monthNumbers, $total, $product);
        $this->data['total'] = $total;
        $this->data['combine'] = array_search($profileId, AmazonAdsProfileTable::COMBINE_PROFILE);
        $this->data['method'] = 'monthly-sales';
        $this->view = 'monthly-sales';
    }


    /**
     * @param $profileId
     * @param $year
     * @param $monthNumbers
     * @param $total
     * @param $product
     * @return void
     * @throws \ErrorException
     * @Action
     */
    public function monthlySalesPcs($profileCode = null)
    {
        if (empty($profileCode))
            $this->redirect('amazon-monthly-sales/monthly-sales-pcs/' . AmazonAdsProfileTable::COMBINE_PROFILE[array_key_first(AmazonAdsProfileTable::COMBINE_PROFILE)]);

        $amazonMonthlySalesTable = new AmazonMonthlySalesTable();

        $monthlyPcsSales = $amazonMonthlySalesTable->getMonthlyPcsSales($profileCode, AmazonMonthlySalesTable::SKU)['all'];
        $this->data['monthlySalesPcsAllProduct'] = $amazonMonthlySalesTable->easyListingAllProduct($monthlyPcsSales);

        if($profileCode == 'eu') // iba pre eu
        {
            $monthlyPcsSales = $amazonMonthlySalesTable->getMonthlyPcsSales($profileCode, AmazonMonthlySalesTable::PROFILE_ID);
            $this->data['monthlySalesPcsIndividualProduct'] = $amazonMonthlySalesTable->easyListingIndividualProduct($monthlyPcsSales);
        }

        /*
        //$this->amazonManager->basicTemplateSetings($profileId, true);

        $selectYear = !empty($year) ? $year : DateTimeUtilities::yearNow();

        $url = 'amazon-monthly-sales/monthly-sales/' . $this->connection->profileId . '/';
        //$monthNumber = !empty($monthNumber) ? $monthNumber : $datetime->format('n');

        $amazonMonthlySalesManager = new AmazonMonthlySalesManager();

        $formDate = new Form('dates');
        $formDate->addSelect('Years','year',$amazonMonthlySalesManager->getYears(), $selectYear,false, 'dates','','font-weight-bolder');
        $formDate->addSelect('Month','month',DateTimeUtilities::getMonthFull(), explode('-', $monthNumbers),true, 'dates','mul-select form-control','font-weight-bolder', true,false);
        $formDate->addSubmit('dates','dates-button','dates','sr-only');
        if($formDate->dataProcesing())
        {
            try
            {
                $formData = $formDate->getData();
                $formDate->validate($formData);
                // zakliknutie aj all aj mesiacov
                $months = $formData['month'][0] === 'all' ? 'all' : implode('-', $formData['month']);

                //print_r($months);die;
                $this->redirect($url . $formData['year'] . '/' . $months . '/' . $total . '/' . $product);
            }
            catch (ValidationException $error)
            {
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }
        if(!empty($product) || $total === 'true')
            $this->data['back'] = $url . $selectYear . '/' . $monthNumbers . '/false';
        else
        {
            $this->data['productUrl'] = $url . $selectYear . '/' . $monthNumbers . '/' . $total;
            $this->data['totalUrl'] = $url . $selectYear . '/' . $monthNumbers . '/true';
        }

        $this->data['formMonths'] = $formDate->createForm();

        $amazonMonthlySalesTable = new AmazonMonthlySalesTable();
        $this->data['monthlySalesData'] = $amazonMonthlySalesTable->getMonthlySales($this->connection->profileId, $monthNumbers, $total, $product);
        $this->data['total'] = $total;
        $this->data['combine'] = array_search($profileId, AmazonAdsProfileTable::COMBINE_PROFILE);

*/

        $this->data['profileCode'] = $profileCode;
        $this->data['menuProfile'] = AmazonAdsProfileTable::COMBINE_PROFILE;
        $this->data['method'] = 'monthly-sales-pcs';
        $this->view = 'monthly-sales-pcs';
    }

    static function view($data)
    {
        $i = 1;
        foreach ($data as $key => $d)
        {
            echo $i . ' : ';
            echo $key . ' -> ';
            print_r($d);echo "<br><br><br>";
            $i++;
        }
    }
}

/*
 * Autor: MiCHo
 */