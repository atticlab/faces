<?php

namespace Companies;

use App\Lib\Response;
use \App\Models\Companies;
use Phalcon\DI;
use Smartmoney\Stellar\Account;
use GuzzleHttp\Client;
use Atticlab\Libface\Recognition\Exception;

/**
 * Class UnitTest
 */
class FacesUnitTest extends \UnitTestCase
{

    public static function facesProvider()
    {
        $no_photo = null;
        $base64_non_image = '';
        $base64_bad_photo = '';
        $base64_good_photo = file_get_contents('base64_good_photo.txt');

        return [

            //example: array (photo, http_code, err_code, login) - if login true - make ogin, else - registration

            //no photo - login
            [$no_photo, 400, Exception::FILE_EMPTY, true],

            //no photo - registration
            [$no_photo, 400, Exception::FILE_EMPTY, false],

//            //non image - login

//            array($base64_non_image, http_code, err_code, true),
//            //non image - registration
//            array($base64_non_image, http_code, err_code, false),
//
//            //bad photo - login
//            array($base64_bad_photo, http_code, err_code, true),

//            //bad photo - registration
//            array($base64_bad_photo, http_code, err_code, false),

            //good photo, login
            [$base64_good_photo, 400, Response::USER_NOT_FOUND],

            //good photo, registration
            [$base64_good_photo, 200, null, false],

            //good photo, login
            [$base64_good_photo, 200, null, true]

        ];

    }

    /**
     * @dataProvider facesProvider
     */
    public function testFaces($base64, $http_code, $err_code, $login)
    {
        parent::setUp();
        $client = new Client();

        if ($login) {
            //login
            $response = $client->request(
                'POST',
                rtrim($this->_host, '/') . '/faces/login',
                [
                    'headers'     => [

                    ],
                    'http_errors' => false,
                    'json'        => [
                        "photo" => $base64,
                    ]
                ]
            );
        } else {
            //register

            $client = new Client();

            $response = $client->request(
                'POST',
                rtrim($this->_host, '/') . '/faces/register',
                [
                    'headers'     => [

                    ],
                    'http_errors' => false,
                    'json'        => [
                        "photo" => $base64,
                    ]
                ]
            );
        }

        $real_http_code = $response->getStatusCode();
        $stream = $response->getBody();
        $body = $stream->getContents();
        $encode_data = json_decode($body);

        //test http code
        $this->assertEquals(
            $http_code,
            $real_http_code
        );

        $this->assertTrue(
            !empty($encode_data)
        );

        if ($err_code) {

            //test error data structure
            $this->assertTrue(
                property_exists($encode_data, 'error')
            );

            //test error code
            $this->assertEquals(
                $err_code,
                $encode_data->error
            );
        }

        //when we make test that success create company
        if ($real_http_code == 200) {

        }

    }

    //TODO:
}