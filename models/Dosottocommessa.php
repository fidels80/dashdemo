<?php

namespace app\models;

use Yii;
 
/**
 * This is the model class for table "dosottocommessa".
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
class Dosottocommessa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dosottocommessa';
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
            // --- AGGIUNGI QUESTA RIGA ---
            // Forza automaticamente il codice a diventare MAIUSCOLO prima del salvataggio
            [['Cd_DOSottoCommessa'], 'filter', 'filter' => 'strtoupper'],    
        [['Cd_DOCommessa', 'Cd_DOSottoCommessa'], 'required'],

            // --- QUESTA RIGA È FONDAMENTALE ---
            // Converte le stringhe vuote in NULL prima di salvare sul DB
            [['DataInizio', 'DataFinePresunta', 'DataFineReale', 'Cd_CF', 'Cd_DOCommessaStato'], 'default', 'value' => null],

            [['DataInizio', 'DataFinePresunta', 'DataFineReale', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['NoteDoSottoCommessa', 'NoteXML', 'Attributi'], 'string'],
            [['Cd_DOCommessa', 'Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['Cd_DOSottoCommessa', 'DescrizioneBreve'], 'string', 'max' => 20],
            [['Descrizione'], 'string', 'max' => 50],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Cd_DOCommessaStato'], 'string', 'max' => 3],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_DOSottoCommessa'], 'unique'],
            [['DataInizio', 'DataFinePresunta', 'DataFineReale'], 'safe'],
            [['DataInizio', 'DataFinePresunta', 'DataFineReale'], 'default', 'value' => null],
            // Relazioni
            [['Cd_DOCommessa'], 'exist', 'skipOnError' => true, 'targetClass' => DOCommessa::className(), 'targetAttribute' => ['Cd_DOCommessa' => 'Cd_DOCommessa']],
            [['Cd_CF'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF' => 'Cd_CF']],
            [['Cd_DOCommessaStato'], 'exist', 'skipOnError' => true, 'targetClass' => DOCommessaStato::className(), 'targetAttribute' => ['Cd_DOCommessaStato' => 'Cd_DOCommessaStato']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_DOSottoCommessa' => 'Id Do Sotto Commessa',
            'Cd_DOCommessa' => 'Cd Do Commessa',
            'Cd_DOSottoCommessa' => 'Cd Do Sotto Commessa',
            'Descrizione' => 'Descrizione',
            'DescrizioneBreve' => 'Descrizione Breve',
            'Cd_CF' => 'Cd Cf',
            'Cd_DOCommessaStato' => 'Cd Do Commessa Stato',
            'DataInizio' => 'Data Inizio',
            'DataFinePresunta' => 'Data Fine Presunta',
            'DataFineReale' => 'Data Fine Reale',
            'NoteDoSottoCommessa' => 'Note Do Sotto Commessa',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'NoteXML' => 'Note Xml',
            'Attributi' => 'Attributi',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
        ];
    }

    /**
     * Relazione con il model CF (Anagrafica)
     * Collega Cd_CF di questa tabella con Cd_CF della tabella cf
     */
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
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $dateFields = ['DataInizio', 'DataFinePresunta', 'DataFineReale'];

            foreach ($dateFields as $field) {
                if (!empty($this->$field)) {
                    // Creiamo un oggetto data per essere sicuri della validità
                    $d = \DateTime::createFromFormat('Y-m-d', $this->$field);
                    if ($d) {
                        // Formato YYYYMMDD senza trattini: è il più digeribile da SQL Server per smalldatetime
                        $this->$field = $d->format('Ymd');
                    } else {
                        // Se il formato in arrivo non è Y-m-d (magari d/m/Y), proviamo strtotime
                        $this->$field = date('Ymd', strtotime($this->$field));
                    }
                } else {
                    // Forza NULL se il campo è vuoto per evitare l'errore nvarchar -> smalldatetime
                    $this->$field = null;
                }
            }

            // Pulizia stringhe vuote per evitare altri errori di conversione
            $this->Cd_CF = $this->Cd_CF ?: null;
            $this->Cd_DOCommessaStato = $this->Cd_DOCommessaStato ?: null;

            return true;
        }
        return false;
    }
}
