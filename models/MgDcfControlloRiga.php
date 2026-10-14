<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_dcf_controllo_riga".
 *
 * Quantita' riscontrata (reale) di una singola riga di un documento DCF.
 *
 * @property int $id
 * @property int $id_controllo
 * @property int $id_riga
 * @property float $qta_documento
 * @property float|null $qta_riscontrata
 * @property float $differenza
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MgDcfControllo $controllo
 * @property MgDocumentoRiga $rigaDocumento
 */
class MgDcfControlloRiga extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_dcf_controllo_riga';
    }

    public function rules()
    {
        return [
            [['id_controllo', 'id_riga'], 'required'],
            [['id_controllo', 'id_riga'], 'integer'],
            [['qta_documento', 'qta_riscontrata', 'differenza'], 'number'],
            [['note'], 'string', 'max' => 500],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_controllo' => 'Controllo',
            'id_riga' => 'Riga documento',
            'qta_documento' => 'Q.tà documento',
            'qta_riscontrata' => 'Q.tà riscontrata',
            'differenza' => 'Differenza',
            'note' => 'Note',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getControllo()
    {
        return $this->hasOne(MgDcfControllo::className(), ['id' => 'id_controllo']);
    }

    public function getRigaDocumento()
    {
        return $this->hasOne(MgDocumentoRiga::className(), ['id' => 'id_riga']);
    }

    /**
     * True se la riga e' stata riscontrata.
     */
    public function isRiscontrata()
    {
        return $this->qta_riscontrata !== null && $this->qta_riscontrata !== '';
    }

    /**
     * True se la quantita' riscontrata differisce da quella documento.
     */
    public function hasDifferenza()
    {
        return $this->isRiscontrata() && abs(round((float) $this->differenza, 4)) > 0.0001;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->isRiscontrata()) {
                $this->qta_riscontrata = null;
                $this->differenza = 0;
            } else {
                $this->differenza = round((float) $this->qta_riscontrata - (float) $this->qta_documento, 4);
            }
            $this->updated_at = new \yii\db\Expression('GETDATE()');
            return true;
        }
        return false;
    }
}
