<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Gacsottoprv;

/**
 * GacsottoprvSearch represents the model behind the search form of `app\models\Gacsottoprv`.
 */
class GacsottoprvSearch extends Gacsottoprv
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sub_prv', 'id_prv', 'tipologia', 'stato'], 'integer'],
            [['descrizione', 'note', 'sottocommessa', 'datacreazione', 'inizioval', 'fineval', 'apertura', 'chiusura', 'apertura_pianificata', 'chiusura_pianificata', 'datastato'], 'safe'],
            [['probacq', 'provvigione'], 'number'],
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
        $query = Gacsottoprv::find();

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
            'id_sub_prv' => $this->id_sub_prv,
            'id_prv' => $this->id_prv,
            'tipologia' => $this->tipologia,
            'datacreazione' => $this->datacreazione,
            'inizioval' => $this->inizioval,
            'fineval' => $this->fineval,
            'probacq' => $this->probacq,
            'provvigione' => $this->provvigione,
            'apertura' => $this->apertura,
            'chiusura' => $this->chiusura,
            'apertura_pianificata' => $this->apertura_pianificata,
            'chiusura_pianificata' => $this->chiusura_pianificata,
            'stato' => $this->stato,
            'datastato' => $this->datastato,
        ]);

        $query->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'sottocommessa', $this->sottocommessa]);

        return $dataProvider;
    }
}
