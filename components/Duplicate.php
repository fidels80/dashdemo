<?php

namespace app\components;

use yii\db\ActiveRecord;
use yii\validators\UniqueValidator;

/**
 * Crea una copia non salvata di un record per la duplicazione.
 *
 * Copia tutti gli attributi tranne:
 * - la chiave primaria (anche composta);
 * - i campi con validazione di unicità (es. codice);
 * - i timestamp created_at / updated_at / deleted_at.
 */
class Duplicate
{
    public static function copy(ActiveRecord $source)
    {
        $class = get_class($source);
        /** @var ActiveRecord $model */
        $model = new $class();

        $attributes = $source->getAttributes();

        foreach ((array) $source->getTableSchema()->primaryKey as $pk) {
            unset($attributes[$pk]);
        }

        foreach (['created_at', 'updated_at', 'deleted_at'] as $timestamp) {
            unset($attributes[$timestamp]);
        }

        foreach ($source->getValidators() as $validator) {
            if ($validator instanceof UniqueValidator) {
                foreach ($validator->getAttributeNames() as $attribute) {
                    unset($attributes[$attribute]);
                }
            }
        }

        $model->setAttributes($attributes, false);

        return $model;
    }
}
