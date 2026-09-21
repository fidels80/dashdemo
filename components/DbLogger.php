<?php

namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use app\models\Log;

class DbLogger
{
    public static function monitor($event)
    {
        $sender = $event->sender;
        if ($sender instanceof \app\models\Log) return;

        $log = new Log();
        $log->userid = Yii::$app->user->id ?? 0;
        
        $className = (new \ReflectionClass($sender))->getShortName();
        $operation = ($event->name === ActiveRecord::EVENT_AFTER_INSERT) ? 'INSERT' : 'UPDATE';
        
        $log->operazione = $operation . " [" . $className . "]";
        $log->valore = json_encode($sender->getAttributes(), JSON_UNESCAPED_UNICODE);

        if ($event->name === ActiveRecord::EVENT_AFTER_UPDATE) {
            $log->old_valore = json_encode($event->changedAttributes, JSON_UNESCAPED_UNICODE);
        } else {
            $log->old_valore = '';
        }

        $log->save(false);
    }

    // NUOVO METODO PER IL DELETE
    public static function monitorDelete($event)
    {
        $sender = $event->sender;
        if ($sender instanceof \app\models\Log) return;

        $log = new Log();
        $log->userid = Yii::$app->user->id ?? 0;
        
        $className = (new \ReflectionClass($sender))->getShortName();
        $log->operazione = "DELETE [" . $className . "]";
        
        // In caso di cancellazione, il valore attuale è vuoto
        $log->valore = ''; 
        // Salviamo tutti i dati che c'erano prima della cancellazione
        $log->old_valore = json_encode($sender->getAttributes(), JSON_UNESCAPED_UNICODE);

        $log->save(false);
    }
}