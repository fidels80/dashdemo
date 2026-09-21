<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_attivita".
 *
 * @property int $id_attivita
 * @property int $id_sub_prv
 * @property int $sequenza
 * @property string $attivita
 * @property string $descrizione
 * @property string $um
 * @property float|null $tempo
 * @property float|null $ore
 * @property string|null $risorsa
 * @property float|null $costo
 * @property float|null $sconto
 * @property float|null $costo_scontato
 * @property float|null $ricarico
 * @property float|null $costo_ricarico
 * @property float|null $sconto_vendita
 * @property float|null $valore_costounitario
 * @property float|null $valore_costotot
 * @property float|null $margine
 * @property float|null $margine_perc
 * @property string|null $note
 * @property string|null $data_apertura
 * @property string|null $data_chiusura
 */
class Gacattivita extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_attivita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sub_prv', 'sequenza', 'attivita', 'descrizione', 'um'], 'required'],
            [['id_sub_prv', 'sequenza'], 'integer'],
            [['tempo', 'ore', 'costo', 'sconto', 'costo_scontato', 'ricarico', 'costo_ricarico', 'sconto_vendita', 'valore_costounitario', 'valore_costotot', 'margine', 'margine_perc'], 'number'],
            [['note'], 'string'],
            [['data_apertura', 'data_chiusura'], 'safe'],
            [['attivita'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 200],
            [['um'], 'string', 'max' => 2],
            [['risorsa'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_attivita' => 'Id Attivita',
            'id_sub_prv' => 'Id Sub Prv',
            'sequenza' => 'Sequenza',
            'attivita' => 'Attivita',
            'descrizione' => 'Descrizione',
            'um' => 'Um',
            'tempo' => 'Tempo',
            'ore' => 'Ore',
            'risorsa' => 'Risorsa',
            'costo' => 'Costo',
            'sconto' => 'Sconto',
            'costo_scontato' => 'Costo Scontato',
            'ricarico' => 'Ricarico',
            'costo_ricarico' => 'Costo Ricarico',
            'sconto_vendita' => 'Sconto Vendita',
            'valore_costounitario' => 'Valore Costounitario',
            'valore_costotot' => 'Valore Costotot',
            'margine' => 'Margine',
            'margine_perc' => 'Margine Perc',
            'note' => 'Note',
            'data_apertura' => 'Data Apertura',
            'data_chiusura' => 'Data Chiusura',
        ];
    }
}
