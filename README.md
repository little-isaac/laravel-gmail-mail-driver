# laravel-gmail-mail-driver
This package is used to provide gmail email service provider for the email driver in laravel.

### this is simple wrapper so you need to manage authentication and app approval process on your end and also need to manage refreshing the access token process on your end as well. 

## Add new `mailers` driver in `config/mail.php` as below

```
'gmail' =>[
    'transport' => 'gmail'
]
```

## Add new `gmailmailer` service in `config/services.php` as below

```
'gmailmailer' => [
    'AppName' => 'Your-app-name',
    'scopes' => [
        /* We need both the scopes for the API to send the email */
        Google_Service_Gmail::GMAIL_READONLY, // This scope is used to get the user's email address (From which we are sending emails)
        Google_Service_Gmail::GMAIL_SEND      // This scope is used to send the emails.
    ],
    'authConfig' => [
    /* 
    *  We are getting this details from the Google console.
    *
    */
        "web" => [
            "client_id" => env('GOOGLE_EMAIL_SEND_API_CLIENT_ID'),
            "client_secret" => env('GOOGLE_EMAIL_SEND_API_CLIENT_SECRET'),
            "redirect_uris" => [
                env('GOOGLE_EMAIL_SEND_API_REDIRECT')
            ],
        ]
    ],
    'tokenType' => 'offline',
    'prompt' => 'select_account consent',
]
```

## You also need to add accesstoken recevied from the google account to the gmailmailer dynamically / Manually 
### I personally prefer dynamically
You need to add it as array
```
Config::set('services.gmailmailer.accesstoken', $accessToken);
```
Where `$accessToken` will be as follows

```
$accessToken = [
    "access_token" => "...",
    "expires_in" => ...,
    "refresh_token" => "...",
    "scope" => "...",
    "token_type" => "...",
    "created" => ...
];
```
