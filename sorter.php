<?php

namespace Sorter;

class Sorter
{
    /**
     * @param int $index
     * @param Sorter|null $node
     */
    public function __construct(public int $index, public ?Sorter $node = null)
    {

    }

    /**
     * @param int $index
     *
     * @return Sorter
     */
    public function push(int $index): Sorter
    {
        if ($this->index > $index) {
            // left

            $node = new Sorter($index);
            $node->node = $this;

            return $node;
        } else if ($this->index < $index) {
            // right

            if ($this->node === null) {
                // new
                $this->node = new Sorter($index);
            } else {
                if ($this->node->index >= $index) {
                    // between || equal

                    $node = new Sorter($index);
                    $node->node = $this->node;

                    $this->node = $node;
                } else {
                    // tail
                    $this->node->push($index);
                }
            }

            return $this;
        } else {
            // equal
            $node = new Sorter($index);
            $node->node = $this;

            return $node;
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $list[] = $this->index;

        $next = $this->node;

        while ($next) {
            $list[] = $next->index;

            $next = $next->node;
        }

        return $list;
    }
}

class Solution
{
    /**
     * @param array<int, int> $list
     *
     * @return Sorter
     */
    public function sorter(array $list): Sorter
    {
        $count = count($list);
        $index = 0;

        $sorter = new Sorter($list[$index]);

        for ($i = $index + 1; $i < $count; $i++) {
            $sorter = $sorter->push($list[$i]);
        }

        return $sorter;
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

$a = array_map(fn() => rand(0, 9), range(1, 10));
$b = array_map(fn() => rand(0, 9), range(1, 10));

$solutions = new Solution();

$merge = $solutions
    ->merge($a, $b);

$sorted = $solutions
    ->sorter($merge);

sort($merge);

print_r($sorted);
print_r($sorted->toArray());

echo 'Equal : ' . ($merge === $sorted->toArray() ? 'true' : 'false') . PHP_EOL;
