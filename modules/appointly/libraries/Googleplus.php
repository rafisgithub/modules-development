<?php defined('BASEPATH') or exit('No direct script access allowed');

require('modules/appointly/vendor/autoload.php');

class Googleplus
{
    public function __construct()
    {

        $this->client = new Google_Client();
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
        $this->client->setApplicationName('Appointly Google Calendar API');
        $this->client->setClientId(get_option('google_client_id'));
        $this->client->setClientSecret(get_option('appointly_google_client_secret'));
        $this->client->setRedirectUri(base_url() . 'appointly/google/auth/oauth');
        $this->client->addScope(Google_Service_Calendar::CALENDAR);

        $usingHttps = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? true : false;

        if (!$usingHttps) {
            $httpClient = new GuzzleHttp\Client([
                'verify' => false, // otherwise HTTPS requests will fail.
            ]);
            $this->client->setHttpClient($httpClient);
        }
    }

    public function client()
    {
        return $this->client;
    }


    public function loginUrl()
    {
        return $this->client->createAuthUrl();
    }


    public function getAuthenticate()
    {
        return $this->client->authenticate();
    }


    public function getAccessToken()
    {
        return $this->client->getAccessToken();
    }


    public function setAccessToken($token)
    {
        return $this->client->setAccessToken($token);
    }

    public function refreshToken($token)
    {
        return $this->client->refreshToken($token);
    }

    public function isAccessTokenExpired()
    {
        return $this->client->isAccessTokenExpired();
    }

    public function revokeToken()
    {
        $token = $this->client->setAccessToken($this->getTokenType('access_token'));
        get_instance()->db->where('staff_id', get_staff_user_id());
        get_instance()->db->delete(db_prefix() . 'appointly_google');
        return $this->client->revokeToken($token);
    }

    public function getTokenType($type)
    {
        get_instance()->db->select($type);
        get_instance()->db->where('staff_id', get_staff_user_id());
        return get_instance()->db->get(db_prefix() . 'appointly_google')->row_array()[$type];
    }
}
