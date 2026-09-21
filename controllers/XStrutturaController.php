<?php

namespace app\controllers;

use Yii;
use app\models\Xstruttura;
use app\models\XstrutturaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\XRoomlist;
use  app\models\Xtravelrow;
/**
 * XstrutturaController implements the CRUD actions for Xstruttura model.
 */
class XstrutturaController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * Lists all Xstruttura models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XstrutturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // --- MODIFICA QUI ---
        // Aggiungiamo la selezione della descrizione dalla tabella anagrafica
        $dataProvider->query->select([
            'x_struttura.*', // Prende tutti i campi della struttura
            'anag.Descrizione AS nome_fornitore' // Prende la descrizione e la nomina 'nome_fornitore'
        ])
            ->leftJoin('adb_auxcoop.dbo.cf anag', 'x_struttura.Cd_cf = anag.Cd_cf') // Collega le tabelle
            ->andWhere(['not', ['Struttura' => null]])
            ->andWhere(['<>', 'Struttura', ''])
            ->andWhere(['<>', 'Struttura', ' ']);

        $dataProvider->pagination = false;

        // 1. Carichiamo le strutture (ora con il campo nome_fornitore incluso)
        $strutture = $dataProvider->query->asArray()->all();

        // 2. Carichiamo i viaggi con la descrizione (JOIN tra row e head)
        // Usiamo una query pulita che il DB digerisce in un attimo
        $travelQuery = (new \yii\db\Query())
            ->select([
                'tr.th_id',
                'tr.struttura',
                'th.descrizione as viaggio_desc',
            'th.datath as datap',
            'th.fatturato'
            ])
            ->from('adb_auxcoop.dbo.xtravelrow tr')
            ->innerJoin('adb_auxcoop.dbo.xtravelhead th', 'tr.th_id = th.th_id')
            ->distinct() // Evita di caricare 10 volte lo stesso hotel per lo stesso th_id
            ->all();

        foreach ($strutture as &$s) {
            $s['viaggi'] = [];

            // 1. Puliamo e rendiamo minuscolo il nome della struttura cercata
            // Usiamo mb_strtolower per gestire anche caratteri accentati
            $nomeStrutturaCercata = mb_strtolower($s['Struttura'], 'UTF-8');
            $nomePulito = str_replace('hotel ', '', $nomeStrutturaCercata);

            foreach ($travelQuery as $t) {
                // 2. Rendiamo minuscolo il valore che arriva da xtravelrow
                $valoreTabellaViaggi = mb_strtolower($t['struttura'], 'UTF-8');

                if (
                    $valoreTabellaViaggi == mb_strtolower($s['id'], 'UTF-8') ||
                    $valoreTabellaViaggi == $nomeStrutturaCercata ||
                    ($valoreTabellaViaggi !== null && strpos($valoreTabellaViaggi, $nomePulito) !== false)
                ) {
                    $s['viaggi'][$t['th_id']] = [
                        'id' => $t['th_id'],
                        'desc' => $t['viaggio_desc'],
                        'datap'=> $t['datap'],
                        'fatturato'=>$t['fatturato']
                    ];
                }
            }
            $s['viaggi'] = array_values($s['viaggi']);
        }

        // $roomlistData = \app\models\XRoomlist::find()->asArray()->all();
        // Usiamo una JOIN per caricare subito descrizione e data da xtravelhead
        $roomlistData = \app\models\XRoomlist::find()
            ->select([
                'x_roomlist.*',
                'th.descrizione AS viaggio_desc',
                'th.datath AS viaggio_data'
            ])
            ->leftJoin('adb_auxcoop.dbo.xtravelhead th', 'x_roomlist.th_id = th.th_id')
            ->asArray()
            ->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'struttureData' => $strutture, // <--- Passiamo questa variabile!
            'roomlistData' => $roomlistData,
        ]);
    }
    /**
     * Displays a single Xstruttura model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Xstruttura model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xstruttura();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                // Se è una richiesta AJAX, ritorna JSON
                if (Yii::$app->request->isAjax) {
                    return $this->asJson([
                        'success' => true,
                        'message' => 'Struttura creata con successo'
                    ]);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Se è una richiesta AJAX, ritorna solo la form senza layout
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('createaj', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Xstruttura model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Xstruttura model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Xstruttura model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Xstruttura the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xstruttura::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionXcaricacitta($q = null)
    {
        $db = Yii::$app->db5;
        $query = new \yii\db\Query;
        $query->select(['cd_citta as id', 'descrizione as text'])
            ->from('x_citta')
            ->where(['like', 'descrizione', $q]);
        // ->limit(20);

        $command = $query->createCommand($db);
        $data = $command->queryAll();
        //var_dump($data); // Aggiungi questa linea per verificare
        //die();


        return \yii\helpers\Json::encode(['items' => $data]);
    }
}
