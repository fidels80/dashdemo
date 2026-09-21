<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_attivita".
 *
 * @property int $id
 * @property string $id_todo
 * @property string|null $user
 * @property string|null $azione
 * @property string|null $campo
 * @property string|null $valore_prima
 * @property string|null $valore_dopo
 * @property string|null $data
 */
class Todoattivita extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_attivita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_todo'], 'required'],
            [['data'], 'safe'],
            [['id_todo'], 'string', 'max' => 50],
            [['user', 'azione', 'campo'], 'string', 'max' => 50],
            [['valore_prima', 'valore_dopo'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_todo' => 'Id Todo',
            'user' => 'Utente',
            'azione' => 'Azione',
            'campo' => 'Campo',
            'valore_prima' => 'Valore prima',
            'valore_dopo' => 'Valore dopo',
            'data' => 'Data',
        ];
    }

    public function getUserdett()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    /**
     * Registra una modifica nella cronologia del task.
     */
    public static function log($idTodo, $campo, $prima, $dopo, $azione = 'update')
    {
        $model = new self();
        $model->id_todo = (string) $idTodo;
        $model->user = Yii::$app->user->identity->username ?? 'system';
        $model->azione = $azione;
        $model->campo = $campo;
        $model->valore_prima = is_scalar($prima) || $prima === null ? (string) $prima : json_encode($prima);
        $model->valore_dopo = is_scalar($dopo) || $dopo === null ? (string) $dopo : json_encode($dopo);
        $model->data = new \yii\db\Expression('GETDATE()');
        return $model->save(false);
    }
}
