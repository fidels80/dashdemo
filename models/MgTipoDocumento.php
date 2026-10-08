<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_tipo_documento".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property int $anno
 * @property int $contatore
 * @property bool $usa_progressivo
 * @property bool $congruita
 * @property bool $attivo
 * @property string $destinazione
 * @property bool $crea_scadenze
 * @property bool $mostra_varianti
 * @property bool $preleva_rapportini
 * @property bool $crea_articoli
 * @property bool $crea_anagrafiche
 * @property bool $mostra_matrice
 * @property int|null $id_magazzino_partenza
 * @property int|null $id_magazzino_arrivo
 * @property string $segno_movimento
 * @property string $varia_impegnato
 * @property string $varia_ordinato
 * @property string|null $created_at
 *
 * @property MgMagazzino|null $magazzinoPartenza
 * @property MgMagazzino|null $magazzinoArrivo
 */
class MgTipoDocumento extends \yii\db\ActiveRecord
{
    const DEST_CLIENTE = 'cliente';
    const DEST_FORNITORE = 'fornitore';

    const MOV_CARICO = 'carico';
    const MOV_SCARICO = 'scarico';
    const MOV_NESSUNO = 'nessuno';

    const VARIA_AUMENTA = 'aumenta';
    const VARIA_DIMINUISCI = 'diminuisci';

    public static function tableName()
    {
        return 'mg_tipo_documento';
    }

    public function init()
    {
        parent::init();
        if ($this->segno_movimento === null) {
            $this->segno_movimento = self::MOV_NESSUNO;
        }
        if ($this->varia_impegnato === null) {
            $this->varia_impegnato = self::MOV_NESSUNO;
        }
        if ($this->varia_ordinato === null) {
            $this->varia_ordinato = self::MOV_NESSUNO;
        }
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['anno', 'contatore'], 'integer'],
            [['usa_progressivo', 'congruita', 'attivo'], 'boolean'],
            [['crea_scadenze'], 'boolean'],
            [['mostra_varianti'], 'boolean'],
            [['preleva_rapportini', 'crea_articoli', 'crea_anagrafiche', 'mostra_matrice'], 'boolean'],
            [['id_magazzino_partenza', 'id_magazzino_arrivo'], 'integer'],
            [['id_magazzino_partenza'], 'exist',
                'targetClass' => MgMagazzino::className(),
                'targetAttribute' => ['id_magazzino_partenza' => 'id'],
                'skipOnEmpty' => true],
            [['id_magazzino_arrivo'], 'exist',
                'targetClass' => MgMagazzino::className(),
                'targetAttribute' => ['id_magazzino_arrivo' => 'id'],
                'skipOnEmpty' => true],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['destinazione'], 'string', 'max' => 20],
            [['destinazione'], 'in', 'range' => array_keys(self::opzioniDestinazione())],
            [['segno_movimento', 'varia_impegnato', 'varia_ordinato'], 'string', 'max' => 10],
            [['segno_movimento'], 'in', 'range' => array_keys(self::opzioniSegnoMovimento())],
            [['varia_impegnato', 'varia_ordinato'], 'in', 'range' => array_keys(self::opzioniVariazione())],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'anno' => 'Anno',
            'contatore' => 'Contatore',
            'usa_progressivo' => 'Numerazione automatica',
            'congruita' => 'Proposta congruità numeri',
            'attivo' => 'Attivo',
            'destinazione' => 'Destinazione',
            'crea_scadenze' => 'Crea scadenze',
            'mostra_varianti' => 'Mostra taglia/colore',
            'preleva_rapportini' => 'Preleva rapportini',
            'crea_articoli' => 'Crea articoli',
            'crea_anagrafiche' => 'Crea anagrafiche',
            'mostra_matrice' => 'Matrice taglie',
            'id_magazzino_partenza' => 'Magazzino partenza',
            'id_magazzino_arrivo' => 'Magazzino arrivo',
            'segno_movimento' => 'Segno movimento',
            'varia_impegnato' => 'Varia impegnato',
            'varia_ordinato' => 'Varia ordinato',
            'created_at' => 'Creato il',
        ];
    }

    public static function opzioniDestinazione()
    {
        return [
            self::DEST_CLIENTE => 'Cliente',
            self::DEST_FORNITORE => 'Fornitore',
        ];
    }

    public static function opzioniSegnoMovimento()
    {
        return [
            self::MOV_CARICO => 'Carico (+)',
            self::MOV_SCARICO => 'Scarico (-)',
            self::MOV_NESSUNO => 'Nessun movimento',
        ];
    }

    public static function opzioniVariazione()
    {
        return [
            self::VARIA_AUMENTA => 'Aumenta (+)',
            self::VARIA_DIMINUISCI => 'Diminuisci (-)',
            self::MOV_NESSUNO => 'Nessuna variazione',
        ];
    }

    public function getSegnoMovimentoLabel()
    {
        $opzioni = self::opzioniSegnoMovimento();
        return $opzioni[$this->segno_movimento] ?? $this->segno_movimento;
    }

    /**
     * Coefficiente numerico del segno movimento: carico +1, scarico -1,
     * nessuno 0.
     */
    public static function coefficienteSegno($valore)
    {
        if ($valore === self::MOV_CARICO) {
            return 1;
        }
        if ($valore === self::MOV_SCARICO) {
            return -1;
        }
        return 0;
    }

    /**
     * Coefficiente numerico di una variazione: aumenta +1, diminuisci -1,
     * nessuno 0.
     */
    public static function coefficienteVariazione($valore)
    {
        if ($valore === self::VARIA_AUMENTA) {
            return 1;
        }
        if ($valore === self::VARIA_DIMINUISCI) {
            return -1;
        }
        return 0;
    }

    public function getVariaImpegnatoLabel()
    {
        $opzioni = self::opzioniVariazione();
        return $opzioni[$this->varia_impegnato] ?? $this->varia_impegnato;
    }

    public function getVariaOrdinatoLabel()
    {
        $opzioni = self::opzioniVariazione();
        return $opzioni[$this->varia_ordinato] ?? $this->varia_ordinato;
    }

    public function getMagazzinoPartenza()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_partenza']);
    }

    public function getMagazzinoArrivo()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_arrivo']);
    }

    public function getMagazzinoPartenzaLabel()
    {
        return $this->magazzinoPartenza ? $this->magazzinoPartenza->etichetta : null;
    }

    public function getMagazzinoArrivoLabel()
    {
        return $this->magazzinoArrivo ? $this->magazzinoArrivo->etichetta : null;
    }

    public function getDestinazioneLabel()
    {
        $opzioni = self::opzioniDestinazione();
        return $opzioni[$this->destinazione] ?? $this->destinazione;
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function getDocumenti()
    {
        return $this->hasMany(MgDocumento::className(), ['id_tipo' => 'id']);
    }

    /**
     * Mappa id_tipo => mostra_varianti (0/1) per la form documento.
     */
    public static function mapMostraVarianti()
    {
        return self::mapFlag('mostra_varianti');
    }

    /**
     * Mappa id_tipo => valore (0/1) di un flag booleano del tipo documento.
     */
    public static function mapFlag($campo)
    {
        $rows = self::find()->select(['id', $campo])->all();
        $map = [];
        foreach ($rows as $t) {
            $map[(int) $t->id] = (int) $t->$campo;
        }
        return $map;
    }

    /**
     * Mappa id_tipo => ['partenza' => id|null, 'arrivo' => id|null] per la
     * form documento: i magazzini proposti di default sulle righe.
     */
    public static function mapMagazzini()
    {
        $rows = self::find()->select(['id', 'id_magazzino_partenza', 'id_magazzino_arrivo'])->all();
        $map = [];
        foreach ($rows as $t) {
            $map[(int) $t->id] = [
                'partenza' => $t->id_magazzino_partenza ? (int) $t->id_magazzino_partenza : null,
                'arrivo' => $t->id_magazzino_arrivo ? (int) $t->id_magazzino_arrivo : null,
            ];
        }
        return $map;
    }

    /**
     * Aggiorna il contatore (ultimo numero usato) e l'anno di riferimento.
     */
    public function aggiornaContatore($numero, $anno)
    {
        if ($numero > (int) $this->contatore) {
            $this->contatore = (int) $numero;
        }
        if ($anno && (int) $this->anno !== (int) $anno) {
            $this->anno = (int) $anno;
        }
        return $this->save(false);
    }
}
