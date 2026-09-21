<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_sprint".
 *
 * @property int $id
 * @property string $nome
 * @property string|null $obiettivo
 * @property string|null $data_inizio
 * @property string|null $data_fine
 * @property string|null $stato
 * @property string|null $created_at
 */
class Todosprint extends \yii\db\ActiveRecord
{
    const STATO_PIANIFICATO = 'pianificato';
    const STATO_ATTIVO = 'attivo';
    const STATO_CHIUSO = 'chiuso';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_sprint';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome'], 'required'],
            [['data_inizio', 'data_fine', 'created_at'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['obiettivo'], 'string', 'max' => 500],
            [['stato'], 'string', 'max' => 20],
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
            'obiettivo' => 'Obiettivo',
            'data_inizio' => 'Data inizio',
            'data_fine' => 'Data fine',
            'stato' => 'Stato',
            'created_at' => 'Creato il',
        ];
    }

    public function getIssues()
    {
        return $this->hasMany(Todomain::className(), ['sprint_id' => 'id']);
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['stato' => SORT_ASC, 'id' => SORT_DESC])->all(),
            'id',
            'nome'
        );
    }

    public static function getAttivo()
    {
        return self::find()->where(['stato' => self::STATO_ATTIVO])->orderBy(['id' => SORT_DESC])->one();
    }
}
