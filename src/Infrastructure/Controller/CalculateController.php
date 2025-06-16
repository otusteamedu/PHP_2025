<?php

namespace App\Infrastructure\Controller;

use App\Application\CalculateBuilder;
use App\Application\DTO\AllowedOperations;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CalculateController extends AbstractController
{
    public function index(Request $request): Response
    {
        $calculate = (new CalculateBuilder())
            ->setOperation($request->get('operation', 'plus'))
            ->setNumber1($request->get('number1', 0))
            ->setNumber2($request->get('number2', 0))
            ->getInstance();

        return $this->render(
            'calculate.html.twig',
            [
                'result' => $calculate->getResult(),
                'number1' => $calculate->getNumber1(),
                'number2' => $calculate->getNumber2(),
                'operation' => $calculate->getOperation(),
                'operations' => (new AllowedOperations())->getAllowedOperations(),
            ]
        );
    }
}
