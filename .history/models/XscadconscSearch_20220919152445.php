<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xscadconsc;
use yii;
/**
 * XscadconscSearch represents the model behind the search form of `app\models\Xscadconsc`.
 */
class XscadconscSearch extends Xscadconsc
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_CGConto_Banca', 'DataScadenza', 'Cd_CF', 'Descrizione', 'DataFattura', 'NumFattura', 'TipoRata', 'Cd_VL', 'FTE_TipoPagamento', 'Stato_Cli', 'Settore_Cli', 'cd_sottocommessa'], 'safe'],
            [['Emessa', 'Contabilizzata', 'Insoluta', 'Pagata', 'id'], 'integer'],
            [['ImportoE', 'ImportoV', 'ImportoDaPagareE', 'ImportoDaPagareV', 'ImportoDare', 'ImportoAvere', 'Saldo'], 'number'],
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
       // $query = Xscadconsc::find();



if (Yii::$app->user->identity->level != 100) {
    $cf = Yii::$app->user->identity->cd_cli;
    $query =Xscadconsc::find()

        ->where(['cd_cf' => $cf]);
        //->orWhere(['altcli' => $cf]);
} else {
    $query = Xscadconsc::find();

}



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
            'DataScadenza' => $this->DataScadenza,
            'DataFattura' => $this->DataFattura,
            'Emessa' => $this->Emessa,
            'Contabilizzata' => $this->Contabilizzata,
            'Insoluta' => $this->Insoluta,
            'ImportoE' => $this->ImportoE,
            'ImportoV' => $this->ImportoV,
            'ImportoDaPagareE' => $this->ImportoDaPagareE,
            'ImportoDaPagareV' => $this->ImportoDaPagareV,
            'Pagata' => $this->Pagata,
            'ImportoDare' => $this->ImportoDare,
            'ImportoAvere' => $this->ImportoAvere,
            'Saldo' => $this->Saldo,
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'Cd_CGConto_Banca', $this->Cd_CGConto_Banca])
            ->andFilterWhere(['like', 'Cd_CF', $this->Cd_CF])
            ->andFilterWhere(['like', 'Descrizione', $this->Descrizione])
            ->andFilterWhere(['like', 'NumFattura', $this->NumFattura])
            ->andFilterWhere(['like', 'TipoRata', $this->TipoRata])
            ->andFilterWhere(['like', 'Cd_VL', $this->Cd_VL])
            ->andFilterWhere(['like', 'FTE_TipoPagamento', $this->FTE_TipoPagamento])
            ->andFilterWhere(['like', 'Stato_Cli', $this->Stato_Cli])
            ->andFilterWhere(['like', 'Settore_Cli', $this->Settore_Cli])
            ->andFilterWhere(['like', 'cd_sottocommessa', $this->cd_sottocommessa]);

        return $dataProvider;
    }
}
