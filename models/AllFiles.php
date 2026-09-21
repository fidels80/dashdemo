<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "all_files".
 *
 * @property int $id
 * @property int|null $id_padre
 * @property resource|null $f_content
 * @property string|null $entita
 * @property string|null $nomefile
 * @property string|null $estensione
 * @property string|null $origne
 * @property string|null $nota
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
            [['id_padre'], 'integer'],
            [['f_content'], 'string'],
            [['entita'], 'string', 'max' => 128],
            [['nomefile'], 'string', 'max' => 260],
            [['estensione'], 'string', 'max' => 3],
            [['origine'], 'string', 'max' => 1],
            [['nota'], 'string'],
            [['sub_entita'], 'string', 'max' => 100],
        [['sub_entita'], 'safe'],
        [['data_inizio', 'data_fine'], 'safe'],
        [['importo'], 'number']
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
            'origne' => 'Origne',
            'nota'=>'nota',
  'sub_entita' => 'Tipologia Documento',
  'data_inizio' => 'Inizio Validità',
        'data_fine' => 'Scadenza',
        'importo' => 'Importo Pagamento (€)'
            ];
    }
    public function upload($file)
    {
        $path = '../web/uploads/' . str_replace(' ', '_', $file->baseName) . '.' . $file->extension;
        if ($file->saveAs($path)) {
            return true;
        }
        return false;
    }
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}
    public function getSubEntitaRel()
{
    return $this->hasOne(SubEntitaLookup::class, ['codice' => 'sub_entita']);
}
}
