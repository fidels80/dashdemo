<?php

namespace app\controllers;

use Yii;
use app\models\DashPermesso;
use app\models\DashPermessoUtente;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\AccessControl;

/**
 * Gestione permessi per form/funzione (ACL) assegnati per utente.
 */
class DashpermessoController extends Controller
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
            'query' => DashPermesso::find()->orderBy(['gruppo' => SORT_ASC, 'ordine' => SORT_ASC]),
            'pagination' => ['pageSize' => 100],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new DashPermesso();
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = new \yii\db\Expression('GETDATE()');
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        DashPermessoUtente::deleteAll(['permesso_id' => $id]);
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Matrice permessi (vista/crea/modifica/elimina) per un utente.
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
                'permessi' => [],
                'assegnati' => [],
                'isSuper' => false,
            ]);
        }

        $user = User::findOne($user_id);
        if (!$user) {
            throw new NotFoundHttpException('Utente non trovato.');
        }

        $isSuper = AccessControl::isSuper($user);

        if (Yii::$app->request->isPost && !$isSuper) {
            $post = Yii::$app->request->post('perm', []);

            DashPermessoUtente::deleteAll(['user_id' => $user_id]);
            foreach ((array) $post as $permessoId => $flags) {
                $rel = new DashPermessoUtente();
                $rel->user_id = $user_id;
                $rel->permesso_id = (int) $permessoId;
                $rel->can_view = !empty($flags['view']) ? 1 : 0;
                $rel->can_create = !empty($flags['create']) ? 1 : 0;
                $rel->can_update = !empty($flags['update']) ? 1 : 0;
                $rel->can_delete = !empty($flags['delete']) ? 1 : 0;
                $rel->save(false);
            }

            Yii::$app->session->setFlash('success', 'Permessi salvati.');
            return $this->redirect(['assegna', 'user_id' => $user_id]);
        }

        return $this->render('assegna', [
            'users' => $users,
            'user_id' => (int) $user_id,
            'user' => $user,
            'permessi' => DashPermesso::find()->orderBy(['gruppo' => SORT_ASC, 'ordine' => SORT_ASC])->all(),
            'assegnati' => DashPermessoUtente::getByUser($user_id),
            'isSuper' => $isSuper,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = DashPermesso::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Risorsa non trovata.');
    }
}
