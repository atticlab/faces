<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('APP_PATH', realpath(__DIR__ . '/..') . '/');
define('MODULE', 'api');
define('MODULE_PATH', APP_PATH . 'app/' . MODULE);
define('CONFIG_PATH', APP_PATH . 'common/config/');
define('TEMP_PATH', APP_PATH . 'temp/');

class Bootstrap extends \Phalcon\Mvc\Application
{
    protected function _registerServices()
    {
        $di = new \Phalcon\DI\FactoryDefault;

        $config = require_once(CONFIG_PATH . 'config.php');
        require_once(CONFIG_PATH . 'services.php');

        # Check available modules
        if (empty($config->modules)) {
            throw new \Exception('Add required modules to config!');
        }

        if (!in_array(MODULE, (array)$config->modules)) {
            throw new \Exception("Unknown module");
        }

        # Register routes
        $di->set('router', function () use ($config) {
            $router = new \Phalcon\Mvc\Router(false);
            $router->setDefaultController('index');
            $router->setDefaultAction('index');

            include CONFIG_PATH . 'routes.php';

            return $router;
        });

        $di->set('dispatcher', function () {
            $dispatcher = new \Phalcon\Mvc\Dispatcher;
            $dispatcher->setDefaultNamespace('App\Controllers\\');

            return $dispatcher;
        });

        $di->set('view', function () {
            $view = new \Phalcon\Mvc\View;
            $view->setViewsDir(MODULE_PATH . '/views/');

            return $view;
        });

        $di->setShared('response', function () {
            return new \App\Lib\Response;
        });

        $this->setDI($di);

        $loader = new \Phalcon\Loader;

        # Register common namespaces
        $loader->registerNamespaces([
            'App\Models'      => APP_PATH . 'common/models/',
            'App\Lib'         => APP_PATH . 'common/lib/',
            'App\Controllers' => MODULE_PATH . '/controllers',
            'RiakMonologHandler' => MODULE_PATH . '/controllers'
        ]);

        $loader->register();
    }

    public function __construct()
    {
        # Require composer autoload
        require_once APP_PATH . 'vendor/autoload.php';

        $this->_registerServices();

        echo $this->handle()->getContent();
    }
}

new Bootstrap();