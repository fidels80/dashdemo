<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Uecanagrafica;

/**
 * UecanagraficaSearch represents the model behind the search form of `app\models\Uecanagrafica`.
 */
class UecanagraficaSearch extends Uecanagrafica
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'cognome', 'indirizzo', 'citta', 'provincia', 'nazione', 'codicefiscale'], 'safe'],
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
        $query = Uecanagrafica::find();

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

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'cognome', $this->cognome])
            ->andFilterWhere(['like', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['like', 'citta', $this->citta])
            ->andFilterWhere(['like', 'provincia', $this->provincia])
            ->andFilterWhere(['like', 'nazione', $this->nazione])
            ->andFilterWhere(['like', 'codicefiscale', $this->codicefiscale]);

        return $dataProvider;
    }
}
