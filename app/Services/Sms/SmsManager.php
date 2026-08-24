<?php

namespace App\Services\Sms;

use Illuminate\Contracts\Container\Container;
use RuntimeException;

class SmsManager
{
    public function __construct(private readonly Container $container)
    {
    }

    /**
     * Resolve the configured SMS provider.
     */
    public function provider(): SmsProvider
    {
        return match (config('sms.provider', 'log')) {
            'twilio' => new TwilioSmsProvider(
                (string) config('services.twilio.sid'),
                (string) config('services.twilio.token'),
                (string) config('services.twilio.from'),
            ),
            'log' => new LogSmsProvider(),
            default => throw new RuntimeException('Unknown SMS provider: '.config('sms.provider')),
        };
    }

    public function send(string $to, string $message): void
    {
        $this->provider()->send($to, $message);
    }
}
