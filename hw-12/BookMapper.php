<?php

declare(strict_types=1);

class BookMapper
{
    private array $identityMap = [];

    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAll(): Books
    {
        $stmt = $this->pdo->query('SELECT id, title, author FROM books');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $books = array_map(fn($row) => new Book(
            id: (int)$row['id'],
            title: $row['title'],
            author: $row['author']
        ), $rows);

        return new Books($books);
    }

    public function findById(int $id): ?Book
    {
        if (isset($this->identityMap[$id])) {
            return $this->identityMap[$id];
        }

        $stmt = $this->pdo->prepare('SELECT id, title, author FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $book = new Book(
            id: (int)$row['id'],
            title: $row['title'],
            author: $row['author']
        );

        $this->identityMap[$id] = $book;

        return $book;
    }

    public function findByAuthor(string $author): Books
    {
        $stmt = $this->pdo->prepare('SELECT id, title, author FROM books WHERE author = :author');
        $stmt->execute(['author' => $author]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $books = array_map(fn($row) => new Book(
            id: (int)$row['id'],
            title: $row['title'],
            author: $row['author']
        ), $rows);

        return new Books($books);
    }

    public function insert(string $title, string $author): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO books (title, author) VALUES (:title, :author)');
        $stmt->execute(['title' => $title, 'author' => $author]);
    }

    public function update(int $id, string $title, string $author): void
    {
        $stmt = $this->pdo->prepare('UPDATE books SET title=:title, author=:author WHERE id=:id');
        $stmt->execute(['id' => $id, 'title' => $title, 'author' => $author]);
    }
}
