<?php

namespace App\Services\Sms;

trait NormalizesPhoneNumbers
{
    /**
     * Convert Romanian local numbers ("07...") to E.164 ("+407...").
     */
    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);

        if (preg_match('/^0(\d{9})$/', $phone)) {
            return '+40'.substr($phone, 1);
        }

        return $phone;
    }
}
