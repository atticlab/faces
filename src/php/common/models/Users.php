<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Phalcon\Mvc\Model;

class Users extends Model
{
    public $uu_id;
    public $id;

    /**
     * @return \Ramsey\Uuid\UuidInterface|string
     * @throws \Exception
     */
    public static function createUser()
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