<?php

declare(strict_types=1);

namespace SergeyGolovanov\Php2025Hw7\Domain;

class Node
{
	public int $val = 0;
	public mixed $next = null;

	public function __construct($val = 0, $next = null)
	{
		$this->val = $val;
		$this->next = $next;
	}
}