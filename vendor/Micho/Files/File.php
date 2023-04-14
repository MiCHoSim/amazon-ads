<?php

namespace Micho\Files;

use Micho\Exception\UserException;
use Micho\Utilities\StringUtilities;

/**
 ** Tríeda služiaca na správu Súborov
 * Class File
 * @package Micho
 */
class File
{
    /**
     ** Uloži popripade vytvorí súbor a uloŽí text
     * @param string $text Text na uloženie
     * @param string $path Cesta uloŽenia súboru
     * @throws UserException
     */
    public function saveFile($text, $path)
    {
        $return = file_put_contents($path, $text);
        if($return === FALSE)
            throw new UserException('Pri uložení došlo k chybe');
    }

    /**
     ** Načíta súbor
     * @param string $path Cesta uloŽenia súboru
     * @return string Načítaný text
     * @throws UserException
     */
    public function readSubor($path)
    {
        $text = file_get_contents($path);
        if($text === FALSE)
            throw new UserException('Pri čítani súboru došlo k chybe');
        return $text;
    }

    /**
     ** Zisti názvy súborov a vráti ich
     * @param string $path Cesta k súboru
     * @return array|false Načitané názvy súborov
     */
    public static function returnFileNames($path)
    {
        $folder = scandir($path);
        array_shift($folder);
        array_shift($folder);
        return $folder;
    }

    /**
     ** Vráti Celý názov hladaného súboru z daneho podretazca
     * @param string $path Cesta priecinka k hladému súboru
     * @param string $substring časť názvu ktory hladám
     * @return string Názov súboru;
     */
    public static function returnFileNameSubstring($path, $substring)
    {
        if (file_exists($path))
        {
            $files = self::returnFileNames($path); // nacita nazvy suborov v priecinku

            foreach ($files as $key => $file)
            {
                if(StringUtilities::contains($file, $substring)) // najde subor obrazka z danim nazvom vyhodui z cyklu
                    return $file;
            }
        }

        return false;
    }

    /**
     ** skopiruje Subor
     * @param string $source Zdroj kopirovania
     * @param string $nameOld Nazov stareho suboru
     * @param string $location Cieľ kopirovania
     * @param string $nameNew Nazov noveho suboru porekopirovaneho
     * @return bool či sa kopirovanie podarilo
     */
    public static function copyFile($source, $nameOld, $location, $nameNew)
    {
        Folder::createFolder($location); //vytvorenie priečinka v pripade ze neexistuje

        if (copy($source. '/' . $nameOld, $location . '/' . $nameNew))
            return true;
        return false;
    }

}
/*
 * Autor: MiCHo
 */