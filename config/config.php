<?php

//define("DIR_ROOT", $_SERVER['DOCUMENT_ROOT']);
//define("DS", DIRECTORY_SEPARATOR);
use app\engine\{Request,Db};
use app\models\repositories\{CartRepository, ProductsRepository, TestRepository, UsersRepository, OrderRepository};

return [
    'root_dir' => __DIR__ . "/../",
    'templates_dir' => __DIR__ . "/../views/",
    'controllers_namespaces' => 'app\controllers\\',
    'components' => [
        'db' => [
            'class' => Db::class,
            'driver' => 'pgsql',
            'host' => 'db',
            'login' => 'db_user',
            'password' => 'test',
            'database' => 'db_php',
            'charset' => 'utf8'
        ],
        'request' => [
            'class' => Request::class
        ],
        //По хорошему сделать для репозиториев отедьное простое хранилище
        //без reflection
//        'cartRepository' => [
//            'class' => CartRepository::class
//        ],
//        'productRepository' => [
//            'class' => ProductsRepository::class
//        ],
//        'userRepository' => [
//            'class' => UsersRepository::class
//        ],
//        'orderRepository' => [
//            'class' => OrderRepository::class
//        ]
        'testRepository' => [
            'class' => TestRepository::class
        ],
    ]
];
