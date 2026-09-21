<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Xtravelrow;

/**
 * XtravelrowSearch represents the model behind the search form of `app\models\Xtravelrow`.
 */
class XtravelrowSearch extends Xtravelrow
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tr_id', 'th_id', 'qta', 'evadi_A', 'evadi_p', 'pagato'], 'integer'],
            [['sottocommessa', 'cd_Ar', 'descrizione', 'stato', 'guest', 'ruolo', 'cd_cf_ft', 'descli', 'citta', 'fornitore', 'desfor', 'struttura', 'check_in', 'check_out', 'citta_da', 'citta_a', 'orario', 'pnr', 'nr_biglietto', 'data_pg', 'cd_pg', 'contabile', 'note', 'descontab', 'codiva', 'xid', 'timeins', 'numero', 'datah', 'x_scdesc'], 'safe'],
            [['prezzo', 'totale', 'tax', 'fee', 'fee_perc', 'imponibile', 'iva', 'Totalegenerale', 'tax_unit', 'totfattura', 'x_pagato'], 'number'],
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
        $query = Xtravelrow::find();

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
            'tr_id' => $this->tr_id,
            'th_id' => $this->th_id,
            'qta' => $this->qta,
            'prezzo' => $this->prezzo,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'orario' => $this->orario,
            'data_pg' => $this->data_pg,
            'totale' => $this->totale,
            'tax' => $this->tax,
            'fee' => $this->fee,
            'fee_perc' => $this->fee_perc,
            'imponibile' => $this->imponibile,
            'iva' => $this->iva,
            'Totalegenerale' => $this->Totalegenerale,
            'evadi_A' => $this->evadi_A,
            'evadi_p' => $this->evadi_p,
            'tax_unit' => $this->tax_unit,
            'totfattura' => $this->totfattura,
            'pagato' => $this->pagato,
            'timeins' => $this->timeins,
            'datah' => $this->datah,
            'x_pagato' => $this->x_pagato,
        ]);

        $query->andFilterWhere(['like', 'sottocommessa', $this->sottocommessa])
            ->andFilterWhere(['like', 'cd_Ar', $this->cd_Ar])
            ->andFilterWhere(['like', 'descrizione', $this->descrizione])
            ->andFilterWhere(['like', 'stato', $this->stato])
            ->andFilterWhere(['like', 'guest', $this->guest])
            ->andFilterWhere(['like', 'ruolo', $this->ruolo])
            ->andFilterWhere(['like', 'cd_cf_ft', $this->cd_cf_ft])
            ->andFilterWhere(['like', 'descli', $this->descli])
            ->andFilterWhere(['like', 'citta', $this->citta])
            ->andFilterWhere(['like', 'fornitore', $this->fornitore])
            ->andFilterWhere(['like', 'desfor', $this->desfor])
            ->andFilterWhere(['like', 'struttura', $this->struttura])
            ->andFilterWhere(['like', 'citta_da', $this->citta_da])
            ->andFilterWhere(['like', 'citta_a', $this->citta_a])
            ->andFilterWhere(['like', 'pnr', $this->pnr])
            ->andFilterWhere(['like', 'nr_biglietto', $this->nr_biglietto])
            ->andFilterWhere(['like', 'cd_pg', $this->cd_pg])
            ->andFilterWhere(['like', 'contabile', $this->contabile])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'descontab', $this->descontab])
            ->andFilterWhere(['like', 'codiva', $this->codiva])
            ->andFilterWhere(['like', 'xid', $this->xid])
            ->andFilterWhere(['like', 'numero', $this->numero])
            ->andFilterWhere(['like', 'x_scdesc', $this->x_scdesc]);

        return $dataProvider;
    }
}
