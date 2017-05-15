<?php

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use SWP\Services\RiakDBService;
use GuzzleHttp\Client;
use Smartmoney\Stellar\Account;

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