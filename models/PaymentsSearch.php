<?php

namespace app\models;
use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payments;

/**
 * PaymentsSearch represents the model behind the search form of `app\models\Payments`.
 */
class PaymentsSearch extends Payments
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'xid_testa', 'Pagata', 'NumEffetto', 'TotEffetti'], 'integer'],
            [['cd_cli', 'Cd_PG', 'DataScadenza', 'DataPagamento', 'DataFattura', 'NumFattura', 'Protocollo'], 'safe'],
            [['ImportoV', 'IncassoV'], 'number'],
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
        $cf=  Yii::$app->user->identity->cd_cli;
        $query = Payments::find()
        ->where(['cd_cli'=>$cf]);
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
            'xid_testa' => $this->xid_testa,
        //    'DataScadenza' => $this->DataScadenza,
            //'DataPagamento' => $this->DataPagamento,
            //'DataFattura' => $this->DataFattura,
            'Pagata' => $this->Pagata,
            'NumEffetto' => $this->NumEffetto,
            'TotEffetti' => $this->TotEffetti,
            'ImportoV' => $this->ImportoV,
            'IncassoV' => $this->IncassoV,
        ]);
        if(isset ($this->DataScadenza)&&$this->DataScadenza!=''){ //you dont need the if function if yourse sure you have a not null date
            $date_explode=explode(" - ",$this->DataScadenza);
            $date1=trim($date_explode[0]);
            $date2=trim($date_explode[1]);
            $query->andFilterWhere(['between','DataScadenza',$date1,$date2]);
          }

          if(isset ($this->DataPagamento)&&$this->DataPagamento!=''){ //you dont need the if function if yourse sure you have a not null date
            $date_explode=explode(" - ",$this->DataPagamento);
            $date1=trim($date_explode[0]);
            $date2=trim($date_explode[1]);
            $query->andFilterWhere(['between','DataPagamento',$date1,$date2]);
          }
          if(isset ($this->DataFattura)&&$this->DataFattura!=''){ //you dont need the if function if yourse sure you have a not null date
            $date_explode=explode(" - ",$this->DataFattura);
            $date1=trim($date_explode[0]);
            $date2=trim($date_explode[1]);
            $query->andFilterWhere(['between','DataFattura',$date1,$date2]);
          }







            $query->andFilterWhere(['like', 'cd_cli', $this->cd_cli])
            ->andFilterWhere(['like', 'Cd_PG', $this->Cd_PG])
            ->andFilterWhere(['like', 'NumFattura', $this->NumFattura])
            ->andFilterWhere(['like', 'Protocollo', $this->Protocollo]);

        return $dataProvider;
    }
}
