<?php

namespace App\Lib;

class Response extends \Phalcon\Http\Response
{
    const USER_NOT_FOUND = 'USER_NOT_FOUND';

    public function error($err_code, $http_code = 400)
    {
        $this->setStatusCode($http_code);

        $this->setJsonContent([
            'error' => $err_code,
        ])->send();

        exit;
    }

    public function sendResponse(array $data)
    {
        return $this->setJsonContent($data)->send();
    }
}