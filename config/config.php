<?php

//define("DIR_ROOT", $_SERVER['DOCUMENT_ROOT']);
//define("DS", DIRECTORY_SEPARATOR);
use app\engine\Request;
use app\models\repositories\CartRepository;
use app\models\repositories\ProductsRepository;
use app\models\repositories\UsersRepository;
use app\engine\Db;

return [
    'root_dir' => __DIR__ . "/../",
    'templates_dir' => __DIR__ . "/../views/",
    'controllers_namespaces' => 'app\controllers\\',
    'components' => [
        'db' => [
            'class' => Db::class,
            'driver' => 'mysql',
            'host' => 'localhost',
            'login' => 'root',
            'password' => '',
            'database' => 'inshop',
            'charset' => 'utf8'
        ],
        'request' => [
            'class' => Request::class
        ],
        //По хорошему сделать для репозиториев отедьное простое хранилище
        //без reflection
        'cartRepository' => [
            'class' => CartRepository::class
        ],
        'productRepository' => [
            'class' => ProductsRepository::class
        ],
        'userRepository' => [
            'class' => UsersRepository::class
        ]

    ]
];