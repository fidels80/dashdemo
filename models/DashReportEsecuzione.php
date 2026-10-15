<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_report_esecuzione".
 *
 * Storico delle esecuzioni di un report: serve per audit e statistiche
 * (chi ha eseguito cosa, con quali parametri, in che formato e con quale esito).
 *
 * @property int $id
 * @property int $report_id
 * @property int|null $user_id
 * @property string|null $parametri
 * @property string|null $esito
 * @property string|null $messaggio
 * @property int|null $righe
 * @property int|null $durata_ms
 * @property string|null $formato
 * @property string|null $created_at
 */
class DashReportEsecuzione extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_report_esecuzione';
    }

    public function rules()
    {
        return [
            [['report_id'], 'required'],
            [['report_id', 'user_id', 'righe', 'durata_ms'], 'integer'],
            [['parametri'], 'string'],
            [['created_at'], 'safe'],
            [['esito'], 'string', 'max' => 20],
            [['messaggio'], 'string', 'max' => 500],
            [['formato'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_id' => 'Report',
            'user_id' => 'Utente',
            'parametri' => 'Parametri',
            'esito' => 'Esito',
            'messaggio' => 'Messaggio',
            'righe' => 'Righe',
            'durata_ms' => 'Durata (ms)',
            'formato' => 'Formato',
            'created_at' => 'Data',
        ];
    }

    public function getReport()
    {
        return $this->hasOne(DashReport::className(), ['id' => 'report_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Registra un'esecuzione (best-effort: non solleva eccezioni).
     */
    public static function log($reportId, $userId, $parametri, $esito, $messaggio = null, $righe = null, $durataMs = null, $formato = null)
    {
        try {
            $log = new self();
            $log->report_id = (int) $reportId;
            $log->user_id = $userId;
            $log->parametri = is_string($parametri) ? $parametri : json_encode($parametri, JSON_UNESCAPED_UNICODE);
            $log->esito = $esito;
            $log->messaggio = $messaggio !== null ? substr($messaggio, 0, 500) : null;
            $log->righe = $righe;
            $log->durata_ms = $durataMs;
            $log->formato = $formato;
            $log->created_at = date('Ymd H:i:s');
            $log->save(false);
        } catch (\Throwable $e) {
            Yii::warning('log esecuzione report: ' . $e->getMessage(), 'report');
        }
    }
}
