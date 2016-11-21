<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 18/11/16
 * Time: 11:47
 */

namespace Payway;

use Matter\Utils;
use Stripe\Stripe;

class Payway {
    public static $token = null;
    public static $ref = null;
    public static $number = null;
    public static $name = null;
    public static $exp_month = null;
    public static $exp_year = null;
    public static $cvc = null;

    public static function init() {
        \Stripe\Stripe::setApiKey(self::getEnvironment('sk_stripe'));

        if (array_key_exists('stripeToken', $_POST) &&
            array_key_exists('number', $_POST) &&
            array_key_exists('name', $_POST) &&
            array_key_exists('exp_month', $_POST) &&
            array_key_exists('exp_year', $_POST) &&
            array_key_exists('cvc', $_POST)) {
            self::$number = $_POST['number'];
            self::$name = $_POST['name'];
            self::$exp_month = $_POST['exp_month'];
            self::$exp_year = $_POST['exp_year'];
            self::$cvc = $_POST['cvc'];

            self::createToken();
        } else {
            throw new \Exception('Invalid parameters!');
        }
    }

    private static function createToken($street = null, $city = null, $zipCode = null, $country = null) {
        self::$token = \Stripe\Token::create(array(
            "card" => array(
                "number" => self::$number,
                "name" => self::$name,
                "exp_month" => self::$exp_month,
                "exp_year" => self::$exp_year,
                "cvc" => self::$cvc,
                "address_country" => $country,
                "address_city" => $city,
                "address_line1" => $street,
                "address_zip" => $zipCode
            )
        ));
    }

    public static function setRef($ref) {
        self::$ref = $ref;
    }

    private static function getEnvironment($key = null) {
        $environment_ini = parse_ini_file('../conf/environment.ini', true);
        $environment = $environment_ini['current_environment']['environment'];
        if ($key == null) return $environment_ini[$environment];
        else return $environment_ini[$environment][$key];
    }

    public static function render(  $action,
                                    $title = 'Secure payment',
                                    $button = 'Payment',
                                    $cardPlaceholder = 'Card number',
                                    $namePlaceholder = 'Full name',
                                    $monthPlaceholder = 'MM',
                                    $yearPlaceholder = 'YY',
                                    $cvcPlaceholder = 'CVC') {
        $html = '
            <link rel="stylesheet" href="kernel/dependency/payway/assets/css/payway.css" type="text/css" />
                    
            <div class="payment-container">
                <label class="payment-title">' . $title . '</label>
                <div class="card-wrapper"></div>

                <div class="form-container active">
                    <form action="' . $action . '" id="payment-form" method="POST">
                        <span class="payment-errors"></span>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                                    </span>
                                    <input class="form-control number-payment" placeholder="' . $cardPlaceholder . '" type="text" name="number" data-stripe="number">
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                    </span>
                                    <input class="form-control number-payment" placeholder="' . $namePlaceholder . '" type="text" name="name" data-stripe="name">
                                </div>
                            </div>
                            <div class="col-xs-2 month-container">
                                <div class="input-group">
                                    <input class="form-control number-payment no-radius" placeholder="' . $monthPlaceholder . '" type="text" name="exp_month" data-stripe="exp_month">
                                </div>
                            </div>
                            <div class="col-xs-2 year-container">
                                <div class="input-group">
                                    <input class="form-control number-payment" placeholder="' . $yearPlaceholder . '" type="text" name="exp_year" data-stripe="exp_year">
                                </div>
                            </div>
                            <div class="col-xs-2 cvc-container">
                                <div class="input-group">
                                    <input class="form-control number-payment" placeholder="' . $cvcPlaceholder . '" type="text" name="cvc" data-stripe="cvc">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="button postfix pay-button">
                            <img class="loader-pay-button" src="kernel/dependency/payway/assets/img/loader.svg" />
                            <span class="label-pay-button">' . $button . '</span>
                        </button>
                    </form>
                </div>
            </div>

            <script src="https://js.stripe.com/v2/" type="text/javascript"></script>
            <script src="kernel/dependency/payway/assets/js/card.js" type="text/javascript"></script>
            <script src="kernel/dependency/payway/assets/js/payway.js" type="text/javascript"></script>
            <script type="text/javascript">
                Stripe.setPublishableKey(\'' . self::getEnvironment('pk_stripe') . '\');
                
                var payway = new Payway();
                payway.init();
            </script>
        ';

        return $html;
    }

    // ************ Payment with Stripe ************ //
    public static function payment ($amount, $customer = null) {
        if (isset(self::$token) && !empty(self::$token) && self::$token != null) {
            if ($customer['stripeId'] == null) {
                self::createToken($customer['street'], $customer['city'], $customer['zip_code'], $customer['country']);

                $customer = \Stripe\Customer::create(array(
                    "description" => $customer['first_name'] . ' ' . $customer['last_name'],
                    "source" => self::$token
                ));

                \Stripe\Charge::create(array(
                    "amount" => $amount,
                    "currency" => "eur",
                    "customer" => $customer->id,
                    "email" => $customer['email'],
                    "description" => self::$ref
                ));
            } else {
                \Stripe\Charge::create(array(
                    "amount" => $amount,
                    "currency" => "eur",
                    "customer" => $customer['stripeId'],
                    "description" => self::$ref
                ));
            }
        } else {
            throw new \Exception('Invalid token!');
        }
    }

    // ************ Invoice with Factures.pro ************ //
    public static function invoice($customer, $invoice) {
        if (isset($customer) && !empty($customer)) {
            if ($customer['facturesId'] != null) {
                self::_invoice($customer['facturesId'], $invoice);
            } else {
                $id = self::_customer($customer);
                if ($id != null) self::_invoice($id, $invoice);
            }
        }
    }

    private static function _invoice ($customerId, $invoice) {
        $options = array(
            "currency" => "EUR",
            "customer_id" => $customerId,
            "title" => $invoice['title'],
            "type_doc" => "draft",
            "payment_ref" => self::$ref,
            "payment_mode" => 2,
            "items" => $invoice['items']
        );

        $curl = curl_init();
        $curl = self::_curlHeader($curl);
        curl_setopt($curl, CURLOPT_URL, 'https://www.facturation.pro/firms/' . self::getEnvironment('firm_id_factures') . '/invoices.json');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options));
        curl_exec($curl);
        curl_close($curl);
    }

    private static function _customer ($customer) {
        $curl = curl_init();
        $curl = self::_curlHeader($curl);
        curl_setopt($curl, CURLOPT_URL, 'https://www.facturation.pro/firms/' . self::getEnvironment('firm_id_factures') . '/customers.json');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($customer));
        $return = self::curlReturn(curl_exec($curl));
        curl_close($curl);

        if (isset($return) && !empty($return) && array_key_exists('id', $return)) return $return->id;
        else return null;
    }

    private static function _curlHeader ($curl) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, self::getEnvironment('access_factures'));
        curl_setopt($curl, CURLOPT_USERAGENT,'User-Agent: ' . self::getEnvironment('app_factures') . ' (' . self::getEnvironment('email_factures') . ')');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3); Failed
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); Failed
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_HEADER, true);
        return $curl;
    }

    private function curlReturn($return) {
        $response = split("\n", $return);
        return json_decode($response[count($response) - 1]);
    }
}