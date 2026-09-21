<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Docommessa".
 *
 * @property int $Id_DOCommessa
 * @property string $Cd_DOCommessa
 * @property string $Descrizione Descrizione commessa.
 * @property string|null $DescrizioneBreve
 * @property string|null $Cd_CF
 * @property string|null $Cd_DOCommessaStato
 * @property string|null $DataInizio
 * @property string|null $DataFinePresunta
 * @property string|null $DataFineReale
 * @property string|null $NoteDoCommessa
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
class Docommessa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Docommessa';
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
            [['Cd_DOCommessa', 'Descrizione'], 'required'],
            [['DataInizio', 'DataFinePresunta', 'DataFineReale', 
            ], 'default', 'value' => null],
            [['NoteDoCommessa', 'NoteXML', 'Attributi'], 'string'],
            [['Cd_DOCommessa', 'Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['Descrizione'], 'string', 'max' => 50],
            [['DescrizioneBreve'], 'string', 'max' => 20],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Cd_DOCommessaStato'], 'string', 'max' => 3],
        
            [['Cd_DOCommessa'], 'unique'],
           
            [['Cd_DOCommessaStato'],'string', 'max' => 3],
            [['Cd_CF', 'Cd_DOCommessaStato', 'DescrizioneBreve', 'DataInizio',
             'DataFinePresunta', 'DataFineReale'], 'default', 'value' => null],
        // ... le altre regole che avevi ...
        [['DataInizio', 'DataFinePresunta', 'DataFineReale'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_DOCommessa' => 'Id Do Commessa',
            'Cd_DOCommessa' => 'Cd Do Commessa',
            'Descrizione' => 'Descrizione',
            'DescrizioneBreve' => 'Descrizione Breve',
            'Cd_CF' => 'Cd Cf',
            'Cd_DOCommessaStato' => 'Cd Do Commessa Stato',
            'DataInizio' => 'Data Inizio',
            'DataFinePresunta' => 'Data Fine Presunta',
            'DataFineReale' => 'Data Fine Reale',
            'NoteDoCommessa' => 'Note Do Commessa',
 
            'NoteXML' => 'Note Xml',
            'Attributi' => 'Attributi',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
        ];
    }


 
public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        
        // 1. FIX TIMESTAMP: Rimuoviamo Ts per evitare l'errore dell'inserimento esplicito
        unset($this->Ts); 

        // 2. FIX DATE: Formattazione per smalldatetime (YYYYMMDD)
        $dateFields = ['DataInizio', 'DataFinePresunta', 'DataFineReale'];
        foreach ($dateFields as $field) {
            if (!empty($this->$field)) {
                $this->$field = date('Ymd', strtotime($this->$field));
            } else {
                $this->$field = null; // FONDAMENTALE: deve essere null, non ''
            }
        }

        // 3. FIX STRINGHE VUOTE: Se Cd_CF o Stato sono vuoti, devono essere NULL
        $this->Cd_CF = !empty($this->Cd_CF) ? $this->Cd_CF : null;
        $this->Cd_DOCommessaStato = !empty($this->Cd_DOCommessaStato) ? $this->Cd_DOCommessaStato : null;
        $this->DescrizioneBreve = !empty($this->DescrizioneBreve) ? $this->DescrizioneBreve : null;

        // 4. FIX TIMEINS/UPD: Formato ISO pulito
        $now = date('Y-m-d H:i:s');
        if ($insert) {
       //     $this->UserIns = Yii::$app->user->identity->username ?? 'system';
          //  $this->TimeIns = $now;
        }
        //$this->UserUpd = Yii::$app->user->identity->username ?? 'system';
        //$this->TimeUpd = $now;

        return true;
    }
    return false;
}
}
