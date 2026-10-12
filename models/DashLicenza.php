<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Stato corrente della licenza dell'applicazione (riga singola).
 *
 * @property int $id
 * @property string|null $license_key
 * @property string|null $machine_id
 * @property string|null $product
 * @property string|null $tier
 * @property string|null $features
 * @property string|null $expires_at
 * @property string|null $status
 * @property string|null $signature
 * @property string|null $first_run_at
 * @property string|null $activated_at
 * @property string|null $last_check_at
 * @property string|null $last_check_ok
 * @property string|null $last_error
 * @property string|null $update_version
 * @property string|null $updated_at
 */
class DashLicenza extends ActiveRecord
{
    public static function tableName()
    {
        return 'dash_licenza';
    }

    public function rules()
    {
        return [
            [['license_key', 'machine_id', 'product', 'tier', 'status', 'signature',
                'update_version'], 'string', 'max' => 255],
            [['features', 'last_error'], 'string'],
            [['expires_at', 'first_run_at', 'activated_at', 'last_check_at',
                'last_check_ok', 'updated_at'], 'safe'],
        ];
    }

    /**
     * Restituisce la riga di stato, creandola se assente.
     */
    public static function current()
    {
        $model = static::find()->orderBy(['id' => SORT_ASC])->one();
        if (!$model) {
            $model = new self();
            $model->product = \Yii::$app->params['licenseProduct'] ?? 'dashdemo';
            $model->first_run_at = date('Ymd H:i:s');
            $model->save(false);
        }
        return $model;
    }

    public function getFeatureArray()
    {
        $decoded = json_decode((string) $this->features, true);
        return is_array($decoded) ? array_values($decoded) : [];
    }
}
