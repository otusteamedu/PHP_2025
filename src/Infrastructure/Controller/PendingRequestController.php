<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\RabbitMQ\RabbitMQBank;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PendingRequestController extends AbstractController
{
    public function index(Request $request): Response
    {
        return $this->render('formPendingRequest.html.twig');
    }
    public function createRequest(RabbitMQBank $rabbitMQBank): RedirectResponse
    {
        $rabbitMQBank->publish();
        $this->addFlash('notice', 'The request to the bank has been accepted for processing.');

        return $this->redirectToRoute('form', [], Response::HTTP_SEE_OTHER);
    }
}
