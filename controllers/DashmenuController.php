<?php

namespace app\controllers;

use Yii;
use app\models\DashMenu;
use app\models\DashMenuUtente;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\AccessControl;

/**
 * Gestione del menu laterale dinamico (voci, sottovoci e assegnazione agli utenti).
 */
class DashmenuController extends Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => DashMenu::find()
                ->orderBy(['genitore_id' => SORT_ASC, 'ordine' => SORT_ASC, 'label' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new DashMenu();
        $model->livello_min = 0;
        $model->ordine = 0;
        $model->per_tutti = true;
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = new \yii\db\Expression('GETDATE()');
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'genitori' => $this->getGenitoriList(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'genitori' => $this->getGenitoriList($model->id),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Elimina anche le assegnazioni e scollega i figli (che diventano voci radice)
        DashMenuUtente::deleteAll(['menu_id' => $id]);
        DashMenu::updateAll(['genitore_id' => null], ['genitore_id' => $id]);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Assegnazione delle voci di menu a un utente.
     */
    public function actionAssegna($user_id = null)
    {
        $users = ArrayHelper::map(
            User::find()->orderBy(['username' => SORT_ASC])->all(),
            'id',
            function ($u) {
                return $u->username . (!empty($u->email) ? ' (' . $u->email . ')' : '');
            }
        );

        if (empty($user_id)) {
            return $this->render('assegna', [
                'users' => $users,
                'user_id' => null,
                'user' => null,
                'menuItems' => [],
                'assigned' => [],
                'isSuper' => false,
                'saved' => false,
            ]);
        }

        $user = User::findOne($user_id);
        if (!$user) {
            throw new NotFoundHttpException('Utente non trovato.');
        }

        $isSuper = AccessControl::isSuper($user);

        if (Yii::$app->request->isPost && !$isSuper) {
            $checked = Yii::$app->request->post('menu', []);
            DashMenuUtente::deleteAll(['user_id' => $user_id]);
            foreach ((array) $checked as $menuId) {
                $rel = new DashMenuUtente();
                $rel->user_id = $user_id;
                $rel->menu_id = (int) $menuId;
                $rel->save(false);
            }
            Yii::$app->session->setFlash('success', 'Assegnazioni salvate.');
            return $this->redirect(['assegna', 'user_id' => $user_id]);
        }

        $menuItems = DashMenu::find()
            ->orderBy(['genitore_id' => SORT_ASC, 'ordine' => SORT_ASC, 'label' => SORT_ASC])
            ->all();

        return $this->render('assegna', [
            'users' => $users,
            'user_id' => (int) $user_id,
            'user' => $user,
            'menuItems' => $menuItems,
            'assigned' => DashMenuUtente::getMenuIdsForUser($user_id),
            'isSuper' => $isSuper,
            'saved' => Yii::$app->session->hasFlash('success'),
        ]);
    }

    /**
     * Elenco voci utilizzabili come genitore (con indentazione per livello).
     */
    private function getGenitoriList($escludiId = null)
    {
        $all = DashMenu::find()
            ->orderBy(['genitore_id' => SORT_ASC, 'ordine' => SORT_ASC, 'label' => SORT_ASC])
            ->all();

        $byParent = [];
        foreach ($all as $it) {
            $byParent[(int) $it->genitore_id][] = $it;
        }

        $result = [];
        $this->flatten($byParent, 0, 0, $escludiId, $result);
        return $result;
    }

    private function flatten(&$byParent, $parentId, $depth, $escludiId, &$result)
    {
        if (empty($byParent[(int) $parentId])) {
            return;
        }
        foreach ($byParent[(int) $parentId] as $it) {
            if ($escludiId && (int) $it->id === (int) $escludiId) {
                continue; // evita di scegliere se stessa come genitore
            }
            $result[$it->id] = str_repeat('— ', $depth) . $it->label;
            $this->flatten($byParent, $it->id, $depth + 1, $escludiId, $result);
        }
    }

    protected function findModel($id)
    {
        if (($model = DashMenu::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Voce di menu non trovata.');
    }
}
