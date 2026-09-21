<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gacmateriali;

/**
 * GacmaterialiSearch represents the model behind the search form of `app\models\Gacmateriali`.
 */
class GacmaterialiSearch extends Gacmateriali
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_sub_prv'], 'integer'],
            [['listino', 'cd_ar', 'descrizione', 'um', 'note'], 'safe'],
            [['qta', 'costounitario', 'scontoacq', 'costounitscontato', 'ricarico', 'costounitarioric', 'sconto_vendita', 'valvendita', 'margine', 'margineperc', 'prezzounitarionetto'], 'number'],
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
        $query = Gacmateriali::find();

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
            'id_sub_prv' => $this->id_sub_prv,
            'qta' => $this->qta,
            'costounitario' => $this->costounitario,
            'scontoacq' => $this->scontoacq,
            'costounitscontato' => $this->costounitscontato,
            'ricarico' => $this->ricarico,
            'costounitarioric' => $this->costounitarioric,
            'sconto_vendita' => $this->sconto_vendita,
            'valvendita' => $this->valvendita,
            'margine' => $this->margine,
            'margineperc' => $this->margineperc,
            'prezzounitarionetto' => $this->prezzounitarionetto,
        ]);

        $query->andFilterWhere(['like', 'listino', $this->listino])
            ->andFilterWhere(['like', 'cd_ar', $this->cd_ar])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'um', $this->um])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
