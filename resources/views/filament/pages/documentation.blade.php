<style>
    details.doc > summary { list-style: none; cursor: pointer; }
    details.doc > summary::-webkit-details-marker { display: none; }
    details.doc > summary .chev { transition: transform 0.2s ease; }
    details.doc[open] > summary .chev { transform: rotate(180deg); }
    details.doc-sub > summary { list-style: none; cursor: pointer; }
    details.doc-sub > summary::-webkit-details-marker { display: none; }
</style>

<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="text-base font-semibold text-gray-900">Documentația panoului de administrare</h2>
        <p class="mt-1 text-sm text-gray-600">Fiecare secțiune de mai jos poate fi extinsă (click pe titlu) și descrie meniurile, câmpurile și butoanele din interfață. Grupările urmează structura meniului din stânga.</p>
    </div>

    {{-- 1. Elemente generale --}}
    <details class="doc rounded-xl border border-gray-200 bg-white" open>
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Elemente generale ale interfeței</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p><strong class="text-gray-900">Meniul lateral (sidebar).</strong> Navighează între resurse, grupat pe domenii. Câmpul de căutare din partea de sus filtrează meniurile.</p>
            <p><strong class="text-gray-900">Bara de sus (top bar).</strong> Conține căutarea globală, comutatorul dark mode și meniul contului (profil + deconectare).</p>
            <p><strong class="text-gray-900">Butonul „New / Create".</strong> Deschide formularul de creare. Opțiunile de salvare: „Create" (salvează și închide), „Create & create another" (salvează și creează altul), „Cancel" (renunță).</p>
            <p><strong class="text-gray-900">Formularele de creare/editare.</strong> Câmpurile obligatorii sunt marcate cu <span class="text-red-500">*</span>; erorile de validare apar sub fiecare câmp.</p>
            <p><strong class="text-gray-900">Tabelele.</strong> Coloanele cu lupă sunt căutabile; click pe titlul coloanei sortează; butonul „Filters" (în dreapta sus) afișează filtrele disponibile.</p>
            <p><strong class="text-gray-900">Acțiuni pe rând.</strong> Butoanele din dreapta fiecărui rând (Edit, Delete etc.); butonul „⋮" afișează acțiunile secundare.</p>
            <p><strong class="text-gray-900">Acțiuni în masă (bulk).</strong> Bifezi rândurile (checkbox din stânga) și aplici o acțiune tuturor (ex. ștergere în masă) din bara „Bulk actions" de jos.</p>
            <p><strong class="text-gray-900">Paginarea.</strong> La finalul tabelului, comută paginile sau modifică numărul de înregistrări pe pagină.</p>
        </div>
    </details>

    {{-- 2. Dashboard --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Dashboard</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p>Prima pagină afișată la autentificare. Conține statistici și un grafic.</p>
            <p><strong class="text-gray-900">Cardurile de statistici</strong> afișează: numărul de locatari, numărul de obiecte (cu câte disponibile), împrumuturile active, cererile în așteptare și numărul de mesaje.</p>
            <p><strong class="text-gray-900">Graficul „Obiecte pe categorii"</strong> este un grafic tip inel care arată distribuția obiectelor pe categorii.</p>
        </div>
    </details>

    {{-- 3. Comunitate --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Comunitate"</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-3 border-t border-gray-100 px-4 py-3">

            <details class="doc-sub overflow-hidden rounded-lg bg-gray-50">
                <summary class="flex items-center justify-between bg-gray-900 px-3 py-2 font-medium text-white">
                    <span>Locatari (Users)</span>
                    <span class="text-xs text-gray-400">▾</span>
                </summary>
                <div class="space-y-2 px-3 pb-3 text-sm text-gray-600">
                    <p><strong>Câmpuri formular:</strong></p>
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Nume complet</strong> — numele afișat.</li>
                        <li><strong>Email</strong> — adresa de autentificare (unică).</li>
                        <li><strong>Telefon</strong> — număr de contact (opțional).</li>
                        <li><strong>Parolă</strong> — la creare este obligatorie; la editare las-o goală pentru a păstra parola actuală.</li>
                        <li><strong>Rol</strong> — Administrator (acces la panou) sau Locatar (acces doar la interfața de locatar).</li>
                        <li><strong>Apartament</strong> — asocierea locatarului la un apartament.</li>
                        <li><strong>Cont blocat</strong> — împiedică autentificarea și deconectează sesiunile active.</li>
                        <li><strong>Autentificare în doi pași</strong> — activează/dezactivează 2FA (TOTP).</li>
                        <li><strong>Afișează etajul / telefonul / emailul</strong> — setări de confidențialitate (ce este vizibil pentru vecini).</li>
                    </ul>
                    <p><strong>Butoane pe rând:</strong></p>
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Blochează / Deblochează</strong> — suspendează sau reactivează contul; la blocare, utilizatorul primește notificare „Cont blocat".</li>
                        <li><strong>Edit</strong> — deschide formularul de editare.</li>
                        <li><strong>Delete</strong> — șterge contul (soft-delete).</li>
                    </ul>
                    <p><strong>Filtre:</strong> după <em>Rol</em> și după starea <em>Blocat</em>.</p>
                </div>
            </details>

            <details class="doc-sub overflow-hidden rounded-lg bg-gray-50">
                <summary class="flex items-center justify-between bg-gray-900 px-3 py-2 font-medium text-white">
                    <span>Categorii (Categories)</span>
                    <span class="text-xs text-gray-400">▾</span>
                </summary>
                <div class="space-y-2 px-3 pb-3 text-sm text-gray-600">
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Nume</strong> — denumirea categoriei; slug-ul se completează automat la tastare.</li>
                        <li><strong>Slug</strong> — identificator unic folosit în URL.</li>
                        <li><strong>Iconiță</strong> — emoji afișat pe obiectele din categorie.</li>
                        <li><strong>Ordine</strong> — poziția categoriei în liste.</li>
                    </ul>
                    <p><strong>Butoane:</strong> Edit și Delete. Coloana „Obiecte" arată câte obiecte folosesc categoria.</p>
                </div>
            </details>

            <details class="doc-sub overflow-hidden rounded-lg bg-gray-50">
                <summary class="flex items-center justify-between bg-gray-900 px-3 py-2 font-medium text-white">
                    <span>Anunțuri (Announcements)</span>
                    <span class="text-xs text-gray-400">▾</span>
                </summary>
                <div class="space-y-2 px-3 pb-3 text-sm text-gray-600">
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Titlu</strong> — subiectul anunțului.</li>
                        <li><strong>Conținut</strong> — textul complet.</li>
                        <li><strong>Publicat la</strong> — data publicării.</li>
                    </ul>
                    <p>La creare, <strong>toți locatarii primesc o notificare</strong> în aplicație cu titlul anunțului. Butoane: Edit și Delete.</p>
                </div>
            </details>

            <details class="doc-sub overflow-hidden rounded-lg bg-gray-50">
                <summary class="flex items-center justify-between bg-gray-900 px-3 py-2 font-medium text-white">
                    <span>Cereri comunitate (Community Requests)</span>
                    <span class="text-xs text-gray-400">▾</span>
                </summary>
                <div class="space-y-2 px-3 pb-3 text-sm text-gray-600">
                    <p>Cererile de tipul „Am nevoie de..." postate de locatari. Poți vizualiza cererea, autorul, categoria și statusul (deschisă/închisă).</p>
                </div>
            </details>

            <details class="doc-sub overflow-hidden rounded-lg bg-gray-50">
                <summary class="flex items-center justify-between bg-gray-900 px-3 py-2 font-medium text-white">
                    <span>Invitații (Invitations)</span>
                    <span class="text-xs text-gray-400">▾</span>
                </summary>
                <div class="space-y-2 px-3 pb-3 text-sm text-gray-600">
                    <p>Comunitatea este privată; conturile se creează doar prin invitație.</p>
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Email / Telefon</strong> — datele invitatului (opționale).</li>
                        <li><strong>Apartament asociat</strong> — apartamentul pre-atribuit la înregistrare.</li>
                        <li><strong>Expiră la</strong> — data expirării (implicit 7 zile).</li>
                    </ul>
                    <p>La creare se generează automat un <strong>cod unic</strong> și un link de înregistrare.</p>
                    <p><strong>Butoane:</strong></p>
                    <ul class="list-inside list-disc space-y-1 pl-1">
                        <li><strong>Link de înregistrare</strong> — copiază link-ul (cu codul) pe care îl trimiți locatarului.</li>
                        <li><strong>Delete</strong> — șterge invitația.</li>
                    </ul>
                    <p>Coloana <strong>Stare</strong> arată: Activă / Folosită / Expirată.</p>
                </div>
            </details>

        </div>
    </details>

    {{-- 4. Clădire --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Clădire"</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p>Structura ierarhică: <strong>Clădire → Scară → Etaj → Apartament → Locatar</strong>.</p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Clădiri</strong> — nume și adresă; tab-ul „Scări" permite adăugarea scărilor direct din clădire.</li>
                <li><strong>Scări</strong> — aparțin unei clădiri; tab-ul „Etaje" adaugă etajele.</li>
                <li><strong>Etaje</strong> — aparțin unei scări și au un număr; tab-ul „Apartamente" adaugă apartamentele.</li>
                <li><strong>Apartamente</strong> — aparțin unui etaj și au un număr; tab-ul „Locatari" permite crearea locatarilor asociați apartamentului.</li>
            </ul>
            <p>Tabelele au butoane <strong>Create</strong>, <strong>Edit</strong> și <strong>Delete</strong>; relațiile (relation managers) apar ca tab-uri în pagina de editare și au propriile butoane de adăugare/editare/ștergere.</p>
        </div>
    </details>

    {{-- 5. Marketplace --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Marketplace" — Obiecte (Items)</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p>Toate obiectele publicate de locatari.</p>
            <p><strong>Câmpuri formular:</strong></p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Titlu / Slug</strong> — numele și identificatorul URL.</li>
                <li><strong>Proprietar</strong> — locatarul care deține obiectul.</li>
                <li><strong>Categorie</strong> — categoria din marketplace.</li>
                <li><strong>Descriere</strong> — detaliile obiectului.</li>
                <li><strong>Stare</strong> — Nou / Foarte bună / Bună / Acceptabilă / Necesită reparații.</li>
                <li><strong>Status</strong> — Disponibil / Rezervat / Împrumutat / Inactiv.</li>
                <li><strong>Zile maxime de împrumut</strong> — limita perioadei de împrumut.</li>
                <li><strong>Predare personală / Poate fi lăsat la ușă</strong> — modul de predare.</li>
                <li><strong>Publicat</strong> — dacă obiectul este vizibil în marketplace.</li>
            </ul>
            <p><strong>Butoane pe rând:</strong></p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Ascunde / Publică</strong> — retrage sau repune obiectul în marketplace.</li>
                <li><strong>Edit</strong> — modifică obiectul.</li>
                <li><strong>Delete</strong> — șterge obiectul.</li>
            </ul>
            <p><strong>Filtre:</strong> după <em>Status</em>, <em>Categorie</em> și <em>Publicat</em>. Coloana „Solicitări" arată numărul de cereri de împrumut.</p>
        </div>
    </details>

    {{-- 6. Împrumuturi --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Împrumuturi"</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-3 border-t border-gray-100 px-4 py-3">
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong>Împrumuturi (Loans).</strong> Fluxul complet al împrumuturilor.</p>
                <ul class="list-inside list-disc space-y-1 pl-1">
                    <li><strong>Obiect / Solicitant / Proprietar</strong> — obiectul și părțile implicate.</li>
                    <li><strong>De la / Până la</strong> — perioada împrumutului.</li>
                    <li><strong>Status</strong> — Solicitat → Acceptat → Împrumutat → Returnat → Finalizat (plus Refuzat, Anulat, Întârziat).</li>
                    <li><strong>Mesaj / Motiv refuz</strong> — mesajul solicitantului și motivul refuzului.</li>
                </ul>
                <p>Butonul <strong>Edit</strong> permite schimbarea manuală a statusului sau perioadei. Filtrul <strong>Status</strong> afișează doar împrumuturile cu un anumit status.</p>
            </div>
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong>Recenzii (Reviews).</strong> Evaluările (1–5 stele) și comentariile lăsate după finalizarea unui împrumut, cu cine a evaluat și cine a fost evaluat.</p>
            </div>
        </div>
    </details>

    {{-- 7. Comunicare --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Comunicare"</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p><strong>Conversații (Conversations).</strong> Chat-ul intern dintre locatari, cu obiectul și împrumutul asociat (dacă există).</p>
            <p><strong>Mesaje (Messages).</strong> Conținutul mesajelor, expeditorul și data. Folosit pentru moderare — poți șterge un mesaj nepotrivit cu butonul Delete.</p>
        </div>
    </details>

    {{-- 8. Moderare --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Moderare" — Raportări (Reports)</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p>Raportările trimise de locatari (obiecte, mesaje sau utilizatori).</p>
            <p><strong>Coloane:</strong></p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Raportat de</strong> — cine a făcut raportarea.</li>
                <li><strong>Tip</strong> — Obiect / Mesaj / Utilizator.</li>
                <li><strong>Motiv</strong> — obiect nepotrivit, descriere nepotrivită, spam, comportament abuziv, mesaj nepotrivit, utilizator problematic.</li>
                <li><strong>Status</strong> — Nou / În analiză / Rezolvat / Respins.</li>
            </ul>
            <p><strong>Butoane pe rând:</strong></p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Rezolvă</strong> — marchează raportarea ca rezolvată.</li>
                <li><strong>Respinge</strong> — marchează raportarea ca respinsă.</li>
                <li><strong>Edit</strong> — schimbă motivul, statusul și adaugă o notă de rezoluție.</li>
            </ul>
        </div>
    </details>

    {{-- 9. Sistem --}}
    <details class="doc overflow-hidden rounded-xl border border-gray-200 bg-white">
        <summary class="flex items-center justify-between bg-black px-4 py-3 font-semibold text-white">
            <span>Grup „Sistem" — Jurnal de audit (Audit Logs)</span>
            <svg class="chev h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="space-y-2 border-t border-gray-100 px-4 py-3 text-sm text-gray-600">
            <p>Înregistrează automat acțiunile administrative. Este doar pentru vizualizare (fără editare).</p>
            <ul class="list-inside list-disc space-y-1 pl-1">
                <li><strong>Utilizator</strong> — cine a efectuat acțiunea.</li>
                <li><strong>Acțiune</strong> — updated / deleted.</li>
                <li><strong>Tip obiect / ID</strong> — ce a fost modificat.</li>
                <li><strong>IP</strong> — adresa IP de la care s-a făcut modificarea.</li>
                <li><strong>Data</strong> — momentul exact.</li>
            </ul>
            <p>Exemple de acțiuni înregistrate: modificarea/ștergerea unui obiect, blocarea unui utilizator, modificarea unui apartament, aprobarea/respingerea unei raportări.</p>
        </div>
    </details>
</div>
