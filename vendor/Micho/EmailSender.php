<?php

namespace Micho;

use Micho\Antispam\AntispamRok;
use Settings;

/**
 ** Pomocná trieda, poskytujuca metody pre odosielanie emailu
 * Class OdosielacEmailov
 * @package Micho
 */
class EmailSender
{
    /**
     * Odošle email ako HTML, dajú sa použiť zakladné HTML tagy a nové
     * riadky je potrebné pisať ako <br> alebo použivať odstavce. Kodovanie je
     * odladené pre UTF-8
     * @param string $to Adresa na ktorú sa posiela/príjemnca
     * @param string $subject Predmet správy
     * @param string $message Správa Može býť aj generovaná ako HTML šablona
     * @param string $from Adresa odosielateľa
     * @param bool $obchPodmOdstupZmluv ci posielam obchodné podmienky
     * @param string|bool $faktura či posielam faktúru ak ańo tak čsilo fakturý
     * @throws UserException
     */
    public function send($to, $subject, $message, $from, $obchPodmOdstupZmluv = false, $faktura = false)
    {
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $hlavicka = "From: <" . $from . ">";// Hlavička pre informácie o odosielateľovi
        $hlavicka .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";// Hlavičky pre prílohy

        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";// Multipart boundary

        // Prílohy
        if($obchPodmOdstupZmluv)
            $subory = array("../public/pdf/Obchodné podmienky.pdf", "../public/pdf/Odstúpenie od zmluvy.pdf");
        if($faktura)
            $subory = array('../public/pdf/' . $faktura . '.pdf', 'F');


        // Príprava prílohy
        if(!empty($subory))
        {
            for($i=0;$i<count($subory);$i++){
                if(is_file($subory[$i]))
                {
                    $file_name = basename($subory[$i]);
                    $file_size = filesize($subory[$i]);

                    $message .= "--{$mime_boundary}\n";
                    $fp =    @fopen($subory[$i], "rb");
                    $data =  @fread($fp, $file_size);
                    @fclose($fp);
                    $data = chunk_split(base64_encode($data));
                    $message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .
                        "Content-Description: ".$file_name."\n" .
                        "Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .
                        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
                }
            }
        }

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $from;

        if (Settings::$debug)
        {
            file_put_contents('subory/emaily/' . uniqid() .'.html', $message);
            return;
        }

        if (!@mail($to, $subject, $message, $hlavicka, $returnpath))
            throw new UserException('Email sa nepodarilo odoslať.');

        if($faktura)
            unlink('../public/pdf/' . $faktura . '.pdf'); // vymaže docastnú PDF faktúru po odoslani emailu
    }
    
    /**
     * Skontroluje, či je zadaný aktuálny rok ako antispam a odošle email
     * @param int $rok Aktuálny rok
     * @param string $komu Emailova Adresa
     * @param string $predmet Predmet
     * @param string $sprava Sprava
     * @param string $od Adresa odosielateľa
     * @throws UserException
     */
    public function odosliSAntispamom($rok, $komu, $predmet, $sprava, $od)
    {
        $antispam = new AntispamRok();
        $antispam->over($rok);
        $this->send($komu, $predmet, $sprava, $od);
    }
}
/* Autor: http://www.itnetwork.cz */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */
