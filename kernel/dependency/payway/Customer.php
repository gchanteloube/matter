<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 21/11/16
 * Time: 15:24
 */

namespace Payway;


class Customer {
    public static function create ($firstName, $lastName, $street, $city, $zipCode, $country, $phone, $email, $individual, $stripeId = null, $facturesId = null) {
        return array(
            'first_name' => ucfirst(trim($firstName)),
            'last_name' => ucfirst(trim($lastName)),
            'street' => trim($street),
            'city' => trim(ucwords($city)),
            'zip_code' => trim($zipCode),
            'country' => trim($country),
            'phone' => trim($phone),
            'email' => trim(strtolower($email)),
            'individual' => $individual,
            'stripeId' => $stripeId,
            'facturesId' => $facturesId
        );
    }
}