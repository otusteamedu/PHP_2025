<?

spl_autoload_register();

if(isset($argv[1]) AND isset($argv[2])) {

    $app = new \MyTestApp\MyApp("http://elasticsearch:9200", "my_index", "_search", '
        {
            "query": {
                "bool": {
                    "must": [
                        {
                            "range": {
                                "price": {
                                "lt": '.$argv[2].'
                                }
                            }
                        },
                            {
                            "match": {
                                "title": {
                                    "query": "'.$argv[1].'",
                                    "fuzziness": "auto"
                                }
                            }
                        }
                    ]
                }
            }
        }
    ');
    
    echo $app->render;

} else {
    echo "Поисковый запрос не задан. Добавьте два аргумента: 1 аргумент - название товара, второй аргумент цена";
}








 