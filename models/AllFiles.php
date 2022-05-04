<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "all_files".
 *
 * @property int $id
 * @property string|null $id_padre
 * @property resource|null $f_content
 * @property string|null $entita
 * @property string|null $nomefile
 * @property string|null $estensione
 */
class AllFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'all_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_padre', 'f_content'], 'string'],
            [['entita'], 'string', 'max' => 128],
            [['nomefile'], 'string', 'max' => 260],
            [['estensione'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_padre' => 'Id Padre',
            'f_content' => 'F Content',
            'entita' => 'Entita',
            'nomefile' => 'Nomefile',
            'estensione' => 'Estensione',
        ];
    }
}
