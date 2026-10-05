<?php

namespace app\models;

/**
 * Registro delle entita' esposte dal servizio REST.
 *
 * E' l'unico elenco di tabelle/modelli che il servizio puo' raggiungere:
 * aggiungere una nuova tabella significa aggiungere una riga qui (oppure
 * eseguire "php yii api/sync" se il modello segue la convenzione Mg*).
 *
 * @property int $id
 * @property string $codice
 * @property string|null $alias
 * @property string $classe
 * @property string $tabella
 * @property string $descrizione
 * @property string|null $chiave_upsert
 * @property int $sola_lettura
 * @property int $cancellabile
 * @property int $attiva
 * @property int $ordinamento
 */
class DashApiEntita extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%dash_api_entita}}';
    }

    public function rules()
    {
        return [
            [['codice', 'classe', 'tabella', 'descrizione'], 'required'],
            [['codice', 'alias', 'classe', 'tabella', 'descrizione', 'chiave_upsert'], 'string', 'max' => 200],
            [['sola_lettura', 'cancellabile', 'attiva', 'ordinamento'], 'integer'],
            [['codice'], 'unique'],
            [['attiva'], 'default', 'value' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'alias' => 'Alias',
            'classe' => 'Classe',
            'tabella' => 'Tabella',
            'descrizione' => 'Descrizione',
            'chiave_upsert' => 'Chiave naturale',
            'sola_lettura' => 'Sola lettura',
            'cancellabile' => 'Cancellabile via API',
            'attiva' => 'Attiva',
            'ordinamento' => 'Ordinamento',
        ];
    }

    public function getRelazioni()
    {
        return $this->hasMany(DashApiRel::className(), ['entita' => 'codice'])
            ->orderBy(['tipo' => SORT_ASC, 'ordinamento' => SORT_ASC, 'id' => SORT_ASC]);
    }

    /**
     * Entita' attive ordinate per menu' di selezione.
     *
     * @return DashApiEntita[]
     */
    public static function attive()
    {
        return static::find()
            ->where(['attiva' => 1])
            ->orderBy(['ordinamento' => SORT_ASC, 'codice' => SORT_ASC])
            ->all();
    }

    /**
     * Cerca un'entita' per codice o alias (case-insensitive).
     *
     * @param string $name
     * @return DashApiEntita|null
     */
    public static function findByName($name)
    {
        $name = strtolower(trim((string) $name));
        if ($name === '') {
            return null;
        }

        $entita = static::findOne(['codice' => $name]);
        if ($entita !== null) {
            return $entita;
        }

        foreach (static::find()->where(['attiva' => 1])->all() as $e) {
            foreach (array_filter(array_map('trim', explode(',', (string) $e->alias))) as $alias) {
                if (strtolower($alias) === $name) {
                    return $e;
                }
            }
        }

        return null;
    }

    /**
     * Chiave naturale di upsert come lista di colonne.
     *
     * @return string[]
     */
    public function getChiaveUpsertArray()
    {
        if (empty($this->chiave_upsert)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->chiave_upsert))));
    }

    /**
     * Il modello ActiveRecord associato all'entita'.
     *
     * @return \yii\db\ActiveRecord|null
     */
    public function getModel()
    {
        $classe = (string) $this->classe;
        if ($classe === '' || !class_exists($classe) || !is_subclass_of($classe, \yii\db\ActiveRecord::className())) {
            return null;
        }
        /** @var \yii\db\ActiveRecord $model */
        $model = new $classe();
        return $model;
    }
}
