<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sottocommessa;

/**
 * SottocommessaSearch represents the model behind the search form of `app\models\Sottocommessa`.
 */
class SottocommessaSearch extends Sottocommessa
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id_DOSottoCommessa'], 'integer'],
            [['Cd_DOCommessa', 'Cd_DOSottoCommessa', 'Descrizione', 'DescrizioneBreve', 'Cd_CF', 'Cd_DOCommessaStato', 'DataInizio', 'DataFinePresunta', 'DataFineReale', 'NoteDoSottoCommessa', 'UserIns', 'UserUpd', 'TimeIns', 'TimeUpd', 'Ts', 'NoteXML', 'Attributi', 'Sconto', 'Provvigione'], 'safe'],
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
        $query = Sottocommessa::find();

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

if (isset($this->DataFinePresunta) && '' != $this->DataFinePresunta) { //you dont need the if function if yourse sure you have a not null date
    $date_explode = explode(" - ", $this->DataFinePresunta);
    $date1        = trim($date_explode[0]);
    $date2        = trim($date_explode[1]);
    $query->andFilterWhere(['between', 'DataFinePresunta', $date1, $date2]);
}

if (isset($this->DataFineReale) && '' != $this->DataFineReale) { //you dont need the if function if yourse sure you have a not null date
    $date_explode = explode(" - ", $this->DataFineReale);
    $date1        = trim($date_explode[0]);
    $date2        = trim($date_explode[1]);
    $query->andFilterWhere(['between', 'DataFineReale', $date1, $date2]);
}
if (isset($this->DataInizio) && '' != $this->DataInizio) { //you dont need the if function if yourse sure you have a not null date
    $date_explode = explode(" - ", $this->DataInizio);
    $date1        = trim($date_explode[0]);
    $date2        = trim($date_explode[1]);
    $query->andFilterWhere(['between', 'DataInizio', $date1, $date2]);
}


        // grid filtering conditions
        $query->andFilterWhere([
            'Id_DOSottoCommessa' => $this->Id_DOSottoCommessa,
       //     'DataInizio' => $this->DataInizio,
      //      'DataFinePresunta' => $this->DataFinePresunta,
      //      'DataFineReale' => $this->DataFineReale,
            'TimeIns' => $this->TimeIns,
            'TimeUpd' => $this->TimeUpd,
            'Ts' => $this->Ts,
        ]);

        $query->andFilterWhere(['like', 'Cd_DOCommessa', $this->Cd_DOCommessa])
            ->andFilterWhere(['like', 'Cd_DOSottoCommessa', $this->Cd_DOSottoCommessa])
            ->andFilterWhere(['like', 'Descrizione', $this->Descrizione])
            ->andFilterWhere(['like', 'DescrizioneBreve', $this->DescrizioneBreve])
            ->andFilterWhere(['like', 'Cd_CF', $this->Cd_CF])
            ->andFilterWhere(['like', 'Cd_DOCommessaStato', $this->Cd_DOCommessaStato])
            ->andFilterWhere(['like', 'NoteDoSottoCommessa', $this->NoteDoSottoCommessa])
            ->andFilterWhere(['like', 'UserIns', $this->UserIns])
            ->andFilterWhere(['like', 'UserUpd', $this->UserUpd])
            ->andFilterWhere(['like', 'NoteXML', $this->NoteXML])
            ->andFilterWhere(['like', 'Attributi', $this->Attributi])
            ->andFilterWhere(['like', 'Sconto', $this->Sconto])
            ->andFilterWhere(['like', 'Provvigione', $this->Provvigione]);

        return $dataProvider;
    }
}
