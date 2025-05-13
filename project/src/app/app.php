<?php
class App
{
    /**
     * @var Validator Экземпляр валидатора
     */
    private $validator;
    
    /**
     * @var View Экземпляр представления
     */
    private $view;
    
    public function __construct() 
    {
        $this->registerAutoLoader();
        $this->validator = new Validator();
        $this->view = new View();
    }
    
    /**
     * Запускает приложение
     * 
     * @return string Результат работы приложения
     */
    public function run() 
    {
        $inputString = $_POST['string'] ?? '';
        
        try {
            $this->validator->validateBrackers($inputString);
            return $this->view->renderSuccess($inputString);
        } catch (Exception $e) {
            http_response_code(400);
            return $this->view->renderError($e->getMessage());
        }
    }

    /** Добавляет текущий путь в список для поиска include-файлов и включаем default механизм автолоадера
     * @return void
     */
    public function registerAutoLoader(): void
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);
        spl_autoload_register();
    }
}
