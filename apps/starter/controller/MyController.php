<?php

namespace Controller;
use Matter\Message;

/**
 * Description of MyController
 *
 * @author guich
 */

class MyController extends \Matter\IController {
    public function _default () {
        $pouet = 'Ma bite';

        $mdl = $this->mdl('MyModel');
        $users = $mdl->users();

        return $this->view('MyView')->data(array('users' => $users));
    }

    public function test () {
        $pouet = 'Ma bite';

        $mdl = $this->mdl('MyModel');
        $users = $mdl->users();

        return $this->view('MyView')->data(array('users' => $users));
    }

    public function payway () {
        $post = \Matter\Conversation::init('POST');
        $token = $post->get('stripeToken');
        $ref = 'REF_' . bin2hex(openssl_random_pseudo_bytes(13));

        if ($token != null) {
            // Payment
            try {
                \Payway\Payway::payment($token, 100, $ref);
            } catch (\Exception $e) {
                return (new Message('Your payment has failed. No transactions have been made on your CB. Contact our technical service or try again later'))->json();
            }

            // Facturation
            try {
                \Payway\Payway::invoice(
                    \Payway\Customer::create(
                        null,
                        'Guillaume',
                        'Chanteloube',
                        '33b rue Bataille',
                        'Lyon',
                        '69008',
                        '0603541823',
                        'guillaumech@gmail.com'
                    ),
                    \Payway\Invoice::create(
                        'Titre de ma facture',
                        array(
                            \Payway\Item::create('Box lancement', 40, 1, 0.200),
                            \Payway\Item::create('Box', 60, 1, 0.200)
                        )
                    ),
                    $ref
                );
            } catch (\Exception $e) {
                // Facturation crash!
            }
        }

        return (new Message('Your payment was done, thank you.'))->json();
    }
}
?>
