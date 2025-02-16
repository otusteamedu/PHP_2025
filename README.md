Сформирован composer.json с подключенным пакетом

Пример использования после composer install:

require_once 'vendor/autoload.php';

use \Shoronovivan\OtusPackage\Rounder;

echo Rounder::roundNumber(12.43, 2);
