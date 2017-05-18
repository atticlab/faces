<?php

namespace App\Controllers;

use App\Lib\Response;
use Atticlab\Libface\Configs\Config;
use Atticlab\Libface\Recognition\Exception;
use Atticlab\Libface\Recognition\RecognitionBase;
use App\Lib\Helpers;
use App\Models\Faces;
use App\Models\Users;
use Atticlab\Libface\Recognition\VisionLabs;
use attics\Lib\Llog\Logger;
use Phalcon\Di;

use Atticlab\Libface\Recognition\Kairos;

/**
 * Class FacesController
 * @package App\Controllers
 */
class FacesController extends ControllerBase
{
    use Logger;

    /**
     * @var array
     * list of face recognition service sort by priority!!!
     */
    private $_services = [];

    /**
     * Connection check
     */
    public function statusAction()
    {
        $this->response->sendResponse(['status' => 'ok']);
    }

    /**
     * @return mixed
     */
    public function registrationAction()
    {
        try {
            $this->setLogger(Di::getDefault()->getShared('logger'));
            $photo = $this->payload->photo ?? null;

            if (is_null($photo)) {
                $this->lerror(Exception::FILE_EMPTY);

                throw new Exception(Exception::FILE_EMPTY);
            } else {
                $recognition = new RecognitionBase($this->getConfigs());
                $response = $recognition->getIds($photo);
                $faceMatches = 0;
                $detected_user = null;
                $new_face_ids = [];

                foreach ($response as $item) {
                    $face = Faces::getFace($item->face_id, $item->service_id);

                    if (!empty($face)) {
                        //this face is already in our db
                        $user = Users::getUser($face->user_id);
                        if (empty($detected_user->uu_id)) {
                            $detected_user = $user;
                        } elseif ($detected_user->uu_id != $user->uu_id) {
                            //PANIC! One photo recognized like few our clients!!!
                            $bad_photo_name = sha1(microtime());
                            file_put_contents(Helpers::sfile($bad_photo_name, '/bad_photo') . $bad_photo_name, $photo);
                            $this->logger->error('PANIC! One photo recognized like few our clients!!!');

                            throw new \Exception('SERVICE_ERROR');
                        }
                        if (!Faces::updateMatches($face->user_id, $item->face_id)
                        ) {
                            $this->logger->error('Can not update matches for face with id ' . $item->face_id . ' and user id ' . $face->user_id);
                        }
                        $faceMatches++;
                    } else {
                        //remember new faces for added it to new/existed user
                        $new_face_ids[] = $item;
                    }
                }

                //check new faces
                if (!empty($new_face_ids)) {
                    //if we don't detected existed user - create new user
                    if (empty($detected_user)) {
                        $detected_user = Users::createUser();
                    }
                    //associate all new faces to detected user
                    foreach ($new_face_ids as $new_face_id) {
                        Faces::createFace($detected_user->id, $new_face_id->face_id, $new_face_id->service_id, 0);
                    }
                }

                return $this->response->sendResponse([
                    'faceId'        => $detected_user->uu_id,
                    'totalServices' => count($response),
                    'faceMatches'   => $faceMatches
                ]);
            }

        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->response->error($e->getCode());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->response->error($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function loginAction()
    {
        try {
            $this->setLogger(Di::getDefault()->getShared('logger'));
            $photo = $this->payload->photo ?? null;

            if (is_null($photo)) {
                $this->lerror(Exception::FILE_EMPTY);
                throw new Exception(Exception::FILE_EMPTY);
            } else {
                $recognition = new RecognitionBase($this->getConfigs());

                foreach ($this->_services as $key => $service) {
                    try {
                        $response = $recognition->getIdByService($photo, $service);
                        $face = Faces::getFace($response->face_id, $response->service_id);
                        if (!empty($face)) {
                            //this face is already in our db
                            $user = Users::getUser($face->user_id);

                            if (!empty($user)) {
                                return $this->response->sendResponse([
                                    'faceId' => $user->uu_id
                                ]);
                            }
                        }
                    } catch (Exception $e) {
                        $this->logger->error('Service ' . (is_object($service) ? '(' . get_class($service) . ')' : '') . ' with priority ' . $key . ' can not get face id');
                        continue;
                    }
                }

                return $this->response->error(Response::USER_NOT_FOUND);
            }
        } catch (Exception $e) {
            return $this->response->error($e->getCode());
        }
    }

    /**
     * get configs
     */
    private function getConfigs()
    {
        $configs = new Config();
        $configs->enableKairos($this->config->Kairos->app_id, $this->config->Kairos->app_key,
            $this->config->Kairos->gallery_name, $this->logger);
        $configs->enableVisionLabs($this->config->VisionLab->token, $this->config->VisionLab->descriptor_lists,
            $this->config->VisionLab->person_lists, $this->logger);

        //add services to priority list
        //first added - max priority services
        //last added - min priority services
        $this->addService(new Kairos($this->config->Kairos->app_id, $this->config->Kairos->app_key,
            $this->config->Kairos->gallery_name));

        $this->addService(new VisionLabs($this->config->VisionLab->token, $this->config->VisionLab->descriptor_lists,
            $this->config->VisionLab->person_lists));

        return $configs;
    }

    /**
     * get configs
     */
    private function addService($service)
    {
        $this->_services[] = $service;
    }
}