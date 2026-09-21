<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Agentifiles;

/**
 * AgentifilesSearch represents the model behind the search form of `app\models\Agentifiles`.
 */
class AgentifilesSearch extends Agentifiles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'kiave_arch'], 'integer'],
            [['cd_agente', 'descrizione', 'nota', 'cartella', 'cartella_padre', 'f_content', 'nome_file', 'estenzione', 'uplfile', 'file'], 'safe'],
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
        $query = Agentifiles::find();

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
            'kiave_arch' => $this->kiave_arch,
        ]);

        $query->andFilterWhere(['like', 'cd_agente', $this->cd_agente])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'nota', $this->nota])
            ->andFilterWhere(['like', 'cartella', $this->cartella])
            ->andFilterWhere(['like', 'cartella_padre', $this->cartella_padre])
            ->andFilterWhere(['like', 'f_content', $this->f_content])
            ->andFilterWhere(['like', 'nome_file', $this->nome_file])
            ->andFilterWhere(['like', 'estenzione', $this->estenzione])
            ->andFilterWhere(['like', 'uplfile', $this->uplfile])
            ->andFilterWhere(['like', 'file', $this->file]);

        return $dataProvider;
    }
}
