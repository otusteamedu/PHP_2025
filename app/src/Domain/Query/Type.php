<?php

namespace App\Domain\Query;


enum Type: string
{
    case RANGE = 'range';

    case MATCH = 'match';

    case TERM = 'term';
}
