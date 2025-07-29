<?

declare(strict_types=1);

require_once __DIR__ . '/App/Handler.php';

$handler = new Handler();

$handler->handle(
    $_SERVER['REQUEST_METHOD'],
    file_get_contents('php://input')
);

