<?php

namespace App\Services\Sms;

interface SmsProvider
{
    /**
     * Send a message to the given phone number.
     *
     * $variables holds the template variables (e.g. the verification code)
     * used by WhatsApp content templates.
     */
    public function send(string $to, string $message, array $variables = []): void;
}
