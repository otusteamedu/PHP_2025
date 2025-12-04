<?php

namespace List;

class Node
{
    /**
     * @param int $index
     * @param Node|null $node
     */
    public function __construct(public readonly int $index, public ?Node $node = null)
    {

    }
}

class Solution
{
    /**
     * @param array<int, int> $list
     *
     * @return Node
     */
    public function forward(array $list): Node
    {
        $count = count($list);
        $index = 0;

        $node = new Node($list[$index]);
        $current = $node;

        for ($i = $index + 1; $i < $count; $i++) {
            $current->node = new Node($list[$i]);
            $current = $current->node;
        }

        return $node;
    }

    /**
     * @param array<int, int> $list
     *
     * @return Node
     */
    public function backward(array $list): Node
    {
        $count = count($list);
        $index = $count - 1;

        $node = new Node($list[$index]);
        $current = $node;

        for ($i = $index - 1; $i >= 0; $i--) {
            $current->node = new Node($list[$i]);
            $current = $current->node;
        }

        return $node;
    }

    /**
     * @param array<int, int> $list1
     * @param array<int, int> $list2
     *
     * @return array<int, int>
     */
    public function merge(array $list1, array $list2): array
    {
        return [...$list1, ...$list2];
    }
}

$solutions = new Solution();

print_r(
    $solutions
        ->forward(
            $solutions
                ->merge([20, 2, 1, 3], [2, 3, 10, 3])
        )
);

print_r(
    $solutions
        ->backward(
            $solutions
                ->merge([20, 2, 1, 3], [2, 3, 10, 3])
        )
);
