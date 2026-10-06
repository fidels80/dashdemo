<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rapportini".
 *
 * @property string $id
 * @property string $cf
 * @property string $commessa
 * @property float $qta
 * @property string $data
 * @property string $ora_in
 * @property string $ora_out
 * @property int $numero
 * @property int $userid
 * @property string|null $note
 * @property string|null $cd_art
 * @property string|null $des_art
 */
class Rapportini extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rapportini';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'note'], 'string'],
            [['cd_cli', 'commessa', 'qta', 'data', 'ora_in', 'ora_out', 'userid'], 'required'],
            [['qta'], 'number'],
            [['data', 'ora_in', 'ora_out', 'pausa_in', 'pausa_out'], 'safe'],
            [['userid', 'numero', 'evaso'], 'integer'],
            [['cd_cli', 'altcli'], 'string', 'max' => 20],
            [['commessa'], 'string', 'max' => 100],
            [['cd_art'], 'string', 'max' => 160],
            [['des_art'], 'string', 'max' => 500],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cd_cli' => 'Cliente',
            'commessa' => 'Sottocommessa',
            'qta' => 'Qta',
            'data' => 'Data',
            'ora_in' => 'Ora In',
            'ora_out' => 'Ora Out',
            'numero' => 'Numero',
            'userid' => 'Userid',
            'note' => 'Note',
            'cd_art' => 'Articolo',
            'des_art' => 'Des Articolo',
            'evaso' => 'Evaso',
        ];
    }

    /**
     * Rapportini non ancora prelevati in un documento.
     */
    public static function scopeNonEvasi()
    {
        return static::find()->where(['not', ['evaso' => 1]]);
    }

    /**
     * Impedisce l'eliminazione di un rapportino già evaso in un documento.
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        return (int) $this->evaso !== 1;
    }
}
