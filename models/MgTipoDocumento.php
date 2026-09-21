<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_tipo_documento".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property int $anno
 * @property int $contatore
 * @property bool $usa_progressivo
 * @property bool $congruita
 * @property bool $attivo
 * @property string|null $created_at
 */
class MgTipoDocumento extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_tipo_documento';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['anno', 'contatore'], 'integer'],
            [['usa_progressivo', 'congruita', 'attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'anno' => 'Anno',
            'contatore' => 'Contatore',
            'usa_progressivo' => 'Numerazione automatica',
            'congruita' => 'Proposta congruità numeri',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function getDocumenti()
    {
        return $this->hasMany(MgDocumento::className(), ['id_tipo' => 'id']);
    }

    /**
     * Aggiorna il contatore (ultimo numero usato) e l'anno di riferimento.
     */
    public function aggiornaContatore($numero, $anno)
    {
        if ($numero > (int) $this->contatore) {
            $this->contatore = (int) $numero;
        }
        if ($anno && (int) $this->anno !== (int) $anno) {
            $this->anno = (int) $anno;
        }
        return $this->save(false);
    }
}
