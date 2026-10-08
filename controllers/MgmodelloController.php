<?php

namespace app\controllers;

use Yii;
use app\models\MgArticolo;
use app\models\MgAttributoArticolo;
use app\models\MgModelloTessuto;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Modelli articolo: vista dedicata sui modelli (attributi tipo "modello")
 * con i tessuti associati e gli articoli generati.
 */
class MgmodelloController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $modelli = MgAttributoArticolo::find()
            ->where(['tipo' => MgAttributoArticolo::TIPO_MODELLO])
            ->orderBy(['descrizione' => SORT_ASC])
            ->all();

        $ids = [];
        foreach ($modelli as $m) {
            $ids[] = (int) $m->id;
        }

        $articoliPerModello = [];
        $tessutiPerModello = [];
        if (!empty($ids)) {
            $righe = (new Query())
                ->select(['id_modello', 'n' => 'COUNT(*)'])
                ->from('{{%mg_articolo}}')
                ->where(['id_modello' => $ids])
                ->groupBy('id_modello')
                ->all();
            foreach ($righe as $r) {
                $articoliPerModello[(int) $r['id_modello']] = (int) $r['n'];
            }

            $righe = (new Query())
                ->select(['mt.id_modello', 'a.descrizione'])
                ->from(['mt' => '{{%mg_modello_tessuto}}'])
                ->leftJoin(['a' => '{{%mg_attributo_articolo}}'], 'a.id = mt.id_tessuto')
                ->where(['mt.id_modello' => $ids])
                ->orderBy(['a.descrizione' => SORT_ASC])
                ->all();
            foreach ($righe as $r) {
                $tessutiPerModello[(int) $r['id_modello']][] = $r['descrizione'];
            }
        }

        return $this->render('index', [
            'modelli' => $modelli,
            'articoliPerModello' => $articoliPerModello,
            'tessutiPerModello' => $tessutiPerModello,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $tessuti = MgModelloTessuto::find()
            ->alias('mt')
            ->with('tessuto')
            ->joinWith(['tessuto t'])
            ->where(['mt.id_modello' => $id])
            ->orderBy(['t.descrizione' => SORT_ASC])
            ->all();

        $articoli = MgArticolo::find()
            ->with(['tessuto', 'colore', 'taglia'])
            ->where(['id_modello' => $id])
            ->orderBy(['codice' => SORT_ASC])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'tessuti' => $tessuti,
            'articoli' => $articoli,
            'matrice' => MgArticolo::matriceTaglie($id),
        ]);
    }

    public function actionCreate($from = null)
    {
        $model = new MgAttributoArticolo();
        $model->tipo = MgAttributoArticolo::TIPO_MODELLO;
        $model->attivo = true;

        $source = null;
        if ($from !== null) {
            $source = MgAttributoArticolo::findOne((int) $from);
            if ($source !== null) {
                $model = \app\components\Duplicate::copy($source);
                $model->tipo = MgAttributoArticolo::TIPO_MODELLO;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MgModelloTessuto::sincronizza($model->id, Yii::$app->request->post('tessuti', []));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'tessuti' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TESSUTO),
            'tessutiSelezionati' => $source !== null && !Yii::$app->request->isPost
                ? array_map('intval', MgModelloTessuto::idTessutiPerModello($source->id))
                : [],
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MgModelloTessuto::sincronizza($model->id, Yii::$app->request->post('tessuti', []));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'tessuti' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TESSUTO),
            'tessutiSelezionati' => array_map('intval', MgModelloTessuto::idTessutiPerModello($model->id)),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (MgArticolo::find()->where(['id_modello' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare il modello: è utilizzato da uno o più articoli.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = MgAttributoArticolo::findOne(['id' => $id, 'tipo' => MgAttributoArticolo::TIPO_MODELLO]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Modello non trovato.');
    }
}
