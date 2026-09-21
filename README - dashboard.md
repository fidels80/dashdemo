<p align="center">
    <h1 align="center">Dashboard Ufficio2000 / Presenze — Gestionale operativo (Yii 2)</h1>
    <br>
</p>

Gestionale web interno **Ufficio2000** (app id `Presenze`, lingua `it-IT`), basato su **Yii 2 Basic Template** con tema **AdminLTE 3**.
È la dashboard operativa aziendale: gestione personale, presenze, planning, rapportini, documenti/commesse, agenda, file, viaggi/eventi, reportistica e integrazioni ERP.

### Cosa fa questo progetto

- **Presenze / Personale / Planning (cuore):** anagrafiche personale, mansioni, reparti, veicoli, locazioni, tipologie presenza; planning giornaliero con controllo conflitti personale/veicolo, copia giorno, update inline, stampa PDF quotidiana; rapportini ore (data, ora_in/ora_out, quantità, commessa, cliente).
- **Documenti / Commesse / Scadenze / Pagamenti:** testate/righe documenti (`doc_head`/`doc_rows` con conferma, rifiuto, annotazioni, lock utente), commesse e sottocommesse, moduli GAC (preventivi, attività, materiali, spese, risorse), scadenziari, pagamenti, etichette da DDT.
- **Agenda / ToDo / Calendario:** agenda con file allegati e calendario (FullCalendar Scheduler + `kriss/yii2-calendar-schedule`), ToDo con gruppi/stati/priorità/assegnazioni, import da Vtiger.
- **Anagrafiche / ERP:** clienti, destinazioni, agenti, articoli, testate (UEC, AR/Arca Evolution), conti/co.ge, collegamenti multi-DB SQL Server + MySQL e Vtiger.
- **Files / DMS:** gestione cartelle/file (`all_files`, `folders`), upload (2amigos file-upload, kartik fileinput), download, anteprime, mail collegate.
- **Viaggi / Eventi:** modulo trasferte/eventi (`xtravelhead/row`, tappe, venue, strutture, roomlist, nominativi, menu), wizard righe/tappe, export dettaglio.
- **Report / Statistiche / Export:** dashboard con ApexCharts/Chart.js, statistic KoolReport, export Excel (PhpSpreadsheet), PDF (mPDF/TCPDF/wkhtmltopdf), PDF parser, griglie Kartik (GridView, Dynagrid, Export, Editable, TreeManager).
- **Mail / Chat / AI / Log:** mailer Outlook, import Exchange (php-ews), chat interna/staff, componenti AI `Gemini/Ollama/Geobadge`, logger DB su insert/update/delete, sessioni DB, menu dinamico da DB per utente/ruolo (`xmenu/xsubmenu/xaction`, `User.moduli/level`).
- **Integrazioni:** Arca Evolution, Vtiger (progetti/ticket), Exchange, Swagger API, Smartsupp chat.

### Stack tecnico

Core `yiisoft/yii2 ~2.0.14`, `yii2-bootstrap` + `yii2-bootstrap5`, `hail812/yii2-adminlte3`, `kartik-v/*`, `phpoffice/phpspreadsheet`, `tecnickcom/tcpdf`, `mikehaertl/phpwkhtmltopdf`, `smalot/pdfparser`, `koolreport`, `kriss/yii2-calendar-schedule`, `onmotion/yii2-widget-apexcharts`, `chart-js`, `wbraganca/yii2-dynamicform`, `2amigos/yii2-ckeditor-widget` + file-upload, `floor12/yii2-summernote`, `guzzlehttp/guzzle` + `yii2-httpclient`, `garethp/php-ews`.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![ci-linux](https://github.com/yiisoft/yii2-app-basic/workflows/ci-linux/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Aci-linux)
[![ci-windows](https://github.com/yiisoft/yii2-app-basic/workflows/ci-windows/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Aci-windows)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.6.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](http://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8000

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](http://codeception.com/).
By default there are 3 test suites:

- `unit`
- `functional`
- `acceptance`

Tests can be executed by running

```
vendor/bin/codecept run
```

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 


### Running  acceptance tests

To execute acceptance tests do the following:  

1. Rename `tests/acceptance.suite.yml.example` to `tests/acceptance.suite.yml` to enable suite configuration

2. Replace `codeception/base` package in `composer.json` with `codeception/codeception` to install full featured
   version of Codeception

3. Update dependencies with Composer 

    ```
    composer update  
    ```

4. Download [Selenium Server](http://www.seleniumhq.org/download/) and launch it:

    ```
    java -jar ~/selenium-server-standalone-x.xx.x.jar
    ```

    In case of using Selenium Server 3.0 with Firefox browser since v48 or Google Chrome since v53 you must download [GeckoDriver](https://github.com/mozilla/geckodriver/releases) or [ChromeDriver](https://sites.google.com/a/chromium.org/chromedriver/downloads) and launch Selenium with it:

    ```
    # for Firefox
    java -jar -Dwebdriver.gecko.driver=~/geckodriver ~/selenium-server-standalone-3.xx.x.jar
    
    # for Google Chrome
    java -jar -Dwebdriver.chrome.driver=~/chromedriver ~/selenium-server-standalone-3.xx.x.jar
    ``` 
    
    As an alternative way you can use already configured Docker container with older versions of Selenium and Firefox:
    
    ```
    docker run --net=host selenium/standalone-firefox:2.53.0
    ```

5. (Optional) Create `yii2basic_test` database and update it by applying migrations if you have them.

   ```
   tests/bin/yii migrate
   ```

   The database configuration can be found at `config/test_db.php`.


6. Start web server:

    ```
    tests/bin/yii serve
    ```

7. Now you can run all available tests

   ```
   # run all available tests
   vendor/bin/codecept run

   # run acceptance tests
   vendor/bin/codecept run acceptance

   # run only unit and functional tests
   vendor/bin/codecept run unit,functional
   ```

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage --coverage-html --coverage-xml

#collect coverage only for unit tests
vendor/bin/codecept run unit --coverage --coverage-html --coverage-xml

#collect coverage for unit and functional tests
vendor/bin/codecept run functional,unit --coverage --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.
