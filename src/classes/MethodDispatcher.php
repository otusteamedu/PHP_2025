<? 

namespace MyTestApp;

Class MethodDispatcher {

    public $answer = "";

    public function __construct($redis) {

        if(isset($_POST["add"])) { 

            $array = json_decode($_POST["add"],true);
            $redis->hset($array["event"], 'priority', $array['priority']);
            foreach($array['conditions'] AS $key=>$val) {
                $redis->hset($array["event"], $key, $val);
            }

        }


        if(isset($_POST["search"])) { 

            $array = json_decode($_POST["search"],true);
            $get_params_array = $array["params"];


            $param_array = $match_array = [];
            $iterator = null;
            while ($keys = $redis->scan($iterator)) {
                foreach ($keys as $key) {

                    $value = $redis->hgetall($key);
                    $priority = null;

                    foreach($value AS $k=>$res) {
                        if($k !== "priority")
                            $param_array[$k] = $res;
                        else 
                            $priority = $res;
                    }

                    if($get_params_array == $param_array) {
                        $match_array[$key] = $priority;
                    }
        
                }
            }

            arsort($match_array);

            /* foreach($match_array AS $key=>$priority) {
                $this->answer .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;    $key: {$priority}</p>";

                
            } */

            foreach($match_array AS $key=>$priority) {
                $this->answer .= "Cобытие {$key}<br/>";
                $value = $redis->hgetall($key);
                foreach($value AS $k=>$res)
                    $this->answer .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;    $k: {$res}</p>";
                break;
                
            }

            //$this->answer = "www";

            
        }


        if(isset($_POST["clear"])) { 

            $redis->flushDB();
            
        }

    }

}