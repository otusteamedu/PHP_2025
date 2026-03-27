<?php

namespace Tree;

readonly class Tree
{
    /**
     * @param int $index
     * @param Tree|null $left
     * @param Tree|null $right
     */
    public function __construct(public int $index, public ?Tree $left = null, public ?Tree $right = null)
    {

    }

    /**
     * @param int $index
     *
     * @return Tree
     */
    public function node(int $index): Tree
    {
        if ($this->index < $index) {
            if ($this->right === null) {
                $right = new Tree($index);
            } else {
                $right = $this->right->node($index);
            }

            return new Tree(
                $this->index,
                $this->left,
                $right
            );
        }

        if ($this->index > $index) {
            if ($this->left === null) {
                $left = new Tree($index);
            } else {
                $left = $this->left->node($index);
            }

            return new Tree(
                $this->index,
                $left,
                $this->right
            );
        }

        return $this;
    }
}

class Solution
{
    /**
     * @param array<int, int> $list
     *
     * @return Tree
     */
    public function make(array $list): Tree
    {
        $count = count($list);
        $index = 0;

        $tree = new Tree($list[$index]);

        for ($i = $index + 1; $i < $count; $i++) {
            $tree = $tree->node($list[$i]);
        }

        return $tree;
    }

    /**
     * @param array<int, int> $a
     * @param array<int, int> $b
     *
     * @return array<int, int>
     */
    public function merge(array $a, array $b): array
    {
        return [...$a, ...$b];
    }
}

$a = range(1, 10);
$b = range(5, 10);

shuffle($a);
shuffle($b);

$solutions = new Solution();

print_r(
    $solutions
        ->make(
            $solutions
                ->merge($a, $b)
        )
);
