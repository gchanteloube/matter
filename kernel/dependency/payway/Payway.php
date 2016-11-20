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
    // ************ Internal attributes ************ //
    public static $modulePath = '';

    // ************ Invoice attributes ************ //
    private static $invoice = null;

    public static function import () {
        require_once self::$modulePath . 'dependency/composer/vendor/autoload.php';
        require_once self::$modulePath . 'dependency/Utils.php';
        require_once self::$modulePath . 'business/Conversation.php';
        require_once self::$modulePath . 'dependency/payway/Invoice.php';
    }

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
                        <button type="submit" class="button postfix pay-button">' . $button . '</button>
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
    public static function payment () {
        \Stripe\Stripe::setApiKey(self::getSkStripe());

        $post = \Matter\Conversation::init('POST');
        $token = $post->get('stripeToken');

        if (isset($token) && !empty($token) && $token != null) {
            try {
                $charge = \Stripe\Charge::create(array(
                    "amount" => 100, // Amount in cents
                    "currency" => "eur",
                    "source" => $token,
                    "description" => "Test invoice"
                ));

                if (isset(self::$invoice) && !empty(self::$invoice)) self::insertInvoice();

                if (isset($charge) && !empty($charge) && $charge != null) echo 'true';
                else echo 'false';
            } catch (\Stripe\Error\Card $e) {
                echo 'false';
            }
        }
    }

    public static function callback ($callback) {

    }

    // ************ Invoice with Factures.pro ************ //
    public static function invoice($invoice) {
        self::$invoice = $invoice;
    }

    private function insertInvoice() {
        $options = array(
            "currency" => "EUR",
            "customer_id" => 722833,
            "invoiced_on" => "2016-11-20",
            "title" => self::$invoice,
            "type_doc" => "draft",
            "items" => array(
                array(
                    "position" => 1,
                    "quantity" => "1.0",
                    "title" => "1 box",
                    "unit_price" => "60",
                    "vat" => "0.200"
                )
            )
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
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_HEADER, true);
        $return = curl_exec($curl);
        curl_close($curl);
    }
}

// ########################################################
// # Internal process (call, callback, etc.) ##############
// ########################################################

/*
if (array_key_exists('stripeToken', $_POST) && isset($_POST['stripeToken']) && !empty($_POST['stripeToken'])) {
    Payway::$modulePath = '../../';
    Payway::import();
    Payway::payment();
} else {
    Payway::import();
}
*/