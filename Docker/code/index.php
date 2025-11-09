<?
declare(strict_types=1);

$obMysqli = new mysqli('mysql', 'adminushka', 'mysql123', 'db123', 3306);
if ($obMysqli->connect_error) {
    die('Error connecting to MySQL: ' . $obMysqli->connect_error);
}
echo 'Connection to MySQL server established!';

$obMysqli->close();

echo "<br>Hello, Otus!";
phpinfo();