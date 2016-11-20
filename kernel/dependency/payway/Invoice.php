<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 20/11/16
 * Time: 18:36
 */

namespace Payway;

class Invoice {
    public static function create ($title, $listItem = array()) {
        return array('title' => $title, 'items' => $listItem);
    }
}

class Item {
    public static function create ($title, $price, $quantity, $vat) {
        return array('title' => $title, 'unit_price' => $price, 'quantity' => $quantity, 'vat' => $vat);
    }
}