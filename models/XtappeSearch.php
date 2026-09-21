<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xtappe;

/**
 * XtappeSearch represents the model behind the search form of `app\models\Xtappe`.
 */
class XtappeSearch extends Xtappe
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_tappa', 'data', 'citta'], 'safe'],
            [['th_id'], 'integer'],
            [['evaso'], 'boolean'],
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
        $query = Xtappe::find();

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
            'data' => $this->data,
            'evaso' => $this->evaso,
        ]);

        $query->andFilterWhere(['like', 'id_tappa', $this->id_tappa])
            ->andFilterWhere(['like', 'citta', $this->citta]);

        return $dataProvider;
    }
}
