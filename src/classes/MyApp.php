<? 

namespace MyTestApp;

Class MyApp {

    public $render = "";

    public function __construct() {
        
        $renderHtml = new \MyTestApp\RenderHtml();

        $redis = (new \MyTestApp\RedisConnect)->redis_connect;
    
        $MethodDispatcher = new \MyTestApp\MethodDispatcher($redis); 

        

        $iterator = null;
        while ($keys = $redis->scan($iterator)) {
            foreach ($keys as $key) {
                $renderHtml->renderHtml("<p><b>{$key}</b></p>");
                $value = $redis->hgetall($key);
                //unset($value["priority"]);
                foreach($value AS $k=>$res)
                    $renderHtml->renderHtml("<p>&nbsp;&nbsp;&nbsp;&nbsp;    $k: {$res}</p>");
            }
        }

        $renderHtml->renderHtml("<hr/>");

        $renderHtml->renderHtml("
        <form method='post'>
            <textarea name='add' style='width:600px; height:300px;' value='' placeholder='Введите json' ></textarea>
            <p><input type='submit' value='Добавить'/></p>
        </form>
        ");

        $search_form = "
        <hr/>
        <form method='post'>
            <textarea name='search' style='width:600px; height:300px;' value='' placeholder='Введите json' ></textarea>
            <p><input type='submit' value='Искать'/></p>
        </form>
        ";

        $renderHtml->renderHtml($search_form);
        $renderHtml->renderHtml("<hr>Ответ на поиск: {$MethodDispatcher->answer} </hr>");

        $renderHtml->renderHtml("
        <hr/>
        <form method='post'>
            <p><input type='submit' name='clear' value='Очистить базу'/></p>
        </form>
        ");

        $this->render = $renderHtml->html;

        
       

    }

}