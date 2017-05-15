<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Phalcon\Mvc\Model;
use App\Models\Faces;

class Users extends Model
{
    public $uu_id;
    public $id;

    /**
     * @param $faceId
     * @param $service_id
     * @return \Ramsey\Uuid\UuidInterface|string
     */
    public static function createUser($faceId, $service_id)
    {
        $user = new self();
        $user->uu_id = Uuid::uuid1()->toString();

        if (!($result = $user->save())) {
            $logger = DI::getDefault()->getLogger();
            $logger->error('Error while try to create user with next error messages');
            foreach ($user->getMessages() as $message) {
                $logger->error($message);
            }
            throw new \Exception('Can not create user');
        }

        if (!Faces::createFace($user->id, $faceId, $service_id, 0)) {
            throw new \Exception('Can not create face');
        }

        return $user;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getUser($user_id)
    {
        return self::findFirst($user_id);
    }
}