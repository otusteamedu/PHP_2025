<?php


function getIntersectionNode($headA, $headB)
{
    $l1 = $headA;
    $l2 = $headB;
    $hash = $hash2 = [];

    while ($l1 || $l2) {
        $hash[] = $l1;
        $hash2[] = $l2;

        if (in_array($l2, $hash, true))
            return $l2;
        if (in_array($l1, $hash2, true))
            return $l1;

        if ($l1)
            $l1 = $l1->next;
        if ($l2)
            $l2 = $l2->next;
    }
    return null;
}
