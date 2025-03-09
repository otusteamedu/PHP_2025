<?php
declare(strict_types=1);

namespace App;

class ListNode
{
    public int $val = 0;
    public ?ListNode $next = null;

    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }

}