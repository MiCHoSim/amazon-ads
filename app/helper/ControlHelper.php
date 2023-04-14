<?php

use App\BaseModul\System\Controller\Controller;

/**
 ** Trieda slúži na zostavenie ovladacích kontroliek
 * Class KontrolkaPomocne
 */
class ControlHelper
{
    /**
     ** Vytvorí ovladaciu kontrolku na ktok späť
     * @param string $url Url adresi an rpesmerovanie späť
     * @return string Html Hod kontrolky
     */
    public static function spat($url)
    {
        $kontrolka =
            '<a href="' . $url . '" class="btn btn-light btn-sm border-dark kontrolka">
                <i title="Späť" class="fa fa-backward"></i>
             </a>';
        return $kontrolka;
    }

    /**
     ** Vytvori ovladaciu kontrolku pre zrúšenie
     * @param string $dataDismis JE to v podstate Trieda ktorou JS bootstrapu reaguje na klik modal/alert/...
     * @return string Html Hod kontrolky
     */
    public static function cancel($dataDismis)
    {
        $control =
            '<button type="button" class="close" data-dismiss="' . $dataDismis . '" aria-label="Zavrieť">
                <i class="fa fa-times-circle" aria-hidden="true"></i>
             </button>   ';
        return $control;
    }

    /**
     ** Vytvori Kontrolku pre informačnú správu
     * @param string $type Týp správi ktorý je k dispozícíí pri správe
     * @return string Html Hod kontrolky
     */
    public static function controlIcon($type)
    {
        $ControlsType = array(
                    Controller::MSG_INFO=> 'fa fa-info-circle',
                    Controller::MSG_SUCCESS=> 'fa fa-check-circle',
                    Controller::MSG_ERROR => 'fa fa-exclamation-circle');

        return   '<i class="' . $ControlsType[$type] . ' p-0 pr-2" aria-hidden="true"></i>';

    }
}
/*
 * Autor: MiCHo
 */