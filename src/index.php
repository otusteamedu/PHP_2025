<?php

if(isset($argv[1]) AND isset($argv[2])) {
	
	echo "\n\nПоиск по запросу \"{$argv[1]}\"\n\n";
	
	$title = $argv[1];
    $price = $argv[2];
	
	$url = "http://elasticsearch:9200/my_index/_search";

	$ch = curl_init();

	$body = '{
        "query": {
            "bool": {
                "must": [
                    {
                        "range": {
                            "price": {
                            "lt": '.$price.'
                            }
                        }
                    },
                        {
                        "match": {
                            "title": {
                                "query": "'.$title.'",
                                "fuzziness": "auto"
                            }
                        }
                    }
                ]
            }
        }
    }';

	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ));
	curl_setopt($ch, CURLOPT_POSTFIELDS,$body);

	$response = curl_exec($ch);

	if ($response === false) {
		exit( 'Ошибка cURL: ' . curl_error($ch) );
	} 

	curl_close($ch);

    $array = json_decode($response,true);

    // print_r($array);

    if($array["hits"]["total"]["value"] == 0)
        exit("Нет совпадений");

    echo "Всего результатов найдено: {$array["hits"]["total"]["value"]}\n\n======\n\n";
    foreach($array["hits"]["hits"] AS $id=>$res) {
        echo "{$id} - {$res["_source"]["title"]} - {$res["_source"]["price"]}\n";
    } 
    echo "\n======\n\n";

    exit();

} else {
	exit("Поисковый запрос не задан. Добавьте два аргумента: 1 аргумент - название товара, второй аргумент цена \n\n");
}

