<?php

namespace App\Controllers;

use App\Lib\User;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    protected $payload;
    protected $user;

    public function beforeExecuteRoute()
    {
        $this->payload = json_decode($this->request->getRawBody());
        if (empty($this->payload)) {
            $this->payload = (object)$this->request->getPost();
        }

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');

        if ($this->request->isOptions()) {
            $this->response->setHeader('Access-Control-Allow-Headers',
                'Origin, X-CSRF-Token, X-Requested-With, X-HTTP-Method-Override, Content-Range, Content-Disposition, Content-Type, Authorization');
            $this->response->setHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, DELETE');
            $this->response->sendHeaders();
            exit;
        }
    }

    public function buildUrl($path = null)
    {
        if (!empty($path)) {
            $path = '/' . trim($path, '/');
        }

        return (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $path;
    }
}