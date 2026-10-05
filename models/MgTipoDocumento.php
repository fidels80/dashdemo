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
 * @property string|null $created_at
 */
class MgTipoDocumento extends \yii\db\ActiveRecord
{
    const DEST_CLIENTE = 'cliente';
    const DEST_FORNITORE = 'fornitore';

    public static function tableName()
    {
        return 'mg_tipo_documento';
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
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['destinazione'], 'string', 'max' => 20],
            [['destinazione'], 'in', 'range' => array_keys(self::opzioniDestinazione())],
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
