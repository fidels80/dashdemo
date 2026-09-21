<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_struttura".
 *
 * @property int $id
 * @property string $Struttura
 * @property string|null $Descrizione
 * @property string $Citta
 * @property string|null $Cd_cf
 * @property string|null $Partitaiva
 * @property string|null $xcheck
 */
class Xstruttura extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    //public $linked_th_id; // Aggiungi questa riga all'inizio della classe
    public static function tableName()
    {
        return 'x_struttura';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Struttura', 'Citta'], 'required'],
            [['Struttura'], 'string', 'max' => 80],
            [['Descrizione', 'Citta'], 'string', 'max' => 200],
            [['Cd_cf'], 'string', 'max' => 7],
            [['Partitaiva'], 'string', 'max' => 13],
            // [['xcheck'], 'string', 'max' => 287],

            [
                ['Struttura', 'Descrizione', 'Citta', 'Cd_cf', 'Partitaiva'],
                'unique',
                'targetAttribute' => ['Struttura', 'Descrizione', 'Citta', 'Cd_cf', 'Partitaiva'],
                'message' => 'Esiste già una struttura con questi dati (combinazione duplicata).'
            ],
        
           ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Struttura' => 'Struttura',
            'Descrizione' => 'Descrizione',
            'Citta' => 'Citta',
            'Cd_cf' => 'Cd Cf',
            'Partitaiva' => 'Partitaiva',
            'xcheck' => 'Xcheck',
        ];
    }
    /**
     * Relazione con il modello Cf (Anagrafica)
     * Collega il Cd_cf di questa tabella al Cd_cf della tabella Cf
     */
    public function getAnagrafica()
    {
        // Sostituisci 'Cf' con il nome corretto della classe del tuo modello anagrafica
        // e 'Cd_cf' con il nome della colonna chiave nella tabella cf
        return $this->hasOne(Cf::class, ['Cd_cf' => 'Cd_cf']);
    }

    /**
     * Restituisce la descrizione (ragione sociale) del Cd_cf
     * @return string
     */
    public function getDescrizioneCf()
    {
        // Se la relazione esiste, restituisce il campo 'Descrizione' (o come si chiama nel modello Cf)
        // Altrimenti restituisce un messaggio di default o il codice stesso
        if ($this->anagrafica) {
            return $this->anagrafica->Descrizione; // Assicurati che il campo si chiami 'Descrizione' in Cf
        }

        return $this->Cd_cf ? "Codice " . $this->Cd_cf : 'Nessuna anagrafica';
    }
    // All'interno della classe Xstruttura

    public function fields()
    {
        $fields = parent::fields();

        // Aggiungiamo un campo personalizzato che chiameremo 'nome_fornitore'
        $fields['nome_fornitore'] = function ($model) {
            return $model->getDescrizioneCf();
        };

        return $fields;
    }
}
