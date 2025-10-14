<?php

declare(strict_types=1);

namespace Restaurant\Iterator;

enum OrderStatus: string
{
    case CREATED = 'Создан';
    case COOKING = 'Готовится';
    case READY = 'Готов';
    case DELIVERED = 'Выдан';
}
