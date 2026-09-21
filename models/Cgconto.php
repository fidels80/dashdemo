<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CGConto".
 *
 * @property int $Id_CGConto
 * @property string $Cd_CGConto
 * @property string $Descrizione Descrizione del conto.
 * @property string $Cd_CGMastro4
 * @property string|null $Old_Cd_CGBil1
 * @property string|null $Old_Cd_CGBil2
 * @property string|null $SezioneBilancio Sezione bilancio P = Patrimoni
 * @property int $TipoConto Per i conti Patrimoniali:     
 * @property int $MastroClienti Impostare a True se il conto è
 * @property int $MastroFornitori Impostare a True se il conto è
 * @property int $ContoBanca
 * @property int $ContoMerceSpesa
 * @property int $ContoIVA Impostare a True se trattasi d
 * @property int $RATipo
 * @property string|null $Cd_CAVda
 * @property string|null $Alias
 * @property int $RR_Enabled
 * @property string|null $Cd_RRConfig
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $xCD_DOSottocommessa Sottocommessa
 * @property string|null $xCd_CGConto_TransitorioFatture Eventuale Conto Transitorio da
 * @property int|null $CD_Altre_Somme_Non_Soggette
 */
class Cgconto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CGConto';
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
            [['Cd_CGConto', 'Cd_CGMastro4'], 'required'],
            [['TipoConto', 'MastroClienti', 'MastroFornitori', 'ContoBanca', 'ContoMerceSpesa', 'ContoIVA', 'RATipo', 'RR_Enabled', 'CD_Altre_Somme_Non_Soggette'], 'integer'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_CGConto', 'Cd_CGMastro4', 'Cd_CAVda', 'Alias', 'xCd_CGConto_TransitorioFatture'], 'string', 'max' => 12],
            [['Descrizione'], 'string', 'max' => 60],
            [['Old_Cd_CGBil1', 'Old_Cd_CGBil2', 'xCD_DOSottocommessa'], 'string', 'max' => 20],
            [['SezioneBilancio'], 'string', 'max' => 1],
            [['Cd_RRConfig'], 'string', 'max' => 10],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_CGConto'], 'unique'],
            [['xCd_CGConto_TransitorioFatture'], 'exist', 'skipOnError' => true, 'targetClass' => Cgconto::className(), 'targetAttribute' => ['xCd_CGConto_TransitorioFatture' => 'Cd_CGConto']],
            [['Cd_CGMastro4'], 'exist', 'skipOnError' => true, 'targetClass' => CGMastro4::className(), 'targetAttribute' => ['Cd_CGMastro4' => 'Cd_CGMastro4']],
            [['Cd_CAVda'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda' => 'Cd_CAVda']],
            [['Cd_RRConfig'], 'exist', 'skipOnError' => true, 'targetClass' => RRConfig::className(), 'targetAttribute' => ['Cd_RRConfig' => 'Cd_RRConfig']],
            [['xCD_DOSottocommessa'], 'exist', 'skipOnError' => true, 'targetClass' => DOSottoCommessa::className(), 'targetAttribute' => ['xCD_DOSottocommessa' => 'Cd_DOSottoCommessa']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_CGConto' => 'Id Cg Conto',
            'Cd_CGConto' => 'Cd Cg Conto',
            'Descrizione' => 'Descrizione',
            'Cd_CGMastro4' => 'Cd Cg Mastro4',
            'Old_Cd_CGBil1' => 'Old Cd Cg Bil1',
            'Old_Cd_CGBil2' => 'Old Cd Cg Bil2',
            'SezioneBilancio' => 'Sezione Bilancio',
            'TipoConto' => 'Tipo Conto',
            'MastroClienti' => 'Mastro Clienti',
            'MastroFornitori' => 'Mastro Fornitori',
            'ContoBanca' => 'Conto Banca',
            'ContoMerceSpesa' => 'Conto Merce Spesa',
            'ContoIVA' => 'Conto Iva',
            'RATipo' => 'Ra Tipo',
            'Cd_CAVda' => 'Cd Ca Vda',
            'Alias' => 'Alias',
            'RR_Enabled' => 'Rr Enabled',
            'Cd_RRConfig' => 'Cd Rr Config',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'xCD_DOSottocommessa' => 'X Cd Do Sottocommessa',
            'xCd_CGConto_TransitorioFatture' => 'X Cd Cg Conto Transitorio Fatture',
            'CD_Altre_Somme_Non_Soggette' => 'Cd Altre Somme Non Soggette',
        ];
    }
}
