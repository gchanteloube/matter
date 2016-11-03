<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 16/03/16
 * Time: 23:29
 */

namespace Metier;

class Email {
    public $recipient = '';
    public $sender = '';
    public $template = '';
    public $token = '';
    public $uid = '';
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $tel = '';
    public $businessCategory = '';
    public $content = '';
    public $ambFname = '';
    public $ambLname = '';
    public $url = 'http://pro.shoops-lyon.fr';

    public function send () {
        $mg = new \Mailgun\Mailgun("key-ec2a41ea10211ef58a0dc2b905ba8c05");
        $domain = "shoops-lyon.fr";

        $messageBldr = $mg->MessageBuilder();

        if ($this->template == 'contact') {
            $messageBldr->setFromAddress($this->recipient, array("first"=>"Contact", "last" => "Commerçant [Site Pro]"));
            $messageBldr->addToRecipient('contact@shoops.eu');

            $messageBldr->setHtmlBody($this->content);
        } else {
            $messageBldr->setFromAddress("no-reply@shoops-lyon.fr", array("first"=>"Shoops", "last" => "Lyon"));

            $templateHTML = new \DOMDocument();
            $templateHTML->loadHTMLFile("EmailTemplates/" . $this->template . ".html");

            $content = '';
            switch ($this->template) {
                case 'validInscription':
                    $messageBldr->addToRecipient($this->recipient);
                    $messageBldr->setSubject("Bienvenue sur Shoops !");
                    $messageBldr->addCampaignId("Inscription");
                    $content = str_replace('%URL_CONFIRM_1%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $templateHTML->saveHTML());
                    $content = str_replace('%URL_CONFIRM_2%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $content);
                    $content = str_replace('%URL_CONFIRM_3%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $content);
                    $content = str_replace('%USER_NAME%', $this->firstName, $content);
                    break;
                case 'validInscriptionGratuit':
                    $messageBldr->addToRecipient("approbation@shoops.eu");
                    $messageBldr->setSubject("Compte gratuit [attente approbation]");
                    $messageBldr->addCampaignId("Inscription");
                    $content = str_replace('%URL_CONFIRM_1%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $templateHTML->saveHTML());
                    $content = str_replace('%URL_CONFIRM_2%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $content);
                    $content = str_replace('%URL_CONFIRM_3%', $this->url . '?t=' . $this->token . '&u=' . $this->uid, $content);
                    $content = str_replace('%USER_FIRST_NAME%', $this->firstName, $content);
                    $content = str_replace('%USER_LAST_NAME%', $this->lastName, $content);
                    $content = str_replace('%USER_EMAIL%', $this->email, $content);
                    $content = str_replace('%USER_CATEGORY%', $this->businessCategory, $content);
                    break;
                case 'resetPasswd':
                    $messageBldr->addToRecipient($this->recipient);
                    $messageBldr->setSubject("Récupération de mot de passe");
                    $messageBldr->addCampaignId("Reset");
                    $content = str_replace('%URL_RESET_1%', $this->url . '?r=true&t=' . $this->token . '&u=' . $this->uid, $templateHTML->saveHTML());
                    $content = str_replace('%URL_RESET_2%', $this->url . '?r=true&t=' . $this->token . '&u=' . $this->uid, $content);
                    $content = str_replace('%URL_RESET_3%', $this->url . '?r=true&t=' . $this->token . '&u=' . $this->uid, $content);
                    break;
                case 'getContacted':
                    $messageBldr->addToRecipient("contact@shoops-lyon.fr");
                    $messageBldr->setSubject("Nouvelle demande de prise de contact");
                    $messageBldr->addCampaignId("Inscription");
                    $content = str_replace('%USER_LAST_NAME%', $this->lastName, $templateHTML->saveHTML());
                    $content = str_replace('%USER_EMAIL%', $this->email, $content);
                    $content = str_replace('%USER_TEL%', $this->tel, $content);
                    $content = str_replace('%USER_FOOD%', $this->content, $content);
                    break;
                case 'unsubscription':
                    $messageBldr->addToRecipient("lionel.lansiart@gmail.com"); // contact@shoops-lyon.fr
                    $messageBldr->setSubject("Nouvelle perte probable de client");
                    $messageBldr->addCampaignId("Inscription");
                    $content = str_replace('%USER_REASON%', $this->email, $templateHTML->saveHTML());
                    $content = str_replace('%USER_TEXTREASON%', $this->lastName, $content);
                    $content = str_replace('%USER_TEL%', $this->tel, $content);
                    $content = str_replace('%USER_AMBLN%', $this->ambLname, $content);
                    $content = str_replace('%USER_AMBFN%', $this->ambFname, $content);
                    break;
            }

            $messageBldr->setHtmlBody($content);
            $messageBldr->setClickTracking(true);
        }

        return $mg->post("{$domain}/messages", $messageBldr->getMessage(), $messageBldr->getFiles());
    }
}