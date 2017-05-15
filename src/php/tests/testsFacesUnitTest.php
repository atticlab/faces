<?php

namespace Companies;

use \App\Models\Companies;
use Phalcon\DI;
use Smartmoney\Stellar\Account;
use GuzzleHttp\Client;
use Atticlab\Libface\Recognition\Exception;

/**
 * Class UnitTest
 */
class CompaniesUnitTest extends \UnitTestCase
{

    public static function getIdProvider()
    {
        $no_photo = null;
        $base64_non_image = '';
        $base64_bad_photo = '';
        $base64_good_photo = file_get_contents('base64_good_photo.txt');

        return [

            //example: array (photo, http_code, err_code)

            //no photo
            [$no_photo, 400, Exception::FILE_EMPTY],

//            //non image
//            array($base64_non_image),
//
//            //bad photo
//            array($base64_bad_photo),

            //good photo
            [$base64_good_photo, 200, ''],

        ];

    }

    /**
     * @dataProvider getIdProvider
     */
    public function testGetId($base64, $http_code, $err_code)
    {

        parent::setUp();

        $client = new Client();

        // Create a POST request
        $response = $client->request(
            'POST',
            rtrim($this->_host, '/') . '/faces/get-id',
            [
                'headers'     => [

                ],
                'http_errors' => false,
                'json'        => [
                    "photo" => $base64,
                ]
            ]
        );

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
            //TODO: check response
        }

    }
}