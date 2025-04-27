<? 

namespace MyTestApp;

Class ElasticSearch {

    public $result;

    public function search($host, $index, $operation, $body) {

        $this->result .= "\n\nПоиск\n\n";
        
        $url = "http://elasticsearch:9200/my_index/_search";
        $url = "{$host}/{$index}/{$operation}";
    
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
    
        $response = curl_exec($ch);
    
        if ($response === false) {
            $this->result .= 'Ошибка cURL: ' . curl_error($ch) ;
        } 
    
        curl_close($ch);
    
        $array = json_decode($response,true);
    
        // print_r($array);
    
        if($array["hits"]["total"]["value"] == 0)
            $this->result .= "Нет совпадений";
    
        $this->result .= "Всего результатов найдено: {$array["hits"]["total"]["value"]}\n\n======\n\n";
        foreach($array["hits"]["hits"] AS $id=>$res) {
            $this->result .= "{$id} - {$res["_source"]["title"]} - {$res["_source"]["price"]}\n";
        } 
        $this->result .= "\n======\n\n";
        
    }

}