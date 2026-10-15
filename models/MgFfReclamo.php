<?php

namespace app\models;

/**
 * This is the model class for table "mg_ff_reclamo".
 *
 * Reclamo inviato a un fornitore per una fattura ricevuta (FAF).
 * Per la stessa fattura possono essere memorizzati piu' reclami.
 *
 * @property int $id
 * @property int $id_documento
 * @property int|null $id_utente
 * @property string $oggetto
 * @property string|null $messaggio
 * @property string $destinatari
 * @property bool $esito
 * @property string|null $errore
 * @property string|null $inviata_at
 * @property string|null $created_at
 *
 * @property MgDocumento $documento
 * @property User|null $utente
 */
class MgFfReclamo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_ff_reclamo';
    }

    public function rules()
    {
        return [
            [['id_documento', 'oggetto', 'destinatari'], 'required'],
            [['id_documento', 'id_utente'], 'integer'],
            [['messaggio'], 'string'],
            [['esito'], 'boolean'],
            [['inviata_at', 'created_at'], 'safe'],
            [['oggetto'], 'string', 'max' => 200],
            [['destinatari'], 'string', 'max' => 400],
            [['errore'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento' => 'Documento',
            'id_utente' => 'Utente',
            'oggetto' => 'Oggetto',
            'messaggio' => 'Messaggio',
            'destinatari' => 'Destinatari',
            'esito' => 'Inviata',
            'errore' => 'Errore',
            'inviata_at' => 'Inviata il',
            'created_at' => 'Creato il',
        ];
    }

    public function getDocumento()
    {
        return $this->hasOne(MgDocumento::className(), ['id' => 'id_documento']);
    }

    public function getUtente()
    {
        return $this->hasOne(User::className(), ['id' => 'id_utente']);
    }

    /**
     * Etichetta dell'utente che ha inviato il reclamo.
     */
    public function getUtenteLabel()
    {
        if ($this->utente === null) {
            return '';
        }
        return $this->utente->username ?? ($this->utente->email ?? '');
    }
}
