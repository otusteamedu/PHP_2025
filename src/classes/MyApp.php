<? 

namespace MyTestApp;

Class MyApp {

    public $render = "";

    public function __construct() {
        
        $redis = (new \MyTestApp\Commands\Redis\Connect);
        $MethodDispatcher = new \MyTestApp\MethodDispatcher($redis); 
        $renderHtml = new \MyTestApp\RenderHtml("views/page.php",["answer"=>$MethodDispatcher->answer]);
        //$this->render = $renderHtml->html;

    }

}