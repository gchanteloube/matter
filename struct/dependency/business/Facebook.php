<?php
/**
 * Created by PhpStorm.
 * User: gchanteloube
 * Date: 22/04/16
 * Time: 10:32
 */

namespace Metier;


class Facebook {
    private $appId = null;
    private $appSecret = null;
    private $version = null;
    private $token = null;
    private $fbInstance = null;

    public function __construct() {
        $conf = parse_ini_file("Config/Facebook.ini");

        // ID Facebook app
        $this->appId = $conf['FB']['appId'];
        $this->appSecret = $conf['FB']['appSecret'];
        $this->version = $conf['FB']['version'];

        $this->fbInstance = new \Facebook\Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'default_graph_version' => $this->version
        ]);

        $operator = \Utils::getCurrentOperator();
        $this->token = $operator->fbToken;

    }

    public function isConnected () {
        if (\Utils::isValid($this->token)) {
            return true;
        } else {
            return false;
        }
    }

    public function getRedirectUrl () {
        $helper = $this->fbInstance->getRedirectLoginHelper();
        return $helper->getLoginUrl(\Utils::getEnvironmentUrl() . '../S/Facebook/callbackConnect', array('scope' => 'publish_actions, publish_pages, manage_pages, pages_show_list'));
    }

    public function getAllFeedShoops () {
        if (\Utils::isValid($this->token)) {
            try {
                $response = $this->fbInstance->get('/me/feed', $this->token);
                return $response;
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                return 'Graph returned an error: ' . $e->getMessage();
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                return 'Facebook SDK returned an error: ' . $e->getMessage();
            }
        }
    }

    public function checkToken () {
        $helper = $this->fbInstance->getRedirectLoginHelper();
        return $helper->getAccessToken();
    }

    public function getImageFromPostId ($postId) {
        if (\Utils::isValid($this->token)) {
            try {
                $response = $this->fbInstance->get('/' . $postId . '?fields=full_picture,picture', $this->token);
                return $response;
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                return 'Graph returned an error: ' . $e->getMessage();
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                return 'Facebook SDK returned an error: ' . $e->getMessage();
            }
        }
    }

    public function postOnFeed ($data) {
        try {
            $operator = \Utils::getCurrentOperator();
            $response = $this->fbInstance->get('/' . $operator->fbPageId . '?fields=access_token', $this->token);

            $pageToken = json_decode($response->getBody())->access_token;
            $response = $this->fbInstance->post('/' . $operator->fbPageId . '/feed', $data, $pageToken);

            return $response;
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }

    public function getAllPages () {
        try {
            $response = $this->fbInstance->get('me/accounts', $this->token);

            return $response->getDecodedBody()['data'];
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }
}