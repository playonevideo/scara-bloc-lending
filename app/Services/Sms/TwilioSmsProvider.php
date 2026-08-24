<?php

namespace App\Services\Sms;

use Twilio\Rest\Client;

class TwilioSmsProvider implements SmsProvider
{
    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from,
    ) {
    }

    public function send(string $to, string $message): void
    {
        $client = new Client($this->sid, $this->token);

        $client->messages->create($to, [
            'from' => $this->from,
            'body' => $message,
        ]);
    }
}
