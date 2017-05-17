<?php

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use SWP\Services\RiakDBService;
use Smartmoney\Stellar\Account;
use Phalcon\Db\Adapter\Pdo\Mysql;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var bool
     */
    private $_loaded = false;
    protected $_host;

    public function setUp()
    {

        parent::setUp();

        // Load any additional services that might be required during testing
        $di = Di::getDefault();

        $di->set('request', function () {
            return new \App\Lib\Request();
        });

        $di->set('response', function () {
            return new \App\Lib\Response();
        });

        # Register DB
        $di->set('db', function () {

            $connection = new Mysql(
                [
                    "host"     => 'mysql',
                    "username" => getenv('MYSQL_USER'),
                    "password" => getenv('MYSQL_PASSWORD'),
                    "dbname"   => getenv('MYSQL_DATABASE'),
                    "port"     => 3306,
                ]
            );

            $connection->connect();

            return $connection;
        });

        $this->setDi($di);

        $this->_host = 'http://192.168.1.125:8001';

        $this->_loaded = true;

    }

    /**
     * Check if the test case is setup properly
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        if (!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }

}