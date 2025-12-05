<?php


function getIntersectionNode($headA, $headB)
{
    $l1 = $headA;
    $l2 = $headB;
    $hash = [];

    while ($l1) {
        $hash[] = $l1;
        $l1 = $l1->next;
    }

    while ($l2) {
        if (in_array($l2, $hash,true))
            return $l2;

        $l2 = $l2->next;
    }
    return null;
}
