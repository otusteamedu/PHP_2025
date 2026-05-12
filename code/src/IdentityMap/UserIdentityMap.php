<?php

namespace App\IdentityMap;

/**
 * Identity Map для объектов User.
 */
class UserIdentityMap extends IdentityMap
{
    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getObjectName(): string
    {
        return 'User';
    }
}
