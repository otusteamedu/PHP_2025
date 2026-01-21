<?php

declare(strict_types=1);

namespace Otus\DataMapper\Domain\Entity;

use DateTime;

final class Product
{
    /**
     * @var int|null
     */
    public ?int $id = null {
        get {
            return $this->id;
        }
        set {
            $this->id = $value;
        }
    }

    /**
     * @param string $brand
     * @param string $title
     * @param int $price
     * @param int $capacity
     * @param bool $hidden
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     */
    public function __construct(
        public string   $brand {
            get {
                return $this->brand;
            }
            set {
                $this->brand = $value;
            }
        },
        public string   $title {
            get {
                return $this->title;
            }
            set {
                $this->title = $value;
            }
        },
        public int      $price {
            get {
                return $this->price;
            }
            set {
                $this->price = $value;
            }
        },
        public int      $capacity {
            get {
                return $this->capacity;
            }
            set {
                $this->capacity = $value;
            }
        },
        public bool     $hidden = false {
            get {
                return $this->hidden;
            }
            set {
                $this->hidden = $value;
            }
        },
        public DateTime $createdAt = new DateTime() {
            get {
                return $this->createdAt;
            }
            set {
                $this->createdAt = $value;
            }
        },
        public DateTime $updatedAt = new DateTime() {
            get {
                return $this->updatedAt;
            }
            set {
                $this->updatedAt = $value;
            }
        },
    )
    {
    }
}
