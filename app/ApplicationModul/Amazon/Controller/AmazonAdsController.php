<?php

namespace App\ApplicationModul\Amazon\Controller;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Report\ConstRaw;
use AmazonAdvertisingApi\Report\DataRaw;
use AmazonAdvertisingApi\Report\ReportDictionary;
use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsBidRecommendationsV2Table;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordRecommendationsTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsSpTargetingTable;
use AmazonAdvertisingApi\Table\AmazonAdsThemeBasedBidRecommendationTable;
use AmazonAdvertisingApi\Table\SelectDateTable;
use AmazonAdvertisingApi\Table\TimeUnitTable;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\Amazon\Model\AmazonManager;
use App\ApplicationModul\Amazon\Model\RecomendationsBidsManager;
use App\BaseModul\System\Controller\Controller;
use App\BaseModul\System\Controller\RouterController;
use Micho\Db;
use Micho\Exception\SettingException;
use Micho\Exception\ValidationException;
use Micho\Form\Form;
use Micho\Form\Input;
use Micho\Utilities\DateTimeUtilities;
use DateTime;
use Exception;
use Micho\Utilities\StringUtilities;
use PDOException;
use ErrorException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Theme;

/**
 * Trieda spracujúca Amazon Ads
 */
class AmazonAdsController extends Controller
{
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
     * @return void
     * @Action
     */
    public function statistics($profileId = null, $portfolioId = null, $campaignId = null, $adGroupId = null, $report = null, $keyword = null, $searchTerm = null)
    {
        $this->amazonManager->basicTemplateSetings($profileId);

        //$this->connection->keywordRecommendations('113407822144441','188722324147875', 'EXACT','filzgleiter schrauben')->prepareData();

        $this->portfolioId = $portfolioId;
        $this->campaignId = $campaignId;
        $this->adGroupId = $adGroupId;
        $this->report = $report;

        $this->createForms();

        if(!empty($this->report))
        {
            $this->reportTable = new ('AmazonAdvertisingApi\\Table\\AmazonAds' . StringUtilities::firstBig($this->report) . 'Table')();

            $selectCol = $this->createChecksFrom();

            $where =  $this->reportTable->createWhere($this->userId,$this->connection->profileId,$this->portfolioId,$this->campaignId, $this->adGroupId);

            if(!empty($keyword) && $keyword !== 0)
            {
                $this->data['back'] = 'amazon-ads/statistics/' . $profileId . '/' . $portfolioId. '/' . $campaignId . '/' . $adGroupId . '/' . $report;
                $where = array_merge($where, [$this->reportTable::TABLE . '.' .  $this->reportTable::KEYWORD => rawurldecode($keyword)]);
            }
            elseif(!empty($searchTerm))
            {
                $this->data['back'] = 'amazon-ads/statistics/' . $profileId . '/' . $portfolioId. '/' . $campaignId . '/' . $adGroupId . '/' . $report;
                $where = array_merge($where, [$this->reportTable::TABLE . '.' .  $this->reportTable::SEARCH_TERM => rawurldecode($searchTerm)]);
            }
            else
                $this->data['keywordUrl'] = self::$currentUrl;

            $reports = array();
            try
            {
                // ak Mam tabulku targeting tak k nej pridavam aj suggestion bid
                if ($this->reportTable instanceof AmazonAdsSpTargetingTable && empty($keyword))
                {
                    $recomendationsBidsManager = new RecomendationsBidsManager($this->connection);
                    $recomendationsBidsManager->downloadBids($where);
                }
                $reports =  $this->reportTable->getReports($where, array_keys($selectCol));
            }
            catch (Exception $error)
            {
                $this->addMessage($error->getMessage(),self::MSG_ERROR);
            }

            $this->data['checkBoxKeys'] =  $this->reportTable->checkBoxKeys;
            $this->data['selectCol'] =  array_merge([SelectDateTable::SELECT_START_DATE,SelectDateTable::SELECT_END_DATE],array_keys($selectCol));
            $this->data['reportTable'] =  $this->reportTable;
            $this->data['reports'] = $reports;
        }
        $this->data['method'] = 'statistics';
        $this->view = 'statistics';
    }

    /**
     ** Vytvorí a spracuje formulár pre check Boxi
     * @return array Vybraté check boxi
     * @throws ErrorException
     */
    private function createChecksFrom() : array
    {
        $formCheckReport = new Form('checkReport');
        foreach ($this->reportTable->checkBoxKeys as $nameDb)
        {
            $formCheckReport->addCheckBox(ReportDictionary::DICTIONARY[$nameDb]['title'],
                $nameDb,'',false,ReportDictionary::DICTIONARY[$nameDb]['description'],'checkReport','','',false);
        }
        $formCheckReport->addSubmit('Select columns','check-report-button','checkReport','btn btn-sm btn-outline-success mt-auto mb-1 small py-0');
        $selectCol =  $this->reportTable::CHECKED_COL;

        if($formCheckReport->dataProcesing())
        {
            try
            {
                $formData = $formCheckReport->getData();
                $formCheckReport->validate($formData);
                $selectCol = $formData;
            }
            catch (ValidationException $error)
            {
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }
        $formCheckReport->setValuesControls($selectCol);
        $this->data['formCheckReport'] = $formCheckReport->createForm();

        return $selectCol;
    }

    /**
     ** Zostavi Formuláre pre selecty
     * @return void
     * @throws ErrorException
     */
    private function createForms() : void
    {
        if(!empty($this->connection->profileId))
        {
            $amazonAdsPortfolioTable = new AmazonAdsPortfolioTable();
            $portfolios = $amazonAdsPortfolioTable->getPair([AmazonAdsPortfolioTable::USER_ID => $this->userId, AmazonAdsPortfolioTable::PROFILE_ID => $this->connection->profileId]);
            $portfolios = $portfolios ?  array_merge([''=>''],$portfolios) : [];
            $formPortfolio = new Form('portfolio');
            $formPortfolio->addSelect('Portfolios',AmazonAdsPortfolioTable::PORTFOLIO_ID,$portfolios, $this->portfolioId,false, 'portfolio','','font-weight-bolder mr-2');
            $formPortfolio->addSubmit('Portfolio','portfolio-button','portfolio','sr-only');
            if($formPortfolio->dataProcesing())
            {
                try
                {
                    $formData = $formPortfolio->getData();
                    $formPortfolio->validate($formData);

                    $this->portfolioId = $formData[AmazonAdsPortfolioTable::PORTFOLIO_ID];

                    $this->redirect('amazon-ads/statistics/' . $this->connection->profileId . '/' . $this->portfolioId);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
            }
            $this->data['formPortfolio'] = $formPortfolio->createForm();
        }

        if(!empty($this->portfolioId))
        {
            $amazonAdsCampaignTable = new AmazonAdsCampaignTable();
            $campaigns = $amazonAdsCampaignTable->getPair([AmazonAdsCampaignTable::USER_ID => $this->userId, AmazonAdsCampaignTable::PROFILE_ID => $this->connection->profileId, AmazonAdsCampaignTable::PORTFOLIO_ID => $this->portfolioId]);
            $campaigns = $campaigns ?  array_merge([''=>''],$campaigns) : [];
            $formCampaign = new Form('campaign');
            $formCampaign->addSelect('Campaigns',AmazonAdsCampaignTable::CAMPAIGN_ID,$campaigns, $this->campaignId,false, 'campaign','','font-weight-bolder mr-2');
            $formCampaign->addSubmit('Campaign','campaign-button','campaign','sr-only');
            if($formCampaign->dataProcesing())
            {
                try
                {
                    $formData = $formCampaign->getData();
                    $formCampaign->validate($formData);

                    $this->campaignId = $formData[AmazonAdsCampaignTable::CAMPAIGN_ID];

                    $this->redirect('amazon-ads/statistics/' . $this->connection->profileId . '/' . $this->portfolioId . '/' . $this->campaignId);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
            }
            $this->data['formCampaign'] = $formCampaign->createForm();
        }

        if(!empty($this->campaignId))
        {
            $amazonAdsAdGroupTable = new AmazonAdsAdGroupTable();
            $adGroups = $amazonAdsAdGroupTable->getPair([AmazonAdsAdGroupTable::USER_ID => $this->userId, AmazonAdsAdGroupTable::PROFILE_ID => $this->connection->profileId, AmazonAdsAdGroupTable::CAMPAIGN_ID => $this->campaignId]);
            $adGroups = $adGroups ? array_merge([''=>''],$adGroups) : [];
            $formAdGroup = new Form('adGroup');
            $formAdGroup->addSelect('AdGroups',AmazonAdsAdGroupTable::AD_GROUP_ID,$adGroups, $this->adGroupId,false, 'adGroup','','font-weight-bolder mr-2');
            $formAdGroup->addSubmit('AdGroup','adGroup-button','adGroup','sr-only');
            if($formAdGroup->dataProcesing())
            {
                try
                {
                    $formData = $formAdGroup->getData();
                    $formAdGroup->validate($formData);

                    $this->adGroupId = $formData[AmazonAdsAdGroupTable::AD_GROUP_ID];

                    $this->redirect('amazon-ads/statistics/' . $this->connection->profileId . '/' . $this->portfolioId . '/' . $this->campaignId . '/' . $this->adGroupId);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
            }
            $this->data['formAdGroup'] = $formAdGroup->createForm();
        }

        if(!empty($this->adGroupId))
        {
            $reports[''] = '';
            $reports = array_merge($reports, ConstRaw::REPORT_TYPE_ID);

            $formReport = new Form('report');
            $formReport->addSelect('Report',DataRaw::REPORT_TYPE_ID,$reports, $this->report,false, 'report','','font-weight-bolder mr-2');
            $formReport->addSubmit('Report','report-button','report','sr-only');
            if($formReport->dataProcesing())
            {
                try
                {
                    $formData = $formReport->getData();
                    $formReport->validate($formData);

                    $this->report = $formData[DataRaw::REPORT_TYPE_ID];

                    $this->redirect('amazon-ads/statistics/' . $this->connection->profileId . '/' . $this->portfolioId . '/' . $this->campaignId  . '/' . $this->adGroupId . '/' . $this->report);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
            }
            $this->data['formReport'] = $formReport->createForm();
        }
    }

    private function chengujTabulku($bidId, $bidTable)
    {
        $targetId = AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID;
        $targetTable = AmazonAdsSpTargetingTable::TABLE;
        $ids = Db::queryAllRows('SELECT ' . $bidId . '
                        FROM ' . $bidTable);
        foreach ($ids as $id)
        {
            $id = $id[$bidId];
            $idTargs = Db::queryAllRows('SELECT ' . $targetId . '
                        FROM ' . $targetTable . ' 
                        WHERE ' . $bidId . ' = ? ', [$id]);
            foreach ($idTargs as $idTarg)
            {
                $idTarg = $idTarg[$targetId];
                Db::query('UPDATE ' . $bidTable . '
                        SET ' . $targetId . ' = ? 
                        WHERE ' . $bidId . ' = ? ', [$idTarg,$id]);
            }
        }
    }

    /**
     * @return void
     * @Action
     */
    public function download($profileId = null, $reportId = null)
    {
/*
        //kvoli transformacii DB
        $themeBidId = AmazonAdsThemeBasedBidRecommendationTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_ID;
        $themeBidTable = AmazonAdsThemeBasedBidRecommendationTable::AMAZON_ADS_THEME_BASED_BID_RECOMMENDATION_TABLE;

        $keywordBidId = AmazonAdsKeywordRecommendationsTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_ID;
        $keywordBidTable = AmazonAdsKeywordRecommendationsTable::AMAZON_ADS_KEYWORD_RECOMMENDATIONS_TABLE;

        $v2BidId = AmazonAdsBidRecommendationsV2Table::AMAZON_ADS_RECOMMENDATIONS_V2_ID;
        $v2BidTable = AmazonAdsBidRecommendationsV2Table::AMAZON_ADS_RECOMMENDATIONS_V2_TABLE;



        $this->chengujTabulku($themeBidId, $themeBidTable);
        $this->chengujTabulku($keywordBidId, $keywordBidTable);
        $this->chengujTabulku($v2BidId, $v2BidTable);


        $targetId = AmazonAdsSpTargetingTable::AMAZON_ADS_SP_TARGETING_ID;
        $targetTable = AmazonAdsSpTargetingTable::TABLE;
        $dataTheme = Db::queryAllRows('SELECT ' . $targetId . ' FROM ' . $themeBidTable);
        $dataKeyword = Db::queryAllRows('SELECT ' . $targetId . ' FROM ' . $keywordBidTable);
        $dataV2 = Db::queryAllRows('SELECT ' . $targetId . ' FROM ' . $v2BidTable);

        $datasky = array_merge($dataTheme,$dataKeyword,$dataV2);

        foreach ($datasky as $datask)
        {
            $datask = $datask[$targetId];
            Db::query('UPDATE ' . $targetTable . '
                        SET ' . AmazonAdsSpTargetingTable::BID . ' = 1 
                        WHERE ' . $targetId . ' = ? ', [$datask]);
        }
*/
        //AmazonAdsController::view($datasky);die;


        $this->amazonManager->basicTemplateSetings($profileId);

        // nastavenia pre tlacidla
        $disabledForm = false;
        $disabledCheck = true;
        $disabledDownload = true;

        $timeUnitTable = new TimeUnitTable();
        $timeUnitPairs = $timeUnitTable->getPair();
        $timeUnit = current($timeUnitPairs);

        if (!empty($reportId)) // je zadaný report Id tak
        {
            $disabledForm = true;
            $disabledCheck = false;
            try
            {
                $this->connection->reportId = $reportId;
                $checkData = $this->connection->report()->check();
            }
            catch (Exception $error)
            {
                $this->addMessage($error->getMessage(),self::MSG_ERROR);
                $this->redirect('amazon-ads/download/' . $this->connection->profileId);
            }

            if ($checkData[Connection::STATUS] === 'COMPLETED')
            {
                // nastavenia pre tlacidla
                $disabledCheck = true;
                $disabledDownload = false;

                $this->addMessage('The data is ready for download.', self::MSG_SUCCESS);
            }
            elseif ($checkData[Connection::STATUS] === 'PENDING')
                $this->addMessage('Data is being prepared for download.', self::MSG_INFO);

            $startDate =   $checkData[Connection::START_DATE];
            $endDate =  $checkData[Connection::END_DATE];
            $timeUnit = $checkData[DataRaw::TIME_UNIT];
            $reportTypeId = $checkData[DataRaw::REPORT_TYPE_ID];
        }

        $date = new DateTime();
        $endDate = isset($_SESSION['selectDate']) ? $_SESSION['selectDate']['endDate'] : $date->format(DateTimeUtilities::DB_DATE_FORMAT);
        $startDate = isset($_SESSION['selectDate']) ? $_SESSION['selectDate']['startDate'] : $date->modify('- 5 day')->format(DateTimeUtilities::DB_DATE_FORMAT);
        $reportTypeId = current(ConstRaw::REPORT_TYPE_ID);


        $formSelect = new Form('select');
        $formSelect->addInput('Start date', DataRaw::START_DATE,Input::TYPE_DATE,$startDate,'select', '','', true, false, $disabledForm);
        $formSelect->addInput('End date', DataRaw::END_DATE,Input::TYPE_DATE,$endDate,'select','','',true,false,$disabledForm);
        $formSelect->addSelect('Select Report Type Id',DataRaw::REPORT_TYPE_ID,ConstRaw::REPORT_TYPE_ID, $reportTypeId,false, 'select','','',true,$disabledForm);
        $formSelect->addSelect('Select Time Unit',DataRaw::TIME_UNIT,$timeUnitPairs, $timeUnit,false, 'select','','',true,$disabledForm);
        $formSelect->addSubmit('Send Request','send-request','select', 'btn btn-success',$disabledForm);

        if($formSelect->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $formSelect->getData();
                $formSelect->validate($formData);

                $_SESSION['selectDate'] = ['startDate' => $formData['startDate'], 'endDate' => $formData['endDate']]; // uloženie dát pre zobrazovanie toho siteho dátumu na viacerých miestach

                $this->connection->report()->request($formData);

                $this->addMessage('The request to generate data has been sent.', self::MSG_SUCCESS);
                $this->redirect('amazon-ads/download/' . $this->connection->profileId . '/' . $this->connection->reportId);
            }
            catch (ValidationException $error)
            {
                $formSelect->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
            catch (Exception $error)
            {
                $this->addMessage($error->getMessage(),self::MSG_ERROR);
                $this->redirect('amazon-ads/download/' . $profileId);
            }
        }

        RouterController::$subPageControllerArray['title'] = 'Amazon Ads Download Data'; // pridanie doplnujúceho description k hlavnému
        RouterController::$subPageControllerArray['description'] = 'Download and save Targeting and Search terms to Database'; // pridanie doplnujúceho description k hlavnému

        $this->data['dateSpTargSearch'] = $this->amazonManager->getDateSpTargSearch($this->userId, $this->connection->profileId);

        //AmazonAdsController::view($this->data['dateSpTargSearch']);

        $this->data['reportId'] = $reportId;
        $this->data['profileId'] = $this->connection->profileId;
        $this->data['currentUrl'] = self::$currentUrl;
        $this->data['disabledCheck'] = $disabledCheck;
        $this->data['disabledDownload'] = $disabledDownload;

        $this->data['formSelect'] = $formSelect->createForm();
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
    public function downloadReport(string $profileId, string $reportId)
    {
        $this->connection->profileId = $profileId; // nastavenia profilu spojenia
        $this->connection->reportId = $reportId; // nastavenie Id reportu
        try
        {
            $this->connection->report()->save();

            $this->addMessage('Reports data have been saved in the Database',self::MSG_SUCCESS);
            //$this->addMessage('Bids have been successfully downloaded and saved',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            //echo  $error->getMessage();die;
            //$this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
            $this->addMessage('Error saving to database.: probably need to restore all the basic settings',self::MSG_ERROR);
            $this->redirect('app-management/settings');
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('amazon-ads/download/' . $profileId);
    }

    /**
     * @Action
     */
    public function deleteReport($reportTypeId, $selectDateId, $userId, $profileId)
    {
        try
        {
            $this->connection->report()->deleteReport($reportTypeId, $selectDateId, $userId, $profileId);
            $this->addMessage('Report has been removed',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            echo $error->getMessage();die;
            $this->addMessage('Error Deleting',self::MSG_ERROR);
        }
        $this->redirect('amazon-ads/download/' . $profileId);
    }

    static function view($data)
    {
        $i = 1;
        foreach ($data as $key => $d)
        {
            echo $i . ' : ';
            echo $key . ' -> ';
            print_r($d);echo "<hr>";
            $i++;
        }
    }
}

/*
 * Autor: MiCHo
 */