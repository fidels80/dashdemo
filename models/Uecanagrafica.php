<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uec_anagrafica".
 *
 * @property int $id
 * @property string $nome
 * @property string $cognome
 * @property string|null $indirizzo
 * @property string|null $citta
 * @property string|null $provincia
 * @property string|null $nazione
 * @property string|null $codicefiscale
 */
class Uecanagrafica extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uec_anagrafica';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'cognome'], 'required'],
            [['nome', 'cognome', 'indirizzo', 'citta', 'nazione'], 'string', 'max' => 250],
            [['provincia'], 'string', 'max' => 3],
            [['codicefiscale'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'cognome' => 'Cognome',
            'indirizzo' => 'Indirizzo',
            'citta' => 'Citta',
            'provincia' => 'Provincia',
            'nazione' => 'Nazione',
            'codicefiscale' => 'Codicefiscale',
        ];
    }
}
