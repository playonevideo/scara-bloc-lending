# Vecini — Platformă de partajare și împrumut de obiecte pentru locatari

O aplicație web modernă destinată unei scări de bloc / unei comunități de locatari. Locatarii pot pune la dispoziția vecinilor obiecte pe care le pot împrumuta, pot solicita obiecte de la alți locatari, pot comunica prin chat intern și pot construi, prin recenzii, o reputație de încredere în comunitate.

Scopul este încurajarea colaborării între vecini, reducerea cumpărării inutile a obiectelor folosite rar și crearea unei comunități mai bine conectate.

---

## Caracteristici

- **Marketplace intern** dedicat exclusiv locatarilor, cu căutare, filtre (categorie, disponibilitate, etaj) și sortare (cele mai noi, populare, după rating).
- **Publicarea obiectelor** cu fotografii, categorie, stare, perioadă maximă de împrumut și condiții speciale.
- **Sistem de solicitare a împrumutului** cu flux complet: `Disponibil → Solicitat → Acceptat → Împrumutat → Returnat → Finalizat` (plus `Refuzat`, `Anulat`, `Întârziat`).
- **Prevenirea conflictelor de rezervare** (fără rezervări simultane, duplicate sau care se suprapun), atât în interfață cât și server-side.
- **Chat intern** 1-la-1, cu timestamp, status citit/necitit și arhivare.
- **Notificări în aplicație** pentru solicitări, acceptări, mesaje, recenzii, raportări și anunțuri administrative.
- **Sistem de rating** (1–5 stele) și reputație a locatarilor.
- **Raportare și moderare** (obiecte, descrieri, spam, comportament abuziv, mesaje).
- **Panou de administrare Filament** complet separat de interfața locatarului, cu statistici, grafice și jurnal de audit.
- **Autentificare modernă**: parole + WebAuthn / Passkeys (amprentă, Face ID, Windows Hello) + autentificare în doi pași prin WhatsApp.
- **Sistem de invitații** — comunitatea este privată; conturile se creează doar prin invitație.
- **Roluri**: Administrator și Locatar.
- **Confidențialitate (GDPR)**: controlul vizibilității datelor, minimizarea datelor, anonimizare la ștergere.
- **Responsive mobile-first** și accesibil.

---

## Stack tehnologic

- **Backend**: PHP 8.3+, Laravel 12
- **Bază de date**: MySQL (în producție) / SQLite (în dezvoltare locală)
- **ORM**: Eloquent
- **Admin**: Filament 3 (Resources, Tables, Forms, Actions, Notifications, Widgets)
- **Frontend locatar**: Blade + Livewire 3 + Alpine.js + Tailwind CSS 4
- **Autentificare**: [laragear/webauthn](https://github.com/Laragear/WebAuthn) (WebAuthn / Passkeys), [Twilio SDK](https://www.twilio.com/) (2FA prin WhatsApp)
- **QR**: simplesoftwareio/simple-qrcode
- **Teste**: PHPUnit

---

## Cerințe de instalare

- PHP >= 8.3 (extensii: `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `gd`, `zip`, `intl`, `bcmath`)
- Composer 2
- Node.js >= 18 și npm
- MySQL 8 (pentru producție) sau SQLite (pentru dezvoltare locală)
- Git

---

## Instalare

```bash
# 1. Clonează repository-ul
git clone https://github.com/<utilizator>/scara-bloc-lending.git
cd scara-bloc-lending

# 2. Instalează dependențele PHP
composer install

# 3. Copiază fișierul de mediu
cp .env.example .env

# 4. Generează cheia aplicației
php artisan key:generate

# 5. Configurează baza de date (vezi mai jos), apoi rulează migrațiile
php artisan migrate --seed

# 6. Configurează storage-ul public
php artisan storage:link

# 7. Instalează și compilează frontend-ul
npm install
npm run build

# 8. Pornește aplicația
php artisan serve
```

---

## Configurare `.env`

Variabilele importante din `.env` (valorile reale NU se commit-uiesc — fișierul `.env` este în `.gitignore`):

```dotenv
APP_NAME="Vecini"
APP_URL=http://localhost

# --- Bază de date (MySQL în producție) ---
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scara_bloc_lending
DB_USERNAME=root
DB_PASSWORD=

# Pentru dezvoltare locală fără server MySQL, folosește SQLite:
# DB_CONNECTION=sqlite
# DB_DATABASE=/cale/absoluta/catre/database/database.sqlite

# --- WhatsApp / 2FA (Twilio) ---
SMS_PROVIDER=twilio      # "twilio" sau "log" (dezvoltare)
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_ACCOUNT_SID=
TWILIO_FROM=+14155238886 # numărul de trimitere WhatsApp (sandbox sau WhatsApp Business)

# Parametri codului de verificare
SMS_CODE_LENGTH=6
SMS_CODE_EXPIRES_MINUTES=10
SMS_CODE_MAX_ATTEMPTS=5
SMS_CODE_THROTTLE_SECONDS=60

# --- WebAuthn / Passkeys ---
WEBAUTHN_NAME="Vecini"
WEBAUTHN_ID=localhost     # domeniul pe care rulează aplicația
WEBAUTHN_ORIGINS=

# --- Mail ---
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Configurarea bazei de date

**MySQL (producție):**

1. Creează baza de date:
   ```sql
   CREATE DATABASE scara_bloc_lending CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Completează variabilele `DB_*` în `.env`.
3. Rulează `php artisan migrate --seed`.

**SQLite (dezvoltare locală):**

1. Setează `DB_CONNECTION=sqlite`.
2. Creează fișierul: `touch database/database.sqlite`.
3. Rulează `php artisan migrate --seed`.

Migrațiile creează toate tabelele (utilizatori, clădiri, scări, etaje, apartamente, categorii, obiecte, imagini, împrumuturi, conversații, mesaje, recenzii, raportări, notificări, invitații, credențiale WebAuthn, jurnal de audit etc.). Seeder-ele adaugă date demonstrative: `Bloc A → Scara 1`, 44 de apartamente, 15 locatari, 25 de obiecte, împrumuturi, conversații și recenzii.

---

## Configurarea serviciului WhatsApp (2FA)

Aplicația trimite codurile de verificare prin WhatsApp. Poți alege între două provider-e (via `SMS_PROVIDER`):

### Meta WhatsApp Cloud API (recomandat, gratuit)

1. Creează un cont pe [Meta for Developers](https://developers.facebook.com/) și o aplicație.
2. Adaugă produsul **WhatsApp** și configurează un număr de telefon (WhatsApp Business).
3. Obține:
   - `WHATSAPP_TOKEN` — token de acces permanent (System User / token).
   - `WHATSAPP_PHONE_NUMBER_ID` — ID-ul numărului de telefon.
4. Creează un șablon de mesaj (tip „Transactional") în **Meta Business Manager → WhatsApp Manager → Message templates**, cu conținutul: `Codul tău de verificare Vecini este: {{1}}` (sau doar `{{1}}`).
5. Completează în `.env`:
   ```dotenv
   SMS_PROVIDER=meta
   WHATSAPP_TOKEN=...
   WHATSAPP_PHONE_NUMBER_ID=...
   WHATSAPP_TEMPLATE_NAME=...   # numele șablonului
   WHATSAPP_TEMPLATE_LANGUAGE=ro
   ```

### Twilio WhatsApp (alternativă)

1. Obține `Account SID` și `Auth Token` (sau un API Key `SK...` + secret) din [Twilio](https://www.twilio.com/).
2. Configurează un număr de trimitere WhatsApp (sandbox `+14155238886` sau WhatsApp Business) și un Content Template (SID `HX...`).
3. Completează în `.env`: `SMS_PROVIDER=twilio`, `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_ACCOUNT_SID`, `TWILIO_FROM`, `TWILIO_CONTENT_SID`.

Pentru dezvoltare, `SMS_PROVIDER=log` scrie codul de verificare în log-ul Laravel (`storage/logs/laravel.log`) în loc să trimită mesajul WhatsApp real.

Codul de verificare: expiră, are limită de încercări, rate limiting și nu poate fi reutilizat (vezi `app/Services/TwoFactorService.php`).

---

## Configurarea autentificării WebAuthn / Passkeys

WebAuthn folosește pachetul `laragear/webauthn`. Configurarea se face în `.env`:

```dotenv
WEBAUTHN_NAME="Vecini"
WEBAUTHN_ID=localhost
WEBAUTHN_ORIGINS=
```

- `WEBAUTHN_NAME` — numele afișat de dispozitiv la autentificare.
- `WEBAUTHN_ID` — domeniul (Relying Party ID) pe care rulează aplicația (ex. `vecini.ro`). În producție setează domeniul real.
- `WEBAUTHN_ORIGINS` — origini suplimentare permise (separate prin virgulă), dacă este cazul.

Datele biometrice (amprentă, Face ID, Windows Hello) rămân pe dispozitiv; serverul verifică doar autentificarea criptografică.

Un locatar își înregistrează passkey-urile din **Profil → Securitate**, iar autentificarea cu passkey este disponibilă pe pagina de login.

---

## Configurarea GitHub

Repository-ul este public și folosește un workflow Git structurat:

- `main` — versiunea stabilă.
- `develop` — integrarea funcționalităților.
- `feature/<nume>` — funcționalități noi.
- `fix/<nume>` — corecturi.

```bash
# Lucrează pe o ramură de feature
git checkout develop
git checkout -b feature/nume-functionalitate
# ... modificări + commit-uri ...
git checkout develop
git merge --no-ff feature/nume-functionalitate
git push origin develop
```

---

## Comenzi utile

```bash
php artisan serve          # pornește serverul de dezvoltare
php artisan migrate        # rulează migrațiile
php artisan migrate:fresh --seed   # reconstruiește baza de date cu date demo
php artisan test           # rulează testele
npm run dev                # compilează activele în mod dev (cu hot-reload)
npm run build              # compilează activele pentru producție
```

---

## Conturi de administrator pentru dezvoltare / demonstrație

După rularea `php artisan migrate:fresh --seed`, sunt disponibile:

| Rol           | Email            | Parolă    |
|---------------|------------------|-----------|
| Administrator | `admin@vecini.ro` | `password` |

> Fiecare locatar își poate seta, schimba sau elimina numărul de telefon pentru 2FA din **Profil → Securitate** (schimbarea necesită confirmare prin WhatsApp pe noul număr și parola contului). În dezvoltare (`SMS_PROVIDER=log`), codul de verificare este scris în `storage/logs/laravel.log`.

Locatarii demo au emailuri de forma `nume@vecini.ro` (ex. `andrei@vecini.ro`) și parola `password`.

Panoul de administrare este disponibil la `/admin`.

---

## Testing

```bash
php artisan test
```

Testele acoperă: autentificarea (login, parolă greșită, cont blocat, logout), autentificarea în doi pași (redirect la challenge, expirare, reutilizare), autorizarea (acces admin vs. locatar), CRUD obiecte, sistemul de împrumut (solicitare, conflict de rezervare, acceptare/refuz), chat-ul, notificările, recenziile și raportările.

---

## Deployment

Variante de hosting recomandate: **Laravel Forge**, **Laravel Cloud**, un **VPS** (DigitalOcean, Hetzner) sau **Render / Railway**.

Pași generali:

1. Clonează repository-ul pe server și `composer install --no-dev --optimize-autoloader`.
2. Configurează `.env` (cheie, MySQL, WhatsApp/Twilio, WebAuthn, mail).
3. `php artisan key:generate` și `php artisan migrate --force --seed`.
4. `php artisan storage:link`.
5. `npm ci && npm run build`.
6. `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
7. Configurează webserverul (Nginx/Apache) să servească `public/`.
8. Configurează un programator pentru sarcini programate: `php artisan schedule:run` (remindere împrumuturi).
9. Setează `APP_ENV=production` și `APP_DEBUG=false`.
10. Pentru coada de notificări: `php artisan queue:work` (dacă folosești notificări asincrone).

> GitHub rămâne repository-ul central al proiectului.

---

## Licență

Proiectul este distribuit sub licența [MIT](https://opensource.org/licenses/MIT).
