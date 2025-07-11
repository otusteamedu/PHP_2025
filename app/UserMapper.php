<?php declare(strict_types=1);

namespace DMapper;

use PDO;

class UserMapper
{
	private $pdo;
	private $identityMap;

	public function __construct(PDO $pdo)
	{
		if ($pdo === null) {
			throw new \InvalidArgumentException('PDO object cannot be null');
		}

		$this->pdo = $pdo;
		$this->identityMap = new IdentityMap();
	}

	// Найти пользователя по ID
	public function findById($id): ?User
	{
		// Проверяем Identity Map перед запросом к БД
		if ($this->identityMap->has(User::class, $id))
		{
			return $this->identityMap->get(User::class, $id);
		}

		$stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row)
		{
			return null;
		}

		return $this->createUserFromRow($row);
	}

	// Получить всех пользователей
	public function findAll(): array
	{
		$stmt = $this->pdo->query("SELECT * FROM users");
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$users = [];
		foreach ($rows as $row)
		{
			// Используем Identity Map для избежания дублирования
			if (!$this->identityMap->has(User::class, $row['id']))
			{
				$user = $this->createUserFromRow($row);
				$this->identityMap->add($user);
			}
			$users[] = $this->identityMap->get(User::class, $row['id']);
		}

		return $users;
	}

	// Сохранить пользователя (вставка или обновление)
	public function save(User $user): void
	{
		if ($user === null)
		{
			throw new \InvalidArgumentException('User object cannot be null');
		}

		if ($user->getId() === null)
		{
			$this->insert($user);
		} else {
			$this->update($user);
		}
	}

	private function insert(User $user): void
	{
		$stmt = $this->pdo->prepare(
			"INSERT INTO users (name, email, created_at) VALUES (:name, :email, :created_at)"
		);
		$stmt->execute([
			'name' => $user->getName(),
			'email' => $user->getEmail(),
			'created_at' => $user->getCreatedAt()
		]);

		$user->setId($this->pdo->lastInsertId());
		$this->identityMap->add($user);
	}

	private function update(User $user): void
	{
		$stmt = $this->pdo->prepare(
			"UPDATE users SET name = :name, email = :email WHERE id = :id"
		);
		$stmt->execute([
			'id' => $user->getId(),
			'name' => $user->getName(),
			'email' => $user->getEmail()
		]);
	}

	private function createUserFromRow(array $row): User
	{
		$user = new User(
			$row['id'],
			$row['name'],
			$row['email'],
			$row['created_at']
		);

		$this->identityMap->add($user);
		return $user;
	}
}