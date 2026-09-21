<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Agente".
 *
 * @property int $Id_Agente
 * @property string $Cd_Agente Codice dell'agente
 * @property string $Cd_CF_For Fornitore associato (Private!A
 * @property string $Descrizione
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di provvi
 * @property string $Email
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $NoteXML
 * @property string|null $Attributi
 * @property string|null $Note_Agente
 * @property string|null $x_f_1
 * @property float|null $x_f_1_imp
 * @property string|null $x_f_2
 * @property float|null $x_f_2_imp
 * @property string|null $x_f_3
 * @property float|null $x_f_3_imp
 * @property string|null $x_f_4
 * @property float|null $x_f_4_imp
 * @property string|null $x_f_5
 * @property float|null $x_f_5_imp
 * @property string|null $x_f_6
 * @property float|null $x_f_6_imp
 * @property float|null $x_kmcoefficente
 * @property string|null $x_d_f1
 * @property string|null $x_d_f2
 * @property string|null $x_d_f3
 * @property string|null $x_d_f4
 * @property string|null $x_d_f5
 * @property string|null $x_d_f6
 * @property string|null $x_n_f1
 * @property string|null $x_n_f2
 * @property string|null $x_n_f3
 * @property string|null $x_n_f4
 * @property string|null $x_n_f5
 * @property string|null $x_n_f6
 * @property bool|null $xis_credito1
 * @property bool|null $xis_credito2
 * @property bool|null $xis_credito3
 * @property bool|null $xis_credito4
 * @property bool|null $xis_credito5
 * @property bool|null $xis_credito6
 * @property bool|null $xis_debito1
 * @property bool|null $xis_debito2
 * @property bool|null $xis_debito3
 * @property bool|null $xis_debito4
 * @property bool|null $xis_debito5
 * @property bool|null $xis_debito6
 *
 * @property CF $cdCFFor
 * @property CF[] $cFs
 * @property CF[] $cFs0
 * @property CFDest[] $cFDests
 * @property CGMovT[] $cGMovTs
 * @property CGMovT[] $cGMovTs0
 * @property DOTes[] $dOTes
 * @property DOTes[] $dOTes0
 * @property LSScAgenteARGruppo[] $lSScAgenteARGruppos
 * @property Operatore[] $operatores
 * @property OperatoreAgente[] $operatoreAgentes
 * @property Operatore[] $operatores0
 * @property OrsAgente2[] $orsAgente2s
 * @property Operatore[] $operatores1
 * @property Provvigione[] $provvigiones
 */
class Agente extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Agente';
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
            [['Cd_Agente', 'Cd_CF_For'], 'required'],
            [['TimeIns', 'TimeUpd', 'Ts', 'x_d_f1', 'x_d_f2', 'x_d_f3', 'x_d_f4', 'x_d_f5', 'x_d_f6'], 'safe'],
            [['NoteXML', 'Attributi', 'Note_Agente'], 'string'],
            [['x_f_1_imp', 'x_f_2_imp', 'x_f_3_imp', 'x_f_4_imp', 'x_f_5_imp', 'x_f_6_imp', 'x_kmcoefficente'], 'number'],
            [['xis_credito1', 'xis_credito2', 'xis_credito3', 'xis_credito4', 'xis_credito5', 'xis_credito6', 'xis_debito1', 'xis_debito2', 'xis_debito3', 'xis_debito4', 'xis_debito5', 'xis_debito6'], 'boolean'],
            [['Cd_Agente'], 'string', 'max' => 3],
            [['Cd_CF_For', 'x_f_1', 'x_f_2', 'x_f_3', 'x_f_4', 'x_f_5', 'x_f_6'], 'string', 'max' => 7],
            [['Descrizione'], 'string', 'max' => 80],
            [['Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['Email'], 'string', 'max' => 100],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['x_n_f1', 'x_n_f2', 'x_n_f3', 'x_n_f4', 'x_n_f5', 'x_n_f6'], 'string', 'max' => 250],
            [['Cd_Agente'], 'unique'],
            [['Cd_CF_For'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF_For' => 'Cd_CF']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_Agente' => 'Id Agente',
            'Cd_Agente' => 'Cd Agente',
            'Cd_CF_For' => 'Cd Cf For',
            'Descrizione' => 'Descrizione',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
            'Email' => 'Email',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'NoteXML' => 'Note Xml',
            'Attributi' => 'Attributi',
            'Note_Agente' => 'Note Agente',
            'x_f_1' => 'X F 1',
            'x_f_1_imp' => 'X F 1 Imp',
            'x_f_2' => 'X F 2',
            'x_f_2_imp' => 'X F 2 Imp',
            'x_f_3' => 'X F 3',
            'x_f_3_imp' => 'X F 3 Imp',
            'x_f_4' => 'X F 4',
            'x_f_4_imp' => 'X F 4 Imp',
            'x_f_5' => 'X F 5',
            'x_f_5_imp' => 'X F 5 Imp',
            'x_f_6' => 'X F 6',
            'x_f_6_imp' => 'X F 6 Imp',
            'x_kmcoefficente' => 'X Kmcoefficente',
            'x_d_f1' => 'X D F1',
            'x_d_f2' => 'X D F2',
            'x_d_f3' => 'X D F3',
            'x_d_f4' => 'X D F4',
            'x_d_f5' => 'X D F5',
            'x_d_f6' => 'X D F6',
            'x_n_f1' => 'X N F1',
            'x_n_f2' => 'X N F2',
            'x_n_f3' => 'X N F3',
            'x_n_f4' => 'X N F4',
            'x_n_f5' => 'X N F5',
            'x_n_f6' => 'X N F6',
            'xis_credito1' => 'Xis Credito1',
            'xis_credito2' => 'Xis Credito2',
            'xis_credito3' => 'Xis Credito3',
            'xis_credito4' => 'Xis Credito4',
            'xis_credito5' => 'Xis Credito5',
            'xis_credito6' => 'Xis Credito6',
            'xis_debito1' => 'Xis Debito1',
            'xis_debito2' => 'Xis Debito2',
            'xis_debito3' => 'Xis Debito3',
            'xis_debito4' => 'Xis Debito4',
            'xis_debito5' => 'Xis Debito5',
            'xis_debito6' => 'Xis Debito6',
        ];
    }

    /**
     * Gets query for [[CdCFFor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCFFor()
    {
        return $this->hasOne(CF::className(), ['Cd_CF' => 'Cd_CF_For']);
    }

    /**
     * Gets query for [[CFs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCFs()
    {
        return $this->hasMany(CF::className(), ['Cd_Agente_1' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[CFs0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCFs0()
    {
        return $this->hasMany(CF::className(), ['Cd_Agente_2' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[CFDests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCFDests()
    {
        return $this->hasMany(CFDest::className(), ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[CGMovTs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCGMovTs()
    {
        return $this->hasMany(CGMovT::className(), ['Cd_Agente_1' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[CGMovTs0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCGMovTs0()
    {
        return $this->hasMany(CGMovT::className(), ['Cd_Agente_2' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[DOTes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDOTes()
    {
        return $this->hasMany(DOTes::className(), ['Cd_Agente_2' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[DOTes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDOTes0()
    {
        return $this->hasMany(DOTes::className(), ['Cd_Agente_1' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[LSScAgenteARGruppos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLSScAgenteARGruppos()
    {
        return $this->hasMany(LSScAgenteARGruppo::className(), ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[Operatores]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatores()
    {
        return $this->hasMany(Operatore::className(), ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[OperatoreAgentes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatoreAgentes()
    {
        return $this->hasMany(OperatoreAgente::className(), ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[Operatores0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatores0()
    {
        return $this->hasMany(Operatore::className(), ['Id_Operatore' => 'Id_Operatore'])->viaTable('OperatoreAgente', ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[OrsAgente2s]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrsAgente2s()
    {
        return $this->hasMany(OrsAgente2::className(), ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[Operatores1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatores1()
    {
        return $this->hasMany(Operatore::className(), ['Id_Operatore' => 'Id_Operatore'])->viaTable('orsAgente2', ['Cd_Agente' => 'Cd_Agente']);
    }

    /**
     * Gets query for [[Provvigiones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProvvigiones()
    {
        return $this->hasMany(Provvigione::className(), ['Cd_Agente' => 'Cd_Agente']);
    }
}
