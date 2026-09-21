<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_sottoprv".
 *
 * @property int $id_sub_prv
 * @property string $descrizione
 * @property int $id_prv
 * @property string|null $note
 * @property int|null $tipologia
 * @property string|null $sottocommessa
 * @property string|null $datacreazione
 * @property string|null $inizioval
 * @property string|null $fineval
 * @property float|null $probacq
 * @property float|null $provvigione
 * @property string|null $apertura
 * @property string|null $chiusura
 * @property string|null $apertura_pianificata
 * @property string|null $chiusura_pianificata
 * @property int|null $stato
 * @property string|null $datastato
 */
class GacSottoPrv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_sottoprv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descrizione', 'id_prv'], 'required'],
            [['id_prv', 'tipologia', 'stato'], 'integer'],
            [['note'], 'string'],
            [['datacreazione', 'inizioval', 'fineval', 'apertura', 'chiusura', 'apertura_pianificata', 'chiusura_pianificata', 'datastato'], 'safe'],
            [['probacq', 'provvigione'], 'number'],
            [['descrizione'], 'string', 'max' => 200],
            [['locked_by'],'string','max'=>125],
            ['is_locked','boolean'],
            [['sottocommessa'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_sub_prv' => 'Id Sub Prv',
            'descrizione' => 'Descrizione',
            'id_prv' => 'Id Prv',
            'note' => 'Note',
            'tipologia' => 'Tipologia',
            'sottocommessa' => 'Sottocommessa',
            'datacreazione' => 'Datacreazione',
            'inizioval' => 'Inizioval',
            'fineval' => 'Fineval',
            'probacq' => 'Probacq',
            'provvigione' => 'Provvigione',
            'apertura' => 'Apertura',
            'chiusura' => 'Chiusura',
            'apertura_pianificata' => 'Apertura Pianificata',
            'chiusura_pianificata' => 'Chiusura Pianificata',
            'stato' => 'Stato',
            'datastato' => 'Datastato',
        ];
    }
}
