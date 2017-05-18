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
        'descriptor_lists' => '764235a0-a5ea-4a62-98b9-0ee3958678c4',
        'person_lists' => '51b9dbcb-66a6-4170-9c1d-abbe23146ec6'
    ]
]);
