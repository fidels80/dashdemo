<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\Response;

/**
 * Comportamento per i controller CRUD: garantisce che il record aperto in
 * modifica sia "in uso" da un solo utente alla volta.
 *
 * Da collegare ai controller che hanno una actionUpdate e una actionView.
 * Configurare 'modelClass' con la classe del modello (serve per il nome
 * della tabella); opzionalmente 'idParam' (default 'id') e 'viewAction'.
 *
 * Se un altro utente sta gia' modificando il record, l'utente corrente
 * viene reindirizzato alla vista in sola lettura.
 *
 * Esempio:
 *   'recordLock' => [
 *       'class' => \app\components\RecordLockBehavior::class,
 *       'modelClass' => MgArticolo::class,
 *   ],
 */
class RecordLockBehavior extends Behavior
{
    /** @var string classe del modello ActiveRecord del record */
    public $modelClass;

    /** @var string nome del parametro che contiene la chiave del record */
    public $idParam = 'id';

    /** @var string azione di sola lettura a cui reindirizzare */
    public $viewAction = 'view';

    /** @var array azioni da proteggere con il blocco */
    public $actions = ['update'];

    /** @var int|null durata del blocco in secondi (null = params['recordLockTimeout']) */
    public $timeout;

    /** @var string messaggio mostrato quando il record e' in uso da altri */
    public $message = 'Il record è in uso da {utente}: è visualizzabile in sola lettura.';

    private $tabella;
    private $idRecord;

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    public function beforeAction($event)
    {
        if (Yii::$app->user->isGuest || empty($this->modelClass)) {
            return;
        }
        if (!in_array($event->action->id, (array) $this->actions, true)) {
            return;
        }

        $id = Yii::$app->request->get($this->idParam);
        if ($id === null || $id === '') {
            return;
        }

        $this->tabella = call_user_func([$this->modelClass, 'tableName']);
        $this->idRecord = (string) $id;

        if (call_user_func([$this->modelClass, 'findOne'], $id) === null) {
            return;
        }

        if (RecordLock::acquire($this->tabella, $this->idRecord, $this->timeout)) {
            return;
        }

        $utente = RecordLock::holder($this->tabella, $this->idRecord);
        Yii::$app->session->setFlash('recordLock', strtr($this->message, [
            '{utente}' => $utente !== null ? $utente : 'un altro utente',
        ]));

        $this->owner->redirect([$this->viewAction, $this->idParam => $id]);
        $event->isValid = false;
    }

    public function afterAction($event)
    {
        if ($this->tabella === null || $this->idRecord === null) {
            return;
        }
        if ($event->result instanceof Response && $event->result->getIsRedirection()) {
            RecordLock::release($this->tabella, $this->idRecord);
        }
    }
}
