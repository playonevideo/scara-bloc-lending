<div class="space-y-8">
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900">Bine ai venit în documentația panoului de administrare</h2>
        <p class="mt-2 text-sm leading-relaxed text-gray-600">
            Această pagină descrie fiecare secțiune, funcție și buton din interfața de administrare.
            Panoul este împărțit în grupuri de navigare (în meniul din stânga) și fiecare resursă
            conține o listă de înregistrări, formulare de creare/editare și acțiuni.
        </p>
        <p class="mt-2 text-sm leading-relaxed text-gray-600">
            📷 <strong>Screenshot-urile</strong> se adaugă în folderul <code class="rounded bg-gray-100 px-1.5 py-0.5">public/images/docs/</code>,
            cu numele de fișier indicat în fiecare locație de mai jos (apare un chenar punctat până când imaginea este adăugată).
        </p>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Elemente generale ale interfeței</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Meniul lateral (sidebar)</p>
                <p class="mt-1 text-sm text-gray-600">Navighezi între toate resursele. Este grupat pe domenii: Comunitate, Clădire, Marketplace, Împrumuturi, Comunicare, Moderare, Sistem. Poți căuta o resursă folosind câmpul de căutare din partea de sus a meniului.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Bara de sus (top bar)</p>
                <p class="mt-1 text-sm text-gray-600">Conține butonul de căutare globală, comutatorul dark mode (dacă este activ) și meniul contului. Din meniul contului poți accesa profilul sau te poți deconecta.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Butonul „+ Create / Nou"</p>
                <p class="mt-1 text-sm text-gray-600">Deschide formularul de creare pentru resursa curentă. Salvează și închide cu „Create", salvează și creează altul cu „Create & create another", sau anulează cu „Cancel".</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Filtre și căutare în tabel</p>
                <p class="mt-1 text-sm text-gray-600">Fiecare tabel are butonul „Filters" (filtre după diverse coloane) și câmpuri de căutare pe coloanele marcate cu lupă. Sortarea se face apăsând pe titlul coloanei.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni pe rând (⋯)</p>
                <p class="mt-1 text-sm text-gray-600">În dreapta fiecărui rând există butoane de acțiune (ex. Edit, Delete, Blochează). Butonul „⋮" (meniu derulant) afișează acțiunile secundare.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni în masă (bulk)</p>
                <p class="mt-1 text-sm text-gray-600">Bifezi mai multe rânduri (checkbox din stânga) și aplici o acțiune asupra tuturor (ex. ștergere în masă) folosind bara „Bulk actions" care apare jos.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Dashboard</h2>
        <p class="mt-1 text-sm text-gray-600">Prima pagină afișată la autentificare. Conține statistici și un grafic.</p>
        <x-docs-screenshot file="dashboard.png" alt="Dashboard cu statistici și grafic" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Carduri de statistici</p>
                <p class="mt-1 text-sm text-gray-600">Afișează: numărul de locatari, numărul de obiecte (cu câte sunt disponibile), împrumuturile active, cererile în așteptare și numărul de mesaje.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Grafic „Obiecte pe categorii"</p>
                <p class="mt-1 text-sm text-gray-600">Grafic tip inel (doughnut) care arată distribuția obiectelor pe categorii.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Locatari (Users)</h2>
        <p class="mt-1 text-sm text-gray-600">Gestionează conturile locatarilor și ale administratorilor. Găsit în grupul „Comunitate".</p>
        <x-docs-screenshot file="users.png" alt="Lista de locatari" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Formular — câmpuri</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Nume complet</strong> — numele utilizatorului.</li>
                    <li><strong>Email</strong> — adresa de autentificare.</li>
                    <li><strong>Telefon</strong> — număr de contact (opțional).</li>
                    <li><strong>Parolă</strong> — doar la creare; la editare poți seta una nouă (lasă gol ca să păstrezi parola actuală).</li>
                    <li><strong>Rol</strong> — Administrator sau Locatar.</li>
                    <li><strong>Apartament</strong> — asocierea la un apartament.</li>
                    <li><strong>Cont blocat</strong> — împiedică autentificarea.</li>
                    <li><strong>Autentificare în doi pași</strong> — activează/dezactivează 2FA (TOTP).</li>
                    <li><strong>Afișează etajul / telefonul / emailul</strong> — setări de confidențialitate.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni (butoane)</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Blochează / Deblochează</strong> — suspendează sau reactivează contul; la blocare utilizatorul este deconectat și nu se mai poate autentifica.</li>
                    <li><strong>Edit</strong> — deschide formularul de editare.</li>
                    <li><strong>Delete</strong> — șterge contul (cu soft-delete).</li>
                    <li><strong>Filters</strong> — filtrează după rol sau starea „Blocat".</li>
                </ul>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Categorii (Categories)</h2>
        <p class="mt-1 text-sm text-gray-600">Categoriile folosite pentru obiectele din marketplace. Găsit în grupul „Comunitate".</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Câmpuri</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Nume</strong> — denumirea categoriei; slug-ul se completează automat.</li>
                    <li><strong>Slug</strong> — identificator unic (URL).</li>
                    <li><strong>Iconiță</strong> — emoji afișat pe obiecte.</li>
                    <li><strong>Ordine</strong> — poziția în liste.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Coloane tabel</p>
                <p class="mt-1 text-sm text-gray-600">Nume, iconiță, numărul de obiecte din categorie și ordinea. Butoanele de acțiune: Edit și Delete.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Anunțuri (Announcements)</h2>
        <p class="mt-1 text-sm text-gray-600">Postează anunțuri administrative (întrerupere apă, ședință, reparații). La creare, toți locatarii primesc o notificare în aplicație. Găsit în grupul „Comunitate".</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Câmpuri</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Titlu</strong> — subiectul anunțului.</li>
                    <li><strong>Conținut</strong> — textul anunțului.</li>
                    <li><strong>Publicat la</strong> — data publicării.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni</p>
                <p class="mt-1 text-sm text-gray-600">Edit și Delete pe fiecare rând.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Cereri comunitate (Community Requests)</h2>
        <p class="mt-1 text-sm text-gray-600">Cererile de tipul „Am nevoie de..." postate de locatari. Găsit în grupul „Comunitate". Poți vizualiza și modera aceste cereri.</p>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Invitații (Invitations)</h2>
        <p class="mt-1 text-sm text-gray-600">Comunitatea este privată; conturile se creează doar prin invitație. Găsit în grupul „Comunitate".</p>
        <x-docs-screenshot file="invitations.png" alt="Lista de invitații cu link-ul de înregistrare" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Câmpuri</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Email / Telefon</strong> — datele invitatului (opționale).</li>
                    <li><strong>Apartament asociat</strong> — apartamentul pre-atribuit.</li>
                    <li><strong>Expiră la</strong> — data expirării invitației (implicit 7 zile).</li>
                </ul>
                <p class="mt-2 text-sm text-gray-600">La creare se generează automat un <strong>cod</strong> unic și un link de înregistrare.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Link de înregistrare</strong> — copiază link-ul (cu codul) pe care îl trimiți locatarului.</li>
                    <li><strong>Delete</strong> — șterge invitația.</li>
                </ul>
                <p class="mt-2 text-sm text-gray-600">Coloana „Stare" arată dacă invitația este Activă, Folosită sau Expirată.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Clădiri, Scări, Etaje, Apartamente</h2>
        <p class="mt-1 text-sm text-gray-600">Structura ierarhică a clădirii: <strong>Clădire → Scară → Etaj → Apartament → Locatar</strong>. Găsit în grupul „Clădire".</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Clădiri</p>
                <p class="mt-1 text-sm text-gray-600">Nume și adresă. Relația „Scări" permite adăugarea scărilor direct din pagina clădirii.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Scări</p>
                <p class="mt-1 text-sm text-gray-600">Aparțin unei clădiri; relația „Etaje" permite adăugarea etajelor.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Etaje</p>
                <p class="mt-1 text-sm text-gray-600">Aparțin unei scări; au un număr. Relația „Apartamente" permite adăugarea apartamentelor.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Apartamente</p>
                <p class="mt-1 text-sm text-gray-600">Aparțin unui etaj; au un număr. Relația „Locatari" permite crearea locatarilor asociați direct apartamentului.</p>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-600">Relațiile („relation managers") se deschid ca tab-uri în pagina de editare a fiecărei înregistrări și au propriile butoane „Create", „Edit" și „Delete".</p>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Obiecte (Items)</h2>
        <p class="mt-1 text-sm text-gray-600">Toate obiectele publicate de locatari. Găsit în grupul „Marketplace".</p>
        <x-docs-screenshot file="objects.png" alt="Lista de obiecte" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Coloane</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Titlu, Proprietar, Categorie</strong> — informații despre obiect.</li>
                    <li><strong>Status</strong> — Disponibil / Rezervat / Împrumutat / Inactiv.</li>
                    <li><strong>Solicitări</strong> — numărul de cereri de împrumut.</li>
                    <li><strong>Publicat</strong> — dacă obiectul este vizibil în marketplace.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Ascunde / Publică</strong> — retrage sau repune obiectul în marketplace.</li>
                    <li><strong>Edit</strong> — modifică obiectul (titlu, categorie, stare, status etc.).</li>
                    <li><strong>Delete</strong> — șterge obiectul.</li>
                    <li><strong>Filters</strong> — filtrează după status, categorie sau „Publicat".</li>
                </ul>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Împrumuturi (Loans)</h2>
        <p class="mt-1 text-sm text-gray-600">Fluxul complet al împrumuturilor. Găsit în grupul „Împrumuturi".</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Coloane</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Obiect</strong> — obiectul împrumutat.</li>
                    <li><strong>Solicitant / Proprietar</strong> — cine cere și cine oferă.</li>
                    <li><strong>Perioada</strong> — data de început a împrumutului.</li>
                    <li><strong>Status</strong> — Solicitat → Acceptat → Împrumutat → Returnat → Finalizat (plus Refuzat, Anulat, Întârziat).</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni</p>
                <p class="mt-1 text-sm text-gray-600">Edit pentru a schimba manual statusul sau perioada. Filtrul „Status" afișează doar împrumuturile cu un anumit status.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Recenzii (Reviews)</h2>
        <p class="mt-1 text-sm text-gray-600">Evaluările (1–5 stele) și comentariile lăsate după finalizarea unui împrumut. Găsit în grupul „Împrumuturi".</p>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Conversații și Mesaje</h2>
        <p class="mt-1 text-sm text-gray-600">Chat-ul intern dintre locatari. Găsit în grupul „Comunicare". Folosit pentru moderare (vizualizarea și ștergerea mesajelor nepotrivite).</p>
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Conversații</p>
                <p class="mt-1 text-sm text-gray-600">Lista conversațiilor, cu obiectul asociat și împrumutul aferent (dacă există).</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Mesaje</p>
                <p class="mt-1 text-sm text-gray-600">Conținutul mesajelor, expeditorul și data. Poți șterge un mesaj nepotrivit.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Raportări (Reports)</h2>
        <p class="mt-1 text-sm text-gray-600">Raportările trimise de locatari (obiecte, mesaje sau utilizatori). Găsit în grupul „Moderare".</p>
        <x-docs-screenshot file="reports.png" alt="Lista de raportări" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Coloane</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Raportat de</strong> — cine a făcut raportarea.</li>
                    <li><strong>Tip</strong> — Obiect / Mesaj / Utilizator.</li>
                    <li><strong>Motiv</strong> — obiect nepotrivit, spam, comportament abuziv etc.</li>
                    <li><strong>Status</strong> — Nou / În analiză / Rezolvat / Respins.</li>
                </ul>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="font-medium text-gray-900">Acțiuni</p>
                <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                    <li><strong>Rezolvă</strong> — marchează raportarea ca rezolvată.</li>
                    <li><strong>Respinge</strong> — marchează raportarea ca respinsă.</li>
                    <li><strong>Edit</strong> — schimbă motivul, statusul și adaugă o notă de rezoluție.</li>
                </ul>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-900">Jurnal de audit (Audit Logs)</h2>
        <p class="mt-1 text-sm text-gray-600">Înregistrează acțiunile administrative: cine a modificat/șters un obiect, cine a blocat un utilizator, cine a schimbat un apartament etc. Găsit în grupul „Sistem". Este doar pentru vizualizare (fără editare).</p>
        <div class="mt-3 rounded-xl border border-gray-200 bg-white p-4">
            <p class="font-medium text-gray-900">Coloane</p>
            <ul class="mt-1 list-inside list-disc text-sm text-gray-600">
                <li><strong>Utilizator</strong> — cine a efectuat acțiunea.</li>
                <li><strong>Acțiune</strong> — updated / deleted.</li>
                <li><strong>Tip obiect / ID</strong> — ce a fost modificat.</li>
                <li><strong>IP</strong> — adresa IP de la care s-a făcut modificarea.</li>
                <li><strong>Data</strong> — momentul exact.</li>
            </ul>
        </div>
    </div>
</div>
