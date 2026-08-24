<?php

namespace App\Services\Sms;

use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

class TwilioWhatsAppProvider implements SmsProvider
{
    use NormalizesPhoneNumbers;

    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from,
        private readonly ?string $accountSid = null,
        private readonly bool $verifySsl = true,
        private readonly ?string $contentSid = null,
    ) {}

    public function send(string $to, string $message, array $variables = []): void
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

        $params = [
            'from' => 'whatsapp:'.$this->from,
        ];

        // WhatsApp Business requires pre-approved templates (Content SID) for
        // outbound messages. The sandbox accepts free-form "body" messages.
        if ($this->contentSid) {
            $params['contentSid'] = $this->contentSid;
            $params['contentVariables'] = json_encode($variables);
        } else {
            $params['body'] = $message;
        }

        $client->messages->create('whatsapp:'.$this->normalizePhone($to), $params);
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
}
