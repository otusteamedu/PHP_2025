<?php
namespace App\Controllers;

use App\Domain\Service\Text\TextProcessingServiceInterface;
use App\Domain\Service\View\ViewInterface;

class indexController extends Controller
{
    private TextProcessingServiceInterface $textService;

    // Внедряем все зависимости через конструктор
    public function __construct(
        TextProcessingServiceInterface $textService,
        ViewInterface $view,
    ) {
        // Передаем зависимости в родительский конструктор
        parent::__construct($view);
        $this->textService = $textService;
    }

    public function indexAction()
    {
        // Теперь render возвращает строку, которую нужно вывести
        echo $this->render('index_page');
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
