<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_tipo".
 *
 * @property int $id
 * @property string $tipo
 * @property string|null $icona
 * @property string|null $colore
 * @property int|null $ordine
 */
class Todotipo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_tipo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'required'],
            [['ordine'], 'integer'],
            [['tipo'], 'string', 'max' => 50],
            [['icona'], 'string', 'max' => 50],
            [['colore'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'icona' => 'Icona',
            'colore' => 'Colore',
            'ordine' => 'Ordine',
        ];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC])->all(),
            'id',
            'tipo'
        );
    }

    public static function listAll()
    {
        return self::find()->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC])->all();
    }

    private static $_cache;

    /**
     * Restituisce il modello del tipo issue per id (con cache interna).
     */
    public static function getById($id)
    {
        if (self::$_cache === null) {
            self::$_cache = \yii\helpers\ArrayHelper::index(self::find()->all(), 'id');
        }
        return self::$_cache[$id] ?? null;
    }
}
