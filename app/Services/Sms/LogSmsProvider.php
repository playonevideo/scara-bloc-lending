<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProvider
{
    public function send(string $to, string $message, array $variables = []): void
    {
        Log::info('[2FA] To: '.$to.' | '.$message);
    }
}
