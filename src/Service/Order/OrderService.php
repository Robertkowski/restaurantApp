<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Service\AbstractService;

class OrderService extends AbstractService
{

    public function create($model): Order
    {
        $order = new Order();
        $this->setCommonFields($order, $model);
        $this->saveEntity($order);
        return $order;
    }

    public function update(Order $order, $model): Order
    {
        $this->setCommonFields($order, $model);
        $this->saveEntity($order);
        return $order;
    }

    private function setCommonFields(Order $order, $model)
    {
        $order->setPrice($model->price);
    }
}
