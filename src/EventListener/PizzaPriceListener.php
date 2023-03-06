<?php

namespace App\EventListener;

use App\Entity\Pizza;
class PizzaPriceListener
{
    public function postLoad(Pizza $pizza): void
    {
        $pizza->setBasicPrice();
    }
}