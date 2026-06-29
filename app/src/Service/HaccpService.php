<?php

namespace App\Service;

use App\Entity\Plat;
use App\Entity\Temp;

class HaccpService
{
    public function isConforme(Temp $temp): bool
    {
        $type = $temp->getPlat()->getType();

        if ($type === Plat::TYPE_PLAT && $temp->getTemperature() >= 63) {
            return true;
        }

        if (($type === Plat::TYPE_ENTREE || $type === Plat::TYPE_DESSERT) && $temp->getTemperature() <= 4) {
            return true;
        }

        return false;
    }

    /**
     * @param iterable<Temp> $temps
     * @return array<int, bool> conformité indexée par id de Temp
     */
    public function mapConformites(iterable $temps): array
    {
        $conformites = [];
        foreach ($temps as $temp) {
            $conformites[$temp->getId()] = $this->isConforme($temp);
        }

        return $conformites;
    }
}
