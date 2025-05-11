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
}
