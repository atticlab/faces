<?php

namespace App\Models;

use Phalcon\DI;
use Phalcon\Mvc\Model;

class Faces extends Model
{
    public $id;
    public $user_id;
    public $services_id;
    public $face_id;
    public $matches;
    public $created_at;
    public $updated_at;

    /**
     * @param $user_id
     * @param $face_id
     * @param $service_id
     * @param $matches
     * @throws \Exception
     */
    public static function createFace($user_id, $face_id, $service_id, $matches)
    {
        if (self::getFace($face_id, $service_id)) {
            throw new \Exception('Face already exist');
        }

        $face = new self();

        $face->user_id = $user_id;
        $face->services_id = $service_id;
        $face->face_id = $face_id;
        $face->matches = $matches;
        $face->created_at = time();
        $face->updated_at = time();

        if (!($result = $face->save())) {
            $logger = DI::getDefault()->getLogger();
            $logger->error('Error while try to create face with next error messages');
            foreach ($face->getMessages() as $message) {
                $logger->error($message);
            }
        }

        return $result;
    }

    /**
     * @param $service_id
     * @param $face_id
     * @return mixed
     */
    public static function getFace($face_id, $service_id)
    {
        $face = self::findFirst(
            [
                "conditions" => "face_id = ?1 AND services_id = ?2",
                "bind"       => [
                    1 => $face_id,
                    2 => $service_id
                ]
            ]
        );

        return $face;
    }

    /**
     * @param $user_id
     * @param $face_id
     * @return bool
     * @throws \Exception
     */
    public static function updateMatches($user_id, $face_id)
    {
        $face = self::findFirst(
            [
                "conditions" => "user_id = ?1 AND face_id = ?2",
                "bind"       => [
                    1 => $user_id,
                    2 => $face_id
                ]
            ]
        );

        if (!$face) {
            throw new \Exception('Face not found');
        }

        $face->matches++;
        $face->updated_at = time();

        if (!($result = $face->update())) {
            $logger = DI::getDefault()->getLogger();
            $logger->error('Error while try to update face with next error messages');
            foreach ($face->getMessages() as $message) {
                $logger->error($message);
            }
        }

        return $result;
    }
}