<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_report_utente".
 *
 * Abilita un singolo report a un singolo operatore (come dash_menu_utente
 * per le voci di menu). `can_run` consente l'esecuzione, `can_edit` la
 * modifica della definizione del report.
 *
 * @property int $id
 * @property int $report_id
 * @property int $user_id
 * @property bool $can_run
 * @property bool $can_edit
 * @property string|null $created_at
 */
class DashReportUtente extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_report_utente';
    }

    public function rules()
    {
        return [
            [['report_id', 'user_id'], 'required'],
            [['report_id', 'user_id'], 'integer'],
            [['can_run', 'can_edit'], 'boolean'],
            [['created_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_id' => 'Report',
            'user_id' => 'Utente',
            'can_run' => 'Puo\' eseguire',
            'can_edit' => 'Puo\' modificare',
            'created_at' => 'Creato il',
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
     * Id dei report abilitati a un utente, opzionalmente filtrati per
     * capacita' di modifica.
     */
    public static function getReportIdsForUser($userId, $canEdit = false)
    {
        $query = self::find()->select('report_id')->where(['user_id' => $userId]);
        if ($canEdit) {
            $query->andWhere(['can_edit' => 1]);
        } else {
            $query->andWhere(['can_run' => 1]);
        }
        return array_map('intval', $query->column());
    }
}
