<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "veicoli".
 *
 * @property int $id
 * @property string $targa
 * @property string|null $marca_modello
 * @property string|null $stato_veicolo
 * @property string|null $scadenza_assicurazione
 * @property string|null $scadenza_revisione
 * @property string|null $scadenza_ztl
 * @property int|null $ultimo_km
 * @property string|null $documento_path
 *
 * @property Planning[] $plannings
 */
class Veicoli extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'veicoli';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['targa'], 'required'],
            [['scadenza_assicurazione', 'scadenza_revisione', 'scadenza_ztl'], 'safe'],
            [['ultimo_km'], 'integer'],
            [['targa'], 'string', 'max' => 20],
            [['marca_modello'], 'string', 'max' => 200],
            [['stato_veicolo'], 'string', 'max' => 50],
            [['documento_path'], 'string', 'max' => 255],
            [['targa'], 'unique'],
            [['data_immatricolazione'], 'safe'],
            [['peso_complessivo', 'classe_euro'], 'string', 'max' => 50],
            [['tipologia'], 'string', 'max' => 50],
        [['data_scadenza_contratto'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'targa' => 'Targa',
            'marca_modello' => 'Marca Modello',
            'stato_veicolo' => 'Stato Veicolo',
            'scadenza_assicurazione' => 'Scadenza Assicurazione',
            'scadenza_revisione' => 'Scadenza Revisione',
            'scadenza_ztl' => 'Scadenza Ztl',
            'ultimo_km' => 'Ultimo Km',
            'documento_path' => 'Documento Path',
            'peso_complessivo' => 'Peso Complessivo',
            'classe_euro' => 'Classe Euro',
            'data_immatricolazione' => 'Data Immatricolazione',
            'tipologia' => 'Tipologia Possesso',
        'data_scadenza_contratto' => 'Scadenza Leasing/Noleggio',
        ];
    }

    /**
     * Gets query for [[Plannings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlannings()
    {
        return $this->hasMany(Planning::className(), ['veicolo_id' => 'id']);
    }
    // models/Veicoli.php

    public function getFiles()
    {
        return $this->hasMany(AllFiles::className(), ['id_padre' => 'id'])
            ->where(['entita' => 'veicoli']);
    }

    public function getStatoBadge()
    {
        $class = 'secondary'; // Default (Dismesso)
        switch ($this->stato_veicolo) {
            case 'Disponibile':
                $class = 'success';
                break;
            case 'In Uso':
                $class = 'info text-white';
                break;
            case 'In Riparazione':
                $class = 'danger';
                break;
            case 'Dismesso':
                $class = 'dark';
                break;
            case 'Fuori Uso':
                $class = 'danger';  ;    
        }

        return "<span class='badge bg-{$class}' style='padding: 8px 12px; font-size: 0.9rem; min-width: 100px;'>{$this->stato_veicolo}</span>";
    }
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}
// Helper per le opzioni (da usare nella form)
public static function getTipologieList()
{
    return [
        'Proprietà' => 'Proprietà',
        'Leasing' => 'Leasing',
        'Noleggio' => 'Noleggio',
    ];
}
}
