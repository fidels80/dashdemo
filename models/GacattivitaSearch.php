<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gacattivita;

/**
 * GacattivitaSearch represents the model behind the search form of `app\models\Gacattivita`.
 */
class GacattivitaSearch extends Gacattivita
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_attivita', 'id_sub_prv', 'sequenza'], 'integer'],
            [['attivita', 'descrizione', 'um', 'risorsa', 'note', 'data_apertura', 'data_chiusura'], 'safe'],
            [['tempo', 'ore', 'costo', 'sconto', 'costo_scontato', 'ricarico', 'costo_ricarico', 'sconto_vendita', 'valore_costounitario', 'valore_costotot', 'margine', 'margine_perc'], 'number'],
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
        $query = Gacattivita::find();

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
            'id_attivita' => $this->id_attivita,
            'id_sub_prv' => $this->id_sub_prv,
            'sequenza' => $this->sequenza,
            'tempo' => $this->tempo,
            'ore' => $this->ore,
            'costo' => $this->costo,
            'sconto' => $this->sconto,
            'costo_scontato' => $this->costo_scontato,
            'ricarico' => $this->ricarico,
            'costo_ricarico' => $this->costo_ricarico,
            'sconto_vendita' => $this->sconto_vendita,
            'valore_costounitario' => $this->valore_costounitario,
            'valore_costotot' => $this->valore_costotot,
            'margine' => $this->margine,
            'margine_perc' => $this->margine_perc,
            'data_apertura' => $this->data_apertura,
            'data_chiusura' => $this->data_chiusura,
        ]);

        $query->andFilterWhere(['like', 'attivita', $this->attivita])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'um', $this->um])
            ->andFilterWhere(['like', 'risorsa', $this->risorsa])
            ->andFilterWhere(['like', 'note', $this->note]);

        return $dataProvider;
    }
}
