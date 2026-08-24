<?php

namespace App\Services;

use App\Models\TwoFactorChallenge;
use App\Models\User;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class TwoFactorService
{
    public function __construct(private readonly SmsManager $sms) {}

    /**
     * Generate and send a one-time code.
     *
     * When $phone is given it is used instead of the user's current phone,
     * which is used for confirming a phone number change.
     *
     * @throws RuntimeException when the phone is missing or the user is throttled.
     */
    public function sendCode(User $user, ?string $phone = null): string
    {
        $to = $phone ?? $user->phone;

        if (! $to) {
            throw new RuntimeException('Nu există un număr de telefon asociat contului.');
        }

        $this->assertNotThrottled($user);

        $length = (int) config('sms.code.length', 6);
        $code = (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);

        TwoFactorChallenge::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes((int) config('sms.code.expires_minutes', 10)),
        ]);

        $this->sms->send($to, "Codul tău de verificare Vecini este: {$code}");

        // In development (log driver) the code is not delivered as a real message,
        // so surface it in the UI via the session flash data.
        if (config('sms.provider') === 'log') {
            session()->flash('sms_code', $code);
        }

        return $code;
    }

    /**
     * Verify a code and mark it as consumed on success.
     */
    public function verify(User $user, string $code): bool
    {
        $challenge = TwoFactorChallenge::query()
            ->where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $challenge) {
            return false;
        }

        $maxAttempts = (int) config('sms.code.max_attempts', 5);

        if ($challenge->attempts >= $maxAttempts) {
            return false;
        }

        if (! Hash::check($code, $challenge->code_hash)) {
            $challenge->increment('attempts');

            return false;
        }

        $challenge->update(['consumed_at' => now()]);

        return true;
    }

    /**
     * Prevent spam by throttling how often a new code can be requested.
     */
    private function assertNotThrottled(User $user): void
    {
        $throttleSeconds = (int) config('sms.code.throttle_seconds', 60);

        $recent = TwoFactorChallenge::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>', now()->subSeconds($throttleSeconds))
            ->exists();

        if ($recent) {
            throw new RuntimeException('Vă rugăm să așteptați înainte de a solicita un nou cod.');
        }
    }
}
