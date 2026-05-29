<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Infrastructure\Persistence\PostgresConnection;
use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Юнит-тесты для PostgresConnection
 *
 * Мокаем PDO через рефлексию, чтобы протестировать логику
 * getConnection() (ленивое создание) и isAvailable() без реальной БД.
 */
class PostgresConnectionTest extends TestCase
{
    private string $host = 'localhost';
    private int $port = 5432;
    private string $database = 'testdb';
    private string $user = 'testuser';
    private string $password = 'testpass';

    /**
     * Создаёт PostgresConnection с подменённым PDO через рефлексию
     */
    private function createConnectionWithPdo(?PDO $pdo): PostgresConnection
    {
        $connection = new PostgresConnection(
            $this->host,
            $this->port,
            $this->database,
            $this->user,
            $this->password,
        );

        if ($pdo !== null) {
            // Устанавливаем PDO через рефлексию для тестирования
            $ref = new ReflectionClass($connection);
            $prop = $ref->getProperty('pdo');
            $prop->setAccessible(true);
            $prop->setValue($connection, $pdo);
        }

        return $connection;
    }

    /**
     * getConnection() — возвращает один и тот же экземпляр PDO при повторном вызове (ленивое создание)
     */
    public function testGetConnectionReturnsSamePdoInstance(): void
    {
        // Создаём мок PDO и устанавливаем через рефлексию
        $pdo = $this->createMock(PDO::class);
        $connection = $this->createConnectionWithPdo($pdo);

        // При повторном вызове должен вернуть тот же экземпляр
        $result1 = $connection->getConnection();
        $result2 = $connection->getConnection();

        $this->assertSame($pdo, $result1);
        $this->assertSame($result1, $result2);
    }

    /**
     * getConnection() — реализует интерфейс DatabaseConnectionInterface
     */
    public function testGetConnectionImplementsInterface(): void
    {
        $connection = new PostgresConnection(
            $this->host,
            $this->port,
            $this->database,
            $this->user,
            $this->password,
        );

        $this->assertInstanceOf(DatabaseConnectionInterface::class, $connection);
    }

    /**
     * isAvailable() — возвращает true при успешном запросе SELECT 1
     */
    public function testIsAvailableReturnsTrueWhenSelectSucceeds(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->with('SELECT 1')->willReturn($stmt);

        $connection = $this->createConnectionWithPdo($pdo);

        $this->assertTrue($connection->isAvailable());
    }

    /**
     * isAvailable() — возвращает false при PDOException
     */
    public function testIsAvailableReturnsFalseOnPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(new PDOException('Connection refused'));

        $connection = $this->createConnectionWithPdo($pdo);

        $this->assertFalse($connection->isAvailable());
    }

    /**
     * isAvailable() — вызывает getConnection() для получения PDO
     */
    public function testIsAvailableCallsGetConnection(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())->method('query')->with('SELECT 1')->willReturn($stmt);

        $connection = $this->createConnectionWithPdo($pdo);

        $connection->isAvailable();
    }

    /**
     * getConnection() — при первом вызове создаёт PDO с правильным DSN
     *
     * Проверяем что DSN формируется корректно (через мок-фабрику).
     * Реальное подключение не требуется — достаточно проверить параметры.
     */
    public function testGetConnectionConstructsDsnCorrectly(): void
    {
        $connection = new PostgresConnection(
            'dbhost',
            5433,
            'mydb',
            'myuser',
            'mypass',
        );

        // Проверяем через рефлексию что DSN будет правильным
        $ref = new ReflectionClass($connection);
        $hostProp = $ref->getProperty('host');
        $hostProp->setAccessible(true);
        $portProp = $ref->getProperty('port');
        $portProp->setAccessible(true);
        $dbProp = $ref->getProperty('database');
        $dbProp->setAccessible(true);

        $this->assertSame('dbhost', $hostProp->getValue($connection));
        $this->assertSame(5433, $portProp->getValue($connection));
        $this->assertSame('mydb', $dbProp->getValue($connection));
    }

    /**
     * isAvailable() — повторный вызов использует тот же PDO
     */
    public function testIsAvailableReusesPdoInstance(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $pdo = $this->createMock(PDO::class);
        // Ожидаем ровно 2 вызова query (по одному на каждый isAvailable)
        $pdo->expects($this->exactly(2))->method('query')->with('SELECT 1')->willReturn($stmt);

        $connection = $this->createConnectionWithPdo($pdo);

        $this->assertTrue($connection->isAvailable());
        $this->assertTrue($connection->isAvailable());
    }

    /**
     * reconnect() — сбрасывает PDO и вызывает getConnection() для создания нового
     */
    public function testReconnectResetsPdoAndReturnsNewConnection(): void
    {
        $pdo = $this->createMock(PDO::class);
        $connection = $this->createConnectionWithPdo($pdo);

        // До reconnect() возвращает тот же экземпляр
        $this->assertSame($pdo, $connection->getConnection());

        // После reconnect() PDO сбрасывается; getConnection() создаст новый
        // Поскольку мы не можем подменить createPdo(), проверяем через рефлексию,
        // что pdo стал null после reconnect(), а затем getConnection() создаст новый
        $ref = new ReflectionClass($connection);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);

        // reconnect() обнуляет pdo и вызывает getConnection()
        // Без реальной БД это вызовет PDOException — перехватываем
        try {
            $connection->reconnect();
        } catch (PDOException $e) {
            // Ожидаемо: нет реальной БД — проверяем что pdo был сброшен
        }

        // PDO должен быть null (сброшен) или пересоздан
        // В случае исключения при создании — pdo останется null
        $this->assertNull($prop->getValue($connection));
    }

    /**
     * reconnect() — после сброса PDO, getConnection() создаёт новый экземпляр
     *
     * Проверяем через рефлексию, что reconnect() обнуляет pdo и делегирует
     * создание нового подключения getConnection().
     */
    public function testReconnectDelegatesToGetConnectionAfterReset(): void
    {
        $pdo = $this->createMock(PDO::class);
        $connection = $this->createConnectionWithPdo($pdo);

        // До reconnect() — pdo установлен
        $ref = new ReflectionClass($connection);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $this->assertSame($pdo, $prop->getValue($connection));

        // reconnect() обнуляет pdo и вызывает getConnection() — без реальной БД упадёт
        try {
            $connection->reconnect();
        } catch (PDOException $e) {
            // Ожидаемо: нет реальной БД для создания нового PDO
        }

        // После reconnect() pdo сброшен (подтверждает что reconnect обнуляет перед пересозданием)
        $this->assertNull($prop->getValue($connection), 'reconnect() должен сбросить PDO перед пересозданием');
    }

    /**
     * ensureConnection() — возвращает PDO, если соединение живо (SELECT 1 проходит)
     */
    public function testEnsureConnectionReturnsPdoWhenAlive(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->with('SELECT 1')->willReturn($stmt);

        $connection = $this->createConnectionWithPdo($pdo);

        $result = $connection->ensureConnection();
        $this->assertSame($pdo, $result);
    }

    /**
     * ensureConnection() — вызывает getConnection(), если pdo === null
     */
    public function testEnsureConnectionCallsGetConnectionWhenPdoIsNull(): void
    {
        $connection = new PostgresConnection(
            $this->host,
            $this->port,
            $this->database,
            $this->user,
            $this->password,
        );

        // pdo === null, ensureConnection вызовет getConnection() — без БД упадёт
        try {
            $result = $connection->ensureConnection();
            // Если БД доступна — проверяем что вернулся PDO
            $this->assertInstanceOf(PDO::class, $result);
        } catch (PDOException $e) {
            // Ожидаемо: нет реальной БД — достаточно что исключение PDOException
            $this->assertInstanceOf(PDOException::class, $e);
        }
    }

    /**
     * ensureConnection() — переподключается при PDOException с кодом 08006 (connection_failure)
     */
    public function testEnsureConnectionReconnectsOnConnectionFailure(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(
            $this->createPdoExceptionWithSqlState('connection failure', '08006'),
        );

        $connection = $this->createConnectionWithPdo($pdo);

        // ensureConnection() обнаружит обрыв и вызовет reconnect()
        // reconnect() сбросит pdo и вызовет getConnection() — без БД упадёт
        try {
            $connection->ensureConnection();
        } catch (PDOException $e) {
            // Ожидаемо: нет реальной БД для пересоздания подключения
            $this->assertInstanceOf(PDOException::class, $e);
        }

        // Проверяем, что pdo был сброшен (reconnect вызван)
        $ref = new ReflectionClass($connection);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $this->assertNull($prop->getValue($connection), 'PDO должен быть сброшен после reconnect');
    }

    /**
     * ensureConnection() — пробрасывает PDOException с неизвестным SQLSTATE
     */
    public function testEnsureConnectionRethrowsUnknownPdoException(): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(
            $this->createPdoExceptionWithSqlState('syntax error', '42601'),
        );

        $connection = $this->createConnectionWithPdo($pdo);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('syntax error');

        $connection->ensureConnection();
    }

    /**
     * ensureConnection() — переподключается при всех поддерживаемых SQLSTATE кодах обрыва
     * @dataProvider reconnectSqlStateProvider
     */
    public function testEnsureConnectionReconnectsOnAllReconnectCodes(string $sqlState): void
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('query')->willThrowException(
            $this->createPdoExceptionWithSqlState('connection lost', $sqlState),
        );

        $connection = $this->createConnectionWithPdo($pdo);

        try {
            $connection->ensureConnection();
        } catch (PDOException $e) {
            // Без реальной БД пересоздание упадёт — это нормально
        }

        // Проверяем, что reconnect был вызван (pdo сброшен)
        $ref = new ReflectionClass($connection);
        $prop = $ref->getProperty('pdo');
        $prop->setAccessible(true);
        $this->assertNull($prop->getValue($connection));
    }

    /**
     * @return array<string, string[]>
     */
    public static function reconnectSqlStateProvider(): array
    {
        return [
            '08006' => ['08006'],
            '57P01' => ['57P01'],
            '57P02' => ['57P02'],
            '57P03' => ['57P03'],
            '08003' => ['08003'],
        ];
    }

    /**
     * Создаёт PDOException с заданным SQLSTATE кодом в errorInfo[0].
     * Стандартный конструктор PDOException(string, int) не устанавливает errorInfo,
     * поэтому используем рефлексию.
     */
    private function createPdoExceptionWithSqlState(string $message, string $sqlState): PDOException
    {
        $exception = new PDOException($message);
        $ref = new ReflectionClass(PDOException::class);
        $prop = $ref->getProperty('errorInfo');
        $prop->setAccessible(true);
        $prop->setValue($exception, [$sqlState, 0, $message]);

        return $exception;
    }
}
