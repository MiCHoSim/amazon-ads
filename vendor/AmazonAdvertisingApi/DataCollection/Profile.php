<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
use App\ApplicationModul\Amazon\Controller\AmazonAdsController;
use App\ApplicationModul\AppManagement\Controller\AppManagementController;
use Micho\Utilities\StringUtilities;
use Exception;

class Profile extends Request
{
    /**
     * Nastavenia
     */
    protected $className = 'Profile';
    protected $contentType = 'application/json';

    protected $endPoint = '/v2/profiles';
    protected $listMethod = 'GET';
    protected $getMethod = 'GET';

    /**
     * Skratky
     */
    const ABBREVIATIONS = ['IT' => 'Italy','SE' => 'Sweden','PL' => 'Poland','FR'=> 'France','NL' => 'Netherlands','ES' => 'Spain','UK' => 'United Kingdom', 'DE' => 'Germany'];

    public function prepareData(): array
    {
        $profiles = $this->list();

        $profilesData = $this->setTableData($profiles);

        if(isset($profilesData[8])) // Dorobenie kvoli tomu ze dakedy neprislo pod indexom 8 nic -.. aj ekd enviem repoco to tu bolo toto priradenie
            $profilesData[] = $profilesData[8];

        // Odstranenie vendora z poli ak tam je tada rpedajcu z dovodu ze martina tam bola ako ako nakupujuca aj ako predavajuca
        foreach ($profilesData as $key => $profile)
        {
            if ($profile[AmazonAdsProfileTable::ACCOUNT_INFO_ARRAY_TYPE] === 'vendor')
                unset($profilesData[$key]);
        }
        return $profilesData;
    }
}