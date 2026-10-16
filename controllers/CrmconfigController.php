<?php

namespace app\controllers;

use Yii;
use app\models\MgCrmTipo;
use app\models\MgCrmStato;
use app\models\MgCrmPriorita;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Configurazione del CRM: tipi richiesta, stati del workflow e priorità.
 * Le voci non si eliminano (sono referenziate dai ticket) ma si disattivano.
 */
class CrmconfigController extends Controller
{
    /** Mappa entità -> classe modello. */
    private function classi()
    {
        return [
            'tipo' => MgCrmTipo::className(),
            'stato' => MgCrmStato::className(),
            'priorita' => MgCrmPriorita::className(),
        ];
    }

    public function actionIndex($edit = null, $entita = null)
    {
        return $this->render('index', [
            'tipi' => MgCrmTipo::find()->orderBy(['ordine' => SORT_ASC])->all(),
            'stati' => MgCrmStato::find()->orderBy(['ordine' => SORT_ASC])->all(),
            'priorita' => MgCrmPriorita::find()->orderBy(['ordine' => SORT_ASC])->all(),
            'edit' => $this->findEdit($entita, $edit),
        ]);
    }

    public function actionSalva()
    {
        $post = Yii::$app->request->post();
        $entita = $post['entita'] ?? null;
        $classi = $this->classi();
        if (!isset($classi[$entita])) {
            throw new NotFoundHttpException('Entità non valida.');
        }
        /** @var \yii\db\ActiveRecord $class */
        $class = $classi[$entita];

        $model = !empty($post['id']) ? $class::findOne((int) $post['id']) : new $class();
        if ($model === null) {
            throw new NotFoundHttpException('Voce non trovata.');
        }

        $model->load($post);
        if ($entita === 'stato') {
            $model->is_chiuso = !empty($post['is_chiuso']) ? 1 : 0;
        }
        $model->attivo = 1;

        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Voce salvata.');
        } else {
            Yii::$app->session->setFlash('error', 'Errore: ' . implode(' ', \yii\helpers\ArrayHelper::getColumn($model->getErrors(), 0)));
        }

        return $this->redirect(['index']);
    }

    public function actionToggle($entita, $id)
    {
        $classi = $this->classi();
        if (!isset($classi[$entita])) {
            throw new NotFoundHttpException('Entità non valida.');
        }
        $class = $classi[$entita];
        $model = $class::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Voce non trovata.');
        }
        $model->attivo = $model->attivo ? 0 : 1;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Stato di attivazione aggiornato.');
        return $this->redirect(['index']);
    }

    private function findEdit($entita, $id)
    {
        $classi = $this->classi();
        if (!$entita || !$id || !isset($classi[$entita])) {
            return null;
        }
        return $classi[$entita]::findOne((int) $id);
    }
}
