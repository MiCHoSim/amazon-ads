<?php
namespace AmazonAdvertisingApi\DataCollection;

use AmazonAdvertisingApi\Connection\Connection;
use AmazonAdvertisingApi\Table\AmazonAdsProfileTable;
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
        $profilesData[] = $profilesData[8];

        // Odstranenie vendora z poli ak tam je
        foreach ($profilesData as $key => $profile)
        {
            if ($profile[AmazonAdsProfileTable::ACCOUNT_INFO_ARRAY_TYPE] === 'vendor')
                unset($profilesData[$key]);
        }
        return $profilesData;
    }






}