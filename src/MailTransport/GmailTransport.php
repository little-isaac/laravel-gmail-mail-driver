<?php

namespace GmailMailService\MailTransport;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;
use Google_Service_Gmail_Message;

class GmailTransport extends Transport {

    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $googleClient;

    /**
     * API key.
     *
     * @var string
     */
    protected $googleService;

    /**
     * Create a new Custom transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string|null  $url
     * @param  string  $key
     * @return void
     */
    public function __construct($googleClient, $googleService) {
        $this->googleClient = $googleClient;
        $this->googleService = $googleService;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null) {
        $this->beforeSendPerformed($message);

//        $payload = $this->getPayload($message);
        $googleMessage = $this->createMessage($message);
        $userId = array_keys($message->getFrom())[0] ? array_keys($message->getFrom())[0] : 'me';
        $this->googleService->users_messages->send($userId, $googleMessage);
        $this->sendPerformed($message);
        return $this->numberOfRecipients($message);
    }

    function createMessage(Swift_Mime_SimpleMessage $swiftMessage) {
        $message = new Google_Service_Gmail_Message();
        $rawMessageString = "From: " . $this->mapContactsToNameEmail($swiftMessage->getFrom()) . "\r\n";
        $rawMessageString .= "To: " . $this->mapContactsToNameEmail($swiftMessage->getTo()) . "\r\n";
        if ($swiftMessage->getCc()) {
            $rawMessageString .= "Cc: " . $this->mapContactsToNameEmail($swiftMessage->getCc()) . "\r\n";
        }
        if ($swiftMessage->getBcc()) {
            $rawMessageString .= "Bcc: " . $this->mapContactsToNameEmail($swiftMessage->getBcc()) . "\r\n";
        }

        $rawMessageString .= "Reply-To: " . $this->mapContactsToNameEmail($swiftMessage->getFrom()) . "\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($swiftMessage->getSubject()) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
        $rawMessageString .= "{$swiftMessage->getBody()}\r\n";

        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessage);
        return $message;
    }

    protected function mapContactsToNameEmail($contacts) {
        if (empty($contacts)) {
            return '<>';
        }
        $str = '';
        foreach ($contacts as $address => $display) {
            $str .= $display." <{$address}>,";
//            $str .= " <{$address}>,";
        }
        return trim($str, ',');
    }

}
