<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "dash_menu".
 *
 * @property int $id
 * @property string $codice
 * @property string $label
 * @property string|null $icona
 * @property string|null $url
 * @property int|null $genitore_id
 * @property int $livello_min
 * @property int $ordine
 * @property bool $per_tutti
 * @property bool $attivo
 * @property string|null $created_at
 */
class DashMenu extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_menu';
    }

    public function rules()
    {
        return [
            [['codice', 'label'], 'required'],
            [['genitore_id', 'livello_min', 'ordine'], 'integer'],
            [['per_tutti', 'attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 50],
            [['label'], 'string', 'max' => 100],
            [['icona'], 'string', 'max' => 50],
            [['url'], 'string', 'max' => 200],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'label' => 'Etichetta',
            'icona' => 'Icona',
            'url' => 'URL / Route',
            'genitore_id' => 'Voce genitore',
            'livello_min' => 'Livello minimo',
            'ordine' => 'Ordine',
            'per_tutti' => 'Visibile a tutti',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getGenitore()
    {
        return $this->hasOne(self::className(), ['id' => 'genitore_id']);
    }

    public function getFigli()
    {
        return $this->hasMany(self::className(), ['genitore_id' => 'id'])
            ->orderBy(['ordine' => SORT_ASC, 'label' => SORT_ASC]);
    }

    public function getAssegnazioni()
    {
        return $this->hasMany(DashMenuUtente::className(), ['menu_id' => 'id']);
    }

    /**
     * Elenco delle voci radice per le tendine (esclude se stessa e i propri discendenti).
     */
    public static function getGenitoriDisponibili($escludiId = null)
    {
        $query = self::find()->where(['genitore_id' => null])->orderBy(['ordine' => SORT_ASC, 'label' => SORT_ASC]);
        if ($escludiId) {
            $query->andWhere(['<>', 'id', $escludiId]);
        }
        return ArrayHelper::map($query->all(), 'id', 'label');
    }

    /**
     * Costruisce l'array di voci per il widget Menu, filtrato per utente/livello.
     */
    public static function buildMenuForUser($userId, $level)
    {
        $items = self::find()
            ->where(['attivo' => 1])
            ->andWhere(['<=', 'livello_min', (int) $level])
            ->orderBy(['ordine' => SORT_ASC, 'label' => SORT_ASC])
            ->all();

        // Il livello 100 (supervisore) vede sempre tutto
        $isSuper = ((int) $level >= 100);

        $assigned = DashMenuUtente::find()
            ->select('menu_id')
            ->where(['user_id' => $userId])
            ->column();
        $assigned = array_map('intval', $assigned);

        $byParent = [];
        foreach ($items as $it) {
            if (!$isSuper && !$it->per_tutti && !in_array((int) $it->id, $assigned, true)) {
                continue;
            }
            $byParent[(int) $it->genitore_id][] = $it;
        }

        return self::buildBranch($byParent, 0);
    }

    private static function buildBranch(&$byParent, $parentId)
    {
        $result = [];
        $key = (int) $parentId;
        if (empty($byParent[$key])) {
            return $result;
        }

        foreach ($byParent[$key] as $it) {
            $node = [
                'label' => $it->label,
                'icon' => $it->icona ?: 'circle',
            ];

            if (!empty($it->url)) {
                $node['url'] = ['/' . ltrim($it->url, '/')];
            } else {
                $node['url'] = '#';
            }

            $children = self::buildBranch($byParent, $it->id);
            if (!empty($children)) {
                $node['items'] = $children;
            }

            $result[] = $node;
        }

        return $result;
    }
}
