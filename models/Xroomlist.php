<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_roomlist".
 *
 * @property string $id_guest
 * @property string $nominativo
 * @property string|null $cd_ar
 * @property int $th_id
 * @property string|null $note
 * @property bool|null $evaso
 * @property string|null $ruolo
 * @property string|null $party
 * @property string|null $commessa
 */
class XRoomlist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_roomlist';
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
            [['id_guest', 'note'], 'string'],
            [['nominativo', 'th_id'], 'required'],
            [['th_id'], 'integer'],
            [['evaso'], 'boolean'],
            [['nominativo'], 'string', 'max' => 250],
            [['cd_ar'], 'string', 'max' => 25],
            [['ruolo', 'commessa'], 'string', 'max' => 20],
            [['party'], 'string', 'max' => 5],
            [['id_guest'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_guest' => 'Id Guest',
            'nominativo' => 'Nominativo',
            'cd_ar' => 'Cd Ar',
            'th_id' => 'Th ID',
            'note' => 'Note',
            'evaso' => 'Evaso',
            'ruolo' => 'Ruolo',
            'party' => 'Party',
            'commessa' => 'Commessa',
        ];
    }

    /**
     * Relazione con il modello Xtravelhead
     * th_id è la chiave di collegamento
     */
    public function getTravelHead()
    {
        // Assicurati che il modello Xtravelhead esista e sia importato
        return $this->hasOne(Xtravelhead::class, ['th_id' => 'th_id']);
    }

    /**
     * Ritorna la descrizione della prenotazione e la data formattata
     * @return array
     */
    public function getInfoViaggio()
    {
        if ($this->travelHead) {
            return [
                'descrizione' => $this->travelHead->descrizione,
                'data' => $this->travelHead->datath,
            ];
        }
        return [
            'descrizione' => 'N/D',
            'data' => null,
        ];
    }
}
