<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 21/11/16
 * Time: 15:24
 */

namespace Payway;


class Customer {
    public static function create ($id = null, $firstName, $lastName, $street, $city, $zipCode, $phone, $email, $individual = true) {
        return array(
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'street' => $street,
            'city' => $city,
            'zip_code' => $zipCode,
            'phone' => $phone,
            'email' => $email,
            'individual' => $individual
        );
    }
}