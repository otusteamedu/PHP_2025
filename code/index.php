<?php

echo "hello world";

require "./MyCollection.php";

$collection = new \MyCollection();

$collection->add("user1", ["name" => "Вася Пупкин" , "age" => 20]);
$collection->add("user2", ["name" => "Сидор Матрасович" , "age" => 45]);

echo "<pre>";
var_dump(
    $collection->get("user1"),
    $collection->get("user2")
);
echo "</pre>";

phpinfo();
