<?php

namespace App\Domain\Cooking;

class CookingStatus
{
    public const string CREATED = 'CREATED';
    public const string COOKING  = 'COOKING';
    public const string COMPLETED = 'COMPLETED';
    public const string CANCELED = 'CANCELED';
}
