<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_report_fonte".
 *
 * Fonte dati (dataset) di un report. Puo' essere:
 * - "strutturata": tabella/vista base + join visivi, definiti in `definizione`;
 * - "sql": query SQL tecnica (riservata ai supervisori), sempre in `definizione`.
 *
 * `connessione` indica il componente Yii (db, db2, ... db6) di db.php su cui
 * gira la query. Per i join cross-database tra db e db5 (stessa istanza SQL
 * Server) si usa il naming a tre parti [Database].[dbo].[Tabella].
 *
 * @property int $id
 * @property string $codice
 * @property string $nome
 * @property string|null $descrizione
 * @property string $tipo
 * @property string $connessione
 * @property string|null $base_tabella
 * @property string|null $definizione
 * @property bool $attivo
 * @property int|null $creato_da
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class DashReportFonte extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_report_fonte';
    }

    public function rules()
    {
        return [
            [['codice', 'nome'], 'required'],
            [['definizione'], 'string'],
            [['attivo'], 'boolean'],
            [['creato_da'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['codice'], 'string', 'max' => 50],
            [['nome'], 'string', 'max' => 150],
            [['descrizione'], 'string', 'max' => 500],
            [['tipo'], 'string', 'max' => 20],
            [['connessione'], 'string', 'max' => 20],
            [['base_tabella'], 'string', 'max' => 128],
            [['codice'], 'unique'],
            [['tipo'], 'in', 'range' => ['strutturata', 'sql']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'nome' => 'Nome',
            'descrizione' => 'Descrizione',
            'tipo' => 'Tipo',
            'connessione' => 'Connessione',
            'base_tabella' => 'Tabella base',
            'definizione' => 'Definizione (JSON)',
            'attivo' => 'Attivo',
            'creato_da' => 'Creato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getCreatore()
    {
        return $this->hasOne(User::className(), ['id' => 'creato_da']);
    }

    public function getReport()
    {
        return $this->hasMany(DashReport::className(), ['fonte_id' => 'id']);
    }

    /**
     * Definizione decodificata (array), vuoto se assente o non valida.
     */
    public function getDefinizioneArray()
    {
        $decoded = json_decode((string) $this->definizione, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Elenco fonti attive per le tendine (id => nome).
     */
    public static function getAttive()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['nome' => SORT_ASC])->all(),
            'id',
            'nome'
        );
    }
}
