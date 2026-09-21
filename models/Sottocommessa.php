<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "DOSottoCommessa".
 *
 * @property int $Id_DOSottoCommessa
 * @property string $Cd_DOCommessa
 * @property string $Cd_DOSottoCommessa
 * @property string $Descrizione Descrizione commessa.
 * @property string|null $DescrizioneBreve
 * @property string|null $Cd_CF
 * @property string|null $Cd_DOCommessaStato
 * @property string|null $DataInizio
 * @property string|null $DataFinePresunta
 * @property string|null $DataFineReale
 * @property string|null $NoteDoSottoCommessa
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $NoteXML
 * @property string|null $Attributi
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di provvi
 */
class Sottocommessa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DOSottoCommessa';
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
            [['Cd_DOCommessa', 'Cd_DOSottoCommessa'], 'required'],
            
            // Forza il maiuscolo sul codice
            [['Cd_DOSottoCommessa'], 'filter', 'filter' => 'strtoupper'],
            
            [['DataInizio', 'DataFinePresunta', 'DataFineReale'], 'safe'],
            [['NoteDoSottoCommessa', 'NoteXML', 'Attributi'], 'string'],
            [['Cd_DOCommessa', 'Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['Cd_DOSottoCommessa', 'DescrizioneBreve'], 'string', 'max' => 20],
            [['Descrizione'], 'string', 'max' => 50],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Cd_DOCommessaStato'], 'string', 'max' => 3],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_DOSottoCommessa'], 'unique'],

            // --- FIX VALIDAZIONE ESISTENZA ---
            // Specifichiamo la classe corretta per il controllo esistenza
            [['Cd_DOCommessa'], 'exist', 'skipOnError' => true, 'targetClass' => DOCommessa::className(), 'targetAttribute' => ['Cd_DOCommessa' => 'Cd_DOCommessa']],
            [['Cd_CF'], 'exist', 'skipOnError' => true, 
            'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF' => 'Cd_CF']],
            [['Cd_DOCommessaStato'], 'exist', 'skipOnError' => true, 'targetClass' => DOCommessaStato::className(), 'targetAttribute' => ['Cd_DOCommessaStato' => 'Cd_DOCommessaStato']],
            
            // Default a NULL per evitare stringhe vuote che rompono smalldatetime
            [['Cd_CF', 'Cd_DOCommessaStato', 
            'DataInizio', 'DataFinePresunta', 'DataFineReale'], 
            'default', 'value' => null],
        ];
    }

    /**
     * Gestione salvataggio dati per SQL Server
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Rimuoviamo Ts per evitare errori su colonne timestamp/rowversion
            unset($this->Ts);

            // Formattazione date Ymd per smalldatetime
            $dateFields = ['DataInizio', 'DataFinePresunta', 'DataFineReale'];
            foreach ($dateFields as $field) {
                if (!empty($this->$field)) {
                    $this->$field = date('Ymd', strtotime($this->$field));
                } else {
                    $this->$field = null;
                }
            }
 
            // Popolamento campi utente e tempo
            $now = date('Y-m-d H:i:s');
            $user = Yii::$app->user->identity->username ?? 'system';

            if ($insert) {
             //   $this->UserIns = $user;
             //   $this->TimeIns = $now;
            }
            //$this->UserUpd = $user;
            //$this->TimeUpd = $now;

            return true;
        }
        return false;
    }


     public function getAnagrafica()
    {
        // Usiamo il model CF. Assicurati che il namespace sia corretto.
        // Specifichiamo la connessione db5 se anche CF si trova lì.
        return $this->hasOne(CF::class, ['Cd_CF' => 'Cd_CF']);
    }

    /**
     * Funzione per ottenere la descrizione del cliente (decodifica)
     */
    public function getDescrizioneCliente()
    {
        // Se la relazione esiste, restituisce la descrizione, altrimenti il codice originale
        return $this->anagrafica ? $this->anagrafica->Descrizione : $this->Cd_CF;
    }
    /**
     * Relazione con il model DOCommessaStato
     * Collega Cd_DOCommessaStato di questa tabella con Cd_DOCommessaStato della tabella docommessastato
     */
    public function getStatoCommessa()
    {
        // Assicurati che esista il model DOCommessaStato e che punti al db5 se necessario
        return $this->hasOne(DOCommessaStato::class, ['Cd_DOCommessaStato' => 'Cd_DOCommessaStato']);
    }

    /**
     * Funzione per ottenere la descrizione dello stato (decodifica)
     */
    public function getDescrizioneStato()
    {
        // Se la relazione esiste e ha un campo 'Descrizione' (o quello che usi nel db), lo restituisce
        // Altrimenti restituisce il codice originale (es. 'APE', 'CHI')
        return $this->statoCommessa ? $this->statoCommessa->Descrizione : $this->Cd_DOCommessaStato;
    }
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}
}
