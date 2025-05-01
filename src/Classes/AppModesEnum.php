<?php

namespace App\Classes;
enum AppModesEnum: string
{
    case BASE = 'base';
    case PREVIEW = 'preview';

    public static function getCasesValues():array
    {
        return [
            self::BASE->value,
            self::PREVIEW->value
        ];
    }
}
