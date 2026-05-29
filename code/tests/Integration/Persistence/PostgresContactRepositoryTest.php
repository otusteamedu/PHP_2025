<?php

declare(strict_types=1);

namespace MkdBot\Tests\Integration\Persistence;

use MkdBot\Domain\Enum\ContactType;
use MkdBot\Domain\Interface\ContactRepositoryInterface;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Infrastructure\Persistence\PostgresContactRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционный тест PostgresContactRepository
 * Требует запущенную БД Postgres — пропускается если БД недоступна
 *
 * Примечание: контакты загружаются через сид-миграцию 008_seed_contacts.sql.
 * Тесты проверяют наличие предзагруженных данных.
 */
class PostgresContactRepositoryTest extends TestCase
{
    private ?ContactRepositoryInterface $repository = null;
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        $host = getenv('PG_HOST') ?: 'postgres';
        $port = getenv('PG_PORT') ?: '5432';
        $db = getenv('PG_DB') ?: 'mkd_bot';
        $user = getenv('PG_USER') ?: 'mkd_user';
        $password = getenv('PG_PASSWORD') ?: 'secret';

        try {
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $this->repository = new PostgresContactRepository(
                new class ($this->pdo) implements DatabaseConnectionInterface {
                    public function __construct(private PDO $pdo)
                    {
                    }
                    public function getConnection(): PDO
                    {
                        return $this->pdo;
                    }
                    public function isAvailable(): bool
                    {
                        return true;
                    }
                    public function reconnect(): PDO
                    {
                        return $this->getConnection();
                    }
                    public function ensureConnection(): PDO
                    {
                        return $this->getConnection();
                    }
                },
            );
        } catch (PDOException $e) {
            $this->markTestSkipped('Postgres недоступна: ' . $e->getMessage());
        }
    }

    #[Test]
    public function testFindByTypeUkReturnsUkContacts(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $contacts = $this->repository->findByType(ContactType::Uk);

        $this->assertNotEmpty($contacts, 'Должен быть хотя бы один контакт УК');
        // Согласно сиду 008: одна запись УК
        foreach ($contacts as $contact) {
            $this->assertEquals(ContactType::Uk, $contact->getType());
        }
    }

    #[Test]
    public function testFindByTypeCouncilReturnsCouncilContacts(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $contacts = $this->repository->findByType(ContactType::Council);

        $this->assertNotEmpty($contacts, 'Должен быть хотя бы один контакт совета дома');
        // Согласно сиду 008: три записи совета дома
        $this->assertCount(3, $contacts, 'Должно быть 3 контакта совета дома');
        foreach ($contacts as $contact) {
            $this->assertEquals(ContactType::Council, $contact->getType());
        }
    }

    #[Test]
    public function testFindByTypeNonExistentReturnsEmptyArray(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        // После конвертации type в ENUM, несуществующее значение вызовет ошибку БД.
        // Проверяем, что валидный тип без записей возвращает пустой массив.
        // Удаляем все контакты типа council, проверяем, затем вставляем обратно.
        $this->pdo->prepare("DELETE FROM contacts WHERE type = 'council'")->execute();

        try {
            $contacts = $this->repository->findByType(ContactType::Council);

            $this->assertIsArray($contacts);
            $this->assertEmpty($contacts, 'Тип без записей должен возвращать пустой массив');
        } finally {
            // Восстанавливаем сид-данные
            $this->pdo->exec(
                "INSERT INTO contacts (type, name, role, phone, email, description, sort)
                VALUES
                ('council', 'Иванов Иван Иванович', 'Председатель совета дома', '+7 (234) 567-89-01', 'ivanov@mkd.example.com', 'Организация работы совета дома, взаимодействие с УК', 1),
                ('council', 'Петрова Мария Сергеевна', 'Заместитель председателя', '+7 (345) 678-90-12', 'petrova@mkd.example.com', 'Заместитель председателя совета дома, вопросы благоустройства', 2),
                ('council', 'Сидоров Алексей Петрович', 'Член совета дома', '+7 (456) 789-01-23', 'sidorov@mkd.example.com', 'Ответственный за вопросы безопасности и парковки', 3)",
            );
        }
    }

    #[Test]
    public function testContactDataStructureFields(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $contacts = $this->repository->findByType(ContactType::Uk);

        $this->assertNotEmpty($contacts, 'Нужен хотя бы один контакт УК для проверки структуры');

        $ukContact = $contacts[0];

        // Проверяем наличие всех полей согласно миграции 004
        $this->assertNotNull($ukContact->getId(), 'id должен быть заполнен');
        $this->assertEquals(ContactType::Uk, $ukContact->getType(), 'type = uk');
        $this->assertNotEmpty($ukContact->getName(), 'name не должен быть пустым');
        $this->assertNotEmpty($ukContact->getRole(), 'role не должен быть пустым');
        $this->assertNotEmpty($ukContact->getPhone(), 'phone не должен быть пустым');
        $this->assertNotEmpty($ukContact->getEmail(), 'email не должен быть пустым');
        $this->assertNotEmpty($ukContact->getDescription(), 'description не должен быть пустым');

        // Проверяем конкретные значения из сида 008
        $this->assertEquals('ООО «ЖилСервис»', $ukContact->getName());
        $this->assertEquals('Управляющая компания', $ukContact->getRole());
        $this->assertEquals('+7 (123) 456-78-90', $ukContact->getPhone());
        $this->assertEquals('info@zhilservice.ru', $ukContact->getEmail());
    }

    #[Test]
    public function testCouncilDataStructure(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $contacts = $this->repository->findByType(ContactType::Council);

        $this->assertGreaterThanOrEqual(1, count($contacts));

        $first = $contacts[0];

        // Проверяем поля первого контакта совета дома (из сида 008)
        $this->assertNotNull($first->getId());
        $this->assertEquals(ContactType::Council, $first->getType());
        $this->assertEquals('Иванов Иван Иванович', $first->getName());
        $this->assertEquals('Председатель совета дома', $first->getRole());
        $this->assertEquals('+7 (234) 567-89-01', $first->getPhone());
        $this->assertEquals('ivanov@mkd.example.com', $first->getEmail());
    }

    #[Test]
    public function testSortBySort(): void
    {
        if ($this->repository === null) {
            $this->markTestSkipped('Репозиторий не инициализирован');
        }

        $contacts = $this->repository->findByType(ContactType::Council);

        $this->assertGreaterThanOrEqual(2, count($contacts), 'Нужно минимум 2 контакта для проверки сортировки');

        // Контакты должны быть отсортированы по sort (ASC)
        $count = count($contacts);
        for ($i = 1; $i < $count; $i++) {
            $this->assertGreaterThanOrEqual(
                $contacts[$i - 1]->getSort(),
                $contacts[$i]->getSort(),
                'Контакты должны быть отсортированы по sort по возрастанию',
            );
        }
    }
}
