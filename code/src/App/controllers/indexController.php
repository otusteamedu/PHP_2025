<?php
namespace App\Controllers;

use Classes\files;
use Classes\View;
use Classes\Text;
use Classes\DataBase;

class indexController extends Controller
{
    protected $view;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {

        $this->view->render('index_page');
    }

    public function hello()
    {
        echo "<div class='col-md-offset-2'>Добро пожаловать на наш сайт</div>" ;
    }

    public function Text_data($value)
    {
        $d = new Text($value);
        $d = $d->get_data();
        $text = "В тексте\n".$d['par']."\n параграфов, \n".$d['words']."\n слов, ".$d['length']."\n символов";
        $this->view->render('index_page', ['text'=> $text]);
    }
}