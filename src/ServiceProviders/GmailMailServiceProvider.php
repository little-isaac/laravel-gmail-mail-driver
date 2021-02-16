<?php

namespace GmailMailService\ServiceProviders;

use Illuminate\Mail\MailServiceProvider;
use GmailMailService\MailManager\GmailMailManager;

class GmailMailServiceProvider extends MailServiceProvider {

    protected function registerIlluminateMailer() {
        $this->app->singleton('mail.manager', function($app) {
            return new GmailMailManager($app);
        });

        // Copied from Illuminate\Mail\MailServiceProvider
        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }

}
