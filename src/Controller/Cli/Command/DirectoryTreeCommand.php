<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:directory:tree')]
class DirectoryTreeCommand extends Command
{
    private const int FULL_DEPTH = 0;
    private const int DEFAULT_DEPTH_WHEN_NO_PATH = 1;

    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument('Путь к директории')] string $path = '',
        #[Argument('Макс. глубина вывода (0 = полная глубина)')] int $depth = self::FULL_DEPTH,
    ): int {
        [$path, $depth] = $this->resolveCommandParameters($path, $depth);

        // Проверяем существование пути
        if (!is_dir($path)) {
            $output->writeln('<error>Указанный путь не существует или не является директорией</error>');
            return Command::FAILURE;
        }

        // Получаем отсортированный список элементов
        $items = $this->getSortedDirectoryItems($path, 0, $depth);

        // Выводим дерево файлов
        $this->printTree($items, 0, [], [], $output);

        return Command::SUCCESS;
    }

    /**
     * Определяет значения пути и глубины вывода на основе переданных аргументов.
     * Если путь не указан, используется корень проекта, а глубина устанавливается в 1 (обзор проекта).
     * Если путь указан, но глубина не указана, то глубина остаётся 0 (полная глубина по умолчанию).
     */
    private function resolveCommandParameters(string $path, int $depth): array
    {
        // Если путь не передан — используем корень проекта и глубину 1
        if ($path === '') {
            return [
                $this->pathResolver->getProjectRoot(),
                self::DEFAULT_DEPTH_WHEN_NO_PATH,
            ];
        }

        return [$path, $depth];
    }

    /**
     * Рекурсивно получаем отсортированные элементы директории с ограничением глубины
     */
    private function getSortedDirectoryItems(
        string $path,
        int $currentDepth,
        int $maxDepth,
    ): array {
        $items = [];

        // Если достигли максимальной глубины, не обходим дальше
        if ($maxDepth > 0 && $currentDepth >= $maxDepth) {
            return $items;
        }

        // Получаем содержимое директории, сортируем
        $contents = scandir($path);

        // Разделяем на папки и файлы
        $directories = [];
        $files = [];

        foreach ($contents as $itemName) {
            // Пропускаем . и ..
            if ($itemName === '.' || $itemName === '..') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $itemName;
            $isDir = is_dir($fullPath);

            if ($isDir) {
                $directories[] = $itemName;
            } else {
                $files[] = $itemName;
            }
        }

        // Сортируем папки и файлы по отдельности
        sort($directories);
        sort($files);

        // Объединяем: сначала папки, потом файлы
        $sortedContents = array_merge($directories, $files);

        foreach ($sortedContents as $itemName) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $itemName;
            $isDir = is_dir($fullPath);
            $isExecutable = $this->isExecutable($fullPath);

            // Добавляем текущий элемент
            $items[] = [
                'name' => $itemName,
                'path' => $fullPath,
                'depth' => $currentDepth,
                'isDir' => $isDir,
                'isExecutable' => $isExecutable,
                'children' => [],
            ];

            // Если это директория, рекурсивно получаем её содержимое (если не превышена глубина)
            if ($isDir) {
                $children = $this->getSortedDirectoryItems(
                    $fullPath,
                    $currentDepth + 1,
                    $maxDepth,
                );
                $items[count($items) - 1]['children'] = $children;
            }
        }

        return $items;
    }

    /**
     * Определяем, является ли файл исполняемым (только для Linux/Unix)
     */
    private function isExecutable(string $filePath): bool
    {
        return is_file($filePath) && is_executable($filePath);
    }

    /**
     * Рекурсивно выводим дерево с правильными отступами и цветами
     */
    private function printTree(
        array $items,
        int $currentDepth,
        array $prefixes,
        array $colorPrefixes,
        OutputInterface $output,
    ): void {
        $total = count($items);

        foreach ($items as $index => $item) {
            // Определяем, последний ли это элемент в списке
            $isLast = ($index === $total - 1);

            // Строим префикс для текущего уровня (с цветами)
            $currentPrefix = '';
            for ($i = 0; $i < $currentDepth; $i++) {
                if (isset($prefixes[$i], $colorPrefixes[$i])) {
                    $currentPrefix .= $colorPrefixes[$i] . $prefixes[$i] . '</>';
                }
            }

            // Символ соединения: ├── или └──
            $connector = $isLast ? '└── ' : '├── ';

            // Иконка и цвет: папка, исполняемый файл или обычный файл
            $icon = '';
            $itemColorTag = '';

            if ($item['isDir']) {
                $icon = '📁';
                $itemColorTag = '<fg=green>';
            } elseif ($item['isExecutable']) {
                $icon = '⚡';
                $itemColorTag = '<fg=red>';
            } else {
                $icon = '📄';
                $itemColorTag = '<fg=yellow>';
            }

            // Цвет для всей строки — цвет родителя
            $lineColorTag = $currentDepth > 0
                ? $colorPrefixes[$currentDepth - 1]
                : '<fg=green>'; // для корневого уровня — зелёный

            // Формируем строку:
            // 1. Отступы + соединитель — цвет родителя
            // 2. Имя файла/папки + иконка — свой цвет
            $coloredLine = $lineColorTag . $currentPrefix . $connector . '</>' .
                $itemColorTag . $item['name'] . ' ' . $icon . '</>';

            // Выводим текущую строку через OutputInterface для корректного отображения цветов
            $output->writeln($coloredLine);

            // Если это директория с детьми, рекурсивно выводим её содержимое
            if ($item['isDir'] && !empty($item['children'])) {
                // Обновляем префиксы для следующего уровня
                $newPrefixes = $prefixes;
                $newColorPrefixes = $colorPrefixes;

                $newPrefixes[$currentDepth] = $isLast ? '    ' : '│   ';
                $newColorPrefixes[$currentDepth] = $lineColorTag; // наследуем цвет строки

                // Рекурсивный вызов для детей
                $this->printTree(
                    $item['children'],
                    $currentDepth + 1,
                    $newPrefixes,
                    $newColorPrefixes,
                    $output,
                );
            }
        }
    }
}
