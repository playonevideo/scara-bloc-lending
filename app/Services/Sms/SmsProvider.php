<?php

namespace App\Services\Sms;

interface SmsProvider
{
    /**
     * Send a text message to the given phone number.
     */
    public function send(string $to, string $message): void;
}
