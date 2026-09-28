<?php

namespace app\controllers;

use Yii;
use app\models\MgAliquotaIva;
use app\models\MgArticolo;
use app\models\MgArticoloUm;
use app\models\MgAttributoArticolo;
use app\models\MgModelloTessuto;
use app\models\MgUnitaMisura;
use yii\web\Controller;
use yii\web\Response;

/**
 * Wizard prodotti: crea in sequenza modello, tessuti, colori e taglie
 * e genera un articolo (con GUID) per ogni combinazione selezionata.
 *
 * Stato del wizard mantenuto in sessione (chiave "mgwizard").
 */
class MgwizardController extends Controller
{
    const SESSION_KEY = 'mgwizard';
    const MAX_COMBINAZIONI = 5000;

    public function actionIndex()
    {
        $stato = $this->getStato();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $idModello = $this->risolviModello($post);
            if ($idModello === null) {
                Yii::$app->session->setFlash('error', 'Seleziona un modello esistente oppure creane uno nuovo.');
                return $this->redirect(['index']);
            }

            // Se cambia il modello, azzera le selezioni degli step successivi.
            if ((int) ($stato['id_modello'] ?? 0) !== (int) $idModello) {
                unset($stato['tessuti'], $stato['colori'], $stato['taglie']);
            }

            $stato['id_modello'] = $idModello;
            $stato['defaults'] = [
                'prezzo' => $post['prezzo'] ?? 0,
                'id_iva_vendita' => $post['id_iva_vendita'] ?: null,
                'id_iva_acquisto' => $post['id_iva_acquisto'] ?: null,
                'id_unita_misura' => $post['id_unita_misura'] ?: null,
            ];
            $this->setStato($stato);

            return $this->redirect(['tessuti']);
        }

        return $this->render('index', [
            'stato' => $stato,
            'modelli' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_MODELLO),
            'aliquote' => MgAliquotaIva::mapAttivi(),
            'unita' => MgUnitaMisura::mapAttivi(),
        ]);
    }

    public function actionTessuti()
    {
        $stato = $this->getStato();
        if (empty($stato['id_modello'])) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $ids = $this->idsPost('tessuti');
            $stato['tessuti'] = $ids;
            $this->setStato($stato);
            MgModelloTessuto::sincronizza($stato['id_modello'], $ids);
            return $this->redirect(['colori']);
        }

        // Preseleziona i tessuti già associati al modello (solo al primo accesso).
        if (!array_key_exists('tessuti', $stato)) {
            $stato['tessuti'] = MgModelloTessuto::idTessutiPerModello($stato['id_modello']);
        }

        return $this->render('tessuti', [
            'stato' => $stato,
            'modello' => MgAttributoArticolo::findOne($stato['id_modello']),
            'voci' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TESSUTO),
        ]);
    }

    public function actionColori()
    {
        $stato = $this->getStato();
        if (empty($stato['id_modello'])) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $stato['colori'] = $this->idsPost('colori');
            $this->setStato($stato);
            return $this->redirect(['taglie']);
        }

        return $this->render('colori', [
            'stato' => $stato,
            'modello' => MgAttributoArticolo::findOne($stato['id_modello']),
            'voci' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_COLORE),
        ]);
    }

    public function actionTaglie()
    {
        $stato = $this->getStato();
        if (empty($stato['id_modello'])) {
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $stato['taglie'] = $this->idsPost('taglie');
            $this->setStato($stato);
            return $this->redirect(['riepilogo']);
        }

        return $this->render('taglie', [
            'stato' => $stato,
            'modello' => MgAttributoArticolo::findOne($stato['id_modello']),
            'voci' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TAGLIA),
        ]);
    }

    public function actionRiepilogo()
    {
        $stato = $this->getStato();
        if (empty($stato['id_modello'])) {
            return $this->redirect(['index']);
        }

        $combinazioni = $this->combinazioni($stato);

        return $this->render('riepilogo', [
            'stato' => $stato,
            'modello' => MgAttributoArticolo::findOne($stato['id_modello']),
            'tessuti' => $this->etichette($stato['tessuti'] ?? []),
            'colori' => $this->etichette($stato['colori'] ?? []),
            'taglie' => $this->etichette($stato['taglie'] ?? []),
            'combinazioni' => $combinazioni,
            'unita' => MgUnitaMisura::findOne($stato['defaults']['id_unita_misura'] ?? null),
            'ivaVendita' => MgAliquotaIva::findOne($stato['defaults']['id_iva_vendita'] ?? null),
            'ivaAcquisto' => MgAliquotaIva::findOne($stato['defaults']['id_iva_acquisto'] ?? null),
        ]);
    }

    public function actionGenera()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $stato = $this->getStato();
        if (empty($stato['id_modello'])) {
            return $this->redirect(['index']);
        }

        $combinazioni = $this->combinazioni($stato);
        if (count($combinazioni) > self::MAX_COMBINAZIONI) {
            Yii::$app->session->setFlash('error',
                'Troppe combinazioni (' . count($combinazioni) . '). Riduci la selezione (max ' . self::MAX_COMBINAZIONI . ').');
            return $this->redirect(['riepilogo']);
        }

        $defaults = $stato['defaults'] ?? [];
        $um = !empty($defaults['id_unita_misura']) ? MgUnitaMisura::findOne($defaults['id_unita_misura']) : null;

        $creati = 0;
        $errori = [];
        $usati = [];

        foreach ($combinazioni as $c) {
            $art = new MgArticolo();
            $art->attivo = true;
            $art->id_modello = $stato['id_modello'];
            $art->id_tessuto = $c['id_tessuto'];
            $art->id_colore = $c['id_colore'];
            $art->id_taglia = $c['id_taglia'];
            $art->descrizione = $c['descrizione'];
            $art->prezzo = $defaults['prezzo'] ?? 0;
            $art->id_iva_vendita = $defaults['id_iva_vendita'] ?? null;
            $art->id_iva_acquisto = $defaults['id_iva_acquisto'] ?? null;
            $art->um = $um ? $um->codice : null;
            $art->codice = $this->codiceUnico($c['codice'], $usati);

            if (!$art->save()) {
                $errori[] = $art->codice . ': ' . json_encode($art->getErrors());
                continue;
            }

            if ($um) {
                $riga = new MgArticoloUm();
                $riga->id_articolo = $art->id;
                $riga->id_unita_misura = $um->id;
                $riga->fattore = 1;
                $riga->predefinita = 1;
                $riga->attivo = true;
                $riga->save(false);
            }

            $creati++;
        }

        if ($creati > 0) {
            Yii::$app->session->setFlash('success', $creati . ' articoli creati correttamente.');
        }
        if (!empty($errori)) {
            Yii::$app->session->setFlash('error', 'Alcuni articoli non sono stati creati: ' . implode(' | ', $errori));
        }

        $this->clearStato();

        return $this->redirect(['mgarticolo/index']);
    }

    public function actionReset()
    {
        $this->clearStato();
        return $this->redirect(['index']);
    }

    /**
     * Risolve il modello scelto: id esistente oppure nuovo (creato al volo).
     */
    private function risolviModello($post)
    {
        if (!empty($post['id_modello'])) {
            return (int) $post['id_modello'];
        }

        $nuovo = trim((string) ($post['modello_nuovo'] ?? ''));
        if ($nuovo === '') {
            return null;
        }

        $model = MgAttributoArticolo::findOrCreate(
            MgAttributoArticolo::TIPO_MODELLO,
            $nuovo,
            $post['modello_codice'] ?? null
        );

        return $model ? $model->id : null;
    }

    /**
     * Legge un elenco di id da POST (checkbox multiple).
     */
    private function idsPost($campo)
    {
        $ids = Yii::$app->request->post($campo, []);
        if (!is_array($ids)) {
            return [];
        }
        return array_values(array_unique(array_map('intval', array_filter($ids))));
    }

    /**
     * Etichette (id => descrizione) per un elenco di id attributo.
     */
    private function etichette(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        return \yii\helpers\ArrayHelper::map(
            MgAttributoArticolo::find()
                ->where(['id' => $ids])
                ->orderBy(['descrizione' => SORT_ASC])
                ->all(),
            'id',
            'descrizione'
        );
    }

    /**
     * Costruisce il prodotto cartesiano modello x tessuto x colore x taglia.
     * Una dimensione vuota equivale a "nessun valore" (un solo articolo).
     */
    private function combinazioni(array $stato)
    {
        $modello = MgAttributoArticolo::findOne($stato['id_modello']);
        if (!$modello) {
            return [];
        }

        $tessuti = $this->attributi($stato['tessuti'] ?? []);
        $colori = $this->attributi($stato['colori'] ?? []);
        $taglie = $this->attributi($stato['taglie'] ?? []);

        $listaT = !empty($tessuti) ? array_values($tessuti) : [null];
        $listaC = !empty($colori) ? array_values($colori) : [null];
        $listaZ = !empty($taglie) ? array_values($taglie) : [null];

        $out = [];
        foreach ($listaT as $t) {
            foreach ($listaC as $c) {
                foreach ($listaZ as $z) {
                    $partiDescrizione = [$modello->descrizione];
                    $partiCodice = [$modello->codice ?: $modello->descrizione];
                    foreach ([$t, $c, $z] as $a) {
                        if ($a) {
                            $partiDescrizione[] = $a->descrizione;
                            $partiCodice[] = $a->codice ?: $a->descrizione;
                        }
                    }

                    $out[] = [
                        'id_tessuto' => $t ? $t->id : null,
                        'id_colore' => $c ? $c->id : null,
                        'id_taglia' => $z ? $z->id : null,
                        'descrizione' => mb_substr(implode(' ', $partiDescrizione), 0, 250),
                        'codice' => $this->codiceBase($partiCodice),
                    ];
                }
            }
        }

        return $out;
    }

    /**
     * Attributi indicizzati per id, nell'ordine passato.
     */
    private function attributi(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $models = MgAttributoArticolo::find()->where(['id' => $ids])->all();
        $byId = [];
        foreach ($models as $m) {
            $byId[(int) $m->id] = $m;
        }
        $out = [];
        foreach ($ids as $id) {
            if (isset($byId[(int) $id])) {
                $out[] = $byId[(int) $id];
            }
        }
        return $out;
    }

    /**
     * Compone il codice base (max 25 char) da modello/tessuto/colore/taglia.
     */
    private function codiceBase(array $parti)
    {
        $pezzi = [];
        foreach ($parti as $p) {
            $p = strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', (string) $p));
            if ($p !== '') {
                $pezzi[] = $p;
            }
        }
        $base = implode('-', $pezzi);
        if ($base === '') {
            $base = 'ART';
        }
        return substr($base, 0, 25);
    }

    /**
     * Rende univoco il codice rispetto al DB e alle righe già generate.
     */
    private function codiceUnico($base, array &$usati)
    {
        $base = substr($base, 0, 25);
        $candidato = $base;
        $n = 1;
        while (isset($usati[$candidato]) || MgArticolo::find()->where(['codice' => $candidato])->exists()) {
            $n++;
            $suffisso = '-' . $n;
            $candidato = substr($base, 0, 25 - strlen($suffisso)) . $suffisso;
        }
        $usati[$candidato] = true;
        return $candidato;
    }

    private function getStato()
    {
        return (array) Yii::$app->session->get(self::SESSION_KEY, []);
    }

    private function setStato(array $stato)
    {
        Yii::$app->session->set(self::SESSION_KEY, $stato);
    }

    private function clearStato()
    {
        Yii::$app->session->remove(self::SESSION_KEY);
    }
}
