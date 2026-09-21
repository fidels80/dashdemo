<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Uectesta;

/**
 * UectestaSearch represents the model behind the search form of `app\models\Uectesta`.
 */
class UectestaSearch extends Uectesta
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'data'], 'safe'],
            [['numero', 'cliente'], 'integer'],
            [['esportato'], 'boolean'],
            [['tipopag'],'string']
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
        $query = Uectesta::find();

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
            'data' => $this->data,
            'numero' => $this->numero,
            'cliente' => $this->cliente,
            'esportato' => $this->esportato,
            'tipopag'=>$this->tipopag
        ]);

        $query->andFilterWhere(['like', 'id', $this->id]);

        return $dataProvider;
    }
}
