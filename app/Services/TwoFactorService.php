<?php

namespace App\Services;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorService
{
    /**
     * Generate a new TOTP secret key.
     */
    public function generateSecretKey(): string
    {
        return (new Google2FA)->generateSecretKey();
    }

    /**
     * Build the otpauth:// URI used to render the QR code.
     */
    public function qrCodeUrl(User $user, string $secret): string
    {
        $issuer = config('app.name', 'Vecini');
        $label = rawurlencode($issuer.':'.$user->email);

        return "otpauth://totp/{$label}?secret={$secret}&issuer=".rawurlencode($issuer);
    }

    /**
     * Verify a 6-digit TOTP code against a secret.
     */
    public function verify(string $secret, string $code): bool
    {
        try {
            return (new Google2FA)->verifyKey($secret, $code);
        } catch (\Throwable) {
            return false;
        }
    }
}
