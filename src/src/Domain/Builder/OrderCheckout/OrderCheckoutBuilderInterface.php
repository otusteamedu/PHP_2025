<?php

namespace App\Domain\Builder\OrderCheckout;

interface OrderCheckoutBuilderInterface 
{  

    public function set_order($order);

    public function get_order();

    public function set_payway($payway);

    public function get_payway();

    public function set_getway($getway);

    public function get_getway();

    public function build();

}