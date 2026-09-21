<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_spese".
 *
 * @property int $id
 * @property int $id_sub_prv
 * @property string $spesa
 * @property string $descrizione
 * @property float $qta
 * @property string $um
 * @property float|null $costounitario
 * @property float|null $sconto
 * @property float|null $costonetto
 * @property float|null $ricarico
 * @property float|null $costoricaricato
 * @property float|null $scontovendita
 * @property float|null $valorenettounitario
 * @property float|null $valorenetto
 * @property float|null $margine
 * @property float|null $margineperc
 * @property string|null $note
 * @property string|null $cd_ar
 * @property string|null $descrizionear
 * @property float|null $prezzoar
 */
class Gacspese extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_spese';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sub_prv', 'spesa', 'descrizione', 'qta', 'um'], 'required'],
            [['id_sub_prv'], 'integer'],
            [['qta', 'costounitario', 'sconto', 'costonetto', 'ricarico', 'costoricaricato', 'scontovendita', 'valorenettounitario', 'valorenetto', 'margine', 'margineperc', 'prezzoar'], 'number'],
            [['note'], 'string'],
            [['spesa', 'cd_ar'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 200],
            [['um'], 'string', 'max' => 2],
            [['descrizionear'], 'string', 'max' => 250],
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
            'spesa' => 'Spesa',
            'descrizione' => 'Descrizione',
            'qta' => 'Qta',
            'um' => 'Um',
            'costounitario' => 'Costounitario',
            'sconto' => 'Sconto',
            'costonetto' => 'Costonetto',
            'ricarico' => 'Ricarico',
            'costoricaricato' => 'Costoricaricato',
            'scontovendita' => 'Scontovendita',
            'valorenettounitario' => 'Valorenettounitario',
            'valorenetto' => 'Valorenetto',
            'margine' => 'Margine',
            'margineperc' => 'Margineperc',
            'note' => 'Note',
            'cd_ar' => 'Cd Ar',
            'descrizionear' => 'Descrizionear',
            'prezzoar' => 'Prezzoar',
        ];
    }
}
