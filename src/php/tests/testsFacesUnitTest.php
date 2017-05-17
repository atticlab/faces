<?php

namespace Faces;

use App\Lib\Response;
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

            //example: array (photo, http_code, err_code, login, clear_after_test) - if login true - make login, else - registration

            //no photo - login
            [$no_photo, 400, Exception::FILE_EMPTY, true, false],

            //no photo - registration
            [$no_photo, 400, Exception::FILE_EMPTY, false, false],

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
            [$base64_good_photo, 400, Response::USER_NOT_FOUND, true, false],

            //good photo, registration
            [$base64_good_photo, 200, null, false, false],

            //good photo, login
            [$base64_good_photo, 200, null, true, true]

        ];

    }

    /**
     * @dataProvider facesProvider
     */
    public function testFaces($base64, $http_code, $err_code, $login, $clear_after_test)
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
            $response = $client->request(
                'POST',
                rtrim($this->_host, '/') . '/faces/registration',
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

        if ($clear_after_test) {

            $this->assertTrue(
                property_exists($encode_data, 'faceId')
            );

            $this->assertTrue(
                !empty($encode_data->faceId)
            );

            $sql = "DELETE FROM users WHERE `uu_id` = (?)";
            DI::getDefault()->getDb()->execute($sql, [$encode_data->faceId]);
        }
    }
}