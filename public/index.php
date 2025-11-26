<?php
declare(strict_types=1);

use App\Controller\VerificationController;
use App\Exception\MethodNotAllowedException;
use App\Exception\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Service\VerificationService;


require __DIR__ . '/../vendor/autoload.php';

session_start();

$request = Request::fromGlobals();
$controller = new VerificationController(new VerificationService());

try {
    $response = $controller->handle($request);
} catch (MethodNotAllowedException $e) {
    $response = new Response(405, $e->getMessage());
} catch (ValidationException $e) {
    $response = new Response(400, $e->getMessage());
} catch (\Throwable $e) {
    $response = new Response(500, 'Internal server error');
}

$response->send();
