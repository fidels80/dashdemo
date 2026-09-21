<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Stackposttags;

/**
 * StackposttagsSearch represents the model behind the search form of `app\models\Stackposttags`.
 */
class StackposttagsSearch extends Stackposttags
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['somma', 'tagname'], 'safe'],
            [['id'], 'integer'],
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
        $query = Stackposttags::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'somma', $this->somma])
            ->andFilterWhere(['like', 'tagname', $this->tagname]);

        return $dataProvider;
    }
}
