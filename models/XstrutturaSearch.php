<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xstruttura;

/**
 * XstrutturaSearch represents the model behind the search form of `app\models\Xstruttura`.
 */
class XstrutturaSearch extends Xstruttura
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['Struttura', 'Descrizione', 'Citta', 'Cd_cf', 'Partitaiva', 'xcheck'], 'safe'],
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
        $query = Xstruttura::find();

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

        $query->andFilterWhere(['like', 'Struttura', $this->Struttura])
            ->andFilterWhere(['like', 'Descrizione', $this->Descrizione])
            ->andFilterWhere(['like', 'Citta', $this->Citta])
            ->andFilterWhere(['like', 'Cd_cf', $this->Cd_cf])
            ->andFilterWhere(['like', 'Partitaiva', $this->Partitaiva])
            ->andFilterWhere(['like', 'xcheck', $this->xcheck]);

        return $dataProvider;
    }
}
