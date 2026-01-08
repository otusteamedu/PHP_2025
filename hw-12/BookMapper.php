<?php

declare(strict_types=1);

class BookMapper
{
    private array $identityMap = [];

    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    public function findAll(int $limit = 50, int $offset = 0): Books
    {
        $stmt = $this->pdo->prepare('SELECT id, title, author FROM books LIMIT :limit OFFSET :offset');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

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

    public function update(int $id, ?string $title = null, ?string $author = null): void
    {
        $fields = [];
        $params = ['id' => $id];

        if ($title !== null) {
            $fields[] = 'title = :title';
            $params['title'] = $title;
        }

        if ($author !== null) {
            $fields[] = 'author = :author';
            $params['author'] = $author;
        }

        if (!$fields) {
            return;
        }

        $sql = 'UPDATE books SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
