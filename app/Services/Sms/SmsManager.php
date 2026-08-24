<?php

namespace App\Services\Sms;

use Illuminate\Contracts\Container\Container;
use RuntimeException;

class SmsManager
{
    public function __construct(private readonly Container $container) {}

    /**
     * Resolve the configured SMS provider.
     */
    public function provider(): SmsProvider
    {
        return match (config('sms.provider', 'log')) {
            'meta' => new MetaWhatsAppProvider(
                (string) config('services.meta_whatsapp.token'),
                (string) config('services.meta_whatsapp.phone_number_id'),
                (string) config('services.meta_whatsapp.template_name'),
                (string) config('services.meta_whatsapp.language', 'ro'),
                (string) config('services.meta_whatsapp.api_version', 'v21.0'),
                filter_var(config('services.meta_whatsapp.verify_ssl', true), FILTER_VALIDATE_BOOLEAN),
            ),
            'log' => new LogSmsProvider,
            default => throw new RuntimeException('Unknown SMS provider: '.config('sms.provider')),
        };
    }

    public function send(string $to, string $message, array $variables = []): void
    {
        $this->provider()->send($to, $message, $variables);
    }
}
