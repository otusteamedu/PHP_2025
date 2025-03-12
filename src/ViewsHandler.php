<?php

declare(strict_types=1);

namespace App;

class ViewsHandler
{
    public function getViewsCount(): int
    {
        if (!isset($_SESSION['views'])) {
            $_SESSION['views'] = 0;
        }

        return $_SESSION['views']++;
    }
}