<?php

declare(strict_types=1);

namespace One;

class ListNode
{
    /**
     * @var int
     */
    public int $value = 0;

    /**
     * @var ListNode|null
     */
    public ?ListNode $next = null;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }
}

class Solution
{
    /**
     * @param ListNode|null $a
     * @param ListNode|null $b
     *
     * @return ListNode|null
     */
    public function handle(?ListNode $a, ?ListNode $b): ?ListNode
    {
        if ($a === null || $b === null) {
            return null;
        }

        $pa = $a;
        $pb = $b;

        while ($pa !== $pb) {
            $pa = ($pa === null) ? $b : $pa->next;
            $pb = ($pb === null) ? $a : $pb->next;
        }

        return $pa;
    }
}

$common = new ListNode(10);
$common->next = new ListNode(5);

$a = new ListNode(4);
$a->next = new ListNode(1);
$a->next->next = $common;

$b = new ListNode(5);
$b->next = new ListNode(6);
$b->next->next = new ListNode(1);
$b->next->next->next = $common;

$result = new Solution()
    ->handle($a, $b);

echo 'Result : ' . ($result->value ?? 'null'), PHP_EOL;
