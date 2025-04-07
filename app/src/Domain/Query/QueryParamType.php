<?php

namespace App\Domain\Query;


enum QueryParamType: string
{
    case MUST = 'must';

    case SHOULD = 'should';

    case FILTER = 'filter';

    case MUST_NOT = 'must_not';

}
