<? 

namespace MyTestApp;

Class MyApp {

    public $render = "";

    public function __construct($host, $index, $operation, $body) {

        $elastic = (new \MyTestApp\ElasticSearch);
        $elastic->search($host, $index, $operation, $body);

        $renderAnswer = new \MyTestApp\RenderAnswer;
        $renderAnswer->renderAnswer($elastic->result);

        $this->render =  $renderAnswer->answer;

    }

}