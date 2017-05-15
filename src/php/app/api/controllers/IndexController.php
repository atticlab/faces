<?php
namespace App\Controllers;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function errorAction()
    {
        return $this->response->error('ROUTE_NOT_FOUND', 404);
    }
}