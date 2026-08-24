<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaWhatsAppProvider implements SmsProvider
{
    use NormalizesPhoneNumbers;

    public function __construct(
        private readonly string $token,
        private readonly string $phoneNumberId,
        private readonly string $templateName,
        private readonly string $language,
        private readonly string $apiVersion,
        private readonly bool $verifySsl,
    ) {}

    public function send(string $to, string $message, array $variables = []): void
    {
        $parameters = [];
        foreach ($variables as $value) {
            $parameters[] = ['type' => 'text', 'text' => (string) $value];
        }

        $http = Http::timeout(20);
        if (! $this->verifySsl) {
            $http = $http->withoutVerifying();
        }

        $response = $http
            ->withToken($this->token)
            ->post("https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->normalizePhone($to),
                'type' => 'template',
                'template' => [
                    'name' => $this->templateName,
                    'language' => ['code' => $this->language],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => $parameters,
                        ],
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('WhatsApp Cloud API error: '.$response->body());
        }
    }
}
