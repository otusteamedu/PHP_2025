<? 

namespace MyTestApp;

Class MyApp {

    public $render;

    public function __construct($str_type) {

        $class_name = '\\MyTestApp\\'.$str_type;
        if (class_exists($class_name)) 
            $this->render = (new \MyTestApp\DataDispatcher(new $class_name))->data;
        else
            throw new \Exception("Класс отсутствует");
        
    }

    public function render() {

        switch (gettype($this->render)) {
            case "string":
                echo $this->render;
                break;
            case "array":
                echo "<pre>".json_encode($this->render, JSON_UNESCAPED_UNICODE)."</pre>";
                break;
            default:
                echo "Ошибка данных";
        }

    }

}