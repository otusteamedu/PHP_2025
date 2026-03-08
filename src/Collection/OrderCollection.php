<?php

namespace Igor\Test\Collection;

use Igor\Test\Model\Order;
use Iterator;
use Countable;

/**
 * Коллекция заказов для массового получения данных
 * Реализует Iterator для удобного перебора
 */
class OrderCollection implements Iterator, Countable
{
    /**
     * @var Order[]
     */
    private array $orders = [];
    private int $position = 0;

    /**
     * Конструктор
     *
     * @param Order[] $orders Массив заказов
     */
    public function __construct(array $orders = [])
    {
        $this->orders = $orders;
        $this->position = 0;
    }

    /**
     * Добавление заказа в коллекцию
     *
     * @param Order $order
     */
    public function add(Order $order): void
    {
        $this->orders[] = $order;
    }

    /**
     * Получение заказа по индексу
     *
     * @param int $index
     * @return Order|null
     */
    public function get(int $index): ?Order
    {
        return $this->orders[$index] ?? null;
    }

    /**
     * Преобразование коллекции в массив
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(function (Order $order) {
            return $order->toArray();
        }, $this->orders);
    }

    /**
     * Фильтрация коллекции по условию
     *
     * @param callable $callback Функция-фильтр (Order $order): bool
     * @return OrderCollection
     */
    public function filter(callable $callback): OrderCollection
    {
        $filtered = array_filter($this->orders, $callback);
        return new OrderCollection(array_values($filtered));
    }

    /**
     * Поиск первого заказа, удовлетворяющего условию
     *
     * @param callable $callback Функция-условие (Order $order): bool
     * @return Order|null
     */
    public function find(callable $callback): ?Order
    {
        foreach ($this->orders as $order) {
            if ($callback($order)) {
                return $order;
            }
        }

        return null;
    }

    /**
     * Применение функции к каждому элементу
     *
     * @param callable $callback Функция (Order $order): mixed
     * @return array
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->orders);
    }

    // Реализация Iterator

    /**
     * Возврат к началу коллекции
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Получение текущего элемента
     *
     * @return Order|null
     */
    public function current(): ?Order
    {
        return $this->orders[$this->position] ?? null;
    }

    /**
     * Получение текущего ключа
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Переход к следующему элементу
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * Проверка валидности текущей позиции
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->orders[$this->position]);
    }

    // Реализация Countable

    /**
     * Подсчет количества элементов
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->orders);
    }

    /**
     * Проверка, пуста ли коллекция
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->orders);
    }

    /**
     * Очистка коллекции
     */
    public function clear(): void
    {
        $this->orders = [];
        $this->position = 0;
    }
}
