<?php

declare(strict_types=1);

namespace App;

use App\Components\IdentityMap;
use App\Database\Config;
use App\Database\PdoBuilder;
use App\Entities\Player;
use App\Entities\Team;
use App\Helpers\PlayerHelper;
use App\Helpers\TeamHelper;
use App\Services\PlayerService;
use App\Services\TeamService;
use PDO;
use Throwable;

/**
 * Class Application
 * @package App
 */
class Application
{
    /**
     * @var Application
     */
    public static Application $app;
    /**
     * @var array
     */
    private array $config;
    /**
     * @var PDO
     */
    private PDO $pdo;
    /**
     * @var TeamService
     */
    private TeamService $teamService;
    /**
     * @var PlayerService
     */
    private PlayerService $playerService;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = $config;

        $pdoConfig = new Config($this->config);
        $this->pdo = PdoBuilder::create($pdoConfig);

        $this->teamService = new TeamService($this->pdo);
        $this->playerService = new PlayerService($this->pdo);
    }

    /**
     * @return void
     */
    public function init(): void
    {
        $ddl = file_get_contents('./db/ddl.sql');
        $this->pdo->exec($ddl);
    }

    /**
     * @return int
     */
    public function run(): int
    {
        try {
            // Создание тестовых команд
            $this->log('Создаем команды...');
            foreach ($this->teamService->getTeamsDataList() as $teamData) {
                $createdTeam = $this->teamService->create(Team::create($teamData));
                $this->log("Команда {$createdTeam->getName()} успешно добавлена, ID = {$createdTeam->getId()}");
            }
            $this->teamService->printAll();

            // Создание тестовых игроков
            $this->log(PHP_EOL . 'Создаем игроков...');
            foreach ($this->playerService->getPlayersDataList() as $playerData) {
                $createdPlayer = $this->playerService->create(Player::create($playerData));
                $this->log("Игрок {$createdPlayer->getName()} успешно добавлен, ID = {$createdPlayer->getId()}");
            }
            $this->playerService->printAll();

            // Изменение команды            
            $updatedTeam = $this->teamService->findById(2);

            if ($updatedTeam) {
                $this->log(PHP_EOL . "Изменяем команду {$updatedTeam->getName()}...");
                $updatedTeam->setName('Реал Мадрид');
                $this->teamService->update($updatedTeam);
                $this->log("Команда {$updatedTeam->getName()} успешно изменена");
                TeamHelper::printTeams([$updatedTeam]);
            } else {
                $this->log("Команда с ID {$updatedTeam->getId()} не найдена.");
            }

            // Поиск команды по ID
            $id = 1;
            $this->log(PHP_EOL . "Поиск команды с ID $id...");
            IdentityMap::deleteByClassAndId(Team::class, $id);
            $foundTeam = $this->teamService->findById(1);

            if ($foundTeam) {
                $this->log("Команда с ID {$foundTeam->getId()} найдена");
                TeamHelper::printTeams([$foundTeam]);

                // Проверка Identity Map для команд
                $this->log(PHP_EOL . 'Проверяем работу Identity Map для команд...');
                if ($this->teamService->testIdentityMap($foundTeam)) {
                    $this->log('Объекты команд равны => вторая сущность вернулась из Identity Map');
                } else {
                    $this->log('Объекты команд не равны => вторая сущность вернулась из БД');
                }
            } else {
                $this->log("Команда с ID {$foundTeam->getId()} не найдена.");
            }

            // Удаление команды
            $deletedTeam = $this->teamService->findById(3);

            if ($deletedTeam) {
                $this->log(PHP_EOL . "Удаляем команду {$deletedTeam->getName()}...");
                $this->teamService->delete($deletedTeam);
                $this->log("Команда {$deletedTeam->getName()} успешно удалена");
                $this->teamService->printAll();
            } else {
                $this->log("Команда с ID {$deletedTeam->getId()} не найдена.");
            }

            // Изменение игрока
            $updatedPlayer = $this->playerService->findById(4);

            if ($updatedPlayer) {
                $this->log(PHP_EOL . "Изменяем игрока {$updatedPlayer->getName()}...");
                $updatedPlayer->setName('Винисиус');
                $this->playerService->update($updatedPlayer);
                $this->log("Игрок {$updatedPlayer->getName()} успешно изменен");
                PlayerHelper::printPlayers([$updatedPlayer]);
            } else {
                $this->log("Игрок с ID {$updatedPlayer->getId()} не найден.");
            }

            // Поиск игрока по ID
            $id = 1;
            $this->log(PHP_EOL . "Поиск игрока с ID $id...");
            IdentityMap::deleteByClassAndId(PlayerHelper::class, $id);
            $foundPlayer = $this->playerService->findById($id);

            if ($foundPlayer) {
                $this->log("Игрок с ID {$foundPlayer->getId()} найден");
                PlayerHelper::printPlayers([$foundPlayer]);

                // Проверка Identity Map для игроков
                $this->log(PHP_EOL . 'Проверяем работу Identity Map для игроков...');
                if ($this->playerService->testIdentityMap($foundPlayer)) {
                    $this->log('Объекты игроков равны => вторая сущность вернулась из Identity Map');
                } else {
                    $this->log('Объекты игроков не равны => вторая сущность вернулась из БД');
                }
            } else {
                $this->log("Игрок с ID {$foundPlayer->getId()} не найден.");
            }

            // Удаление игрока
            $deletedPlayer = $this->playerService->findById(7);

            if ($deletedPlayer) {
                $this->log(PHP_EOL . "Удаляем игрока {$deletedPlayer->getName()}...");
                $this->playerService->delete($deletedPlayer);
                $this->log("Игрок {$deletedPlayer->getName()} успешно удален");
                $this->playerService->printAll();
            } else {
                $this->log("Игрок с ID {$deletedPlayer->getId()} не найден.");
            }

        } catch (Throwable $e) {
            $this->log('Error: ' . $e->getMessage());
        } finally {
            return 0;
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * @param string $message
     * @return void
     */
    public function log(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
