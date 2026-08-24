<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\CommunityRequestController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ObjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\WebAuthn\WebAuthnLoginController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

/*
|--------------------------------------------------------------------------
| Guest / Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('inregistrare/{code}', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('inregistrare', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    Route::get('two-factor/challenge', [TwoFactorChallengeController::class, 'create'])->name('two-factor.challenge');
    Route::post('two-factor/challenge', [TwoFactorChallengeController::class, 'store'])->name('two-factor.verify');
});

/*
|--------------------------------------------------------------------------
| WebAuthn / Passkeys
|--------------------------------------------------------------------------
*/
Route::prefix('webauthn')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('login/options', [WebAuthnLoginController::class, 'options']);
        Route::post('login', [WebAuthnLoginController::class, 'login']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('register/options', [WebAuthnRegisterController::class, 'options']);
        Route::post('register', [WebAuthnRegisterController::class, 'register']);
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated resident area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'active', 'resident'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Marketplace
    Route::get('/obiecte', [ObjectController::class, 'index'])->name('objects.index');
    Route::get('/obiecte/create', [ObjectController::class, 'create'])->name('objects.create');
    Route::post('/obiecte', [ObjectController::class, 'store'])->name('objects.store');
    Route::get('/obiecte/{object}', [ObjectController::class, 'show'])->name('objects.show');
    Route::get('/obiecte/{object}/edit', [ObjectController::class, 'edit'])->name('objects.edit');
    Route::put('/obiecte/{object}', [ObjectController::class, 'update'])->name('objects.update');
    Route::delete('/obiecte/{object}', [ObjectController::class, 'destroy'])->name('objects.destroy');
    Route::post('/obiecte/{object}/favorite', [ObjectController::class, 'toggleFavorite'])->name('objects.favorite');

    // Loans
    Route::get('/imprumuturi', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/obiecte/{object}/imprumut', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/imprumuturi/{loan}/accept', [LoanController::class, 'accept'])->name('loans.accept');
    Route::post('/imprumuturi/{loan}/refuse', [LoanController::class, 'refuse'])->name('loans.refuse');
    Route::post('/imprumuturi/{loan}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');
    Route::post('/imprumuturi/{loan}/mark-borrowed', [LoanController::class, 'markBorrowed'])->name('loans.mark-borrowed');
    Route::post('/imprumuturi/{loan}/mark-returned', [LoanController::class, 'markReturned'])->name('loans.mark-returned');
    Route::post('/imprumuturi/{loan}/complete', [LoanController::class, 'complete'])->name('loans.complete');
    Route::post('/imprumuturi/{loan}/review', [LoanController::class, 'review'])->name('loans.review');

    // Chat
    Route::get('/mesaje', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/mesaje/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/mesaje', [ConversationController::class, 'store'])->name('conversations.store');
    Route::post('/mesaje/{conversation}/arhiveaza', [ConversationController::class, 'archive'])->name('conversations.archive');

    // Notifications
    Route::get('/notificari', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificari/{id}/citita', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notificari/citeste-toate', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Profile & security
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/setari/securitate', [SecurityController::class, 'show'])->name('security.show');
    Route::post('/setari/securitate/2fa/setup', [SecurityController::class, 'setupTwoFactor'])->name('security.two-factor.setup');
    Route::post('/setari/securitate/2fa/confirm', [SecurityController::class, 'confirmTwoFactor'])->name('security.two-factor.confirm');
    Route::post('/setari/securitate/2fa/disable', [SecurityController::class, 'disableTwoFactor'])->name('security.two-factor.disable');
    Route::post('/setari/securitate/parola', [SecurityController::class, 'updatePassword'])->name('security.update-password');
    Route::post('/setari/securitate/passkeys/{credential}', [SecurityController::class, 'removePasskey'])->name('security.remove-passkey');

    // Favorites
    Route::get('/favorite', [FavoriteController::class, 'index'])->name('favorites.index');

    // Community requests
    Route::get('/cereri-comunitate', [CommunityRequestController::class, 'index'])->name('community-requests.index');
    Route::post('/cereri-comunitate', [CommunityRequestController::class, 'store'])->name('community-requests.store');
    Route::post('/cereri-comunitate/{communityRequest}/inchide', [CommunityRequestController::class, 'close'])->name('community-requests.close');

    // Reports
    Route::post('/raporteaza', [ReportController::class, 'store'])->name('reports.store');

    // History
    Route::get('/istoric', [HistoryController::class, 'index'])->name('history.index');
});
