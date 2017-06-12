<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 18/11/16
 * Time: 11:47
 */

namespace Payway;

use Matter\Conversation;
use Matter\Crypt;
use Matter\Utils;

class Payway {
    private static $token = null;
    private static $ref = null;
    private static $number = null;
    private static $name = null;
    private static $exp_month = null;
    private static $exp_year = null;
    private static $cvc = null;
    private static $paypal = false;
    public static $customer = null;


    public static function init() {
        if (array_key_exists('stripeToken', $_POST)) {
            // Stripe
            \Stripe\Stripe::setApiKey(self::getEnvironment('sk_stripe'));

            $session = Conversation::init('SESSION');
            $data = Crypt::decrypt($session->get('pw_JgZ6XDu8354aynFTwy3Z2HZgM2xqNv64t66', false));
            self::$customer = unserialize($data);

            if (array_key_exists('number', $_POST) &&
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
        } else {
            // Paypal
            self::$customer = array(
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'street' => $_POST['address_street'],
                'city' => $_POST['address_city'],
                'zip_code' => $_POST['address_zip'],
                'country' => $_POST['address_country'],
                'country_code' => $_POST['address_country_code'],
                'phone' => null,
                'email' => $_POST['payer_email'],
                'individual' => true,
                'stripe_id' => null,
                'facture_id' => null
            );
            self::$paypal = true;
        }
    }

    private static function createToken() {
        self::$token = \Stripe\Token::create(array(
            "card" => array(
                "number" => self::$number,
                "name" => self::$name,
                "exp_month" => self::$exp_month,
                "exp_year" => self::$exp_year,
                "cvc" => self::$cvc,
                "address_country" => self::$customer['country'],
                "address_city" => self::$customer['city'],
                "address_line1" => self::$customer['street'],
                "address_zip" => self::$customer['zip_code']
            )
        ));
    }

    public static function setRef($ref) {
        self::$ref = $ref;
    }

    public static function paypal($inputs) {
        self::$paypal = $inputs;
    }

    public static function currentCustomer() {
        $session = Conversation::init('SESSION');
        $data = Crypt::decrypt($session->get('pw_JgZ6XDu8354aynFTwy3Z2HZgM2xqNv64t66', false));
        return unserialize($data);
    }

    public static function clear() {
        $session = Conversation::init('SESSION');
        $session->set('pw_JgZ6XDu8354aynFTwy3Z2HZgM2xqNv64t66', '');
    }

    public static function customer($firstName, $lastName, $street, $city, $zipCode, $country, $countryCode, $phone, $email, $individual, $stripeId = null, $facturesId = null) {
        $customer = array(
            'first_name' => ucfirst(trim($firstName)),
            'last_name' => ucfirst(trim($lastName)),
            'street' => trim($street),
            'city' => trim(ucwords($city)),
            'zip_code' => trim($zipCode),
            'country' => trim($country),
            'country_code' => $countryCode,
            'phone' => trim($phone),
            'email' => trim(strtolower($email)),
            'individual' => $individual,
            'stripe_id' => $stripeId,
            'facture_id' => $facturesId
        );

        $session = Conversation::init('SESSION');
        $data = Crypt::encrypt(serialize($customer));
        $session->set('pw_JgZ6XDu8354aynFTwy3Z2HZgM2xqNv64t66', $data, false);
    }

    private static function getEnvironment($key = null) {
        $environment_ini = parse_ini_file('../conf/environment.ini', true);
        $environment = $environment_ini['current_environment']['environment'];
        if ($key == null) return $environment_ini[$environment];
        else return $environment_ini[$environment][$key];
    }

    public static function render(  $action,
                                    $title = 'Paiement sécurisé',
                                    $button = 'Payer',
                                    $cardPlaceholder = 'Numéro de carte',
                                    $namePlaceholder = 'Nom du titulaire',
                                    $monthPlaceholder = 'MM',
                                    $yearPlaceholder = 'YY',
                                    $cvcPlaceholder = 'CVC') {
        $session = Conversation::init('SESSION');
        $data = Crypt::decrypt($session->get('pw_JgZ6XDu8354aynFTwy3Z2HZgM2xqNv64t66', false));
        $customer = unserialize($data);

        if (!Utils::valid($customer)) {
            return 'A customer required!';
        } else {
            $html = '
                <link rel="stylesheet" href="kernel/dependency/payway/assets/css/payway.css" type="text/css" />
                        
                <div class="payment-container">
                    <label class="payment-title"><i class="fa fa-shield" aria-hidden="true"></i> ' . $title . '</label>
                    <img class="cb-img" src="kernel/dependency/payway/assets/img/cb.jpg" />
                    <div class="card-wrapper"></div>

                    <div class="form-container active">
                        <form action="' . $action . '" id="payment-form" method="POST">
                            <span class="payment-errors"></span>
                            <div class="row">
                                <div class="col-xs-12">
                                    Numéro de carte
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
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                    Nom du titulaire
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                        </span>
                                        <input class="form-control" placeholder="' . $namePlaceholder . '" type="text" name="name" data-stripe="name">
                                    </div>
                                </div>
                                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 month-container">
                                    Mois
                                    <div class="input-group">
                                        <input class="form-control no-radius" placeholder="' . $monthPlaceholder . '" type="text" name="exp_month" data-stripe="exp_month">
                                    </div>
                                </div>
                                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 year-container">
                                    Année
                                    <div class="input-group">
                                        <input class="form-control" placeholder="' . $yearPlaceholder . '" type="text" name="exp_year" data-stripe="exp_year">
                                    </div>
                                </div>
                                <div class="col-xs-4 col-sm-2 col-md-2 col-lg-2 cvc-container">
                                    Code
                                    <div class="input-group">
                                        <input class="form-control cvc-payment" placeholder="' . $cvcPlaceholder . '" type="text" name="cvc" data-stripe="cvc">
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
            ';

            if (self::$paypal != null) {
                $html .= '
                    <label class="or-paypal">ou</label>
                    <div class="paypal-container">
                        <img class="paypal-logo" src="kernel/dependency/payway/assets/img/paypal.png" />
                        <div class="paypal-module">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">                                
                                <input type="hidden" name="cmd" value="_s-xclick">
                                ' . self::$paypal . '
                                <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal, le réflexe sécurité pour payer en ligne">
                                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                                <input type="hidden" name="first_name" value="' . $customer['first_name'] . '">
                                <input type="hidden" name="last_name" value="' . $customer['last_name'] . '">
                                <input type="hidden" name="address1" value="' . $customer['street'] . '">
                                <input type="hidden" name="city" value="' . $customer['city'] . '">
                                <input type="hidden" name="zip" value="' . $customer['zip_code'] . '">
                                <input type="hidden" name="night_phone_a" value="' . $customer['phone'] . '">
                                <input type="hidden" name="email" value="' . $customer['email'] . '">
                                <input name="notify_url" value="' . Utils::getEnvironment()['site'] . '/' . $action . '" type="hidden">
                                
                ';

                $html .= '
                            </form>
                        </div>
                    </div>
                ';
            }

            $html .= '
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
    }

    // ************ Payment with Stripe ************ //
    public static function payment ($amount) {
        if (!self::$paypal) {
            if (isset(self::$token) && !empty(self::$token) && self::$token != null) {
                if (self::$customer['stripe_id'] == null) {
                    $customer = \Stripe\Customer::create(array(
                        "description" => self::$customer['first_name'] . ' ' . self::$customer['last_name'],
                        "source" => self::$token
                    ));

                    \Stripe\Charge::create(array(
                        "amount" => $amount,
                        "currency" => "eur",
                        "customer" => $customer->id,
                        "description" => self::$ref
                    ));
                } else {
                    \Stripe\Charge::create(array(
                        "amount" => $amount,
                        "currency" => "eur",
                        "customer" => self::$customer['stripe_id'],
                        "description" => self::$ref
                    ));
                }
            } else {
                throw new \Exception('Invalid token!');
            }
        }
    }

    // ************ Invoice with Factures.pro ************ //
    public static function invoice($invoice) {
        if (isset(self::$customer) && !empty(self::$customer)) {
            if (self::$customer['facture_id'] != null) {
                self::_invoice(self::$customer['facture_id'], $invoice);
            } else {
                $id = self::_customer();
                if ($id != null) self::_invoice($id, $invoice);
            }
        }
    }

    private static function _invoice ($customerId, $invoice) {
        $data = array(
            "currency" => "EUR",
            "customer_id" => $customerId,
            "title" => $invoice['title'],
            "payment_ref" => self::$ref,
            "payment_mode" => 2,
            "vat_exemption" => 'Offre de lancement',
            "paid_on" => (new \DateTime('now'))->format('Y-m-d G:i:s'),
            "items" => $invoice['items']
        );

        $curl = curl_init();
        $curl = self::_curlHeader($curl);
        curl_setopt($curl, CURLOPT_URL, 'https://www.facturation.pro/firms/' . self::getEnvironment('firm_id_facturepro') . '/invoices.json');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $return = curl_exec($curl);
        curl_close($curl);
    }

    private static function _customer () {
        $data = array(
            'first_name' => self::$customer['first_name'],
            'last_name' => self::$customer['last_name'],
            'street' => self::$customer['street'],
            'city' => self::$customer['city'],
            'zip_code' => self::$customer['zip_code'],
            'country' => self::$customer['country_code'],
            'phone' => self::$customer['phone'],
            'email' => self::$customer['email'],
            'individual' => true
        );

        $curl = curl_init();
        $curl = self::_curlHeader($curl);
        curl_setopt($curl, CURLOPT_URL, 'https://www.facturation.pro/firms/' . self::getEnvironment('firm_id_facturepro') . '/customers.json');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        $return = self::curlReturn(curl_exec($curl));
        curl_close($curl);

        if (isset($return) && !empty($return) && array_key_exists('id', $return)) return $return->id;
        else return null;
    }

    private static function _curlHeader ($curl) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, self::getEnvironment('access_facturepro'));
        curl_setopt($curl, CURLOPT_USERAGENT,'User-Agent: ' . self::getEnvironment('app_facturepro') . ' (' . self::getEnvironment('email_facturepro') . ')');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        //curl_setopt($curl, CURLOPT_SSLVERSION, 3); Failed
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); Failed
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_HEADER, true);
        return $curl;
    }

    private static function curlReturn($return) {
        $response = split("\n", $return);
        return json_decode($response[count($response) - 1]);
    }
}