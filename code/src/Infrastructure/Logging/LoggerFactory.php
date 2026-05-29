<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Logging;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Psr\Log\LoggerInterface;

/**
 * Фабрика логгера — создаёт настроенный экземпляр Monolog
 * с ResilientRotatingFileHandler (не бросает исключения при ошибках записи)
 */
class LoggerFactory
{
    /**
     * Создаёт логгер с ротируемым файловым обработчиком и процессорами контекста
     *
     * @param string $name Имя логгера
     * @param string $path Путь к файлу логов
     * @param string $levelName Уровень логирования (DEBUG, INFO, ERROR)
     * @param int $maxFiles Максимальное количество файлов ротации (по умолчанию 7 дней)
     * @param int $filePermission Права доступа к файлу логов (по умолчанию 0664)
     */
    public function create(
        string $name,
        string $path,
        string $levelName = 'DEBUG',
        int $maxFiles = 7,
        int $filePermission = 0664,
    ): LoggerInterface {
        $logger = new Logger($name);
        $level = Level::fromName($levelName);

        // Ротируемый файловый обработчик (не бросает исключения при ошибках записи)
        $logger->pushHandler(new ResilientRotatingFileHandler($path, $maxFiles, $level, true, $filePermission));

        // Процессоры для контекста
        $logger->pushProcessor(new WebProcessor());
        $logger->pushProcessor(new ProcessIdProcessor());
        $logger->pushProcessor(new IntrospectionProcessor($level));

        return $logger;
    }
}
