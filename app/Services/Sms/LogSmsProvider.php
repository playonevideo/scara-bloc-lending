<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProvider
{
    public function send(string $to, string $message): void
    {
        Log::info('[SMS] To: '.$to.' | '.$message);
    }
}
