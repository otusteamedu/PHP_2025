<? 

namespace MyTestApp;

Class MethodDispatcher {

    public $answer = "";

    public function __construct($redis) {

        if(isset($_POST["add"])) { 

            $this->method_add($_POST["add"]);

        }

        if(isset($_POST["search"])) { 

            $this->method_search($_POST["search"]);

        }

        if(isset($_POST["clear"])) { 

            $this->method_clear();
            
        }

        $this->method_show();

    }

    public function method_add($data) {
        
        $method = new \MyTestApp\Commands\Redis\CommandAdd();
        $this->answer .= $method->add($data);

    }

    public function method_search($data) {
        
        $method = new \MyTestApp\Commands\Redis\CommandSearch();
        $this->answer .= $method->search($data);

    }

    public function method_clear() {
        
        $method = new \MyTestApp\Commands\Redis\CommandClear();
        $this->answer .= $method->clear();

    }

    public function method_show() {
        
        $method = new \MyTestApp\Commands\Redis\CommandShow();
        $this->answer .= $method->show();

    }

}