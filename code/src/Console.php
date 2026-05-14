<?php

namespace App;

use App\Database\Connection;
use App\Entity\User;
use App\IdentityMap\UserIdentityMap;
use App\Mapper\UserMapper;
use Throwable;

/**
 * Консольное приложение для выполнения CRUD-команд над таблицей users.
 */
class Console
{
    private ?UserMapper $userMapper = null;

    public function run(): void
    {
        global $argv;

        $command = $argv[1] ?? 'help';

        try {
            match ($command) {
                'init' => $this->init(),
                'list' => $this->list($argv),
                'get' => $this->get($argv),
                'create' => $this->create($argv),
                'update' => $this->update($argv),
                'delete' => $this->delete($argv),
                'help' => $this->help(),
                default => $this->unknownCommand($command),
            };
        } catch (Throwable $exception) {
            echo 'Ошибка: ' . $exception->getMessage() . PHP_EOL;
        }
    }

    /**
     * Создает таблицу users и заполняет ее тестовыми данными.
     */
    private function init(): void
    {
        $sqlPath = dirname(__DIR__) . '/database/init.sql';
        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            echo 'Не удалось прочитать файл ' . $sqlPath . PHP_EOL;
            return;
        }

        Connection::getInstance()->getConnection()->exec($sql);
        (new UserIdentityMap())->clear();
        echo 'Таблица users создана и заполнена тестовыми данными.' . PHP_EOL;
    }

    private function list(array $arguments): void
    {
        $limit = $this->getOptionalPositiveIntArgument($arguments, 2, 100, 'limit');
        $offset = $this->getOptionalPositiveIntArgument($arguments, 3, 0, 'offset');
        $users = $this->getUserMapper()->findAll($limit, $offset);

        echo 'Найдено пользователей: ' . count($users) . PHP_EOL;
        echo 'limit: ' . $limit . ', offset: ' . $offset . PHP_EOL;
        echo 'id | name | phone' . PHP_EOL;
        echo '---|------|------' . PHP_EOL;

        foreach ($users as $user) {
            $this->printUser($user);
        }
    }

    private function get(array $arguments): void
    {
        $id = $this->requireIntArgument($arguments, 2, 'id');
        $user = $this->getUserMapper()->findById($id);

        if ($user === null) {
            echo 'Пользователь с id ' . $id . ' не найден.' . PHP_EOL;
            return;
        }

        $this->printUser($user);
    }

    private function create(array $arguments): void
    {
        $name = $this->requireStringArgument($arguments, 2, 'name');
        $phone = $this->getOptionalPhone($arguments, 3);
        $user = $this->getUserMapper()->create($name, $phone);

        echo 'Пользователь создан:' . PHP_EOL;
        $this->printUser($user);
    }

    private function update(array $arguments): void
    {
        $id = $this->requireIntArgument($arguments, 2, 'id');
        $name = $this->requireStringArgument($arguments, 3, 'name');
        $phone = $this->getOptionalPhone($arguments, 4);

        $this->getUserMapper()->update(new User($id, $name, $phone));
        echo 'Пользователь с id ' . $id . ' обновлен.' . PHP_EOL;
    }

    private function delete(array $arguments): void
    {
        $id = $this->requireIntArgument($arguments, 2, 'id');
        $this->getUserMapper()->delete($id);
        echo 'Пользователь с id ' . $id . ' удален.' . PHP_EOL;
    }

    private function help(): void
    {
        echo 'Доступные команды:' . PHP_EOL;
        echo '  init                       Создать таблицу users и заполнить тестовыми данными' . PHP_EOL;
        echo '  list [limit] [offset]      Показать пользователей постранично' . PHP_EOL;
        echo '  get <id>                   Показать пользователя по id' . PHP_EOL;
        echo '  create <name> [phone]      Создать пользователя' . PHP_EOL;
        echo '  update <id> <name> [phone] Обновить пользователя' . PHP_EOL;
        echo '  delete <id>                Удалить пользователя' . PHP_EOL;
        echo '  help                       Показать справку' . PHP_EOL;
    }

    private function unknownCommand(string $command): void
    {
        echo 'Неизвестная команда: ' . $command . PHP_EOL;
        $this->help();
    }

    private function printUser(User $user): void
    {
        $phone = $user->getPhone() === null ? '-' : (string) $user->getPhone();
        echo $user->getId() . ' | ' . $user->getName() . ' | ' . $phone . PHP_EOL;
    }

    private function requireStringArgument(array $arguments, int $index, string $name): string
    {
        if (!isset($arguments[$index]) || $arguments[$index] === '') {
            throw new \InvalidArgumentException('Не передан обязательный аргумент ' . $name . '.');
        }

        return $arguments[$index];
    }

    private function requireIntArgument(array $arguments, int $index, string $name): int
    {
        $value = $this->requireStringArgument($arguments, $index, $name);

        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException('Аргумент ' . $name . ' должен быть целым положительным числом.');
        }

        return (int) $value;
    }

    private function getOptionalPhone(array $arguments, int $index): ?int
    {
        if (!isset($arguments[$index]) || $arguments[$index] === '') {
            return null;
        }

        if (!ctype_digit($arguments[$index])) {
            throw new \InvalidArgumentException('Телефон должен быть целым положительным числом.');
        }

        return (int) $arguments[$index];
    }

    private function getOptionalPositiveIntArgument(array $arguments, int $index, int $default, string $name): int
    {
        if (!isset($arguments[$index]) || $arguments[$index] === '') {
            return $default;
        }

        if (!ctype_digit($arguments[$index])) {
            throw new \InvalidArgumentException('Аргумент ' . $name . ' должен быть целым положительным числом.');
        }

        return (int) $arguments[$index];
    }

    private function getUserMapper(): UserMapper
    {
        if ($this->userMapper === null) {
            $connection = Connection::getInstance()->getConnection();
            $this->userMapper = new UserMapper($connection, new UserIdentityMap());
        }

        return $this->userMapper;
    }
}
