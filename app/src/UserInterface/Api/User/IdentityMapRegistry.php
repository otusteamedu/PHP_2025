<?php

namespace App\UserInterface\Api\User;

use App\Domain\UserIdentityMap;

final class IdentityMapRegistry
{
    private static ?UserIdentityMap $userMap = null;

    public static function user(): UserIdentityMap
    {
        return self::$userMap ??= new UserIdentityMap();
    }
}
