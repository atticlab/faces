<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;
use Phalcon\Db\Adapter\Pdo\Mysql;

#Logger
$di->setShared('logger', function () use ($config, $di) {

    $format = new Monolog\Formatter\LineFormatter("[%datetime%] %level_name%: %message% %context%\n");

    $stdout = new StreamHandler('php://stdout', Logger::DEBUG);
    $stdout->setFormatter($format);

    $stream = new StreamHandler(ini_get('error_log'), Logger::DEBUG);
    $stream->setFormatter($format);

    $log = new Logger(__FUNCTION__);
    $log->pushProcessor(new IntrospectionProcessor());
    $log->pushHandler($stdout);
    $log->pushHandler($stream);

    return $log;
});

# Register DB
$di->set('db', function () use ($config) {

    $connection = new Mysql(
        [
            "host"     => $config->db->host,
            "username" => $config->db->username,
            "password" => $config->db->password,
            "dbname"   => $config->db->dbName,
            "port"     => 3306,
        ]
    );

    $connection->connect();

    return $connection;
});

# Config
$di->setShared('config', $config);