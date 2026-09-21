<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xruoli;

/**
 * XRuoliSearch represents the model behind the search form of `app\models\Xruoli`.
 */
class XRuoliSearch extends Xruoli
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cd_ruolo', 'descrizione'], 'safe'],
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
        $query = Xruoli::find();

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
        $query->andFilterWhere(['like', 'cd_ruolo', $this->cd_ruolo])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione]);

        return $dataProvider;
    }
}
