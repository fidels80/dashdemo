<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sc;

/**
 * ScSearch represents the model behind the search form of `app\models\Sc`.
 */
class ScSearch extends Sc
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id_SC', 'Id_CGMovT', 'Id_DOTes', 'Id_SCDistinta', 'Id_SC_P_Split', 'Id_SC_P_Ins', 'Id_SC_P_Gruppo', 'Id_SCGruppo', 'TipoGruppo', 'Decimali', 'PartAnno', 'Bloccata', 'Emessa', 'Contabilizzata', 'Pagata', 'Insoluta', 'Compensata', 'RiemessaSuInsoluto', 'NumEffetto', 'TotEffetti', 'Girate', 'Sollecito', 'ProvvisorioDaDocumento', 'Riconciliato', 'Id_RBTes', 'Provvisorio'], 'integer'],
            [['Tipolink', 'Cd_CF', 'Cd_CGConto_Banca', 'Cd_CGConto_Portafoglio', 'Cd_CGConto_InPortafoglio', 'Cd_CGConto_InSbf', 'Cd_VL', 'Cd_PG', 'Descrizione', 'DataScadenza', 'DataPagamento', 'DataFattura', 'NumFattura', 'Protocollo', 'PartNum', 'TipoRata', 'NoteSC', 'Piazza', 'Traente', 'Cd_SL', 'DataUltimoSollecito', 'DataValuta', 'Cd_Simulazione', 'Iban', 'BicCode', 'Cd_Abicab', 'ContoCorrente', 'Cin_It', 'UserIns', 'UserUpd', 'TimeIns', 'TimeUpd', 'Ts', 'DataRivalutazione', 'NoteXML', 'CIG', 'CUP', 'SDD_IdMandato', 'SDD_DtMandato', 'SDD_SqMandato', 'ExtraInfo', 'FTE_TipoPagamento'], 'safe'],
            [['Cambio', 'ImportoE', 'ImportoV', 'EmessoV', 'EmessoE', 'IncassoV', 'PercImponibile', 'PercImposta', 'PercProvvigione', 'CambioStorico', 'xQuotaV_RA', 'xQuotaV_RE', 'xQuotaE_RA', 'xQuotaE_RE', 'xImportoVNettoRitenute', 'xImportoENettoRitenute'], 'number'],
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
        $query = Sc::find();

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
            'Id_SC' => $this->Id_SC,
            'Id_CGMovT' => $this->Id_CGMovT,
            'Id_DOTes' => $this->Id_DOTes,
            'Id_SCDistinta' => $this->Id_SCDistinta,
            'Id_SC_P_Split' => $this->Id_SC_P_Split,
            'Id_SC_P_Ins' => $this->Id_SC_P_Ins,
            'Id_SC_P_Gruppo' => $this->Id_SC_P_Gruppo,
            'Id_SCGruppo' => $this->Id_SCGruppo,
            'TipoGruppo' => $this->TipoGruppo,
            'Cambio' => $this->Cambio,
            'Decimali' => $this->Decimali,
            'DataScadenza' => $this->DataScadenza,
            'DataPagamento' => $this->DataPagamento,
            'DataFattura' => $this->DataFattura,
            'PartAnno' => $this->PartAnno,
            'Bloccata' => $this->Bloccata,
            'Emessa' => $this->Emessa,
            'Contabilizzata' => $this->Contabilizzata,
            'Pagata' => $this->Pagata,
            'Insoluta' => $this->Insoluta,
            'Compensata' => $this->Compensata,
            'RiemessaSuInsoluto' => $this->RiemessaSuInsoluto,
            'NumEffetto' => $this->NumEffetto,
            'TotEffetti' => $this->TotEffetti,
            'ImportoE' => $this->ImportoE,
            'ImportoV' => $this->ImportoV,
            'EmessoV' => $this->EmessoV,
            'EmessoE' => $this->EmessoE,
            'IncassoV' => $this->IncassoV,
            'PercImponibile' => $this->PercImponibile,
            'PercImposta' => $this->PercImposta,
            'PercProvvigione' => $this->PercProvvigione,
            'Girate' => $this->Girate,
            'Sollecito' => $this->Sollecito,
            'DataUltimoSollecito' => $this->DataUltimoSollecito,
            'DataValuta' => $this->DataValuta,
            'ProvvisorioDaDocumento' => $this->ProvvisorioDaDocumento,
            'TimeIns' => $this->TimeIns,
            'TimeUpd' => $this->TimeUpd,
            'Ts' => $this->Ts,
            'CambioStorico' => $this->CambioStorico,
            'DataRivalutazione' => $this->DataRivalutazione,
            'SDD_DtMandato' => $this->SDD_DtMandato,
            'Riconciliato' => $this->Riconciliato,
            'Id_RBTes' => $this->Id_RBTes,
            'xQuotaV_RA' => $this->xQuotaV_RA,
            'xQuotaV_RE' => $this->xQuotaV_RE,
            'xQuotaE_RA' => $this->xQuotaE_RA,
            'xQuotaE_RE' => $this->xQuotaE_RE,
            'xImportoVNettoRitenute' => $this->xImportoVNettoRitenute,
            'xImportoENettoRitenute' => $this->xImportoENettoRitenute,
            'Provvisorio' => $this->Provvisorio,
        ]);

        $query->andFilterWhere(['like', 'Tipolink', $this->Tipolink])
            ->andFilterWhere(['like', 'Cd_CF', $this->Cd_CF])
            ->andFilterWhere(['like', 'Cd_CGConto_Banca', $this->Cd_CGConto_Banca])
            ->andFilterWhere(['like', 'Cd_CGConto_Portafoglio', $this->Cd_CGConto_Portafoglio])
            ->andFilterWhere(['like', 'Cd_CGConto_InPortafoglio', $this->Cd_CGConto_InPortafoglio])
            ->andFilterWhere(['like', 'Cd_CGConto_InSbf', $this->Cd_CGConto_InSbf])
            ->andFilterWhere(['like', 'Cd_VL', $this->Cd_VL])
            ->andFilterWhere(['like', 'Cd_PG', $this->Cd_PG])
            ->andFilterWhere(['like', 'Descrizione', $this->Descrizione])
            ->andFilterWhere(['like', 'NumFattura', $this->NumFattura])
            ->andFilterWhere(['like', 'Protocollo', $this->Protocollo])
            ->andFilterWhere(['like', 'PartNum', $this->PartNum])
            ->andFilterWhere(['like', 'TipoRata', $this->TipoRata])
            ->andFilterWhere(['like', 'NoteSC', $this->NoteSC])
            ->andFilterWhere(['like', 'Piazza', $this->Piazza])
            ->andFilterWhere(['like', 'Traente', $this->Traente])
            ->andFilterWhere(['like', 'Cd_SL', $this->Cd_SL])
            ->andFilterWhere(['like', 'Cd_Simulazione', $this->Cd_Simulazione])
            ->andFilterWhere(['like', 'Iban', $this->Iban])
            ->andFilterWhere(['like', 'BicCode', $this->BicCode])
            ->andFilterWhere(['like', 'Cd_Abicab', $this->Cd_Abicab])
            ->andFilterWhere(['like', 'ContoCorrente', $this->ContoCorrente])
            ->andFilterWhere(['like', 'Cin_It', $this->Cin_It])
            ->andFilterWhere(['like', 'UserIns', $this->UserIns])
            ->andFilterWhere(['like', 'UserUpd', $this->UserUpd])
            ->andFilterWhere(['like', 'NoteXML', $this->NoteXML])
            ->andFilterWhere(['like', 'CIG', $this->CIG])
            ->andFilterWhere(['like', 'CUP', $this->CUP])
            ->andFilterWhere(['like', 'SDD_IdMandato', $this->SDD_IdMandato])
            ->andFilterWhere(['like', 'SDD_SqMandato', $this->SDD_SqMandato])
            ->andFilterWhere(['like', 'ExtraInfo', $this->ExtraInfo])
            ->andFilterWhere(['like', 'FTE_TipoPagamento', $this->FTE_TipoPagamento]);

        return $dataProvider;
    }
}
