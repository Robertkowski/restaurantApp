<?php

namespace App\Service\Space;

use App\Entity\Space;
use App\Service\AbstractService;

class SpaceService extends AbstractService
{

    public function create($model): Space
    {
        $space = new Space();
        $this->setCommonFields($space, $model);
        $this->saveEntity($space);
        return $space;
    }

    public function update(Space $space, $model): Space
    {
        $this->setCommonFields($space, $model);
        $this->saveEntity($space);
        return $space;
    }

    private function setCommonFields(Space $space, $model)
    {
        $space->setDescription($model->description);
        $space->setNumber($model->number);
        $space->setPlaces($model->places);
        $space->setState(Space::STATE_SUPPORTED);
    }
}