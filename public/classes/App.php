<?php

namespace classes;

class App
{
    public function run()
    {
        $solution = new Solution();
        $list1 = $solution->makeLinkedListFromArray([1,2,4]);
        $list2 = $solution->makeLinkedListFromArray([1,3,4]);
        return $solution->mergeTwoLists($list1, $list2);
    }

}
