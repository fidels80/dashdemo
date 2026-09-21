<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "export_jobs".
 *
 * @property int $id
 * @property string $job_id
 * @property string $data_id
 * @property string $username
 * @property string $user_email
 * @property string $status
 * @property int|null $progress
 * @property string|null $message
 * @property string|null $file_path
 * @property int|null $file_size
 * @property int|null $records_count
 * @property float|null $processing_time
 * @property string|null $error_message
 * @property string|null $export_type
 * @property string|null $server_info
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Export_jobs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_jobs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['job_id', 'data_id', 'username', 'user_email'], 'required'],
            [['progress', 'file_size', 'records_count'], 'integer'],
            [['message', 'error_message', 'server_info'], 'string'],
            [['processing_time'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['job_id', 'data_id', 'username', 'user_email'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['file_path'], 'string', 'max' => 500],
            [['export_type'], 'string', 'max' => 50],
            [['job_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'job_id' => 'Job ID',
            'data_id' => 'Data ID',
            'username' => 'Username',
            'user_email' => 'User Email',
            'status' => 'Status',
            'progress' => 'Progress',
            'message' => 'Message',
            'file_path' => 'File Path',
            'file_size' => 'File Size',
            'records_count' => 'Records Count',
            'processing_time' => 'Processing Time',
            'error_message' => 'Error Message',
            'export_type' => 'Export Type',
            'server_info' => 'Server Info',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
