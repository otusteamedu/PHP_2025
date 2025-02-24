<? 

namespace MyTestApp;

Class MyApp {

    public $render = "";

    public function __construct() {

        session_start();
        
        $renderHtml = new \MyTestApp\RenderHtml();

        $redis = (new \MyTestApp\RedisConnect)->redis_connect;
    
        new \MyTestApp\MethodDispatcher($redis); 

        // Если нет данных с ключем сессии, то создаем запись с этим ключем

        if(!$redis->get(session_id()))
            $redis->set(session_id(), "Запрос обработал контейнер: " . $_SERVER['HOSTNAME'].". Сессия записана в Redis. Ключ: ".session_id()."</br>");

        // Выводим данные по ключу сессии (для кластера)

        else 
            $renderHtml->renderHtml($redis->get(session_id()));

        $renderHtml->renderHtml("<hr/>");
        $renderHtml->renderHtml("
        <form method='post'>
            <input type='text' size='50' name='string' value='' placeholder='Введите строку для проверки' />
            <input type='submit' value='Проверить'/>
        </form>
        ");

        $this->render = $renderHtml->html;

        
       

    }

}