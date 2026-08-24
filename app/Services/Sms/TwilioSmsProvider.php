<?php

namespace App\Services\Sms;

use Twilio\Rest\Client;

class TwilioSmsProvider implements SmsProvider
{
    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from,
        private readonly ?string $accountSid = null,
    ) {}

    public function send(string $to, string $message): void
    {
        // Twilio API Keys (SK...) require the Account SID as the third argument,
        // whereas Account SID + Auth Token auth only needs the first two.
        $client = $this->accountSid
            ? new Client($this->sid, $this->token, $this->accountSid)
            : new Client($this->sid, $this->token);

        $client->messages->create($this->normalize($to), [
            'from' => $this->from,
            'body' => $message,
        ]);
    }

    /**
     * Convert Romanian local numbers ("07...") to E.164 ("+407...").
     */
    private function normalize(string $phone): string
    {
        $phone = trim($phone);

        if (preg_match('/^0(\d{9})$/', $phone)) {
            return '+40'.substr($phone, 1);
        }

        return $phone;
    }
}
