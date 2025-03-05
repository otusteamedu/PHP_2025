<?php

declare(strict_types=1);

namespace App;

use App\Array\ListNode;
use App\Array\Helpers;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function run(): void
    {
        $list1 = new ListNode(next: new ListNode(1, new ListNode(2, new ListNode(4))));
        $list2 = new ListNode(next: new ListNode(1, new ListNode(3, new ListNode(4))));

        $list3 = new ListNode();
        $list4 = new ListNode();

        $list5 = new ListNode();
        $list6 = new ListNode(next: new ListNode());

        $arrContent = [
            Helpers::toStringListNode(Helpers::mergeTwoLists($list1->next, $list2->next)),
            Helpers::toStringListNode(Helpers::mergeTwoLists($list3->next, $list4->next)),
            Helpers::toStringListNode(Helpers::mergeTwoLists($list5->next, $list6->next)),
        ];

        $content = \implode('<br>', $arrContent);
        $response = new Response($content);
        $response->send();
    }
}
