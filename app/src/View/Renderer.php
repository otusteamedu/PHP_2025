<?php
declare(strict_types=1);

namespace App\View;

final class Renderer
{
    public function renderForm(string $serverAddr, string $lastCheck): string
    {
        return '<!doctype html>' .
            '<html lang="ru">' .
            '<head>' .
            '    <meta charset="utf-8">' .
            '    <title>Проверка скобок</title>' .
            '</head>' .
            '<body>' .
            '<form method="post" action="/">' .
            '    <label>' .
            '        Строка со скобками:' .
            '        <input type="text" name="string" style="width: 600px" value="">' .
            '    </label>' .
            '    <button type="submit">Проверить</button>' .
            '</form>' .
            '<div>' .
            '  Сервер: ' . htmlspecialchars($serverAddr, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>' .
            '  Последняя проверка: ' . htmlspecialchars($lastCheck, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') .
            '</div>' .
            '</body>' .
            '</html>';
    }
}
