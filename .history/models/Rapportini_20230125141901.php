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
            [['cd_cli','altcli', 'commessa', 'qta', 'data', 'ora_in', 'ora_out', 'userid'], 'required'],
            [['qta'], 'number'],
            [['data', 'ora_in', 'ora_out'], 'safe'],
            [['userid'], 'integer'],
            [['cf'], 'string', 'max' => 7],
            [['commessa'], 'string', 'max' => 50],
            [['cd_art'], 'string', 'max' => 80],
            [['des_art'], 'string', 'max' => 250],
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
            'cd_cli' => Yii::t('app', 'cd_cli'),
            'commessa' => Yii::t('app', 'Commessa'),
            'qta' => Yii::t('app', 'Qta'),
            'data' => Yii::t('app', 'Data'),
            'ora_in' => Yii::t('app', 'Ora In'),
            'ora_out' => Yii::t('app', 'Ora Out'),
            'numero' => Yii::t('app', 'Numero'),
            'userid' => Yii::t('app', 'Userid'),
            'note' => Yii::t('app', 'Note'),
            'cd_art' => Yii::t('app', 'Cd Art'),
            'des_art' => Yii::t('app', 'Des Art'),
        ];
    }
}
