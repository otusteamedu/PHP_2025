<?php

namespace App\Infrastructure\Http\News\Controller;

use App\Domain\Entity\News;
use App\Infrastructure\Services\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class NewsController extends AbstractController
{
    public function __construct(
        protected NewsService $newsService
    ){}

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
        $url = $request->request->get('url');
        $arNewsTitles = $this->newsService->getHtmlByUrl($url, 'title');

        //TODO исключением!
        if (is_array($arNewsTitles) && !empty($arNewsTitles)) {
            $mainTitle = reset($arNewsTitles);

            $project = new News();
            $project->setTitle($mainTitle);
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
        }



        return $this->json($data);
    }

    public function generateReport(EntityManagerInterface $entityManager, Request $request, KernelInterface $kernel): JsonResponse
    {




        $arRequest = $request->toArray();
        if (is_array($arRequest['news']) && !empty($arRequest['news'])) {
            $arNews = $entityManager->getRepository(News::class)->findBy(['id' => $arRequest['news']]);

            $htmlReport = '<ul>';
            foreach ($arNews as $news) {
                $url = $news->getUrl();
                $title = $news->getTitle();

                $htmlReport .= '<li><a href="'.$url.'">'.$title.'</a><li>';
            }
            $htmlReport .= '</ul>';

            dd($htmlReport);
        }

        //TODO использовать try/catch


        //TODO вынести в сервис
        $filesystem = new Filesystem();


        $projectDirectory = $kernel->getProjectDir();
        $filesystem->dumpFile($projectDirectory.'/src/include/reports/report.txt', 'Hello World');

        try {
            $filesystem->mkdir(
                Path::normalize(sys_get_temp_dir().'/'.random_int(0, 1000)),
            );


        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }



//        $data = [];
//        foreach ($news as $el) {
//            $data[] = [
//                'id' => $el->getId(),
//                'title' => $el->getTitle(),
//                'url' => $el->getUrl(),
//                'create_date' => $el->getCreateDate(),
//            ];
//        }
//
//        return $this->json($data);
    }

}