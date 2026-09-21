<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Todorelgrpusr;

/**
 * TodorelgrpusrSearch represents the model behind the search form of `app\models\Todorelgrpusr`.
 */
class TodorelgrpusrSearch extends Todorelgrpusr
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_gruppo', 'id_user', 'id'], 'integer'],
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
        $query = Todorelgrpusr::find();

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
            'id_gruppo' => $this->id_gruppo,
            'id_user' => $this->id_user,
            'id' => $this->id,
        ]);

        return $dataProvider;
    }
}
