<?php

namespace App\BaseModul\Controller;

use App\BaseModul\System\Controller\Controller;
use Settings;

/**
 ** Spracovava chybovú stránku
 * Class ErrorController
 * @package App\BaseModul\Controller
 */
class ErrorController extends Controller
{
    /**
     ** Odošle chybovú hlavičku
     * @Action
     */
    public function index()
    {
        // hlavička požiadavky
        header('HTTP/1.0 404 Not Found');

        $this->data['domainName'] = Settings::$domainName;

        $this->view = "index";
    }
}
