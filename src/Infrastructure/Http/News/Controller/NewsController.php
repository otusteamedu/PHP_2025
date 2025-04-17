<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Domain\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api', name: 'api_')]
class NewsController extends AbstractController
{
    #[Route('/news', name: 'news_index', methods:['get'] )]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $news = $entityManager
            ->getRepository(News::class)
            ->findAll();

        $data = [];

        foreach ($news as $el) {
            $data[] = [
                'id' => $el->getId(),
                'title' => $el->getTitle(),
                'url' => $el->getUrl(),
                'create_date' => $el->getCreateDate(),
            ];
        }

        return $this->json($data);
    }


    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $project = new News();
        $project->setTitle($request->request->get('title'));
        $project->setUrl($request->request->get('url'));

        $createDate = new \DateTime('now', new \DateTimeZone('Europe/Moscow'));
        $project->setCreateDate($createDate);

        $entityManager->persist($project);
        $entityManager->flush();

        $data =  [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'url' => $project->getUrl(),
        ];

        return $this->json($data);
    }


    #[Route('/news/{id}', name: 'news_show', methods:['get'] )]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(News::class)->find($id);

        if (!$project) {
            return $this->json('No news found for id ' . $id, 404);
        }

        $data =  [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'url' => $project->getUrl(),
            'date' => $project->getCreateDate(),
        ];

        return $this->json($data);
    }

    #[Route('/update_news/{id}', name: 'news_update', methods:['put', 'patch'] )]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(News::class)->find($id);

        if (!$project) {
            return $this->json('No news found for id ' . $id, 404);
        }

        $project->setTitle($request->request->get('title'));
        $project->setUrl($request->request->get('url'));
        $entityManager->flush();

        $data =  [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'url' => $project->getUrl(),
        ];

        return $this->json($data);
    }

    #[Route('/news/{id}', name: 'news_delete', methods:['delete'] )]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $project = $entityManager->getRepository(News::class)->find($id);

        if (!$project) {
            return $this->json('No news found for id ' . $id, 404);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json('Deleted a news successfully with id ' . $id);
    }
}