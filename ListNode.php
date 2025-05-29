<?php

declare(strict_types=1);

class ListNode {
    public mixed $val = 0;
    public mixed $next = null;

    public function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}