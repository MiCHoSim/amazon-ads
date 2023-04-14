<?php

namespace App\ApplicationModul\Amazon\Model;


use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use AmazonAdvertisingApi\Table\Table;
use App\ApplicationModul\Amazon\Controller\AmazonMonthlySalesController;
use App\BaseModul\System\Controller\Controller;
use Matrix\Exception;
use Micho\Exception\ValidationException;
use Micho\Files\FileXlsx;
use Micho\Form\Form;
use Micho\Utilities\ArrayUtilities;
use Micho\Utilities\DateTimeUtilities;

/**
 * Trieda spracujuca požiadavky AmazonManager
 */
class AmazonManager
{
    /**
     * @var Controller
     */
    private Controller $controller;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     ** Zakladný pohľad pre amazon app
     * @param string|null $profileId Id profilu v ktorom pracujem
     * @param bool $remember Či chem aby si zapamental ostatne údaje z url
     * @return void
     */
    public function basicTemplateSetings(string|null $profileId, bool $remember = false)
    {
        $amazonAdsProfileTable = new AmazonAdsProfileTable();
        $profiles = $amazonAdsProfileTable->getPair([Table::USER_ID => $this->controller->userId]);

        // pridenie ostatnych mozosti výberu polapožiadavky
        if(get_class($this->controller) === AmazonMonthlySalesController::NAME)
            $profiles = array_merge($profiles,AmazonAdsProfileTable::COMBINE_PROFILE);

        if(!$profiles)
        {
            $this->controller->addMessage('Basic settings are not created',Controller::MSG_ERROR);
            $this->controller->redirect('app-management/settings');
        }

        $this->controller->connection->profileId = !empty($profileId) ? $profileId : current($profiles); // nastavenia profilu spojenia
        if(empty($profiles))
            $this->controller->redirect('app-management/settings');

        $formStates = new Form('states');
        $formStates->addSelect('Select state',AmazonAdsProfileTable::PROFILE_ID,$profiles, $this->controller->connection->profileId,false, 'states');
        $formStates->addSubmit('Display','display-button','states','sr-only');

        if($formStates->dataProcesing())
        {
            try
            {
                $formData = $formStates->getData();
                $formStates->validate($formData);

                $url = explode('/', Controller::$currentUrl);
                if($remember)
                {
                    $url[2] = $formData[AmazonAdsProfileTable::PROFILE_ID];
                    $url = implode('/', $url);
                }
                else
                    $url = $url[0] . '/' . $url[1] . '/' . $formData[AmazonAdsProfileTable::PROFILE_ID];

                $this->controller->redirect($url);
            }
            catch (ValidationException $error)
            {
                $this->controller->addMessage($error->getMessages(), Controller::MSG_ERROR);
            }
        }
        $this->controller->data['profile'] = $this->controller->connection->profileId;
        $this->controller->data['formStates'] = $formStates->createForm();
        $this->controller->data['profileMenu'] = '../app/ApplicationModul/Amazon/View/menu-header.phtml';
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