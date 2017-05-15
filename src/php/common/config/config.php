<?php

return new \Phalcon\Config([

    'modules' => ['api'],

    'db' => [
        'host'     =>  'mysql',
        'username' =>  getenv('MYSQL_USER'),
        'password' =>  getenv('MYSQL_PASSWORD'),
        'dbName'   =>  getenv('MYSQL_DATABASE'),
        'port'     =>  ''
    ],

    'Kairos' => [
        'app_id' => '8053a393',
        'app_key' => 'f0385fae65661043c9ac66d1df3b2804',
        'gallery_name' => 'users'
    ],

    'VisionLab' => [
        'token' => '52d844c1-61e5-43ca-b61b-755121542c5d',
        'list_id' => 'c2939c76-b93c-4bb9-813a-7b3ec76683c1',
        'person_lists' => 'c2939c76-b93c-4bb9-813a-7b3ec76683c1'
    ]
]);
