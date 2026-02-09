<?php

namespace Restaurant\Domain\Enums;

enum IngredientType: string
{
    case BUN = 'булочка';
    case BREAD = 'хлеб';
    case BAGEL = 'бублик';

    case BEEF_PATTY = 'котлета';
    case SAUSAGE = 'колбаса';
    case SAUSAGE_HOT_DOG = 'сосиска';
    case BACON = 'бекон';
    case HAM = 'ветчина';

    case ONION = 'лук';
    case PEPPER = 'перец';
    case TOMATO = 'томат';
    case CUCUMBER = 'огурец';
    case CARROT = 'морковь';
    case SALAD = 'салат';

    case MAYO = 'майонез';
    case KETCHUP = 'кетчуп';
    case MUSTARD = 'горчица';

    case CHEESE = 'сыр';

    case SPICES = 'специи';
    case BUTTER = 'масло';
}
