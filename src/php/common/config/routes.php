<?php

$router->addGet('/', [
    'controller' => 'index',
    'action' => 'index'
]);

$router->addGet('/faces', [
    'controller' => 'faces',
    'action'     => 'index',
]);

$router->addGet('/faces/status', [
    'controller' => 'faces',
    'action'     => 'status',
]);

$router->addPost('/faces/registration', [
    'controller' => 'faces',
    'action'     => 'registration',
]);

$router->addPost('/faces/login', [
    'controller' => 'faces',
    'action'     => 'login',
]);

$router->notFound([
    'controller' => 'index',
    'action'     => 'error'
]);