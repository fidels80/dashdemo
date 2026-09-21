<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\models\Log;

class LogBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function afterSave($event)
    {
        $sender = $this->owner;
        $operation = $event->name == ActiveRecord::EVENT_AFTER_INSERT ? 'INSERT' : 'UPDATE';
        
        $log = new Log();
        $log->userid = Yii::$app->user->id ?? 0;
        $log->operazione = $operation . ' on ' . $sender->tableName();
        
        // Salviamo gli attributi correnti (nuovi)
        $log->valore = json_encode($sender->getAttributes());
        
        // Se è un update, salviamo i vecchi valori per il confronto
        if ($operation == 'UPDATE') {
            $log->old_valore = json_encode($event->changedAttributes);
        } else {
            $log->old_valore = '';
        }

        $log->save(false); // false per evitare validazioni ricorsive
    }

    public function beforeDelete($event)
    {
        $sender = $this->owner;
        $log = new Log();
        $log->userid = Yii::$app->user->id ?? 0;
        $log->operazione = 'DELETE on ' . $sender->tableName();
        $log->valore = '';
        $log->old_valore = json_encode($sender->getAttributes());
        $log->save(false);
    }
}
