<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Models\Movie;

class MovieController
{
    private Request $request;
    private Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getOne(int $id): string
    {
        if ($id <= 0) {
            return $this->response->error(400, 'Некорректный идентификатор.');
        }

        $movie = Movie::find($id);
        if ($movie === null) {
            return $this->response->error(404, 'Фильм не найден.');
        }

        return $this->response->success($movie->toArray());
    }

    public function getAll(): string
    {
        $movies = Movie::all();
        $data = array_map(static fn(Movie $movie): array => $movie->toArray(), $movies);

        return $this->response->success($data);
    }

    public function create(): string
    {
        $body = $this->request->getBody();

        $validationError = $this->validateBody($body);
        if ($validationError !== null) {
            return $this->response->error(400, $validationError);
        }

        $movie = new Movie(
            (string)$body['title'],
            (int)$body['duration'],
            $body['description'] ?? null
        );

        $movie->save();

        return $this->response->success($movie->toArray(), 201);
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return $this->response->error(400, 'Некорректный идентификатор.');
        }

        $movie = Movie::find($id);
        if ($movie === null) {
            return $this->response->error(404, 'Фильм не найден.');
        }

        $movie->delete();

        return $this->response->success(['message' => 'Фильм удален.']);
    }

    private function validateBody(array $body): ?string
    {
        if (!isset($body['title']) || trim((string)$body['title']) === '') {
            return 'Поле title обязательно.';
        }

        if (!isset($body['duration']) || !is_numeric($body['duration'])) {
            return 'Поле duration обязательно и должно быть числом.';
        }

        return null;
    }
}
