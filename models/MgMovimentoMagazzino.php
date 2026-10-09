<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_movimentimagazzino".
 *
 * Movimento di magazzino generato da una riga documento: quantità e
 * magazzini della riga, configurazione di movimento del tipo documento e
 * quantità calcolate (qta * segno/variazioni).
 *
 * @property int $id
 * @property int $id_documento_riga
 * @property int|null $id_lotto
 * @property string|null $codice_articolo
 * @property float|null $qta
 * @property int|null $id_unita_misura
 * @property string|null $um
 * @property float $fattore
 * @property int|null $id_magazzino_partenza
 * @property int|null $id_magazzino_arrivo
 * @property string $segno_movimento
 * @property string $varia_impegnato
 * @property string $varia_ordinato
 * @property float|null $qta_movimento
 * @property float|null $qta_impegnato
 * @property float|null $qta_ordinato
 *
 * @property MgDocumentoRiga $riga
 * @property MgLotto|null $lotto
 * @property MgUnitaMisura|null $unitaMisura
 * @property MgMagazzino|null $magazzinoPartenza
 * @property MgMagazzino|null $magazzinoArrivo
 */
class MgMovimentoMagazzino extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_movimentimagazzino';
    }

    public function rules()
    {
        return [
            [['id_documento_riga'], 'required'],
            [['id_documento_riga', 'id_unita_misura', 'id_magazzino_partenza', 'id_magazzino_arrivo', 'id_lotto'], 'integer'],
            [['qta', 'fattore', 'qta_movimento', 'qta_impegnato', 'qta_ordinato'], 'number'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['um'], 'string', 'max' => 10],
            [['segno_movimento', 'varia_impegnato', 'varia_ordinato'], 'string', 'max' => 10],
            [['segno_movimento'], 'in', 'range' => array_keys(MgTipoDocumento::opzioniSegnoMovimento())],
            [['varia_impegnato', 'varia_ordinato'], 'in', 'range' => array_keys(MgTipoDocumento::opzioniVariazione())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento_riga' => 'Riga documento',
            'id_lotto' => 'Lotto',
            'codice_articolo' => 'Codice articolo',
            'qta' => 'Q.tà',
            'id_unita_misura' => 'Unità di misura',
            'um' => 'U.M.',
            'fattore' => 'Fattore conversione',
            'id_magazzino_partenza' => 'Magazzino partenza',
            'id_magazzino_arrivo' => 'Magazzino arrivo',
            'segno_movimento' => 'Segno movimento',
            'varia_impegnato' => 'Varia impegnato',
            'varia_ordinato' => 'Varia ordinato',
            'qta_movimento' => 'Q.tà movimento',
            'qta_impegnato' => 'Q.tà impegnata',
            'qta_ordinato' => 'Q.tà ordinata',
        ];
    }

    /**
     * Crea i movimenti di magazzino di una riga documento applicando la
     * configurazione di movimento del tipo documento.
     *
     * Se la riga ha righe di dettaglio con lotto, viene creato un movimento
     * per ogni lotto (Q.tà pari alla somma delle quantità del lotto); gli
     * eventuali pezzi senza lotto confluiscono in un movimento con id_lotto
     * nullo. Se la riga non ha lotti, resta un unico movimento con la
     * quantità della riga (comportamento storico).
     *
     * @param MgDocumentoRiga $riga
     * @param MgTipoDocumento|null $tipo
     * @return static[] movimenti creati
     */
    public static function creaDaRiga($riga, $tipo = null)
    {
        $dati = self::datiArticolo($riga);
        $segno = $tipo ? $tipo->segno_movimento : MgTipoDocumento::MOV_NESSUNO;
        $variaImpegnato = $tipo ? $tipo->varia_impegnato : MgTipoDocumento::MOV_NESSUNO;
        $variaOrdinato = $tipo ? $tipo->varia_ordinato : MgTipoDocumento::MOV_NESSUNO;

        $gruppi = [];
        $qtaSenzaLotto = 0.0;
        foreach ($riga->dettagli as $dettaglio) {
            if ($dettaglio->id_lotto === null) {
                $qtaSenzaLotto += (float) $dettaglio->qta;
                continue;
            }
            $id = (int) $dettaglio->id_lotto;
            $gruppi[$id] = ($gruppi[$id] ?? 0) + (float) $dettaglio->qta;
        }

        $movimenti = [];

        if (empty($gruppi)) {
            $movimenti[] = self::salvaMovimento($riga, $dati, (float) $riga->qta, null, $segno, $variaImpegnato, $variaOrdinato);
            return $movimenti;
        }

        foreach ($gruppi as $idLotto => $qta) {
            $movimenti[] = self::salvaMovimento($riga, $dati, $qta, (int) $idLotto, $segno, $variaImpegnato, $variaOrdinato);
        }

        if ($qtaSenzaLotto > 0) {
            $movimenti[] = self::salvaMovimento($riga, $dati, $qtaSenzaLotto, null, $segno, $variaImpegnato, $variaOrdinato);
        }

        return $movimenti;
    }

    /**
     * Crea e salva un singolo movimento di magazzino.
     *
     * @return static
     */
    private static function salvaMovimento($riga, $dati, $qta, $idLotto, $segno, $variaImpegnato, $variaOrdinato)
    {
        $movimento = new static();
        $movimento->id_documento_riga = $riga->id;
        $movimento->id_lotto = $idLotto;
        $movimento->codice_articolo = $dati['codice_articolo'];
        $movimento->qta = $qta;
        $movimento->id_unita_misura = $dati['id_unita_misura'];
        $movimento->um = $dati['um'];
        $movimento->fattore = $dati['fattore'];
        $movimento->id_magazzino_partenza = $riga->id_magazzino_partenza;
        $movimento->id_magazzino_arrivo = $riga->id_magazzino_arrivo;
        $movimento->segno_movimento = $segno;
        $movimento->varia_impegnato = $variaImpegnato;
        $movimento->varia_ordinato = $variaOrdinato;
        $movimento->qta_movimento = $qta * MgTipoDocumento::coefficienteSegno($segno);
        $movimento->qta_impegnato = $qta * MgTipoDocumento::coefficienteVariazione($variaImpegnato);
        $movimento->qta_ordinato = $qta * MgTipoDocumento::coefficienteVariazione($variaOrdinato);
        $movimento->save(false);

        return $movimento;
    }

    /**
     * Codice articolo, unità di misura e fattore di conversione della riga.
     * Quando la riga non riporta il codice o l'unità di misura si usano i
     * default dell'articolo (mg_articolo.codice e unità predefinita in
     * mg_articolo_um); il fattore è 1 se non determinabile.
     *
     * @param MgDocumentoRiga $riga
     * @return array{codice_articolo: string|null, id_unita_misura: int|null, um: string|null, fattore: float}
     */
    public static function datiArticolo($riga)
    {
        $articolo = null;
        if ($riga->id_articolo) {
            $articolo = MgArticolo::findOne($riga->id_articolo);
        }
        if (!$articolo && $riga->codice_articolo) {
            $articolo = MgArticolo::findOne(['codice' => $riga->codice_articolo]);
        }

        $codice = $riga->codice_articolo ?: null;
        $idUm = $riga->id_unita_misura ?: null;
        $um = $riga->um ?: null;
        $fattore = 1;

        if ($articolo) {
            if ($codice === null) {
                $codice = $articolo->codice;
            }

            $umArticolo = null;
            if ($idUm) {
                $umArticolo = MgArticoloUm::find()
                    ->where(['id_articolo' => $articolo->id, 'id_unita_misura' => $idUm])
                    ->one();
            }
            if (!$umArticolo) {
                $umArticolo = $articolo->unitaPredefinita;
                if (!$idUm && $umArticolo) {
                    $idUm = $umArticolo->id_unita_misura;
                }
            }
            if ($umArticolo) {
                $fattore = (float) $umArticolo->fattore;
            }
        }

        if ($um === null && $idUm) {
            $unita = MgUnitaMisura::findOne($idUm);
            if ($unita) {
                $um = $unita->codice;
            }
        }
        if ($um === null && $articolo && $articolo->um) {
            $um = $articolo->um;
        }
        if ($idUm === null && $um !== null) {
            $unita = MgUnitaMisura::findOne(['codice' => $um]);
            if ($unita) {
                $idUm = (int) $unita->id;
            }
        }

        return [
            'codice_articolo' => $codice,
            'id_unita_misura' => $idUm,
            'um' => $um,
            'fattore' => $fattore,
        ];
    }

    public function getRiga()
    {
        return $this->hasOne(MgDocumentoRiga::className(), ['id' => 'id_documento_riga']);
    }

    public function getLotto()
    {
        return $this->hasOne(MgLotto::className(), ['id' => 'id_lotto']);
    }

    public function getUnitaMisura()
    {
        return $this->hasOne(MgUnitaMisura::className(), ['id' => 'id_unita_misura']);
    }

    public function getMagazzinoPartenza()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_partenza']);
    }

    public function getMagazzinoArrivo()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_arrivo']);
    }
}
