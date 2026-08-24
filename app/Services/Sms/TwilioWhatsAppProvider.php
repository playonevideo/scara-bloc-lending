<?php

namespace App\Services\Sms;

use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

class TwilioWhatsAppProvider implements SmsProvider
{
    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from,
        private readonly ?string $accountSid = null,
        private readonly bool $verifySsl = true,
    ) {}

    public function send(string $to, string $message): void
    {
        // Twilio API Keys (SK...) require the Account SID as the third argument,
        // whereas Account SID + Auth Token auth only needs the first two.
        $client = new Client(
            $this->sid,
            $this->token,
            $this->accountSid,
            null,
            $this->httpClient(),
        );

        $client->messages->create('whatsapp:'.$this->normalize($to), [
            'from' => 'whatsapp:'.$this->from,
            'body' => $message,
        ]);
    }

    /**
     * A cURL client that skips SSL verification when the local environment
     * lacks a CA bundle (development only; keep verification enabled in prod).
     */
    private function httpClient(): ?CurlClient
    {
        if ($this->verifySsl) {
            return null;
        }

        return new CurlClient([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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
