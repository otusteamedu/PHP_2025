<?

require_once "vendor/autoload.php";

$app = new \MyTestApp\MyApp;
echo $app->render;

/* $json = '
  
{
    "event": "event1",
    "priority": 1000,
    "conditions": {
        "param1": 1,
        "param2": 2 
    }
}

';




$array = json_decode($json,true);

foreach($array AS $data) {
    $redis->hset($data["event"], 'priority', $data['priority']);
    foreach($data['conditions'] AS $key=>$val) {
        $redis->hset($data["event"], $key, $val);
    }
}

echo "<pre>";
    print_r($array);
echo "</pre>"; */
/* 
$jsonsearch = '
{
    "params": {
        "param1": 1,
        "param2": 2 
    }
}
';

$array = json_decode($jsonsearch,true);

echo "<pre>";
    print_r($array);
echo "</pre>";

$get_params_array = $array["params"];

echo "<pre>";
    print_r($get_params_array);
echo "</pre>";

echo "<hr/>";

$eq_array = [];
$eq_array["param2"] = 2;
$eq_array["param1"] = 1;


if($get_params_array == $eq_array) {
    echo "Равны";
}
    
else 
    echo "Не Равны";


echo "<pre>";
    print_r($get_params_array);
echo "</pre>";

echo "<pre>";
    print_r($eq_array);
echo "</pre>"; */





 