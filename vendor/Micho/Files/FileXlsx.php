<?php

namespace Micho\Files;

use Micho\Exception\UserException;
use Micho\Utilities\StringUtilities;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


/**
 ** Tríeda služiaca na správu Xlsx excel suborov
 * Class File
 * @package Micho
 */
class FileXlsx
{
    /**
     ** Načita dáta z excelu avráti ich ako pole pricom prvá časť obsahuje nazov hlavičiek a druhá jednotlivé riadky excelu
     * @param string $pathToFile Cesta k uloženému súboru
     * @return array Poľe hodnot
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function getTitleOtherRow(string $pathToFile) : array
    {
        require '../vendor/phpspreadsheet/vendor/autoload.php';

        $reader = new Xlsx();
        $spreadsheet = $reader->load($pathToFile);

        $firstSheet = $spreadsheet->getSheet(0)->toArray();

        $titleRow = array_shift($firstSheet);

        $otherRow = $firstSheet;

        return ['titleRow' => $titleRow, 'otherRow' => $otherRow];
    }
}
/*
 * Autor: MiCHo
 */