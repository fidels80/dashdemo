<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gacspese;

/**
 * GacspeseSearch represents the model behind the search form of `app\models\Gacspese`.
 */
class GacspeseSearch extends Gacspese
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_sub_prv'], 'integer'],
            [['spesa', 'descrizione', 'um', 'note', 'cd_ar', 'descrizionear'], 'safe'],
            [['qta', 'costounitario', 'sconto', 'costonetto', 'ricarico', 'costoricaricato', 'scontovendita', 'valorenettounitario', 'valorenetto', 'margine', 'margineperc', 'prezzoar'], 'number'],
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
        $query = Gacspese::find();

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
            'sconto' => $this->sconto,
            'costonetto' => $this->costonetto,
            'ricarico' => $this->ricarico,
            'costoricaricato' => $this->costoricaricato,
            'scontovendita' => $this->scontovendita,
            'valorenettounitario' => $this->valorenettounitario,
            'valorenetto' => $this->valorenetto,
            'margine' => $this->margine,
            'margineperc' => $this->margineperc,
            'prezzoar' => $this->prezzoar,
        ]);

        $query->andFilterWhere(['like', 'spesa', $this->spesa])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'um', $this->um])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'cd_ar', $this->cd_ar])
            ->andFilterWhere(['like', 'descrizionear', $this->descrizionear]);

        return $dataProvider;
    }
}
