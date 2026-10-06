# AGENTS.md — Dashboard Planorys (dashdemo)

Applicazione **Yii2 (PHP)** su **SQL Server**, servita da XAMPP
(`E:\xampp82\htdocs\i3qtv\aivora\mg`). Interfaccia in italiano con AdminLTE/Bootstrap 4 e
DataTables.

## Comandi utili

- Avvio web: XAMPP (Apache + PHP).
- Console Yii: `php yii <controller>/<action>` (es. `php yii migrate`).
- Migrazioni: `php yii migrate` (per ogni migrazione `.php` esiste il gemello
  `.sql` da eseguire manualmente su SQL Server, quando serve).
- Lint di un file PHP: `php -l <file>`.
- Controllo allineamento con GitHub (a inizio sessione): vedi
  «Regola obbligatoria: allineamento con GitHub a inizio sessione».

## Struttura

- `controllers/` — controller web (`app\controllers`).
- `models/` — ActiveRecord; tabella `dash_*` = tabelle applicative.
- `views/` — viste; layout in `views/layouts/`.
- `commands/` — comandi console (`app\commands`).
- `migrations/` — migrazioni Yii + script `.sql`.
- `components/AccessControl.php` — ACL: il livello 100 (o le email in
  `params['superEmails']`) ha accesso completo, gli altri hanno default deny.
- `components/DbLogger.php` — log automatico insert/update/delete dei model.
- `views/site/manuale.php` — manuale utente ufficiale.
- Menu laterale dinamico: tabella `dash_menu` (con `livello_min`, `per_tutti`,
  `genitore_id`).

## Regola obbligatoria: allineamento con GitHub a inizio sessione

**Prima di qualsiasi altra cosa**, appena aperta la sessione, l'agente deve
verificare che la copia locale corrisponda all'ultima committata su GitHub.

- **Remote giusto: `dashdemo`** (`https://github.com/fidels80/dashdemo.git`).
  Il remote `origin` punta a `fidels80/dashviv`, che è un progetto **diverso**:
  non usarlo mai per fetch, pull o push.
- **Branch locale `master`**, che fa tracking di `dashdemo/main` (nomi diversi).
  Il branch canonico su GitHub è **`main`**: `master` esiste solo in locale.

Attenzione alla trappola dei nomi: **`git push dashdemo master` e
`git pull dashdemo master` sono sbagliati**, perché `master` viene risolto sul
remote, dove non deve esistere. Il push finirebbe su `refs/heads/master`
creando un branch fantasma e `main` resterebbe indietro. Usare sempre
`HEAD:main` (o il `git push`/`git pull` senza argomenti, che seguono già
l'upstream configurato).

Il controllo si esegue con (già verificato funzionante in questo progetto):

```
git fetch dashdemo --prune
git rev-parse HEAD
git rev-parse dashdemo/main
git rev-list --count dashdemo/main..HEAD
git rev-list --count HEAD..dashdemo/main
```

Interpretazione:

| Esito | Significato | Cosa fare |
| --- | --- | --- |
| entrambi i contatori `0` | allineato | basta dirlo e proseguire |
| `HEAD..dashdemo/main` > 0 | **su GitHub ci sono commit che qui non ci sono** | avvisare l'utente e proporre `git pull --ff-only dashdemo main`; non fare il pull senza conferma |
| `dashdemo/main..HEAD` > 0 | **ci sono commit locali non pushati** | avvisare l'utente e proporre `git push dashdemo HEAD:main` |

Dopo un push o un pull conviene riverificare con `git rev-parse dashdemo/main`:
un `git push dashdemo master` sbagliato termina comunque con exit code 0 e non
segnala nulla, quindi il confronto tra i due SHA è l'unico controllo affidabile.

Comunicare il risultato in una riga sola all'inizio della sessione, per esempio
`Allineato con GitHub: 88a8f16`. Non fermare il lavoro se le copie divergono:
segnalare e chiedere, poi continuare con il resto della richiesta.

## Regola obbligatoria: documentazione dopo il commit

Dopo **ogni commit git che introduce migliorie**, l'agente deve:

1. Aggiornare il **manuale utente** `views/site/manuale.php` con le modifiche
   visibili all'utente, mantenendo struttura e stile esistenti.
2. Registrare nel **changelog** (tabella `dash_changelog`) **cosa** è stato
   modificato e **in che modo**:
   - `php yii changelog/git 1` importa il commit (l'hook `post-commit` lo fa
     automaticamente);
   - `php yii changelog/set-dettaglio <id> "<descrizione>"` arricchisce il
     dettaglio.

Per questo esiste l'agente `doc-git` (`.opencode/agent/doc-git.md`) e il
comando `/aggiorna-manuale`.

L'hook `post-commit` (`.githooks/post-commit`, attivato con
`git config core.hooksPath .githooks`) crea la voce di changelog a ogni commit.

## Convenzioni Yii2 del progetto

- **Controller**: namespace `app\controllers`, classe estende `yii\web\Controller`.
  Azioni: `actionIndex` con `ActiveDataProvider`, `actionView/Update/Create/Delete`,
  `findModel()` che lancia `NotFoundHttpException`. `VerbFilter` per `delete` (POST).
  Ajax: `Yii::$app->response->format = Response::FORMAT_JSON`.
- **Numero singolare nel nome**: il controller è singolare (`MgunitamisuraController`),
  il modello è `MgUnitaMisura`. Segui sempre lo stesso schema per gli altri moduli.
- **Viste index**: usano `app\components\DataTables` (componente custom del progetto):
  `<table id="..." class="table table-striped table-bordered"...>` con `<thead>` e righe
  in `<tbody>`, chiuse con `<?php DataTables::render('id-tabella', 1, 'asc'); ?>`.
  Bottoni azione con `Html::a('<i class="fas fa-..."></i>', [...], [...])` e colonna
  `class="text-center text-nowrap no-export"`.
- **Layout**: la navbar è in `views/layouts/navbar.php`, sidebar in `views/layouts/main.php`
  e `sidebar-*.php`. Il menu laterale è dinamico (tabella `dash_menu`).
- **Migrazioni**: ogni migrazione Yii ha il gemello `.sql` (per SQL Server) nella stessa
  cartella `migrations/`, da eseguire a mano quando serve.
- **ACL**: `components/AccessControl.php` — livello 100 o email in `params['superEmails']`
  = accesso completo; le altre pagine riservate vanno protette sia nel menu
  (`livello_min = 100`, `per_tutti = 0`) sia nel controller (`AccessControl::isSuper`).

## Convenzioni

- Non aggiungere commenti al codice se non richiesto.
- Seguire lo stile dei controller/viste vicini (es. `MgunitamisuraController`).
- Le pagine riservate ai livelli elevati vanno protette sia nel menu
  (`livello_min = 100`, `per_tutti = 0`) sia nel controller
  (`AccessControl::isSuper`).
- Non committare/pushare senza richiesta esplicita.
- **Non cancellare mai dati** (righe in database, file, branch locali o remoti,
  tag, commit) se non richiesto esplicitamente dall'utente. Vale per qualsiasi
  operazione distruttiva: `DELETE`/`TRUNCATE`/`DROP`, `rm`/`Remove-Item`,
  `git branch -d`, `git push --delete`, `git reset --hard`, `git clean`,
  sovrascritture di migrazioni, ecc. Se un'operazione distruttiva sembra
  necessaria ma non è stata chiesta, fermarsi e chiedere conferma.
