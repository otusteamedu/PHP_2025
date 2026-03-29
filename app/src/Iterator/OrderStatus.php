<?php

namespace App\Iterator;

enum OrderStatus: string {
    case Received = 'received';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Delivered = 'delivered';
}