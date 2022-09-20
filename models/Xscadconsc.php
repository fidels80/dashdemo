<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_ScadCon_SC".
 *
 * @property string|null $Cd_CGConto_Banca
 * @property string $DataScadenza
 * @property string $Cd_CF
 * @property string $Descrizione
 * @property string|null $DataFattura
 * @property string|null $NumFattura
 * @property string $TipoRata
 * @property int $Emessa
 * @property int $Contabilizzata
 * @property int $Insoluta
 * @property string $Cd_VL
 * @property float $ImportoE
 * @property float $ImportoV
 * @property float|null $ImportoDaPagareE
 * @property float|null $ImportoDaPagareV
 * @property int $Pagata
 * @property string|null $FTE_TipoPagamento
 * @property float $ImportoDare
 * @property float $ImportoAvere
 * @property float $Saldo
 * @property string $Stato_Cli
 * @property string $Settore_Cli
 * @property string $cd_sottocommessa
 * @property int $id
 */
class Xscadconsc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_ScadCon_SC';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['DataScadenza', 'Cd_CF', 'Descrizione', 'TipoRata', 'Emessa', 'Contabilizzata', 'Insoluta', 'Cd_VL', 'ImportoE', 'ImportoV', 'Pagata', 'ImportoDare', 'ImportoAvere', 'Saldo', 'Stato_Cli', 'Settore_Cli', 'cd_sottocommessa'], 'required'],
            [['DataScadenza', 'DataFattura'], 'safe'],
            [['Emessa', 'Contabilizzata', 'Insoluta', 'Pagata'], 'integer'],
            [['ImportoE', 'ImportoV', 'ImportoDaPagareE', 'ImportoDaPagareV', 'ImportoDare', 'ImportoAvere', 'Saldo'], 'number'],
            [['Cd_CGConto_Banca'], 'string', 'max' => 12],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Descrizione'], 'string', 'max' => 80],
            [['NumFattura', 'cd_sottocommessa'], 'string', 'max' => 20],
            [['TipoRata'], 'string', 'max' => 1],
            [['Cd_VL', 'Stato_Cli', 'Settore_Cli'], 'string', 'max' => 3],
            [['FTE_TipoPagamento'], 'string', 'max' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Cd_CGConto_Banca' => 'Cd Cg Conto Banca',
            'DataScadenza' => 'Data Scadenza',
            'Cd_CF' => 'Cd Cf',
            'Descrizione' => 'Descrizione',
            'DataFattura' => 'Data Fattura',
            'NumFattura' => 'Num Fattura',
            'TipoRata' => 'Tipo Rata',
            'Emessa' => 'Emessa',
            'Contabilizzata' => 'Contabilizzata',
            'Insoluta' => 'Insoluta',
            'Cd_VL' => 'Cd Vl',
            'ImportoE' => 'Importo E',
            'ImportoV' => 'Importo V',
            'ImportoDaPagareE' => 'Importo Da Pagare E',
            'ImportoDaPagareV' => 'Importo Da Pagare V',
            'Pagata' => 'Pagata',
            'FTE_TipoPagamento' => 'Fte Tipo Pagamento',
            'ImportoDare' => 'Importo Dare',
            'ImportoAvere' => 'Importo Avere',
            'Saldo' => 'Saldo',
            'Stato_Cli' => 'Stato Cli',
            'Settore_Cli' => 'Settore Cli',
            'cd_sottocommessa' => 'Cd Sottocommessa',
            'id' => 'ID',
        ];
    }
}
