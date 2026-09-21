<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "DOCommessaStato".
 *
 * @property int $Id_DOCommessaStato
 * @property string $Cd_DOCommessaStato
 * @property string $Descrizione
 * @property string|null $NoteDOCommessaStato
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string $Ts
 *
 * @property DOCommessa[] $dOCommessas
 * @property DOSottoCommessa[] $dOSottoCommessas
 */
class DOCommessaStato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DOCommessaStato';
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
            [['Cd_DOCommessaStato', 'Descrizione', 'Ts'], 'required'],
            [['NoteDOCommessaStato'], 'string'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_DOCommessaStato'], 'string', 'max' => 3],
            [['Descrizione'], 'string', 'max' => 80],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_DOCommessaStato'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_DOCommessaStato' => 'Id Do Commessa Stato',
            'Cd_DOCommessaStato' => 'Cd Do Commessa Stato',
            'Descrizione' => 'Descrizione',
            'NoteDOCommessaStato' => 'Note Do Commessa Stato',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
        ];
    }

    /**
     * Gets query for [[DOCommessas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDOCommessas()
    {
        return $this->hasMany(DOCommessa::className(), ['Cd_DOCommessaStato' => 'Cd_DOCommessaStato']);
    }

    /**
     * Gets query for [[DOSottoCommessas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDOSottoCommessas()
    {
        return $this->hasMany(DOSottoCommessa::className(), ['Cd_DOCommessaStato' => 'Cd_DOCommessaStato']);
    }
}
