<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_materiali".
 *
 * @property int $id
 * @property int $id_sub_prv
 * @property string $listino
 * @property string $cd_ar
 * @property string $descrizione
 * @property float $qta
 * @property string $um
 * @property float|null $costounitario
 * @property float|null $scontoacq
 * @property float|null $costounitscontato
 * @property float|null $ricarico
 * @property float|null $costounitarioric
 * @property float|null $sconto_vendita
 * @property float|null $valvendita
 * @property float|null $margine
 * @property float|null $margineperc
 * @property float|null $prezzounitarionetto
 * @property string|null $note
 */
class Gacmateriali extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_materiali';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sub_prv', 'listino', 'cd_ar', 'descrizione', 'qta', 'um'], 'required'],
            [['id_sub_prv'], 'integer'],
            [['qta', 'costounitario', 'scontoacq', 'costounitscontato', 'ricarico', 'costounitarioric', 'sconto_vendita', 'valvendita', 'margine', 'margineperc', 'prezzounitarionetto'], 'number'],
            [['note'], 'string'],
            [['listino'], 'string', 'max' => 10],
            [['cd_ar'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 200],
            [['um'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sub_prv' => 'Id Sub Prv',
            'listino' => 'Listino',
            'cd_ar' => 'Cd Ar',
            'descrizione' => 'Descrizione',
            'qta' => 'Qta',
            'um' => 'Um',
            'costounitario' => 'Costounitario',
            'scontoacq' => 'Scontoacq',
            'costounitscontato' => 'Costounitscontato',
            'ricarico' => 'Ricarico',
            'costounitarioric' => 'Costounitarioric',
            'sconto_vendita' => 'Sconto Vendita',
            'valvendita' => 'Valvendita',
            'margine' => 'Margine',
            'margineperc' => 'Margineperc',
            'prezzounitarionetto' => 'Prezzounitarionetto',
            'note' => 'Note',
        ];
    }
}
