<?php

namespace SergeyGolovanov\Php2025Hw7\Domain;

class NodeList
{
	private ?Node $head;

	public function __construct()
	{
		$this->head = null;
	}

	public function append($val = null)
	{
		$newNode = new Node($val);
		if ($this->head === null) {
			$this->head = $newNode;

			return;
		}

		$last = $this->head;
		while ($last->next !== null) {
			$last = $last->next;
		}

		$last->next = $newNode;
	}

	/**
	 * @return ?Node
	 */
	public function getHead(): ?Node
	{
		return $this->head;
	}

	/**
	 * @param Node|null $head
	 */
	public function setHead(?Node $head): void
	{
		$this->head = $head;
	}
}