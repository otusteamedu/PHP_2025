<? 

namespace MyTestApp;

Class MyApp {

    public $render;

    public function __construct() {

        $renderHtml = new \MyTestApp\RenderHtml();
        
        new \MyTestApp\DataDispatcher($renderHtml);

        $email_list = $_POST["email_list"] ?? 'usermail1@ya.ru'.PHP_EOL.'usermail1@yandex.ru2'.PHP_EOL.'usermail1';
        
        $renderHtml->renderHtml("<hr/>");
        $renderHtml->renderHtml("
        <form method='post'>
            <textarea name='email_list' style='width:300px; height:200px;'>{$email_list}</textarea>
            <p><input type='submit' value='Проверить'/></p>
        </form>
        ");

        $this->render = $renderHtml->html;

    }

    public function render() {
        return $this->render;
    }

}