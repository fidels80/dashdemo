<?php

namespace app\models;
use yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Rapportini;
 
/**
 * RapportiniSearch represents the model behind the search form of `app\models\Rapportini`.
 */
class RapportiniSearch extends Rapportini
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cd_cli', 'commessa', 'data', 'ora_in', 'ora_out', 'note', 'cd_art', 'des_art'], 'safe'],
            [['qta'], 'number'],
            [['numero', 'userid'], 'integer'],
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


        if (Yii::$app->user->identity->level != 100) {
    $cf = Yii::$app->user->identity->cd_cli;
    $query = Rapportini::find()
        ->where(['cd_cli' => $cf])
        ->orWhere(['altcli' => $cf]);
} else {
    $query = Rapportini::find();

}

      //  $query = Rapportini::find();

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
            'qta' => $this->qta,
            'data' => $this->data,
            'ora_in' => $this->ora_in,
            'ora_out' => $this->ora_out,
            'numero' => $this->numero,
            'userid' => $this->userid,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'cd_cli', $this->cd_cli])
            ->andFilterWhere(['like', 'altcli', $this->altcli])
            
            ->andFilterWhere(['like', 'commessa', $this->commessa])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'cd_art', $this->cd_art])
            ->andFilterWhere(['like', 'des_art', $this->des_art]);

        return $dataProvider;
    }
}
