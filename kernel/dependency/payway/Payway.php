<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 18/11/16
 * Time: 11:47
 */

namespace Payway;

use Matter\Utils;

class Payway {
    public static $modulePath = '';

    public static function getEnvironment() {
        $environment_ini = parse_ini_file(self::$modulePath . '../conf/environment.ini', true);
        $environment = $environment_ini['current_environment']['environment'];
        return $environment_ini[$environment];
    }

    public static function getSkStripe() {
        $environment = Payway::getEnvironment();
        return $environment['sk_stripe'];
    }

    public static function getPkStripe() {
        $environment = Payway::getEnvironment();
        return $environment['pk_stripe'];
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
                                    <input class="form-control number-payment no-radius" placeholder="' . $monthPlaceholder . '" type="text" name="expiry" data-stripe="exp_month">
                                </div>
                            </div>
                            <div class="col-xs-2 year-container">
                                <div class="input-group">
                                    <input class="form-control number-payment" placeholder="' . $yearPlaceholder . '" type="text" name="expiry" data-stripe="exp_year">
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
                Stripe.setPublishableKey(\'' . self::getPkStripe() . '\');
                
                var payway = new Payway();
                payway.init();
            </script>
        ';

        return $html;
    }

    // ************ Payment with Stripe ************ //
    public static function payment ($token, $amount, $label) {
        \Stripe\Stripe::setApiKey(self::getSkStripe());

        if (isset($token) && !empty($token) && $token != null) {
            \Stripe\Charge::create(array(
                "amount" => $amount, // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => $label
            ));
        } else {
            throw new \Exception('Invalid token!');
        }
    }

    // ************ Invoice with Factures.pro ************ //
    public static function invoice($invoice) {
        $options = array(
            "currency" => "EUR",
            "customer_id" => 722833,
            "title" => $invoice['title'],
            "type_doc" => "draft",
            "items" => $invoice['items']
        );
        $creation = json_encode($options);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.facturation.pro/firms/44295/invoices.json');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $creation);
        curl_setopt($curl, CURLOPT_USERPWD, '41335:UkJ9u5Na0yKZsVVDGeG6');
        curl_setopt($curl, CURLOPT_USERAGENT,'User-Agent: MonApp (guillaumech@gmail.com)');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3); Failed
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); Failed
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_exec($curl);
        curl_close($curl);
    }
}