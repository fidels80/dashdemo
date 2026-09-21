<?php

namespace app\models;

use Yii;
use app\models\Doc_rows;
/**
 * This is the model class for table "doc_head".
 *
 * @property string $id
 * @property string $cd_doc
 * @property string $data
 * @property string $numdoc
 * @property string $cd_cli
 * @property string|null $cd_pg
 * @property string|null $sconto
 * @property string|null $note
 * @property int|null $confermato
 * @property int|null $xid_testa
 * @property int|null $rifiutato
 * @property string|null $rifiutato_nota
 * @property string|null $dest
 * @property string|null $altcli
 * @property string|null $xnota
 */
class DocHead extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_head';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'note', 'rifiutato_nota', 'xnota'], 'string'],
            [['cd_doc', 'data', 'numdoc', 'cd_cli'], 'required'],
            [['data'], 'safe'],
            [['confermato', 'xid_testa', 'rifiutato'], 'integer'],
            [['cd_doc', 'dest'], 'string', 'max' => 3],
            [['numdoc', 'cd_cli', 'cd_pg', 'sconto', 'altcli'], 'string', 'max' => 10],
            [['id'], 'unique'],
           [ ['items'] , 'safe' ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cd_doc' => 'Cd Doc',
            'data' => 'Data',
            'numdoc' => 'Numdoc',
            'cd_cli' => 'Cd Cli',
            'cd_pg' => 'Cd Pg',
            'sconto' => 'Sconto',
            'note' => 'Note',
            'confermato' => 'Confermato',
            'xid_testa' => 'Xid Testa',
            'rifiutato' => 'Rifiutato',
            'rifiutato_nota' => 'Rifiutato Nota',
            'dest' => 'Dest',
            'altcli' => 'Altcli',
            'xnota' => 'Xnota',
        ];
    }
  
  public function getDocRows()
{
   // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return $this->hasMany(Doc_rows::class, ['doc_head_id' => 'id']);
}



public function getXrows($id){
$artdett=Doc_rows::find()
 ->where(['doc_head_id'=>$id])
->all();
//\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//$out = ['result'=>['']];
// $out['results'] = array_values($artdett);
return  ($artdett);

        }
}
