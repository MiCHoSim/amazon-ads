<?php

namespace App\ApplicationModul\AppManagement\Controller;

use AmazonAdvertisingApi\Table\AmazonAdsAdGroupTable;
use AmazonAdvertisingApi\Table\AmazonAdsCampaignTable;
use AmazonAdvertisingApi\Table\AmazonAdsKeywordTable;
use AmazonAdvertisingApi\Table\AmazonAdsPortfolioTable;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\AmazonAdsTargetTable;
use AmazonAdvertisingApi\Table\Table;
use App\ApplicationModul\AppManagement\Model\AmazonAdsConfigTable;
use App\ApplicationModul\AppManagement\Model\AmazonAdsRegionTable;
use AmazonAdvertisingApi\Connection\Connection;
use App\AccountModul\Model\UserTable;
use App\ApplicationModul\AppManagement\Model\AmazonProductDataTable;
use App\BaseModul\System\Controller\Controller;
use Micho\Exception\UserException;
use Micho\Exception\ValidationException;
use Micho\Form\File;
use Micho\Form\Form;
use Micho\Form\Input;
use Exception;
use PDOException;

/**
 * Trieda spracujuca Základné nastavenie pre AmazonADS
 */
class AppManagementController extends Controller
{
    /**
     * @var string $userId Id uživateľa
     */
    public $userId;

    public function __construct()
    {
        $this->userId = UserTable::$user[UserTable::USER_ID];
        $this->userVerification();
    }

    /**
     ** Spracuje sytem nastavenia pre Amazon Ads
     * @return void
     * @Action
     */
    public function settings($selectUser = null)
    {
        $selectUser = !empty($selectUser) ? $selectUser : $this->userId;
        $this->userVerification($selectUser);

        $userManager =  new UserTable();

        $users = $userManager->loadAllUsersPairs();

        if (UserTable::$user[UserTable::ADMIN])
        {
            $formUsers = new Form('users');
            $formUsers->addSelect('Select user',UserTable::USER_ID,$users,$selectUser,false, 'users','','font-weight-bold');
            $formUsers->addSubmit('Save','users-button','users', 'sr-only');

            if($formUsers->dataProcesing())
            {
                $formData = array();
                try
                {
                    $formData = $formUsers->getData();
                    $formUsers->validate($formData);
                    $this->redirect('app-management/settings/' . $formData['user_id']);
                }
                catch (ValidationException $error)
                {
                    $this->addMessage($error->getMessages(), self::MSG_ERROR);
                }
            }
            $this->data['formUsers'] = $formUsers->createForm();
        }

        $connection = new Connection($selectUser);
        $regions = $connection->amazonAdsRegionTable->getPairs();

        $formSet = new Form('amazon-ads-app-manag');
        $formSet->addInput('AmazonAdsAppId', AmazonAdsConfigTable::AMAZON_ADS_CONFIG_ID, Input::TYPE_HIDDEN,$connection->amazonAdsConfigTable->getAmazonAdsConfigId());
        $formSet->addInput('userId', AmazonAdsConfigTable::USER_ID, Input::TYPE_HIDDEN, $selectUser);
        $formSet->addSelect('Select region',AmazonAdsRegionTable::AMAZON_ADS_REGION_ID,$regions,$connection->amazonAdsConfigTable->getAmazonAdsRegionId(),false, 'amazon-ads-app-manag','','font-weight-bold mx-2');
        $formSet->addInput('Client Id', AmazonAdsConfigTable::CLIENT_ID, Input::TYPE_TEXT,$connection->amazonAdsConfigTable->getClientId(),'amazon-ads-app-manag','','font-weight-bold mx-2',true);//,
            //array('description' => 'Enter the client id.','pattern' => "/amzn1\.application-oa2-client\.[a-z0-9]{32}$/i"));
        $formSet->addInput('Client Secret', 'client_secret', Input::TYPE_TEXT,$connection->amazonAdsConfigTable->getClientSecret(),'amazon-ads-app-manag','','font-weight-bold mx-2',true);//,
            //array('description' => 'Enter the client secret.', 'pattern' => "/[a-z0-9]{64}$/i"));
        $formSet->addSubmit('Save','save-button','amazon-ads-app-manag', 'btn btn-sm btn-success');

        if($formSet->dataProcesing())
        {
            $formData = array();
            try
            {
                $formData = $formSet->getData();
                $formSet->validate($formData);
                $connection->amazonAdsConfigTable->setRefreshToken(null); // resetujem refresh token kvoli zmene základných údajov
                $connection->amazonAdsConfigTable->setData($formData);
                print_r($formData);
                $connection->amazonAdsConfigTable->save(true);

                $this->addMessage('Amazon Advertising configuration data has been saved.', self::MSG_SUCCESS);
                $this->redirect('app-management/settings/'.$selectUser);
            }
            catch (ValidationException $error)
            {
                $formSet->setValuesControls($formData);
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
            }
        }

        //ak je id záznamu tak už mam pristopové tokeny a teda môzem generovať refresh url
        if($connection->amazonAdsConfigTable->getAmazonAdsConfigId() && empty($connection->amazonAdsConfigTable->getRefreshToken()))
            $this->data['refreshUrl'] = $connection->generateTokens()->generateRefreshUrl() ;//$generateTokens->generateRefreshUrl();
        else
            $this->data['refreshToken'] = $connection->amazonAdsConfigTable->getRefreshToken();

        $this->data['profiles'] = (new AmazonAdsProfileTable())->get([Table::USER_ID => $selectUser]);
        $this->data['portfolios'] = (new AmazonAdsPortfolioTable())->get([Table::USER_ID => $selectUser]);
        $this->data['campaigns'] = (new AmazonAdsCampaignTable())->get([Table::USER_ID => $selectUser]);
        $this->data['adGroups'] = (new AmazonAdsAdGroupTable())->get([Table::USER_ID => $selectUser]);
        $this->data['keywords'] = (new AmazonAdsKeywordTable())->get([Table::USER_ID => $selectUser]);
        $this->data['targets'] = (new AmazonAdsTargetTable())->get([Table::USER_ID => $selectUser]);

        $this->data['settingStatus'] = empty($this->data['targets']) ? 'Settings are not complete' : 'Settings are complete';
        $this->data['selectUser'] = $selectUser;

        $this->data['formSet'] = $formSet->createForm();

        $this->data['method'] = 'settings';
        $this->view = 'settings';
    }

    /**
     ** Vygeneruje Refresh Token
     * @return void
     * @Action
     */
    public function generateRefreshToken()
    {
        $connection = new Connection($this->userId);
        try
        {
            $connection->generateTokens()->generateRefreshToken();
            $connection->amazonAdsConfigTable->save(true);
            $this->addMessage('Refresh Token have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        catch (Exception $error)
        {
            $this->addMessage('Error loading Refresh Token',self::MSG_ERROR);
        }

        $this->redirect('app-management/settings/' . $this->userId);
    }

    /**
     ** Stiahne a uloži profili Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListProfiles(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        try
        {
            $profiles = $connection->profile()->prepareData();
            $profiles = $amazonAdsProfileTable->sortByCustomer($profiles);
            $amazonAdsProfileTable->save($profiles);

            $this->addMessage('Profiles have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }

    /**
     ** Stiahne a uloži portfolia Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListPortfolios(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsPortfolioTable = new AmazonAdsPortfolioTable();
        try
        {
            $portfolios = $connection->portfolio()->prepareData();
            $amazonAdsPortfolioTable->save($portfolios);

            $this->addMessage('Portfolios have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }

    /**
     ** Stiahne a uloži kampane  Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListCampaigns(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsCampaignTable = new AmazonAdsCampaignTable();
        try
        {
            $campaigns = $connection->campaign()->prepareData();
            $amazonAdsCampaignTable->save($campaigns);

            $this->addMessage('Campaigns have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }
    /**
     ** Stiahne a uloži reklamne skupiny Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListAdGroups(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsAdGroupTable = new AmazonAdsAdGroupTable();
        try
        {
            $adGroups = $connection->adGroup()->prepareData();
            $amazonAdsAdGroupTable->save($adGroups);

            $this->addMessage('Ad Groups have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }
    /**
     ** Stiahne a uloži klučové slová Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListKeywords(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsKeywordTable = new AmazonAdsKeywordTable();
        try
        {
            $keywords = $connection->keyword()->prepareData();

            $amazonAdsKeywordTable->save($keywords);

            $this->addMessage('Keywords have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }

    /**
     ** Stiahne a uloži klučové slová Amazon Ads do DB
     * @param string $userId  id uživateľa, ktorého hodnoty sťahujem
     * @return void
     * @Action
     */
    public function downloadListTargetings(string $userId)
    {
        $this->userVerification($userId);

        $connection = new Connection($userId);
        $amazonAdsTargetTable = new AmazonAdsTargetTable();
        try
        {
            $keywords = $connection->target()->prepareData();

            $amazonAdsTargetTable->save($keywords);

            $this->addMessage('Targetings have been saved in the Database',self::MSG_SUCCESS);
        }
        catch (Exception $error)
        {
            $this->addMessage($error->getMessage(),self::MSG_ERROR);
        }
        catch (PDOException $error)
        {
            $this->addMessage('Error saving to database.: ' . $error->getMessage(),self::MSG_ERROR);
        }
        $this->redirect('app-management/settings/' . $userId);
    }

    /**
     ** spracuje požiadavku na nahratie nových údajov o produktoch z excelu
     * @return void
     * @Action
     */
    public function productsData()
    {
        $amazonProductDataTable = new AmazonProductDataTable();
        $form = new Form('upload');
        $form->addFile('Products data', 'products-data', 'upload', '', '', $required = true, false, File::XLXS);
        $form->addSubmit('Upload','upload-button','upload','btn btn-success');
        if($form->dataProcesing())
        {
            try
            {
                $formData = $form->getData('products-data');

                $form->validate($formData);

                $amazonProductDataTable->prepareAndSaveProductData($formData['products-data'][File::TMP_NAME], $this->userId);

                $this->addMessage('New products data has been saved to the database', self::MSG_SUCCESS);
                $this->redirect();
            }
            catch (ValidationException $error)
            {
                $this->addMessage($error->getMessages(), self::MSG_ERROR);
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

        $this->data['method'] = 'products-data';
        $this->view = 'products-data';
    }



    static function view($data)
    {
        foreach ($data as $key => $d)
        {

            echo ++$key . ' -> ' ;
            print_r($d);echo "<br><br>";
        }

    }
}