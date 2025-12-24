<?php

declare(strict_types=1);

namespace App\Array;

class ListNode
{
    public mixed $val = 0;
    public self|null $next = null;

    public function __construct(mixed $val = 0, self|null $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}
