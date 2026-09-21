<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xvenue;

/**
 * XvenueSearch represents the model behind the search form of `app\models\Xvenue`.
 */
class XvenueSearch extends Xvenue
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'venue', 'citta'], 'safe'],
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
        $query = Xvenue::find();

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
        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'venue', $this->venue])
            ->andFilterWhere(['like', 'citta', $this->citta]);

        return $dataProvider;
    }
}
