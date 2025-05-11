<?php
declare(strict_types=1);

require __DIR__ . '/../../../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\Application;
use Zibrov\OtusPhp2025\Database\Mysql\Config as MysqlConfig;

?>

<p><-- <a href="/mysql/">назад</a></p>

<p>Создать таблицу в Mysql</p>

<?php
$obMysqlConfig = new MysqlConfig();
$app = new Application($obMysqlConfig);
$app->deleteTable();
?>

<p>Tаблица в Mysql удалена успешна!</p>
