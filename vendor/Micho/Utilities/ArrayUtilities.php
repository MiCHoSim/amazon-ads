<?php

namespace Micho\Utilities;

/**
 ** Pomocná trieda na prácu s poľom
 * Class Pole
 */
class ArrayUtilities
{
    /**
     ** Prefiltruje kľúče v poly Porovná či klúče vsupného poľa sa zhoduju s požadovanými klúčami
     * @param array $inputArray Vstupné pole, ktoré chceme filtrovať
     * @param array $keys Pole povolených kľúčov
     * @return array Výsledne prefiltrované pole
     */
    public static function filterKeys(array $inputArray, array $keys)
    {
        $p = $inputArray;
        if(is_array(array_shift($p)))
        {
            $pole = array();
            foreach ($inputArray as $kluc => $hodnota)
            {
                $pole[$kluc] = self::filterKeys($inputArray[$kluc], $keys);
            }
            return $pole;
        }
        else
            return array_intersect_key($inputArray, array_flip($keys));
    }

    /**
     ** Prefiltruje kľuče poľa tak, aby obsahovalo len tie zo zadanou predponou
     * @param StringUtilities $predpona Predpona
     * @param array $vstupnePole Vstupné pole, ktoré chceme filtrovať
     * @return array Výsledne prefiltrované pole
     */
    public static function filtrujKluceSPredponou($predpona, array $vstupnePole)
    {
        $vystup = array();
        foreach ($vstupnePole as $kluc => $hodnota)
        {
            if (mb_strpos($kluc, $predpona) === 0)
                $vystup[$kluc] = $hodnota;
        }
        return $vystup;
    }

    /**
     ** Mapuje pole riadkov (asociatívnych polí) tak, že je výsledkom jedno asociatívne pole, pričom jeho klúče a hodntoy odpovedáju určitým kľúčom jednotlivých riadkov
     * @param array $array Vstupné pole riadkov
     * @param StringUtilities $keyKey Klúč riadku, ktorý bude kľúčom výstupného poľa
     * @param StringUtilities $keyValue Klúč riadku, ktorý bude hodnotou výstupného poľa
     * @return array Výsledné asociatívne pole
     */
    public static function getPairs(array $array, $keyKey, $keyValue)
    {
        if(empty($array))
            return false;

        foreach ($array as $row)
        {
            $key = $row[$keyKey];
            // Kontrola kolizií klúčov
            if (isset($pairs[$key]))
            {
                $i = 1;
                while (isset($pairs[$key . ' (' . $i . ')'])) // zväčuje sa číslo pokiaľ je kolízia
                {
                    $i++;
                }
                $key .= ' (' . $i . ')';
            }
            $pairs[$key] = $row[$keyValue];
        }
        return $pairs;
    }

    /**
     ** Mapuje pole riadkov (asociatívnych polí) tak, že je výsledkom jedno pole, do ktorého sú vložené hodnoty z riadkov po daným klúčom
     * @param array $poleRiadkov Vstupné pole riadkov
     * @param StringUtilities $kluc Názov klúča, ktorého hodnotu vkladáme do výstupného poľa
     * @return array Výsledné pole hodnôt
     */
    public static function ziskajHodnoty(array $poleRiadkov, $kluc)
    {
        $hodnoty = array();
        foreach ($poleRiadkov as $riadok)
        {
            $hodnoty[] = $riadok[$kluc];
        }
        return $hodnoty;
    }

    /**
     ** Rekurzivne pridá predpony kľúčom v poli
     * @param StringUtilities $predpona Predpona klúča, ktorú chceme pridať
     * @param array $vstupnePole
     * @return array Výsledne pole
     */
    public static function pridajPredponu($predpona, array $vstupnePole)
    {
        $vystup = array();
        foreach ($vstupnePole as $kluc => $hodnota)
        {
            $kluc = $predpona . $kluc;
            if (is_array($hodnota))
                $hodnota = self::pridajPredponu($predpona, $hodnota);
            $vystup[$kluc] = $hodnota;
        }
        return $vystup;
    }

    /**
     ** Rekurzivne odstráni predpony kľúčom v poli
     * @param StringUtilities $predpona Predpona klúča, ktorú chceme odstrániť
     * @param array $vstupnePole
     * @return array Výsledne pole
     */
    public static function odstranPredponu($predpona, array $vstupnePole)
    {
        $vystup = array();
        foreach ($vstupnePole as $kluc => $hodnota)
        {
            if (strpos($kluc, $predpona) === 0)
                $kluc = substr($kluc, mb_strlen($predpona));
            if (is_array($hodnota))
                $hodnota = self::odstranPredponu($predpona, $hodnota);
            $vystup[$kluc] = $hodnota;
        }
        return $vystup;
    }

    /**
     ** Prevedie camel notaciu kľúča poľa na podčiarkovnikovú
     * @param array $vstupnePole Vstupné pole
     * @return array Výsledne pole
     */
    public static function camelNaPodciarkovnik($vstupnePole)
    {
        $vystup = array();
        foreach ($vstupnePole as $kluc => $hodnota)
        {
            $kluc= StringUtilities::camelToUnderline($kluc);
            if (is_array($hodnota))
                $hodnota = self::camelNaPodciarkovnik($hodnota);
            $vystup[$kluc] = $hodnota;
        }
        return $vystup;
    }

    /**
     ** Prevedie  podčiarkovnikovú notaciu kľúča poľa na camel
     * @param array $vstupnePole Vstupné pole
     * @return array Výsledne pole
     */
    public static function podciarkovnikNaCamel($vstupnePole)
    {
        $vystup = array();
        foreach ($vstupnePole as $kluc => $hodnota)
        {
            $kluc = StringUtilities::underlineToCamel($kluc);
            if (is_array($hodnota))
                $hodnota = self::podciarkovnikNaCamel($hodnota);
            $vystup[$kluc] = $hodnota;
        }
        return $vystup;
    }

    /**
     ** Zisti Či sa v poli klúčov nachadza klúč s hľadaným podreŤazcom
     * @param StringUtilities $podretazecKluc kľadaný podretazec klúča
     * @param array $data Pole hodnôt
     * @return bool či sa naŠei dany podretazec v poli klúča
     */
    public static function najdyPodretazecKluca($podretazecKluc ,$data)
    {
        return !empty(preg_filter('~' . preg_quote($podretazecKluc, '~') . '~', null, array_flip($data)));
    }

    /**
     ** Mapuje pole riadkov (asociatívnych polí) tak, že je výsledkom jedno pole, do ktorého sú vložené hodnoty z riadkov po daným klúčom avŠak sú unikátne
     * @param array $poleRiadkov Vstupné pole riadkov
     * @param StringUtilities $kluc Názov klúča, ktorého hodnotu vkladáme do výstupného poľa
     * @return array Výsledné pole hodnôt
     */
    public static function ziskajUnikatneHodnoty(array $poleRiadkov, $kluc)
    {
        $hodnoty = self::ziskajHodnoty($poleRiadkov, $kluc);
        return array_merge(array_unique($hodnoty), array());
    }

    /**
     ** Vráti unikatne Pole poli Viecerých klúčov naraz
     * @param array $poleRiadkov Vstupné pole riadkov
     * @param array $kluce Klúče ktore majú byť unikátne
     */
    public static function ziskajUnikatnePole(array $poleRiadkov, $kluce)
    {
        $zlucene = '';
        $poleTriedene = array();
        foreach ($poleRiadkov as $kluc => $rez)
        {
            $zluc = '';
            foreach ($kluce as $kl) // vytvori rezec ktory ma byt unikatni v poli ...
            {
                $zluc .= $rez[$kl];
            }
            if ($zlucene !== $zluc) // ak je unikatni tak ho ulozim
            {
                $poleTriedene[$kluc] = $rez;
            }

            $zlucene = $zluc;
        }
        return $poleTriedene;
    }

}
/* Autor: http://www.itnetwork.cz */

/*
 * Niektoré časti sú upravené
 * Autor: MiCHo
 */
