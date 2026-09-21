<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "presenze".
 *
 * @property int $id
 * @property int|null $personale_id
 * @property string $data_presenza
 * @property string|null $ora_ingresso
 * @property string|null $ora_uscita
 * @property float|null $ore_lavorate
 * @property string|null $tipo_assenza
 * @property int|null $ritardo_minuti
 * @property string|null $note
 *
 * @property Personale $personale
 */
class Presenze extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'presenze';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['personale_id', 'ritardo_minuti'], 'integer'],
            [['data_presenza'], 'required'],
            [['data_presenza', 'ora_ingresso', 'ora_uscita'], 'safe'],
            [['ore_lavorate','prz_ora'], 'number'],
            [['note'], 'string'],
            [['tipo_assenza'], 'string', 'max' => 50],
            [['cd_dosottocommessa'], 'string', 'max' => 20],
            [['personale_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personale::className(), 'targetAttribute' => ['personale_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'personale_id' => 'Dipendente',
            'data_presenza' => 'Data Presenza',
            'ora_ingresso' => 'Ora Ingresso',
            'ora_uscita' => 'Ora Uscita',
            'ore_lavorate' => 'Ore Lavorate',
            'tipo_assenza' => 'Tipo Assenza',
            'ritardo_minuti' => 'Ritardo Minuti',
            'cd_dosottocommessa' => 'Sottocommessa/Cantiere',
            'note' => 'Note',
            'prz_ora' => 'Prezzo Ora', // Etichetta per il nuovo campo
        ];
    }
/**
 * Relazione con la Sottocommessa sul DB5
 */
public function getSottocommessa()
{
    return $this->hasOne(Sottocommessa::className(), ['Cd_DOSottoCommessa' => 'cd_sottocommessa']);
}
    /**
     * Restituisce il nominativo completo del personale collegato.
     * Utile per la decodifica rapida nelle tabelle.
     * @return string
     */
    public function getNominativoPersonale()
    {
        return $this->personale ? $this->personale->cognome . ' ' . $this->personale->nome : 'N.D.';
    }

    /**
     * Gets query for [[Personale]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonale()
    {
        return $this->hasOne(Personale::className(), ['id' => 'personale_id']);
    }
    public function getTipologiaRel()
    {
        return $this->hasOne(TipologiaPresenza::className(), ['codice' => 'tipo_assenza']);
    }

    public function getDescrizioneTipologia()
    {
        return $this->tipologiaRel ? $this->tipologiaRel->descrizione : $this->tipo_assenza;
    }
   

    public function getDescrizionePresenza()
    {
        return $this->tipologiaRel ? $this->tipologiaRel->descrizione : $this->tipo_assenza;
    }
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}
}
