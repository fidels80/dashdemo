<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_report".
 *
 * Definizione di un report costruito con il designer. `definizione` contiene
 * in JSON colonne, filtri, parametri, raggruppamenti, aggregazioni, colonne
 * calcolate e ordinamenti; `layout` l'impaginazione di stampa.
 *
 * Attivazione: `licenza_feature` (se valorizzata) lega il report a una
 * feature della licenza; `dash_report_utente` lo abilita ai singoli operatori.
 *
 * @property int $id
 * @property string $codice
 * @property string $nome
 * @property string|null $descrizione
 * @property string|null $categoria
 * @property string|null $icona
 * @property int|null $fonte_id
 * @property string $tipo
 * @property string|null $definizione
 * @property string|null $layout
 * @property string|null $licenza_feature
 * @property bool $attivo
 * @property int $ordine
 * @property int|null $creato_da
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class DashReport extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_report';
    }

    public function rules()
    {
        return [
            [['codice', 'nome'], 'required'],
            [['definizione', 'layout'], 'string'],
            [['fonte_id', 'ordine', 'creato_da'], 'integer'],
            [['attivo'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['codice'], 'string', 'max' => 50],
            [['nome'], 'string', 'max' => 150],
            [['descrizione'], 'string', 'max' => 500],
            [['categoria', 'licenza_feature'], 'string', 'max' => 50],
            [['icona'], 'string', 'max' => 50],
            [['tipo'], 'string', 'max' => 20],
            [['codice'], 'unique'],
            [['tipo'], 'in', 'range' => ['tabella', 'raggruppato', 'pivot']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'nome' => 'Nome',
            'descrizione' => 'Descrizione',
            'categoria' => 'Categoria',
            'icona' => 'Icona',
            'fonte_id' => 'Fonte dati',
            'tipo' => 'Tipo',
            'definizione' => 'Definizione (JSON)',
            'layout' => 'Layout (JSON)',
            'licenza_feature' => 'Feature licenza',
            'attivo' => 'Attivo',
            'ordine' => 'Ordine',
            'creato_da' => 'Creato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getFonte()
    {
        return $this->hasOne(DashReportFonte::className(), ['id' => 'fonte_id']);
    }

    public function getCreatore()
    {
        return $this->hasOne(User::className(), ['id' => 'creato_da']);
    }

    public function getAssegnazioni()
    {
        return $this->hasMany(DashReportUtente::className(), ['report_id' => 'id']);
    }

    public function getEsecuzioni()
    {
        return $this->hasMany(DashReportEsecuzione::className(), ['report_id' => 'id']);
    }

    public function getDefinizioneArray()
    {
        $decoded = json_decode((string) $this->definizione, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getLayoutArray()
    {
        $decoded = json_decode((string) $this->layout, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * True se il report e' abilitato a un singolo operatore (riga in
     * dash_report_utente), indipendentemente dal fatto che sia super.
     */
    public function isAbilitatoUtente($userId, $canEdit = false)
    {
        $ass = DashReportUtente::findOne([
            'report_id' => $this->id,
            'user_id' => $userId,
        ]);
        if (!$ass) {
            return false;
        }
        return $canEdit ? (bool) $ass->can_edit : (bool) $ass->can_run;
    }

    /**
     * True se la feature di licenza del report (se presente) e' abilitata.
     */
    public function licenzaOk()
    {
        if (empty($this->licenza_feature)) {
            return true;
        }
        return \app\components\Licensing::hasFeature($this->licenza_feature);
    }
}
