<?php
declare(strict_types=1);


namespace App\Domain\Repository;

class Pager
{
    public const DEFAULT_LIMIT = 10;
    public const MAX_LIMIT = 100;
    public const DEFAULT_PAGE = 1;

    public int $total_pages = 0;
    public int $per_page;

    public function __construct(
        public int  $page,
        int         $per_page,
        public ?int $total_items = null
    )
    {
        $this->setLimit($per_page);
        $this->setTotalPages();
    }

    public static function emptySet(): self
    {
        return new self(1, 0);
    }

    public static function fromPage(?int $page, ?int $perPage): self
    {
        return new self($page ?? self::DEFAULT_PAGE, $perPage ?? self::DEFAULT_LIMIT);
    }

    public function getOffset(): int
    {
        if (1 === $this->page) {
            return 0;
        }

        return $this->page * $this->per_page - $this->per_page;
    }

    public function getLimit(): int
    {
        return $this->per_page;
    }

    private function setTotalPages(): void
    {
        if (!$this->total_items) {
            $this->total_pages = 0;
        } else {
            $this->total_pages = (int)ceil($this->total_items / $this->per_page);
        }
    }

    private function setLimit(int $per_page): void
    {
        $this->per_page = $per_page <= static::MAX_LIMIT ? $per_page : static::DEFAULT_LIMIT;
    }
}