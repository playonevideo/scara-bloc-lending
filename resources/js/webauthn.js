const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const jsonHeaders = () => ({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': csrfToken(),
});

function base64UrlDecode(input) {
    input = input.replace(/-/g, '+').replace(/_/g, '/');
    const pad = input.length % 4;
    if (pad === 1) {
        throw new Error('InvalidLengthError');
    }
    if (pad) {
        input += '='.repeat(4 - pad);
    }
    return atob(input);
}

const uint8 = (input) => Uint8Array.from(base64UrlDecode(input), (c) => c.charCodeAt(0));

const arrayToBase64 = (arrayBuffer) => btoa(String.fromCharCode(...new Uint8Array(arrayBuffer)));

function parseServerOptions(publicKey) {
    publicKey.challenge = uint8(publicKey.challenge);

    if ('user' in publicKey) {
        publicKey.user = { ...publicKey.user, id: uint8(publicKey.user.id) };
    }

    ['excludeCredentials', 'allowCredentials']
        .filter((key) => key in publicKey)
        .forEach((key) => {
            publicKey[key] = publicKey[key].map((data) => ({ ...data, id: uint8(data.id) }));
        });

    return publicKey;
}

function serializeCredentials(credentials) {
    const result = {
        id: credentials.id,
        type: credentials.type,
        rawId: arrayToBase64(credentials.rawId),
        response: {},
    };

    ['clientDataJSON', 'attestationObject', 'authenticatorData', 'signature', 'userHandle']
        .filter((key) => key in credentials.response)
        .forEach((key) => {
            result.response[key] = arrayToBase64(credentials.response[key]);
        });

    return result;
}

async function registerPasskey() {
    if (typeof PublicKeyCredential === 'undefined') {
        alert('Dispozitivul tău nu suportă passkey-uri.');
        return;
    }

    if (!window.isSecureContext) {
        alert('Passkey-urile necesită un context securizat. Accesează aplicația prin http://localhost:8000 (nu 127.0.0.1).');
        return;
    }

    try {
        const optionsResponse = await fetch('/webauthn/register/options', {
            method: 'POST',
            headers: jsonHeaders(),
        });

        if (!optionsResponse.ok) {
            throw new Error('Nu s-a putut obține challenge-ul de înregistrare.');
        }

        const publicKey = parseServerOptions(await optionsResponse.json());
        const credentials = await navigator.credentials.create({ publicKey });
        const payload = serializeCredentials(credentials);

        const registerResponse = await fetch('/webauthn/register', {
            method: 'POST',
            headers: jsonHeaders(),
            body: JSON.stringify(payload),
        });

        if (!registerResponse.ok) {
            throw new Error('Înregistrarea passkey-ului a eșuat.');
        }

        window.location.reload();
    } catch (error) {
        console.error(error);
        alert('Înregistrarea passkey-ului a eșuat: ' + error.message);
    }
}

async function loginWithPasskey() {
    if (typeof PublicKeyCredential === 'undefined') {
        alert('Dispozitivul tău nu suportă passkey-uri.');
        return;
    }

    if (!window.isSecureContext) {
        alert('Passkey-urile necesită un context securizat. Accesează aplicația prin http://localhost:8000 (nu 127.0.0.1).');
        return;
    }

    try {
        const optionsResponse = await fetch('/webauthn/login/options', {
            method: 'POST',
            headers: jsonHeaders(),
            body: JSON.stringify({}),
        });

        if (!optionsResponse.ok) {
            throw new Error('Nu s-a putut obține challenge-ul de autentificare.');
        }

        const publicKey = parseServerOptions(await optionsResponse.json());
        const credentials = await navigator.credentials.get({ publicKey });
        const payload = serializeCredentials(credentials);

        const loginResponse = await fetch('/webauthn/login', {
            method: 'POST',
            headers: jsonHeaders(),
            body: JSON.stringify(payload),
        });

        if (!loginResponse.ok) {
            throw new Error('Autentificarea cu passkey a eșuat.');
        }

        window.location = '/dashboard';
    } catch (error) {
        console.error(error);
        alert('Autentificarea cu passkey a eșuat: ' + error.message);
    }
}

window.registerPasskey = registerPasskey;
window.loginWithPasskey = loginWithPasskey;
