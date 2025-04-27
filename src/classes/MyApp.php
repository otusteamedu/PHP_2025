<? 

namespace MyTestApp;

Class MyApp {

    public function __construct() {
        
        $redis = (new \MyTestApp\Commands\Redis\Connect);
        $MethodDispatcher = new \MyTestApp\MethodDispatcher($redis); 
        new \MyTestApp\RenderHtml("views/page.php",["answer"=>$MethodDispatcher->answer]);

    }

}