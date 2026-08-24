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
            'twilio' => new TwilioWhatsAppProvider(
                (string) config('services.twilio.sid'),
                (string) config('services.twilio.token'),
                (string) config('services.twilio.from'),
                (string) config('services.twilio.account_sid') ?: null,
                filter_var(config('services.twilio.verify_ssl', true), FILTER_VALIDATE_BOOLEAN),
                (string) config('services.twilio.content_sid') ?: null,
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
