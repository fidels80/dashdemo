<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_modello_tessuto".
 *
 * Associa i tessuti disponibili a un modello (attributo variante "modello").
 *
 * @property int $id
 * @property int $id_modello
 * @property int $id_tessuto
 * @property bool $attivo
 *
 * @property MgAttributoArticolo $modello
 * @property MgAttributoArticolo $tessuto
 */
class MgModelloTessuto extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_modello_tessuto';
    }

    public function rules()
    {
        return [
            [['id_modello', 'id_tessuto'], 'required'],
            [['id_modello', 'id_tessuto'], 'integer'],
            [['attivo'], 'boolean'],
            [['id_modello', 'id_tessuto'], 'unique', 'targetAttribute' => ['id_modello', 'id_tessuto'],
                'message' => 'Questo tessuto è già associato al modello.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_modello' => 'Modello',
            'id_tessuto' => 'Tessuto',
            'attivo' => 'Attivo',
        ];
    }

    public function getModello()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_modello']);
    }

    public function getTessuto()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_tessuto']);
    }

    /**
     * Tessuti attivi associati a un modello, come elenco di id.
     */
    public static function idTessutiPerModello($idModello)
    {
        return self::find()
            ->select('id_tessuto')
            ->where(['id_modello' => $idModello, 'attivo' => 1])
            ->column();
    }

    /**
     * Sostituisce in blocco i tessuti associati a un modello.
     */
    public static function sincronizza($idModello, array $idTessuti)
    {
        self::deleteAll(['id_modello' => $idModello]);

        $visti = [];
        foreach ($idTessuti as $idTessuto) {
            $idTessuto = (int) $idTessuto;
            if ($idTessuto <= 0 || isset($visti[$idTessuto])) {
                continue;
            }
            $visti[$idTessuto] = true;
            $riga = new self();
            $riga->id_modello = (int) $idModello;
            $riga->id_tessuto = $idTessuto;
            $riga->attivo = true;
            $riga->save(false);
        }
    }
}
