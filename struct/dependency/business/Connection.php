<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 08/03/16
 * Time: 19:22
 */

namespace Metier;

require_once 'Lib/FactoryClass.php';

class Connection {
    private static $instance = null;

    public function Connection() {}

    public static function getDefaultConnection() {
            if (!isset(self::$instance)) {
                self::$instance = new Connection();
            }
            return self::$instance;
    }

    public function login ($email, $password) {
        if (\Utils::isValid($email) && \Utils::isValid($password)) {
            /* @var $access \Access */
            $access = \FactoryClass::getClassDAL('Access');
            /* @var $select \Select */
            $select = \FactoryClass::getClassDAL("Select");
            $sql = "
            Select * from public.user
                where
                    email = '@1'
                    and password = '@2'
                    and enable = @3;
            ";
            $select->selectBrute($sql);
            $select->args($email, $password, 'true');
            $access->addTransaction($select);
            $result = $access->executeTransaction();

            if (\Utils::isValidSql($result)) {
                $response = \Utils::getSqlResponse($result);
                if (\Utils::isValid($response[0])) {
                    /* @var $user User */
                    $user = $this->loadUser($response[0]);

                    if (\Utils::isValid($user)) {
                        /*  @var $session \Conversation */
                        $session = \Conversation::getDefaultConversation("SESSION");
                        $session->setValue("ePshj4uSyk5N4yu455yd", serialize($user), false);

                        return $user;
                    } else {
                        return 'Unknown user';
                    }
                } else {
                    return 'Unknown user';
                }
            } else {
                return 'Unknown user';
            }
        } else {
            return 'Invalid email or password';
        }
    }

    public function loginWithToken ($token) {
        if (\Utils::isValid($token)) {
            /* @var $access \Access */
            $access = \FactoryClass::getClassDAL('Access');
            /* @var $select \Select */
            $select = \FactoryClass::getClassDAL("Select");
            $sql = "
            Select * from public.user
                where
                    token = '@1';
            ";
            $select->selectBrute($sql);
            $select->args($token);
            $access->addTransaction($select);
            $result = $access->executeTransaction();

            if (\Utils::isValidSql($result)) {
                $response = \Utils::getSqlResponse($result);
                if (\Utils::isValid($response[0])) {
                    /* @var $user User */
                    $user = $this->loadUser($response[0]);

                    if (\Utils::isValid($user)) {
                        /*  @var $session \Conversation */
                        $session = \Conversation::getDefaultConversation("SESSION");
                        $session->setValue("ePshj4uSyk5N4yu455yd", serialize($user), false);

                        return $user;
                    } else {
                        return 'Unknown user';
                    }
                } else {
                    return 'Unknown user';
                }
            } else {
                return 'Unknown user';
            }
        } else {
            return 'Invalid token';
        }
    }

    public function logout () {
        /*  @var $session \Conversation */
        $session = \Conversation::getDefaultConversation("SESSION");
        $session->destroyDefaultConversation();
    }

    public function refresh () {
        $user = \Utils::getCurrentUser();

        if (\Utils::isValid($user) && \Utils::isValid($user->token) && \Utils::isValid($user->id)) {
            $remoteUser = $this->loginWithToken($user->token);

            if (\Utils::isValid($remoteUser)) {
                $userRefreshed = $this->loadUser($remoteUser);

                /*  @var $session \Conversation */
                $session = \Conversation::getDefaultConversation("SESSION");
                $session->setValue("ePshj4uSyk5N4yu455yd", serialize($userRefreshed), false);

                return "User refreshed";
            } else {
                return "Unknown user or session die";
            }
        } else {
            return "Unknown user";
        }
    }

    private function loadUser ($data) {
        /* @var $operator \Metier\User */
        $user = new User();
        if (\Utils::isValid($data['id'])) $user->id = $data['id'];
        if (\Utils::isValid($data['email'])) $user->email = $data['email'];
        if (\Utils::isValid($data['first_name'])) $user->firstName = $data['first_name'];
        if (\Utils::isValid($data['last_name'])) $user->lastName = $data['last_name'];
        if (\Utils::isValid($data['credit'])) $user->credit = $data['credit'];
        if (\Utils::isValid($data['validation_token'])) $user->validationToken = $data['validation_token'];

        /* @var $location \Metier\Location */
        $location = $this->loadLocation($data);
        if (\Utils::isValid($location)) {
            $user->setLocation($location);
        }

        return $user;
    }

    private function loadLocation ($data) {
        /* @var $location \Metier\Location */
        $location = new Location();

        if (\Utils::isValid($data['address'])) $location->address = $data['address'];
        if (\Utils::isValid($data['city'])) $location->city = $data['city'];
        if (\Utils::isValid($data['zipcode'])) $location->zipcode = $data['zipcode'];
        if (\Utils::isValid($data['country'])) $location->country = $data['country'];
        if (\Utils::isValid($data['lat'])) $location->lat = $data['lat'];
        if (\Utils::isValid($data['lng'])) $location->lng = $data['lng'];

        return $location;
    }
}