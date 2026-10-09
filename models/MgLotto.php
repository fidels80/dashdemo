<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_lotto".
 *
 * @property int $id
 * @property int|null $id_articolo
 * @property string|null $codice_articolo
 * @property string $codice_lotto
 * @property string|null $descrizione
 * @property string|null $data_scadenza
 * @property string|null $nota
 * @property string|null $created_at
 *
 * @property MgArticolo|null $articolo
 */
class MgLotto extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_lotto';
    }

    public function rules()
    {
        return [
            [['codice_lotto'], 'required'],
            [['id_articolo'], 'integer'],
            [['id_articolo'], 'exist',
                'targetClass' => MgArticolo::className(),
                'targetAttribute' => ['id_articolo' => 'id'],
                'skipOnEmpty' => true],
            [['data_scadenza', 'created_at'], 'safe'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['codice_lotto'], 'string', 'max' => 50],
            [['descrizione'], 'string', 'max' => 200],
            [['nota'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_articolo' => 'Articolo',
            'codice_articolo' => 'Codice articolo',
            'codice_lotto' => 'Codice lotto',
            'descrizione' => 'Descrizione',
            'data_scadenza' => 'Data scadenza',
            'nota' => 'Nota',
            'created_at' => 'Creato il',
        ];
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function getDataScadenzaLabel()
    {
        return $this->data_scadenza ? date('d/m/Y', strtotime((string) $this->data_scadenza)) : null;
    }

    public function getEtichetta()
    {
        $label = $this->codice_lotto;
        if (!empty($this->descrizione)) {
            $label .= ' - ' . $this->descrizione;
        }
        return $label;
    }

    /**
     * Lotti di un articolo (per id e/o codice articolo).
     *
     * @param int|null $idArticolo
     * @param string|null $codiceArticolo
     * @return MgLotto[]
     */
    public static function perArticolo($idArticolo, $codiceArticolo = null)
    {
        $query = self::find();
        $conditions = [];
        if (!empty($idArticolo)) {
            $conditions[] = ['id_articolo' => (int) $idArticolo];
        }
        if ($codiceArticolo !== null && $codiceArticolo !== '') {
            $conditions[] = ['codice_articolo' => $codiceArticolo];
        }

        if (empty($conditions)) {
            return [];
        }

        $query->andWhere(array_merge(['or'], $conditions));
        return $query->orderBy(['codice_lotto' => SORT_ASC])->all();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->id_articolo === '') {
                $this->id_articolo = null;
            }
            if ($this->data_scadenza === '') {
                $this->data_scadenza = null;
            }
            if ($this->id_articolo && empty($this->codice_articolo)) {
                $articolo = $this->articolo;
                if ($articolo) {
                    $this->codice_articolo = $articolo->codice;
                }
            }
            if ($insert && empty($this->created_at)) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
            }
            return true;
        }
        return false;
    }
}
