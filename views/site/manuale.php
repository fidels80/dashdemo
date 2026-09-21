<?php
/* @var $this yii\web\View */
/* Manuale utente - renderizzato dentro il layout dell'applicazione */

$this->title = 'Manuale Utente';

$this->registerCss(<<<'CSS'
  .manual {
    --m-primary: #1f4e79;
    --m-primary-light: #2f6db0;
    --m-bg: #f4f6f9;
    --m-card: #ffffff;
    --m-text: #333;
    --m-muted: #6c757d;
    --m-border: #dee2e6;
    --m-ok: #2e7d32;
    --m-warn: #b36b00;
    --m-info: #0d6efd;
  }
  .manual * { box-sizing: border-box; }
  .manual { line-height: 1.6; color: var(--m-text); }
  .manual-wrap { display: flex; gap: 22px; align-items: flex-start; }
  .manual-toc {
    width: 270px;
    min-width: 270px;
    background: var(--m-card);
    border: 1px solid var(--m-border);
    border-radius: 10px;
    padding: 16px 12px;
    position: sticky;
    top: 16px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
  }
  .manual-toc .manual-search {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 12px;
    border: 1px solid var(--m-border);
    border-radius: 6px;
    font-size: 14px;
  }
  .manual-toc a { display: block; color: var(--m-primary); text-decoration: none; font-size: 14px; padding: 6px 8px; border-radius: 5px; margin-bottom: 1px; }
  .manual-toc a:hover { background: #eaf1f8; }
  .manual-toc a.manual-l1 { font-weight: 700; margin-top: 8px; }
  .manual-toc a.manual-l2 { padding-left: 20px; }
  .manual-main { flex: 1; min-width: 0; }
  .manual-title { margin-bottom: 24px; }
  .manual-title h1 { color: var(--m-primary); font-size: 28px; }
  .manual-title p { color: var(--m-muted); }
  .manual section { margin-bottom: 24px; }
  .manual-card { background: var(--m-card); border: 1px solid var(--m-border); border-radius: 10px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
  .manual-card h2 { color: var(--m-primary); font-size: 20px; border-bottom: 2px solid var(--m-primary-light); padding-bottom: 8px; margin-bottom: 14px; }
  .manual-card h3 { color: var(--m-primary); font-size: 16px; margin: 18px 0 8px; }
  .manual-card h4 { color: #35424a; font-size: 15px; margin: 14px 0 6px; }
  .manual-card p { margin: 8px 0; font-size: 15px; }
  .manual-card ul, .manual-card ol { margin: 8px 0 8px 22px; font-size: 15px; }
  .manual-card li { margin: 4px 0; }
  .manual-note, .manual-tip, .manual-warn { border-left: 5px solid; padding: 12px 16px; border-radius: 6px; margin: 14px 0; font-size: 14.5px; }
  .manual-note { background: #eaf1f8; border-color: var(--m-info); }
  .manual-tip  { background: #e8f5e9; border-color: var(--m-ok); }
  .manual-warn { background: #fff7e6; border-color: var(--m-warn); }
  .manual-card table { border-collapse: collapse; width: 100%; margin: 12px 0; font-size: 14.5px; }
  .manual-card th, .manual-card td { border: 1px solid var(--m-border); padding: 8px 12px; text-align: left; vertical-align: top; }
  .manual-card th { background: #eef3f8; color: var(--m-primary); }
  .manual-card tr:nth-child(even) td { background: #fafbfc; }
  .manual-btn { display: inline-block; background: var(--m-primary-light); color: #fff; padding: 2px 10px; border-radius: 5px; font-size: 13px; font-weight: 600; white-space: nowrap; }
  .manual-btn-gray { background: #6c757d; }
  .manual-btn-green { background: var(--m-ok); }
  .manual-btn-red { background: #c0392b; }
  .manual code { background: #eef1f4; padding: 1px 6px; border-radius: 4px; font-size: 13px; font-family: Consolas, monospace; }
  @media (max-width: 900px) {
    .manual-wrap { flex-direction: column; }
    .manual-toc { width: 100%; min-width: 0; height: auto; max-height: 300px; position: static; }
  }
CSS
);

$this->registerJs(<<<'JS'
  var links = Array.prototype.slice.call(document.querySelectorAll('#toc-links a'));
  var search = document.getElementById('search');
  if (search) {
    search.addEventListener('input', function () {
      var q = search.value.toLowerCase().trim();
      links.forEach(function (a) {
        var txt = a.textContent.toLowerCase();
        var show = q === '' || txt.indexOf(q) !== -1;
        a.style.display = show ? 'block' : 'none';
      });
    });
  }
JS
);
?>

<div class="manual">

  <div class="manual-wrap">

    <!-- ===== INDICE ===== -->
    <nav class="manual-toc">
      <input type="text" class="manual-search" id="search" placeholder="Cerca nel manuale...">
      <div id="toc-links">
        <a class="manual-l1" href="#introduzione">1. Introduzione</a>
        <a class="manual-l2" href="#cosa-fa">Cosa fa l'applicazione</a>
        <a class="manual-l1" href="#accesso">2. Accesso all'applicazione</a>
        <a class="manual-l2" href="#login">Login</a>
        <a class="manual-l2" href="#password">Password dimenticata</a>
        <a class="manual-l2" href="#logout">Logout</a>
        <a class="manual-l1" href="#navigazione">3. Navigazione e menu</a>
        <a class="manual-l2" href="#livelli">Livelli e moduli</a>
        <a class="manual-l1" href="#planning">4. Planning (pianificazione)</a>
        <a class="manual-l2" href="#planning-calendario">Calendario attivit&agrave;</a>
        <a class="manual-l2" href="#planning-nuova">Creare un'attivit&agrave;</a>
        <a class="manual-l2" href="#planning-lista">Lista attivit&agrave;</a>
        <a class="manual-l2" href="#planning-varia">Varia veicoli</a>
        <a class="manual-l2" href="#planning-copia">Copia giornata</a>
        <a class="manual-l2" href="#planning-pdf">Stampe PDF</a>
        <a class="manual-l1" href="#presenze">5. Presenze e HR</a>
        <a class="manual-l1" href="#anagrafiche">6. Anagrafiche</a>
        <a class="manual-l2" href="#anag-personale">Personale</a>
        <a class="manual-l2" href="#anag-veicoli">Veicoli</a>
        <a class="manual-l2" href="#anag-squadre">Squadre</a>
        <a class="manual-l2" href="#anag-ditte">Ditte esterne</a>
        <a class="manual-l2" href="#anag-altre">Altre anagrafiche</a>
        <a class="manual-l1" href="#reports">7. Report</a>
        <a class="manual-l2" href="#report-centro">Centro reportistica</a>
        <a class="manual-l2" href="#report-presenze">Report Presenze e Costi</a>
        <a class="manual-l2" href="#report-flotta">Report Attivit&agrave; Flotta</a>
        <a class="manual-l2" href="#report-consuntivo">Consuntivo Ditta Esterna</a>
        <a class="manual-l2" href="#report-squadra">Rapporto per Squadra</a>
        <a class="manual-l1" href="#amministrazione">8. Amministrazione</a>
        <a class="manual-l2" href="#admin-utenti">Gestione utenti</a>
        <a class="manual-l2" href="#admin-menu">Menu e permessi</a>
        <a class="manual-l1" href="#segnalazione">9. Segnalare un problema</a>
        <a class="manual-l1" href="#glossario">10. Glossario</a>
      </div>
    </nav>

    <!-- ===== CONTENUTO ===== -->
    <main class="manual-main">

      <div class="manual-title">
        <h1>Manuale Utente dell'Applicazione</h1>
        <p>Guida semplice e completa all'uso del gestionale operativo. Spiega cosa fa ogni funzione e come si usa.</p>
      </div>

      <!-- 1 INTRODUZIONE -->
      <section id="introduzione">
        <div class="manual-card">
          <h2>1. Introduzione</h2>
          <p>
            <strong>Ufficio 2000</strong> &egrave; il gestionale operativo usato da <em>Programma 2000 srl</em> per gestire le
            attivit&agrave; di un'azienda di servizi sul territorio. Serve a pianificare il lavoro delle squadre,
            registrare presenze e ore, gestire veicoli, ditte esterne, commesse, documenti, anagrafiche e a produrre report.
          </p>
          <p>Questa guida &egrave; divisa in sezioni che corrispondono alle aree che trovi nel menu. Per ogni funzione trovi:</p>
          <ul>
            <li><strong>Cosa fa</strong> &mdash; a cosa serve la funzione in parole semplici.</li>
            <li><strong>Come si usa</strong> &mdash; il procedimento passo passo.</li>
          </ul>

          <h3 id="cosa-fa">Cosa fa l'applicazione</h3>
          <p>In sintesi, l'applicazione permette di:</p>
          <ul>
            <li><strong>Pianificare le attivit&agrave; giornaliere</strong> (Planning): assegnare personale, veicoli, orari, giri e commesse, con controllo dei conflitti e colori di stato.</li>
            <li><strong>Registrare presenze e costi</strong> (HR): ore lavorate, ferie, permessi, malattie, ritardi e costi orari.</li>
            <li><strong>Gestire anagrafiche</strong>: clienti, agenti, personale, squadre, mansioni, reparti, articoli, pagamenti, etichette, archivio file.</li>
            <li><strong>Collaborare</strong>: agenda, calendario e email.</li>
            <li><strong>Produrre report e statistiche</strong> con esportazione in Excel, PDF, CSV o stampa.</li>
            <li><strong>Amministrare il sistema</strong>: utenti, livelli, gruppi, moduli abilitati e menu.</li>
          </ul>

          <div class="manual-note">
            <strong>Nota:</strong> le funzioni che vedi nel menu dipendono dal tuo <em>livello</em> e dai
            <em>moduli</em> abilitati per il tuo utente. Non tutti vedono tutto: &egrave; un comportamento voluto dal sistema.
          </div>
        </div>
      </section>

      <!-- 2 ACCESSO -->
      <section id="accesso">
        <div class="manual-card">
          <h2>2. Accesso all'applicazione</h2>
          <p>L'accesso &egrave; obbligatorio: chi non &egrave; collegato viene portato automaticamente alla pagina di accesso e non pu&ograve; usare le altre pagine.</p>

          <h3 id="login">Login</h3>
          <ol>
            <li>Apri l'indirizzo dell'applicazione nel browser.</li>
            <li>Nella pagina di accesso inserisci la tua <strong>e-mail</strong> (o nome utente) e la <strong>password</strong>.</li>
            <li>Completa la verifica di sicurezza (captcha), se presente.</li>
            <li>Premi il pulsante di <strong>Accesso</strong>.</li>
          </ol>
          <p>Dopo un accesso corretto vieni reindirizzato automaticamente al <strong>Cruscotto Operativo</strong> (Planning &amp; Presenze), che &egrave; la pagina principale di lavoro.</p>

          <div class="manual-tip">
            <strong>Suggerimento:</strong> se commetti errori nelle credenziali, il sistema ti avvisa. Controlla che la password non abbia blocchi maiuscolo accidentali (Caps Lock).
          </div>

          <h3 id="password">Password dimenticata</h3>
          <ol>
            <li>Nella pagina di accesso clicca su <span class="manual-btn manual-btn-gray">Password dimenticata?</span>.</li>
            <li>Inserisci l'indirizzo e-mail del tuo account.</li>
            <li>Riceverai un'e-mail con un collegamento per <strong>reimpostare la password</strong>.</li>
            <li>Apri il collegamento, inserisci la nuova password e confermala. Poi accedi con la nuova password.</li>
          </ol>

          <h3 id="logout">Logout (uscita)</h3>
          <p>Per uscire dall'applicazione premi l'icona <strong>di uscita</strong> (freccia che esce dalla porta) in alto a destra nella barra di navigazione.</p>
          <div class="manual-note">
            Quando esci, il sistema <strong>sblocca automaticamente le pratiche</strong> che stavi modificando (documenti e sotto-progetti), cos&igrave; altri colleghi possono riprenderle.
          </div>
        </div>
      </section>

      <!-- 3 NAVIGAZIONE -->
      <section id="navigazione">
        <div class="manual-card">
          <h2>3. Navigazione e menu</h2>
          <p>Dopo l'accesso, l'applicazione si presenta con questa struttura:</p>
          <ul>
            <li><strong>Barra in alto</strong>: icone utili (segnala problema, schermo intero, modalit&agrave; scura, chat, uscita).</li>
            <li><strong>Menu laterale a sinistra</strong>: le voci dell'applicazione, organizzate per area.</li>
            <li><strong>Area centrale</strong>: il contenuto della pagina che stai usando.</li>
            <li><strong>In fondo al menu compaiono anche</strong>: la voce <span class="manual-btn manual-btn-gray">Utente</span> (il tuo profilo) e l'indicazione dell'<strong>ultimo accesso</strong> (data e ora).</li>
          </ul>

          <h3 id="livelli">Livelli e moduli (perch&eacute; vedi certe voci e non altre)</h3>
          <p>Il menu &egrave; <strong>dinamico e personalizzato</strong> per ogni utente. Le voci mostrate dipendono da due cose:</p>
          <ul>
            <li><strong>Livello</strong> (<code>level</code>): ogni voce richiede un livello minimo. Il livello 100 vedi tutto.</li>
            <li><strong>Moduli abilitati</strong>: l'amministratore decide quali aree pu&ograve; usare ogni utente.</li>
          </ul>
          <table>
            <tr><th>Livello</th><th>Chi &egrave;</th><th>Cosa vede</th></tr>
            <tr><td><span class="manual-btn manual-btn-red">100</span></td><td>Supervisore / Amministratore</td><td>Tutto, senza limiti. Gestisce anche utenti, menu e permessi.</td></tr>
            <tr><td><span class="manual-btn">80 &ndash; 99</span></td><td>Amministratore / staff interno</td><td>Funzioni avanzate; pu&ograve; filtrare e vedere pi&ugrave; dati. Almeno 81 per gestire altri profili utente.</td></tr>
            <tr><td><span class="manual-btn manual-btn-gray">70</span></td><td>Cliente / operatore esterno</td><td>Limitato ai propri clienti (<code>cd_cli</code>). Questo &egrave; il livello predefinito dei nuovi utenti.</td></tr>
          </table>
          <div class="manual-warn">
            <strong>Importante:</strong> se provi ad aprire con l'indirizzo una pagina a cui non sei autorizzato, compare il messaggio
            <em>&laquo;NON SEI AUTORIZZATO AD ACCEDERE!!!&raquo;</em>. Questo &egrave; normale: chiedi all'amministratore di abilitare il modulo giusto.
          </div>
        </div>
      </section>

      <!-- 4 PLANNING -->
      <section id="planning">
        <div class="manual-card">
          <h2>4. Planning (pianificazione attivit&agrave;)</h2>
          <p>Il Planning &egrave; <strong>il cuore dell'applicazione</strong>. Ogni giorno le squadre vengono assegnate a
          un'attivit&agrave; con orario, luogo, personale, veicolo e commessa. Da qui si pianifica il lavoro di tutti.</p>

          <h3 id="planning-calendario">4.1 Il calendario delle attivit&agrave;</h3>
          <p>Quando accedi ti trovi nel cruscotto con due schede: <strong>Planning Flotta</strong> e <strong>Calendario Presenze</strong>.</p>
          <p>Nella scheda <strong>Planning Flotta</strong> vedi un calendario con le attivit&agrave; del mese. Puoi cambiare vista con:</p>
          <ul>
            <li><strong>Mese</strong>, <strong>Settimana</strong>, <strong>Lista</strong>, <strong>Giornaliero</strong>, e i pulsanti <span class="manual-btn manual-btn-gray">Oggi</span> / <span class="manual-btn manual-btn-gray">Giornaliero</span>.</li>
            <li>Ogni riquadro &egrave; <strong>colorato in base al veicolo</strong> assegnato; in alto vedi la <strong>legenda colori dei mezzi</strong>.</li>
            <li>Passando il <strong>mouse su un'attivit&agrave;</strong> appare una finestra con cliente, personale, veicolo, attivit&agrave; e luogo.</li>
            <li><strong>Clic su un'attivit&agrave;</strong> apre il dettaglio.</li>
            <li><strong>Doppio clic su uno spazio vuoto</strong> apre la creazione di una nuova attivit&agrave; con la data gi&agrave; impostata.</li>
          </ul>

          <h3 id="planning-nuova">4.2 Creare una nuova attivit&agrave;</h3>
          <p>Premi <span class="manual-btn">+ Nuova Attivit&agrave;</span> (o doppio clic su una data). Il modulo &egrave; diviso in tre blocchi, spieghiamo ogni campo:</p>
          <h4>Blocco &laquo;Tempi e Luogo&raquo;</h4>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Data Attivit&agrave;</strong></td><td>Il giorno dell'intervento. La scegli con il calendario.</td></tr>
            <tr><td><strong>Ora Inizio</strong></td><td>A che ora inizia il lavoro (es. 08:00).</td></tr>
            <tr><td><strong>Ora Fine</strong></td><td>A che ora finisce il lavoro (es. 16:00).</td></tr>
            <tr><td><strong>Giro</strong></td><td>L'ordine in cui l'attivit&agrave; viene svolta nella giornata (numero da 1 a 24). Il giro 1 si fa per primo.</td></tr>
            <tr><td><strong>Cliente</strong></td><td>Il cliente per cui si lavora. Si cerca per codice o descrizione.</td></tr>
            <tr><td><strong>Commessa</strong></td><td>La sotto-commessa (lavoro specifico) collegata all'intervento.</td></tr>
            <tr><td><strong>Indirizzo</strong></td><td>Il luogo fisico dove si svolge l'attivit&agrave; (es. cantiere, sede del cliente).</td></tr>
          </table>
          <h4>Blocco &laquo;Risorse&raquo;</h4>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Ditta Esterna</strong></td><td>Se l'intervento lo svolge una ditta in appalto. Facoltativo. Se la scegli, i dipendenti si disabilitano.</td></tr>
            <tr><td><strong>Dipendenti</strong></td><td>Il personale interno assegnato (scelta multipla). Se scegli un dipendente, la ditta esterna si disabilita.</td></tr>
            <tr><td><strong>Q.t&agrave; Operai</strong></td><td>Quanti operai servono per l'intervento (numero).</td></tr>
            <tr><td><strong>Veicoli</strong></td><td>I mezzi da usare, scelti tra quelli con stato <strong>DISPONIBILE</strong> (scelta multipla).</td></tr>
          </table>
          <div class="manual-note">
            <strong>Mutua esclusione:</strong> se scegli una <em>Ditta Esterna</em> il campo <em>Dipendenti</em> si disabilita, e viceversa.
            Usi o l'una o l'altro, non entrambi.
          </div>
          <h4>Blocco &laquo;Dettagli&raquo;</h4>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Stato Completamento</strong></td><td>La fase dell'attivit&agrave;: es. Da Iniziare, In Corso, Completato, Annullato. Ogni stato ha il suo colore.</td></tr>
            <tr><td><strong>Descrizione</strong></td><td>Note libere sul lavoro da fare (preparazione, materiali, indicazioni per la squadra).</td></tr>
          </table>
          <p>Premi <span class="manual-btn manual-btn-green">Salva Impegno</span> per salvare. Se il sistema rileva che un dipendente o un veicolo &egrave;
          gi&agrave; impegnato nello stesso orario, ti avvisa: puoi comunque procedere oppure correggere.</p>

          <h3 id="planning-lista">4.3 La lista delle attivit&agrave;</h3>
          <p>Premi <span class="manual-btn">Lista Attivit&agrave;</span> per vedere l'elenco in tabella. Qui puoi:</p>
          <ul>
            <li><strong>Filtrare</strong> per data, cliente, commessa, veicolo, dipendente e stato (con scelta multipla).</li>
            <li><strong>Modificare i dati direttamente dalla riga</strong> (orari, veicolo, personale, stato, note) senza aprire la scheda.</li>
            <li>Usare i pulsanti a destra della riga: <span class="manual-btn manual-btn-gray">Modifica</span>, <span class="manual-btn manual-btn-gray">Duplica</span>, <span class="manual-btn manual-btn-red">Elimina</span>.</li>
            <li><strong>Esportare</strong> i dati in Excel, PDF o CSV (pulsanti in alto).</li>
          </ul>
          <div class="manual-tip">
            Le righe modificate di recente si evidenziano di <strong>giallo</strong> (salvataggio in corso) e di <strong>verde</strong> (salvataggio riuscito). Cos&igrave; sai sempre che la modifica &egrave; andata a buon fine.
          </div>

          <h3 id="planning-varia">4.4 Varia veicoli (sostituzione rapida)</h3>
          <p>Serve per sostituire un veicolo quando, ad esempio, un mezzo &egrave; in panne, senza modificare ogni riga a mano.</p>
          <ol>
            <li>Premi <span class="manual-btn">Varia Veicoli</span>.</li>
            <li>Scegli la <strong>data</strong> dell'attivit&agrave;.</li>
            <li>Indica la <strong>targa di origine</strong> (quella da sostituire).</li>
            <li>Indica la <strong>targa di sostituzione</strong> (il nuovo mezzo).</li>
            <li>Premi <span class="manual-btn manual-btn-green">Esegui Sostituzione</span>.</li>
          </ol>

          <h3 id="planning-copia">4.5 Copia intera giornata</h3>
          <p>Ti permette di <strong>duplicare tutte le attivit&agrave; di un giorno</strong> in un altro giorno (utile per lavori ripetitivi).</p>
          <ol>
            <li>Premi <span class="manual-btn">Copia Giornata</span>.</li>
            <li>Indica la <strong>data di origine</strong> (da copiare) e la <strong>data di destinazione</strong> (dove copiare).</li>
            <li>Premi <span class="manual-btn manual-btn-green">Esegui Copia</span>.</li>
          </ol>
          <div class="manual-note">I record copiati nascono con stato <strong>&laquo;Da Iniziare&raquo;</strong>, cos&igrave; puoi aggiornarli senza errori.</div>

          <h3 id="planning-pdf">4.6 Stampe PDF</h3>
          <p>Il pulsante <span class="manual-btn">Stampa</span> genera il PDF <strong>&laquo;Procedure Giornaliere&raquo;</strong> del giorno scelto, raggruppato per squadra (veicolo + personale). Il PDF &egrave; ordinato e leggibile, con intestazioni e numero di pagina, e si apre in una nuova scheda pronto da stampare o salvare.</p>
        </div>
      </section>

      <!-- 5 PRESENZE -->
      <section id="presenze">
        <div class="manual-card">
          <h2>5. Presenze e HR</h2>
          <p>Qui si registrano le <strong>presenze</strong> del personale: chi ha lavorato, quando, quante ore, e se c'&egrave; stata un'assenza (ferie, malattia, permesso, ritardo).</p>

          <h3>5.1 Registro presenze</h3>
          <p>Nella pagina <strong>&laquo;Registro Presenze e Costi&raquo;</strong> trovi l'elenco di tutte le presenze con:</p>
          <ul>
            <li>Data, dipendente, orario ingresso/uscita, ore lavorate, tipo di presenza e costo totale.</li>
            <li>Il <strong>costo</strong> &egrave; calcolato automaticamente: <em>ore lavorate &times; tariffa oraria</em> del dipendente.</li>
            <li>Esportazione in Excel, PDF, CSV o stampa.</li>
          </ul>

          <h3>5.2 Registrare una presenza</h3>
          <p>Premi <span class="manual-btn">Registra Presenza</span> e compila i campi:</p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Dipendente</strong></td><td>Per chi registri la presenza. Si digita il nome o si sceglie dall'elenco del personale attivo.</td></tr>
            <tr><td><strong>Cantiere/Sottocommessa</strong></td><td>Collega la presenza a un lavoro specifico (facoltativo). Si cerca per codice o descrizione.</td></tr>
            <tr><td><strong>Data Presenza</strong></td><td>Il giorno della presenza. Di solito oggi, ma puoi cambiarlo.</td></tr>
            <tr><td><strong>Causale</strong></td><td>Il tipo di presenza: lavoro, ferie, malattia, permesso... Si digita o si sceglie dall'elenco.</td></tr>
            <tr><td><strong>Ora Ingresso</strong></td><td>Fascia oraria di ingresso (formato HH:mm, es. 08:00).</td></tr>
            <tr><td><strong>Ora Uscita</strong></td><td>Fascia oraria di uscita (es. 16:30).</td></tr>
            <tr><td><strong>Ore Lavorate</strong></td><td>Si calcola in automatico da ingresso e uscita; puoi comunque correggerlo a mano (es. 8.0).</td></tr>
            <tr><td><strong>Prezzo Ora</strong></td><td>La tariffa oraria usata per calcolare il costo (si riempie da sola se il dipendente ha una tariffa).</td></tr>
            <tr><td><strong>Ritardo (minuti)</strong></td><td>L'eventuale ritardo in minuti (0 se puntuale).</td></tr>
            <tr><td><strong>Note</strong></td><td>Annotazioni libere (es. motivo del permesso, giustificativo).</td></tr>
          </table>
          <p>Premi <span class="manual-btn manual-btn-green">Salva Registrazione</span>.</p>

          <div class="manual-note">
            Se registri un <strong>turno notturno</strong> (es. ingresso alle 22:00 e uscita alle 06:00 del giorno dopo),
            il sistema lo gestisce da solo e calcola le ore corrette.
          </div>

          <h3>5.3 Calendario presenze</h3>
          <p>Dalla scheda <strong>Calendario Presenze</strong> del cruscotto vedi le presenze sul calendario con i colori:</p>
          <ul>
            <li><span class="manual-btn manual-btn-green">Lavoro</span> &mdash; verde.</li>
            <li><span class="manual-btn">Ferie / Permesso</span> &mdash; giallo.</li>
            <li><span class="manual-btn manual-btn-red">Malattia / Infortunio</span> &mdash; rosso.</li>
          </ul>
          <p>Puoi filtrare per <strong>dipendente</strong>, premere <span class="manual-btn">Crea Presenza</span>, o fare doppio clic su una data per registrare subito.</p>
        </div>
      </section>

      <!-- 6 ANAGRAFICHE -->
      <section id="anagrafiche">
        <div class="manual-card">
          <h2>6. Anagrafiche</h2>
          <p>Sono gli <strong>archivi di base</strong> usati in tutta l'applicazione. Gestirli bene rende tutto il resto pi&ugrave; semplice.</p>

          <h3 id="anag-personale">Personale</h3>
          <p>Elenco dei dipendenti con dati anagrafici, <strong>tariffa oraria</strong>, mansione, reparto, contatti e allegati/documenti. Dalla scheda del dipendente vedi anche le attivit&agrave; che gli sono state assegnate nel Planning.</p>
          <p><strong>Campi della scheda &laquo;Anagrafica e Stato Contrattuale&raquo;:</strong></p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Nome</strong> / <strong>Cognome</strong></td><td>Il nome e cognome del dipendente.</td></tr>
            <tr><td><strong>Luogo Data Nascita</strong></td><td>Es. &laquo;Roma, 01/01/1980&raquo;.</td></tr>
            <tr><td><strong>Documento</strong></td><td>Il documento di riconoscimento (es. codice fiscale o carta d'identit&agrave;), in maiuscolo.</td></tr>
            <tr><td><strong>Data Assunzione</strong></td><td>Quando &egrave; stato assunto.</td></tr>
            <tr><td><strong>Tipo Contratto</strong></td><td>Indeterminato, Determinato, Apprendistato, Co.Co.Co., Partita IVA.</td></tr>
            <tr><td><strong>Tariffa Oraria (EUR/h)</strong></td><td>Quanto costa il dipendente all'ora. Serve per il calcolo costi di presenze e report.</td></tr>
            <tr><td><strong>Data Inserimento</strong></td><td>Compilata automaticamente dal sistema.</td></tr>
            <tr><td><strong>Dipendente in Forza</strong></td><td>Spunta se il dipendente &egrave; ancora attivo. I non attivi non compaiono nelle nuove assegnazioni.</td></tr>
          </table>
          <p><strong>Campi &laquo;Inquadramento Aziendale&raquo;:</strong> <strong>Reparto</strong>, <strong>Ruolo</strong> e <strong>Mansione</strong> (scelti dagli archivi creati con le rispettive anagrafiche).</p>
          <p><strong>Campi &laquo;Contatti e Recapiti&raquo;:</strong> <strong>Cellulare</strong>, <strong>Email</strong>, <strong>Indirizzo</strong>, <strong>Citt&agrave;</strong>.</p>
          <p>Premi <span class="manual-btn manual-btn-green">Salva Anagrafica</span> per confermare.</p>

          <h3 id="anag-veicoli">Veicoli</h3>
          <p>Flotta aziendale. La scheda veicolo raccoglie identificazione, dati tecnici e scadenze burocratiche. Solo i mezzi con stato <strong>DISPONIBILE</strong> compaiono nella creazione attivit&agrave; del Planning.</p>
          <p><strong>Prima riga del modulo:</strong></p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Tipologia</strong></td><td>Propriet&agrave;, noleggio, leasing... Se scegli &laquo;Propriet&agrave;&raquo; il campo scadenza contratto si nasconde.</td></tr>
            <tr><td><strong>Data Scadenza Contratto</strong></td><td>Quando scade il contratto (solo per mezzi non di propriet&agrave;).</td></tr>
          </table>
          <p><strong>&laquo;Identificazione Mezzo&raquo;:</strong> <strong>Targa</strong> (es. AA000BB), <strong>Marca Modello</strong> (es. Fiat Ducato L2H2), <strong>Stato Veicolo</strong> (Disponibile, In Uso, In Riparazione, Dismesso).</p>
          <p><strong>&laquo;Dati Tecnici e Utilizzo&raquo;:</strong> <strong>Data Immatricolazione</strong>, <strong>Classe Euro</strong> (Euro 4/5/6, Elettrico, Ibrido), <strong>Peso Complessivo</strong> (es. 35 q.li), <strong>Chilometraggio (km)</strong> attuale.</p>
          <p><strong>&laquo;Scadenze Burocratiche e Archivio&raquo;:</strong> <strong>Scadenza Assicurazione</strong>, <strong>Scadenza Revisione</strong>, <strong>Scadenza ZTL</strong>, <strong>Collocazione Cartaceo</strong> (dove &egrave; conservato il fascicolo cartaceo, es. &laquo;Armadio A, Ripiano 2&raquo;).</p>
          <p>Premi <span class="manual-btn manual-btn-green">Salva Scheda Veicolo</span> per confermare.</p>

          <h3 id="anag-squadre">Squadre</h3>
          <p>Gruppi di lavoro. Ogni squadra ha:</p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Nome</strong></td><td>Il nome della squadra (es. &laquo;Squadra Alpha&raquo;).</td></tr>
            <tr><td><strong>Ditta Esterna</strong></td><td>Se la squadra appartiene a una ditta in appalto (facoltativo, &laquo;Nessuna Ditta Esterna&raquo; per l'azienda).</td></tr>
            <tr><td><strong>Mezzo</strong></td><td>Il veicolo principale della squadra (es. &laquo;Furgone ABC 123&raquo;).</td></tr>
            <tr><td><strong>Data Fine Validit&agrave;</strong></td><td>Quando la squadra termina. <strong>Vuoto = valida a tempo indeterminato</strong>. Attenzione: un dipendente non pu&ograve; far parte di pi&ugrave; squadre attive nello stesso momento.</td></tr>
            <tr><td><strong>Membri</strong></td><td>I dipendenti che fanno parte della squadra (scelta multipla dal personale attivo).</td></tr>
          </table>
          <p>Puoi creare una squadra <strong>direttamente dall'elenco</strong> (creazione inline).</p>

          <h3 id="anag-ditte">Ditte esterne</h3>
          <p>Le aziende in appalto usate nel Planning come alternativa ai dipendenti. Hanno due campi:</p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Codice</strong></td><td>Il codice identificativo (es. DITTA001). Si imposta alla creazione e non si pu&ograve; pi&ugrave; cambiare.</td></tr>
            <tr><td><strong>Descrizione</strong></td><td>Il nome della ditta esterna.</td></tr>
          </table>

          <h3 id="anag-altre">Altre anagrafiche</h3>
          <ul>
            <li><strong>Clienti</strong> &mdash; anagrafica clienti (codice, ragione sociale, indirizzo, localit&agrave;, CAP, nazione, P.IVA, codice fiscale), usata per la ricerca cliente in Planning e nel resto dell'applicazione.</li>
            <li><strong>Reparti</strong> e <strong>Mansioni</strong> &mdash; archivi con <strong>Codice</strong> (breve, in maiuscolo) e <strong>Descrizione</strong>; servono per inquadrare il personale.</li>
            <li><strong>Relazione Clienti</strong> &mdash; collega un <strong>Cliente Padre</strong> a un <strong>Cliente Figlio</strong> (es. societ&agrave; capogruppo e controllata).</li>
            <li><strong>Causali Presenza</strong> &mdash; archivio delle causali (<strong>Codice</strong> + <strong>Descrizione</strong>) usate nelle presenze (es. FER = ferie, MAL = malattia).</li>
            <li><strong>Liste prezzi</strong> &mdash; codici-conto con tipo (<strong>Acquisti</strong> o <strong>Vendite</strong>).</li>
            <li><strong>Locazioni</strong> &mdash; descrizione e <strong>colore</strong> associato (per distinguere visivamente le sedi).</li>
            <li><strong>Elementi email</strong> &mdash; archivio dei messaggi inviati/ricevuti con possibilit&agrave; di risposta.</li>
          </ul>
        </div>
      </section>

      <!-- 7 REPORTS -->
      <section id="reports">
        <div class="manual-card">
          <h2>7. Report e statistiche</h2>

          <h3 id="report-centro">Centro reportistica</h3>
          <p>La pagina <strong>&laquo;Centro Reportistica&raquo;</strong> raccoglie i report principali. Scegli la card e premi <span class="manual-btn">Vai al Report</span>. Ogni report ha una barra di <strong>filtri</strong> in cima: imposta i criteri che ti servono e premi <span class="manual-btn">Genera</span> per costruire la tabella. <span class="manual-btn manual-btn-gray">Reset</span> azzera tutti i filtri. Ogni tabella pu&ograve; essere <strong>esportata</strong> (Copia, Excel, PDF, CSV) o <strong>stampata</strong> con i pulsanti in alto.</p>

          <h3 id="report-presenze">7.1 Report Presenze e Costi</h3>
          <p><strong>A cosa serve:</strong> analizza ore lavorate, assenze, ferie e i costi del personale in un periodo. &Egrave; il report di riferimento per capire quanto sta costando il lavoro.</p>
          <p><strong>Filtri disponibili:</strong></p>
          <ul>
            <li><strong>Dal / Al</strong> &mdash; il periodo da analizzare (date).</li>
            <li><strong>Dipendente</strong> &mdash; uno o pi&ugrave; dipendenti (scelta multipla, <em>&laquo;Tutti...&raquo;</em> per vederli tutti).</li>
            <li><strong>Causale</strong> &mdash; uno o pi&ugrave; tipi di presenza (lavoro, ferie, malattia...) (<em>&laquo;Tutte...&raquo;</em>).</li>
          </ul>
          <p><strong>Indicatori in alto:</strong> <em>Totale Ore Lavorate</em> (in ore) e <em>Costo Stimato Totale</em> (in euro).</p>
          <p><strong>Colonne della tabella:</strong> Data · Dipendente · Orario Ingresso/Uscita · Ore lavorate · Causale (con colore) · Costo (&#0144;).</p>

          <h3 id="report-flotta">7.2 Report Attivit&agrave; Flotta</h3>
          <p><strong>A cosa serve:</strong> mostra come vengono usati i veicoli nelle attivit&agrave; pianificate, con chi li guida (dipendenti o ditte esterne) e dove.</p>
          <p><strong>Filtri disponibili:</strong></p>
          <ul>
            <li><strong>Dal / Al</strong> &mdash; periodo.</li>
            <li><strong>Ditta</strong> &mdash; una o pi&ugrave; ditte esterne (<em>&laquo;Tutte...&raquo;</em>).</li>
            <li><strong>Dipendente</strong> &mdash; uno o pi&ugrave; dipendenti (<em>&laquo;Tutti...&raquo;</em>).</li>
            <li><strong>Veicolo</strong> &mdash; uno o pi&ugrave; mezzi (<em>&laquo;Tutti...&raquo;</em>).</li>
            <li><strong>Stato</strong> &mdash; stato di completamento (<em>&laquo;Tutti...&raquo;</em>).</li>
          </ul>
          <p><strong>Indicatori in alto:</strong> <em>Totale Attivit&agrave;</em> (interventi), <em>Dipendenti Coinvolti</em> (persone) e <em>Veicoli Impiegati</em> (mezzi).</p>
          <p><strong>Colonne della tabella:</strong> Data · Giro · Orario · Dipendenti · Ditta Esterna · Veicolo/i (targa e modello) · Indirizzo · Note.</p>

          <h3 id="report-consuntivo">7.3 Consuntivo Ore per Ditta Esterna</h3>
          <p><strong>A cosa serve:</strong> riepiloga giorni, ore e costi di ogni ditta esterna in appalto. Utile per il controllo dei costi esterni.</p>
          <p><strong>Filtri disponibili:</strong></p>
          <ul>
            <li><strong>Ditta Esterna</strong> &mdash; una o pi&ugrave; ditte (<em>&laquo;Tutte le ditte...&raquo;</em>).</li>
            <li><strong>Dal / Al</strong> &mdash; periodo.</li>
          </ul>
          <p><strong>Indicatori in alto:</strong> <em>Ditte</em> (numero), <em>Totale Ore</em>, <em>Dipendenti</em> (persone) e <em>Costo Totale</em> (euro).</p>
          <p><strong>Prima tabella &laquo;Riepilogo per Ditta Esterna&raquo;:</strong> una riga per ditta con numero dipendenti, giornate, ore totali e costo totale.</p>
          <p><strong>Seconda tabella &laquo;Dettaglio Attivit&agrave;&raquo;:</strong> Data · Giro · Dipendente · Cliente · Indirizzo · Note attivit&agrave;.</p>

          <h3 id="report-squadra">7.4 Rapporto Attivit&agrave; per Squadra</h3>
          <p><strong>A cosa serve:</strong> raggruppa le attivit&agrave; per squadra, cos&igrave; vedi a colpo d'occhio il lavoro di ogni squadra nel periodo.</p>
          <p><strong>Filtri disponibili:</strong></p>
          <ul>
            <li><strong>Dal / Al</strong> &mdash; periodo.</li>
            <li><strong>Dipendente</strong> &mdash; uno o pi&ugrave; dipendenti (<em>&laquo;Tutti...&raquo;</em>).</li>
            <li><strong>Veicolo</strong> &mdash; uno o pi&ugrave; mezzi (<em>&laquo;Tutti...&raquo;</em>).</li>
            <li><strong>Ditta Esterna</strong> &mdash; una o pi&ugrave; ditte (<em>&laquo;Tutte...&raquo;</em>).</li>
          </ul>
          <p><strong>Indicatori in alto:</strong> <em>Squadre</em>, <em>Attivit&agrave;</em> e <em>Clienti</em> coinvolti.</p>
          <p><strong>Come si legge:</strong> le attivit&agrave; sono raggruppate per squadra in pannelli <strong>espandibili/collassabili</strong> (pulsanti <span class="manual-btn manual-btn-gray">Espandi Tutti</span> / <span class="manual-btn manual-btn-gray">Comprimi Tutti</span>). C'&egrave; anche un campo di <strong>ricerca rapida</strong> (indirizzo, cliente, targa, nominativo) e il pulsante <span class="manual-btn manual-btn-gray">Stampa</span>.</p>
          <p><strong>Colonne della tabella:</strong> Giro · Data · Orario · Cliente · Indirizzo · Note · Stato (con colore).</p>
        </div>
      </section>

      <!-- 8 AMMINISTRAZIONE -->
      <section id="amministrazione">
        <div class="manual-card">
          <h2>8. Amministrazione</h2>
          <p>Riservata agli <strong>amministratori</strong> (livello 100 e, in parte, 80/81). Da qui si configura il sistema.</p>
          <p>Gli amministratori vedono una voce <strong>ADMIN panel</strong> nel menu con gli strumenti seguenti.</p>

          <h3 id="admin-utenti">Gestione utenti</h3>
          <p>Nella pagina <strong>Utenti</strong> puoi creare e modificare gli account. La scheda &egrave; divisa in due blocchi:</p>
          <p><strong>Blocco &laquo;Interfaccia Utente&raquo;</strong> (campi utili all'utente stesso):</p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Username</strong> / <strong>Email</strong></td><td>Le credenziali di accesso (visibili agli amministratori).</td></tr>
            <tr><td><strong>Avatar</strong></td><td>L'immagine del profilo (appare in chat e nelle liste).</td></tr>
            <tr><td><strong>Colore Griglia</strong> / <strong>Colore Sidebar</strong></td><td>I colori dell'interfaccia personale (tabelle e menu laterale).</td></tr>
            <tr><td><strong>P.IVA</strong></td><td>La partita IVA collegata all'utente.</td></tr>
            <tr><td><strong>Password</strong> (e conferma)</td><td>La password di accesso; va ripetuta per conferma, altrimenti il sistema avvisa che le due non coincidono.</td></tr>
          </table>
          <p><strong>Blocco &laquo;Admin Panel&raquo;</strong> (solo per amministratori):</p>
          <table>
            <tr><th>Campo</th><th>Cosa significa</th></tr>
            <tr><td><strong>Status</strong></td><td>Attivato o Disabilitato.</td></tr>
            <tr><td><strong>Gruppo</strong></td><td>Il gruppo utenti a cui appartiene (da &laquo;Gruppo Utenti&raquo;).</td></tr>
            <tr><td><strong>Moduli</strong></td><td>Le aree dell'applicazione che l'utente potr&agrave; vedere nel menu (scelta multipla).</td></tr>
            <tr><td><strong>Reports</strong></td><td>I report a cui l'utente avr&agrave; accesso.</td></tr>
            <tr><td><strong>Cliente</strong> (cd_cli)</td><td>Il/i cliente/i di cui l'utente vede i dati (scelta multipla).</td></tr>
            <tr><td><strong>Tour Manager</strong></td><td>Spunta se l'utente &egrave; un tour manager.</td></tr>
            <tr><td><strong>Membro Aux</strong></td><td>Spunta per i membri del personale ausiliario.</td></tr>
            <tr><td><strong>Agente</strong></td><td>Il codice agente collegato (per statistiche e provvigioni).</td></tr>
          </table>
          <div class="manual-warn">
            Esiste un <strong>limite massimo di utenti attivi</strong> per l'area clienti (15). Se lo raggiungi, il sistema ti avvisa.
          </div>
          <p>Un utente normale pu&ograve; modificare <strong>solo il proprio profilo</strong>; per modificare altri profili serve livello alto.</p>

          <h3 id="admin-menu">Menu e permessi</h3>
          <ul>
            <li><strong>Menu</strong> (<code>xmenu</code>) e <strong>sottomenu</strong> (<code>xsubmenu</code>): le voci del menu laterale, con livello minimo e url.</li>
            <li><strong>Ruoli</strong>: l'elenco dei ruoli interni (codice + descrizione).</li>
            <li><strong>Azioni</strong> e <strong>permessi</strong> (relazioni utente-form-azione): controllo <em>puntuale</em> su chi pu&ograve; fare cosa (leggere, scrivere, eliminare) in determinate pagine.</li>
          </ul>
          <div class="manual-note">
            Il sistema non usa i ruoli standard di Yii: i permessi sono gestiti in modo personalizzato (livello + moduli + permessi puntuali). Per questo la configurazione va fatta con attenzione.
          </div>
        </div>
      </section>

      <!-- 9 SEGNALAZIONE -->
      <section id="segnalazione">
        <div class="manual-card">
          <h2>9. Segnalare un problema</h2>
          <p>Se trovi un errore o un comportamento strano, usa il pulsante <strong>Segnala Anomalia</strong>: l'icona a forma di <strong>insetto (bug)</strong> in alto a destra nella barra di navigazione.</p>
          <ol>
            <li>Clicca l'icona <strong>bug</strong>.</li>
            <li>Si apre una finestra in cui puoi <strong>descrivere il problema</strong>.</li>
            <li>Premi <span class="manual-btn manual-btn-red">Invia Segnalazione</span>.</li>
          </ol>
          <p>Il sistema <strong>aggiunge automaticamente</strong> dati utili al tecnico (pagina in cui ti trovi, indirizzo, browser, utente, sessione) e invia il tutto via e-mail al supporto tecnico. Non devi copiare e incollare nulla.</p>
          <div class="manual-tip">Pi&ugrave; dettagli ci sono, pi&ugrave; &egrave; facile per noi risolvere il problema. Spiega cosa stavi facendo e cosa ti aspettavi.</div>
        </div>
      </section>

      <!-- 10 GLOSSARIO -->
      <section id="glossario">
        <div class="manual-card">
          <h2>10. Glossario</h2>
          <table>
            <tr><th>Termine</th><th>Significato</th></tr>
            <tr><td><strong>Planning</strong></td><td>La pianificazione giornaliera delle attivit&agrave; delle squadre.</td></tr>
            <tr><td><strong>Giro</strong></td><td>L'ordine (1&ndash;24) in cui le attivit&agrave; vengono svolte nella giornata.</td></tr>
            <tr><td><strong>Commessa / Sotto-commessa</strong></td><td>Un lavoro (pratica) collegato a un cliente; la sotto-commessa &egrave; un lavoro pi&ugrave; specifico.</td></tr>
            <tr><td><strong>Ditta esterna</strong></td><td>Un'azienda in appalto che svolge lavoro al posto dei dipendenti interni.</td></tr>
            <tr><td><strong>cd_cli</strong></td><td>Il codice cliente assegnato all'utente; determina quali dati lui vede.</td></tr>
            <tr><td><strong>Livello (level)</strong></td><td>Il grado di permesso dell'utente (100 = amministratore).</td></tr>
            <tr><td><strong>Moduli</strong></td><td>Le aree dell'applicazione abilitate per un utente.</td></tr>
            <tr><td><strong>Segnala Anomalia</strong></td><td>Il pulsante (icona bug) per inviare al supporto la descrizione di un problema.</td></tr>
          </table>
        </div>
      </section>

    </main>
  </div>
</div>