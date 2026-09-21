<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "PG".
 *
 * @property int $Id_PG
 * @property string $Cd_PG Codice del pagamento.
 * @property string|null $Cd_CGConto_Banca
 * @property string $Descrizione Descrizione.
 * @property string|null $Note_PG
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di provvi
 * @property int $Partenza Paramentro per calcolare la da
 * @property int $PrimaRata
 * @property int $NumeroRate Numero di scadenze generate fi
 * @property int $Blocco Genera effetti già bloccati.
 * @property float $AccontoFissoE Valore dell'acconto per la cre
 * @property float $AccontoPerc
 * @property int $ApplicaNC
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 */
class Pg extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PG';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_PG'], 'required'],
            [['Note_PG'], 'string'],
            [['Partenza', 'PrimaRata', 'NumeroRate', 'Blocco', 'ApplicaNC'], 'integer'],
            [['AccontoFissoE', 'AccontoPerc'], 'number'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_PG'], 'string', 'max' => 4],
            [['Cd_CGConto_Banca'], 'string', 'max' => 12],
            [['Descrizione'], 'string', 'max' => 60],
            [['Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_PG'], 'unique'],
            [['Cd_CGConto_Banca'], 'exist', 'skipOnError' => true, 'targetClass' => Banca::className(), 'targetAttribute' => ['Cd_CGConto_Banca' => 'Cd_CGConto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_PG' => 'Id Pg',
            'Cd_PG' => 'Cd Pg',
            'Cd_CGConto_Banca' => 'Cd Cg Conto Banca',
            'Descrizione' => 'Descrizione',
            'Note_PG' => 'Note Pg',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
            'Partenza' => 'Partenza',
            'PrimaRata' => 'Prima Rata',
            'NumeroRate' => 'Numero Rate',
            'Blocco' => 'Blocco',
            'AccontoFissoE' => 'Acconto Fisso E',
            'AccontoPerc' => 'Acconto Perc',
            'ApplicaNC' => 'Applica Nc',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
        ];
    }
}
