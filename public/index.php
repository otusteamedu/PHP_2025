<?php declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';

use App\Tasks\Controllers\TaskController;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = AppFactory::create();

// CORS Middleware
$app->add(function (Request $request, $handler): Response {
	$response = $handler->handle($request);

	return $response
		->withHeader('Access-Control-Allow-Origin', '*')
		->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
		->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// OPTIONS Pre-flight requests
$app->options('/{routes:.*}', function (Request $request, Response $response) {
	return $response;
});

// Middleware для парсинга JSON
$app->addBodyParsingMiddleware();

// Включим обработку ошибок
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Маршруты
$taskController = new TaskController();

// Главная страница
$app->get('/', function (Request $request, Response $response) {
	$response->getBody()->write(json_encode([
		'message' => 'Task Queue API is running',
		'version' => '1.0.0',
		'endpoints' => [
			'POST /tasks' => 'Create new task',
			'GET /tasks/{id}' => 'Get task status',
			'GET /docs' => 'API documentation',
			'GET /api-docs' => 'Swagger JSON'
		]
	]));
	return $response->withHeader('Content-Type', 'application/json');
});

// API маршруты
$app->post('/tasks', [$taskController, 'createTask']);
$app->get('/tasks/{id}', [$taskController, 'getTask']);

// Swagger UI
$app->get('/docs', function (Request $request, Response $response) {
	$html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <title>Task Queue API Documentation</title>
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css">
        <style>
            html { box-sizing: border-box; overflow-y: scroll; }
            *, *:before, *:after { box-sizing: inherit; }
            body { margin: 0; background: #fafafa; }
            .swagger-ui .topbar { display: none; }
        </style>
    </head>
    <body>
        <div id="swagger-ui"></div>
        <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
        <script>
            SwaggerUIBundle({
                url: '/swagger.yaml',
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.presets.standalone
                ],
                layout: "BaseLayout",
                deepLinking: true,
                showExtensions: true,
                showCommonExtensions: true
            });
        </script>
    </body>
    </html>
    HTML;

	$response->getBody()->write($html);
	return $response->withHeader('Content-Type', 'text/html');
});

$app->run();