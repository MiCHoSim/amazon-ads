<?php

namespace Micho\Files;


/**
 ** Tríeda služiaca na správu priečinkov
 * Class Folder
 * @package Micho
 */
class Folder
{

    /**
     ** Zisti, či existuje priečinok ak nie tak ho vytvori
     * @param string $path CEsta umiestnenia priecinka
     * @return bool
     */
    public static function createFolder($path)
    {
        if (file_exists($path))
            return true;

        mkdir($path, 0777);
        return $path;
    }

    /**
     ** Nájde podpriečinky vymaže z nich súbory a nakoniec vymaže aj hlavny priečinok
     * @param string $path Cesta k pričinku
     */
    public static function deleteFolder($path)
    {
        if (!file_exists($path))
            return false;

        $folders = scandir($path);

        array_shift($folders);
        array_shift($folders);

        if ($folders)
        {
            foreach ($folders as $subFolder)
            {
                if(mb_strpos($subFolder,'.') !== false)
                {
                    unlink($path . '/' . $subFolder);
                }
                else
                {
                    $newPath = $path . '/' . $subFolder;
                    self::deleteFolder($newPath); //rekruzivne prejde vŠetky prvky
                }
            }
        }
        rmdir($path);
    }

}
/*
 * Autor: MiCHo
 */