<?php

namespace GmailMailService\MailManager;

use Illuminate\Mail\MailManager;
use GmailMailService\MailTransport\GmailTransport;
use Google_Client;
use Google_Service_Gmail;

class GmailMailManager extends MailManager {

    protected function createGmailTransport() {
        $config = $this->app['config']->get('services.gmailmailer', []);

        $googleClient = $this->getGoogleClient($config);

        $googleService = null;
        if (isset($config['accesstoken']) && $config['accesstoken'] != null) {
            /* AccessToken Will be set dynamically from tenant's(store's) database */
            $googleClient->setAccessToken($config['accesstoken']);
            $googleService = new Google_Service_Gmail($googleClient);
        }

        return new GmailTransport(
                $googleClient, $googleService
        );
    }

    function getGoogleClient($config) {
        $appName = isset($config['AppName']) ? $config['AppName'] : env('APP_NAME');
        $client = new Google_Client();
        $client->setApplicationName($appName);
        $client->setScopes($config['scopes']);
        $client->setAuthConfig($config['authConfig']);
        $client->setAccessType($config['tokenType']);
        $client->setPrompt($config['prompt']);
        return $client;
    }

}
