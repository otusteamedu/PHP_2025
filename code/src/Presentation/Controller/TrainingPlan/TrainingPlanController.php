<?php

declare(strict_types=1);

namespace App\Presentation\Controller\TrainingPlan;

use App\Application\UseCase;
use App\Presentation\Validation\TrainingPlanValidator;
use App\Presentation\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class TrainingPlanController
{
    /**
     * @param TrainingPlanValidator $validator Валидатор данных плана тренировок.
     * @param UseCase\TrainingPlan\CreateTrainingPlanUseCase $createTrainingPlanUseCase Use case для создания плана тренировок.
     * @param UseCase\TrainingPlan\GetTrainingPlanWithExercisesByDay $getTrainingPlanWithExercisesByDay
     * @param UseCase\TrainingPlan\GetAllTrainingPlansUseCase $getAllTrainingPlansUseCase
     */
    public function __construct(
        private TrainingPlanValidator $validator,
        private UseCase\TrainingPlan\CreateTrainingPlanUseCase $createTrainingPlanUseCase,
        private UseCase\TrainingPlan\GetTrainingPlanWithExercisesByDay $getTrainingPlanWithExercisesByDay,
        private UseCase\TrainingPlan\GetAllTrainingPlansUseCase $getAllTrainingPlansUseCase
    ) {
    }

    /**
     * Создает новый план тренировок.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @return Response Ответ с результатом операции.
     * @throws \JsonException
     */
    public function createTrainingPlan(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        try {
            $this->validator->validate($data);
        } catch (ValidationException $exception) {
            $response->getBody()->write(json_encode(['errors' => $exception->getErrors()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $trainingPlan = $this->createTrainingPlanUseCase->execute($data['name'], $data['description'], );
        } catch (\Exception $exception) {
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $responseData = [
            'message' => 'Training plan created successfully.',
            'training_plan' => [
                'id' => $trainingPlan->getId(),
                'name' => $trainingPlan->getName(),
                'description' => $trainingPlan->getDescription(),
                'created_at' => $trainingPlan->getCreatedAt()->format('Y-m-d H:i:s'),
            ]
        ];

        $response->getBody()->write(json_encode($responseData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \JsonException
     */
    public function getList(Request $request, Response $response): Response
    {
        $plans = $this->getAllTrainingPlansUseCase->execute();
        $response->getBody()->write(json_encode($plans, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Возвращает детальную информацию о плане тренировок, включая упражнения по дням.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @param array $args Аргументы маршрута, содержащие ID плана тренировок.
     * @return Response Ответ с детальной информацией о плане тренировок.
     * @throws \JsonException
     */

    public function getTrainingPlan(Request $request, Response $response, array $args): Response
    {
        $trainingPlanId = (int)$args['id'];
        $dto = ($this->getTrainingPlanWithExercisesByDay)($trainingPlanId);

        $response->getBody()->write(json_encode($dto, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
