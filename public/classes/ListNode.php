<?php

namespace classes;

class ListNode
{
      public $val = 0;
      public $next = null;
      function __construct($val = 0, $next = null) {
          $this->val = $val;
          $this->next = $next;
      }

    function appendToListEnd(int $val) {
        if ($this->next == null) {
            $this->next = new ListNode($val);
        } else {
            $temp = $this->next;
            while ($temp->next != null) {
                $temp = $temp->next;
            }
            $temp->next = new ListNode($val);
        }
    }
}