<?php
namespace App\Controllers;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\Text\TextProcessingServiceInterface;
use App\Domain\Service\View\ViewInterface;

class indexController extends Controller
{
    private const USERS_PER_PAGE = 20;

    private TextProcessingServiceInterface $textService;
    private UserRepositoryInterface $userRepository;

    // Внедряем все зависимости через конструктор
    public function __construct(
        TextProcessingServiceInterface $textService,
        ViewInterface $view,
        UserRepositoryInterface $userRepository,
    ) {
        // Передаем зависимости в родительский конструктор
        parent::__construct($view);
        $this->textService = $textService;
        $this->userRepository = $userRepository;
    }

    public function indexAction()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = self::USERS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $users = $this->userRepository->findAll($limit, $offset);
        
        // Для пагинации также нужно общее количество пользователей.
        // Давай добавим метод count() в репозиторий.
        // А пока просто передадим номер страницы.
        
        echo $this->render('index_page', [
            'users' => $users,
            'currentPage' => $page,
            // 'totalPages' => $totalPages // Это нужно будет добавить
        ]);
    }

    public function hello()
    {
        echo "<div class='col-md-offset-2'>Добро пожаловать на наш сайт</div>" ;
    }

    public function Text_data($value)
    {
        $stats = $this->textService->analyze($value);

        $text = sprintf(
            "В тексте\n%d\n параграфов, \n%d\n слов, %d\n символов",
            $stats['paragraphs'],
            $stats['words'],
            $stats['length']
        );

        // Передаем данные в шаблон и выводим результат
        echo $this->render('index_page', ['text' => $text]);
    }
}
