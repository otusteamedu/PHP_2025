<?php

namespace App\ES\Query\Base;

interface QueryComponent {
    public function build(): array;
}
