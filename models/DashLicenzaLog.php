<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Storico degli eventi di licenza e aggiornamento.
 *
 * @property int $id
 * @property string $event_type
 * @property string|null $detail
 * @property string|null $created_at
 */
class DashLicenzaLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'dash_licenza_log';
    }

    public function rules()
    {
        return [
            [['event_type'], 'required'],
            [['event_type'], 'string', 'max' => 50],
            [['detail'], 'string', 'max' => 1000],
            [['created_at'], 'safe'],
        ];
    }

    public static function log($eventType, $detail = null)
    {
        $log = new self();
        $log->event_type = $eventType;
        $log->detail = is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : $detail;
        $log->created_at = date('Ymd H:i:s');
        $log->save(false);
        return $log;
    }
}
