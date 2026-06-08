<?php

namespace App\Service;

use App\Entity\Temp;



class Haccpservice

{   
    public function isconforme(Temp $temp ):bool
    {
        
    $type = $temp->getPlat()->getType();

    if ($type === 'plat' && $temp->getTemperature() >= 63) {
        return true;
    }

    if (($type === 'entree' || $type === 'dessert') && $temp->getTemperature() <= 4) {
        return true;
    }

    return false;
}

}

  
