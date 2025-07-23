<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Exceptions\EmptyStringException;
use App\Exceptions\InvalidBracketsException;
use App\Http\Request;
use App\Http\Response;
use App\Services\BracketsValidator;

$request = new Request();
$response = new Response();
$validator = new BracketsValidator();

try {
    $response->addHeader('Content-Type', 'text/plain');

    if ($request->getMethod() !== 'POST') {
        $response->setStatusCode(405)
            ->setContent('Method Not Allowed')
            ->send();
        exit;
    }

    $inputString = (string)$request->getPostParam('string', '');
    $isValid = $validator->validate($inputString);

    if (!$isValid) {
        $response->setStatusCode(400)
            ->setContent('Failed: The brackets are not balanced')
            ->send();
        exit;
    }
    $response->setStatusCode(200)
        ->setContent('OK: The brackets are balanced')
        ->send();

} catch (EmptyStringException|InvalidBracketsException $e) {
    $response->setStatusCode(400)
        ->setContent('Bad Request: ' . $e->getMessage())
        ->send();
} catch (\Throwable $e) {
    $response->setStatusCode(500)
        ->setContent('Internal Server Error')
        ->send();
}