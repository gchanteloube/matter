<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 30/08/16
 * Time: 16:32
 */

namespace Metier;


class User {
    public $id = null;
    public $email = null;
    public $firstName = null;
    public $lastName = null;
    private $location = null;
    public $credit = null;
    public $token = null;
    public $validationToken = null;

    public function getLocation () {
        /* @var $location \Metier\Location */
        $location = $this->location;

        return $location;
    }

    public function setLocation (Location $location) {
        $this->location = $location;
    }
}