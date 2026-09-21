<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xtravelhead;

/**
 * XtravelheadSearch represents the model behind the search form of `app\models\Xtravelhead`.
 */
class XtravelheadSearch extends Xtravelhead
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['th_id', 'evaso_A', 'evaso_p','fatturato','bloccato'], 'integer'],
            [['datath', 'numero', 'descrizione', 'timeins', 'x_cd_cf', 'x_cfdesk'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */

    public function search($params)
    {
        $userId =  \Yii::$app->user->id;
        $user = \app\models\User::findOne($userId);

        // Inizializziamo la query UNA SOLA VOLTA
        $query = Xtravelhead::find();

        // 1. Logica di visibilità basata sui permessi (LIVELLO 70)
        if ($user && $user->level === 70) {
            // --- Filtro per Clienti (cd_cli) ---
            $cdCliList = $user->cd_cli;
            if ($cdCliList && @unserialize($cdCliList) !== false) {
                $cdCliList = unserialize($cdCliList);
            }
            if (!is_array($cdCliList)) {
                $cdCliList = array_filter([$cdCliList]);
            }

            if (!empty($cdCliList)) {
                $query->andWhere(['x_cd_cf' => $cdCliList]);
            }

            if ($user->istourmanager) {
                // Invece di usare joinWith (che tira in ballo la tabella user), 
                // usiamo una subquery diretta e leggerissima sulla tabella ponte!
                $subQuery = (new \yii\db\Query())
                    ->select('th_id')
                    ->from('adb_auxcoop.dbo.xtravel_managers')
                    ->where(['user_id' => $userId]);

                // Diciamo alla query principale: "prendi solo le pratiche (th_id) che sono in quella lista"
                $query->andWhere(['th_id' => $subQuery]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['datath' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 2. Filtri della GridView
        $query->andFilterWhere([
            'th_id' => $this->th_id,
            'datath' => $this->datath,
            'timeins' => $this->timeins,
         //   'evaso_A' => $this->evaso_A,
         //   'evaso_p' => $this->evaso_p,
            'fatturato' => $this->fatturato,
            'bloccato' => $this->bloccato,
        ]);

        $query->andFilterWhere(['like', 'numero', $this->numero])
            ->andFilterWhere(['like', 'x_cd_cf', $this->x_cd_cf])
            ->andFilterWhere(['like', 'x_cfdesk', $this->x_cfdesk])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }
    public function  ori_search($params)
    {
/*
if (unserialize($model->cd_cli)==false){
    $cli=$model->cd_cli;
}else{

$cli=unserialize($model->cd_cli);

}

*/
$userId = \Yii::$app->user->id;

    // Recupera il valore di cd_cli dall'utente loggato
    $user = \app\models\User::findOne($userId);
    
     if ( $user->level===70) {
    $cdCliList = $user ? $user->cd_cli : null;

    // Se cd_cli è serializzato, lo deserializziamo
    if ($cdCliList && @unserialize($cdCliList) !== false) {
        $cdCliList = unserialize($cdCliList);
    }
if (!is_array($cdCliList)) {
        $cdCliList = [$cdCliList]; // Trasformiamo in array anche un valore singolo
    }

             }else {
                $cdCliList =[];
             }
                     $query = Xtravelhead::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'th_id' => $this->th_id,
            'datath' => $this->datath,
            'timeins' => $this->timeins,
            'evaso_A' => $this->evaso_A,
            'evaso_p' => $this->evaso_p,
          // 'fatturato'=>$this->fatturato
            

        ]);

        $query->andFilterWhere(['like', 'numero', $this->numero])
        ->andFilterWhere(['like', 'x_cd_cf', $this->x_cd_cf])
        ->andFilterWhere(['like', 'x_cfdesk', $this->x_cfdesk])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
              ->andFilterWhere(['fatturato'=> $this->fatturato])
              ->andFilterWhere(['bloccato'=> $this->bloccato])            ;

if (!empty($cdCliList)) {
        $query->andFilterWhere(['x_cd_cf' => $cdCliList]);
    }

    
        return $dataProvider;
    }
}
