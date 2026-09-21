<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xroomlist;

/**
 * XroomlistSearch represents the model behind the search form of `app\models\Xroomlist`.
 */
class XroomlistSearch extends Xroomlist
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_guest', 'nominativo', 'cd_ar', 'note', 'ruolo', 'party', 'commessa'], 'safe'],
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
        $query = Xroomlist::find();

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
            'evaso' => $this->evaso,
        ]);

        $query->andFilterWhere(['like', 'id_guest', $this->id_guest])
            ->andFilterWhere(['like', 'nominativo', $this->nominativo])
            ->andFilterWhere(['like', 'cd_ar', $this->cd_ar])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'ruolo', $this->ruolo])
            ->andFilterWhere(['like', 'party', $this->party])
            ->andFilterWhere(['like', 'commessa', $this->commessa]);

        return $dataProvider;
    }
}
