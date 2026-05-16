<?php
declare(strict_types=1);
namespace App\Presentation\Controller\TrainingPlan;

use App\Application\UseCase\TrainingPlan\AssignTrainingPlanToUserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Domain\Exception\DomainRecordNotFoundException;

class UserController {
    public function __construct(
        private readonly AssignTrainingPlanToUserUseCase $assignTrainingPlanToUserUseCase
    ) {}

    public function attach(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $userId = (int) ($data['user_id'] ?? 0);
        $trainingPlanId = (int) ($data['training_plan_id'] ?? 0);

        try {
            $this->assignTrainingPlanToUserUseCase->execute($userId, $trainingPlanId);

            $response->getBody()->write(json_encode(['message' => 'User successfully attached to training plan']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (DomainRecordNotFoundException $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }
}
