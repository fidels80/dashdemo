<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Todomain;

/**
 * TodomainSearch represents the model behind the search form of `app\models\Todomain`.
 */
class TodomainSearch extends Todomain
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user', 'group', 'cd_cli', 'priorita', 'progresso', 'id_padre', 'descrizione', 'data_inizio', 'data_fine', 'data_scadenza', 'stato'], 'safe'],
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
        $query = Todomain::find();

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
            'data_inizio' => $this->data_inizio,
            'data_fine' => $this->data_fine,
            'data_scadenza' => $this->data_scadenza,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'user', $this->user])
            ->andFilterWhere(['like', 'group', $this->group])
            ->andFilterWhere(['like', 'cd_cli', $this->cd_cli])
            ->andFilterWhere(['like', 'priorita', $this->priorita])
            ->andFilterWhere(['like', 'progresso', $this->progresso])
            ->andFilterWhere(['like', 'id_padre', $this->id_padre])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'stato', $this->stato]);

        return $dataProvider;
    }
}
