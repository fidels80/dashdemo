<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xparty;

/**
 * XpartySearch represents the model behind the search form of `app\models\Xparty`.
 */
class XpartySearch extends Xparty
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cd_party', 'descrizione'], 'safe'],
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
        $query = Xparty::find();

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
        $query->andFilterWhere(['like', 'cd_party', $this->cd_party])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }
}
