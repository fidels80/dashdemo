<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "xtravelhead".
 *
 * @property int $th_id
 * @property string $datath
 * @property string $numero
 * @property string $descrizione
 * @property string|null $timeins
 * @property int|null $evaso_A
 * @property int|null $evaso_p
 * @property int|null $fatturato
 * @property float|null $totaleservizi
 * @property float|null $tax
 * @property float|null $fee
 * @property float|null $totft
 * @property int|null $rige
 * @property int|null $xid_dotesft
 * @property int|null $righe
 * @property float|null $x_acconto
 * @property string|null $x_cd_cf
 * @property string|null $x_cfdesk
 * @property int|null $bloccato
 * @property string $decodificatore
 * @property string $x_maxdata
 * @property int|null $tourmanager
 */
class Xtravelhead extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xtravelhead';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['datath', 'numero', 'descrizione'], 'required'],
            [['datath', 'timeins', 'x_maxdata'], 'safe'],
            [['evaso_A', 'evaso_p', 'fatturato', 'rige', 'xid_dotesft', 
            'righe', 'bloccato','x_tiposhow',
                'tourmanager'], 'integer'],
            [['totaleservizi', 'tax', 'fee', 'totft', 'x_acconto'], 'number'],
            [['numero'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['x_cd_cf'], 'string', 'max' => 7],
            [['x_cfdesk'], 'string', 'max' => 80],
            [['decodificatore'], 'string', 'max' => 233],
            [['manager_ids'], 'safe'],
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'th_id' => 'Th ID',
            'datath' => 'Datath',
            'numero' => 'Numero',
            'descrizione' => 'Descrizione',
            'timeins' => 'Timeins',
            'evaso_A' => 'Evaso A',
            'evaso_p' => 'Evaso P',
            'fatturato' => 'Fatturato',
            'totaleservizi' => 'Totaleservizi',
            'tax' => 'Tax',
            'fee' => 'Fee',
            'totft' => 'Totft',
            'rige' => 'Rige',
            'xid_dotesft' => 'Xid Dotesft',
            'righe' => 'Righe',
            'x_acconto' => 'X Acconto',
            'x_cd_cf' => 'X Cd Cf',
            'x_cfdesk' => 'X Cfdesk',
            'bloccato' => 'Bloccato',
            'decodificatore' => 'Decodificatore',
            'x_tiposhow'=>'Tipo Show',
            'tourmanager' => 'Tour Manager', // Aggiunto qui
        ];
    }
    public function getTravelRows()
    {
        //\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->hasMany(
            xtravelrow::class,
            ['th_id' => 'th_id']

        )->orderBy(['tr_id' => SORT_ASC]);
    }


    public function getFilesall()
    {
        return $this->hasMany(allfiles::className(), ['id_padre' => 'th_id']);
    }
public function getConcatenatedFields()
{
    $query = (new \yii\db\Query())
        ->select([
            new \yii\db\Expression(" distinct
                STRING_AGG( sottocommessa+' ', ', ') AS sottocommesse,
                STRING_AGG( x_scdesc, ', ') AS x_scdescs
            "),
        ])
        ->from('xtravelrow')
        ->where(['th_id' => $this->th_id])
        ->one();

    return $query ? $query['sottocommesse'] . '   ' . $query['x_scdescs'] : '';
}

public function getImponibile()
{
    // Usa la relazione per accedere alle righe e somma i prezzi
    return $this->getTravelRows()->sum('imponibile');
}
public function getTax()
{
    // Usa la relazione per accedere alle righe e somma i prezzi
    return $this->getTravelRows()->sum('tax');
}
public function getIva()
{
    // Usa la relazione per accedere alle righe e somma i prezzi
    return $this->getTravelRows()->sum('iva');
}
public function getsomma($colonna=null)
{
$colonna=$colonna ??'imponibile';

    // Usa la relazione per accedere alle righe e somma i prezzi
    return $this->getTravelRows()->sum($colonna);
}

public function getUniqueSottocommesseAndDescriptions()
{
    // Ottieni tutte le righe correlate
    $travelRows = $this->getTravelRows()->all();

    // Estrai i valori univoci di sottocommessa e x_scdesc
    $uniqueValues = [];
    foreach ($travelRows as $row) {
    
    if (strlen($row->sottocommessa)>0 and strlen($row->x_scdesc)){
        $key = $row->sottocommessa . ' : ' . $row->x_scdesc;
        $uniqueValues[$key] = true; // Usa una chiave per garantire l'unicità
    }
    }
    // Concatena i valori univoci in una stringa separata da virgole
    return implode('<br>', array_keys($uniqueValues));
}

public function getTravelRowsCount()
{
    // Conta il numero di righe collegate utilizzando la relazione
    return $this->getTravelRows()->count();
}
 
public function getLatestTravelRow()
{
    return $this->hasOne(xtravelrow::class, ['th_id' => 'th_id'])
        ->orderBy(['check_in' => SORT_DESC])
        ->one();
}

    public function getPagamentiPerTipohotel(): float
    {
        $connection = \Yii::$app->db5;

        $sql = "
        SELECT /*CAST(SUM(
        case when imponibile=0 and fee<>0 then fee
else imponibile end
+tax) AS DECIMAL(18,2)) 

*/
CAST(sum( coalesce([dbo].[afn_Scorporo_GetImponibile](xtravelrow.prezzo * xtravelrow.qta,
        Aliquota.Aliquota, 2)+coalesce(fee,0) +coalesce(tax ,0) ,0))AS DECIMAL(18,2)) as imp
        
 
        FROM xtravelrow
        LEFT JOIN ar ON ar.cd_ar = xtravelrow.cd_ar
          LEFT JOIN Aliquota ON Aliquota.Cd_Aliquota =  xtravelrow.codiva
        WHERE
        -- x_pagato IS NOT NULL
          --AND 
          th_id = :th_id
          AND ar.cd_arclasse12 = 'TRVACC'
          AND ar.x_isacconto IS NULL
    ";

        $importo = $connection->createCommand($sql, [':th_id' => $this->th_id])->queryScalar();

        return (float)$importo;
    }

    public function getPagamentiPerTipoviaggi(): float
    {
        $connection = \Yii::$app->db5;

        $sql = "
        SELECT  /*CAST(SUM(imponibile+tax) AS DECIMAL(18,2)) AS imp*/
        CAST(sum( coalesce([dbo].[afn_Scorporo_GetImponibile](xtravelrow.prezzo * xtravelrow.qta,
        Aliquota.Aliquota, 2)+coalesce(fee,0) +coalesce(tax ,0) ,0))AS DECIMAL(18,2)) as imp
        
        
        FROM xtravelrow
        LEFT JOIN ar ON ar.cd_ar = xtravelrow.cd_ar
         LEFT JOIN Aliquota ON Aliquota.Cd_Aliquota =  xtravelrow.codiva
        WHERE x_pagato IS NOT NULL
          AND th_id = :th_id
          AND ar.cd_arclasse12 IN ('TRVBIG', 'TRVVOL', 'TRVTRA', 'TRVNOL')
          AND ar.x_isacconto IS NULL
    ";

        $importo = $connection->createCommand($sql, [':th_id' => $this->th_id])->queryScalar();

        return (float)$importo;
    }

    public function getPagamentiDocumenti(): float
    {
        $connection = \Yii::$app->db5;

        try {
            $checkSql = "
            SELECT COUNT(*) 
            FROM adb_auxcoop.dbo.dotes
            WHERE dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1
        ";

            $countResult = $connection->createCommand($checkSql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            if ($countResult == 0) {
                return 0.0;
            }

            $sql = "
            SELECT CAST(ISNULL(SUM(CAST(dototali.totimponibilev AS DECIMAL(18,2))), 0) AS DECIMAL(18,2)) as imp
            FROM adb_auxcoop.dbo.dotes
            LEFT JOIN adb_auxcoop.dbo.dototali ON dototali.Id_DoTes = dotes.Id_DoTes
            WHERE dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1
        ";

            $importo = $connection->createCommand($sql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            return (float)$importo;
        } catch (\Exception $e) {
            \Yii::error("Errore SQL in getPagamentiDocumenti: " . $e->getMessage());
            return 0.0;
        }
    }
    public function getPagamentiDocumentihotel(): float
    {
        $connection = \Yii::$app->db5;

        try {
            $checkSql = "
            SELECT COUNT(*) 
            FROM adb_auxcoop.dbo.dotes
            WHERE 
            cd_do='FVH' and (
            dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1)
        ";

            $countResult = $connection->createCommand($checkSql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            if ($countResult == 0) {
                return 0.0;
            }

            $sql = "
            SELECT CAST(ISNULL(SUM(CAST(dototali.totimponibilev AS DECIMAL(18,2))), 0) 
            AS DECIMAL(18,2)) as imp
            FROM adb_auxcoop.dbo.dotes
            LEFT JOIN adb_auxcoop.dbo.dototali ON dototali.Id_DoTes = dotes.Id_DoTes
            WHERE 
            cd_do='FVH' and (
            dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1)
        ";

            $importo = $connection->createCommand($sql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            return (float)$importo;
        } catch (\Exception $e) {
            \Yii::error("Errore SQL in getPagamentiDocumenti: " . $e->getMessage());
            return 0.0;
        }
    }
    public function getPagamentiDocumentiviaggi(): float
    {
        $connection = \Yii::$app->db5;

        try {
            $checkSql = "
            SELECT COUNT(*) 
            FROM adb_auxcoop.dbo.dotes
            WHERE 
            cd_do='FVB' and (
            dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1)
        ";

            $countResult = $connection->createCommand($checkSql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            if ($countResult == 0) {
                return 0.0;
            }

            $sql = "
            SELECT CAST(ISNULL(SUM(CAST(dototali.totimponibilev AS DECIMAL(18,2))), 0) AS DECIMAL(18,2)) as imp
            FROM adb_auxcoop.dbo.dotes
            LEFT JOIN adb_auxcoop.dbo.dototali ON dototali.Id_DoTes = dotes.Id_DoTes
            WHERE 
            cd_do='FVB' and (
            dotes.Id_DoTes IN (
                SELECT xid_dotesft 
                FROM adb_auxcoop.dbo.xtravelhead 
                WHERE th_id = :th_id
            )
            OR dotes.x_th_id = :th_id1)
        ";

            $importo = $connection->createCommand($sql, [
                ':th_id' => $this->th_id,
                ':th_id1' => $this->th_id,
            ])->queryScalar();

            return (float)$importo;
        } catch (\Exception $e) {
            \Yii::error("Errore SQL in getPagamentiDocumenti: " . $e->getMessage());
            return 0.0;
        }
    }




    public static function findWithMaxRowDate()
    {
        return self::find()
            ->alias('h')
            ->leftJoin('xtravelrow r', 'r.th_id = h.th_id')
            ->addSelect([
                'h.*',
                'max_data' => new \yii\db\Expression("
                    MAX(
                        CASE 
                            WHEN r.check_out > r.check_in THEN r.check_out
                            ELSE r.check_in
                        END
                    )
                ")
            ])
            ->groupBy('h.th_id');
    }

    /**
     * Relazione con gli utenti (Tour Managers) situati su un altro DB
     */
    public function getTourManagers()
    {
        // Usiamo il modello User che punta al DB 'web_frontier'
        // e passiamo per la tabella di giunzione che sta su 'db5'
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('adb_auxcoop.dbo.xtravel_managers', ['th_id' => 'th_id']);
    }

    public $manager_ids;
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Se manager_ids è null, significa che non era presente nel form.
        // Se è un array vuoto, l'utente ha rimosso tutti i manager.
        if ($this->manager_ids !== null) {
            $db = Yii::$app->db5;
            $tableName = '[adb_auxcoop].[dbo].[xtravel_managers]';

            try {
                // 1. Elimina i vecchi legami
                $db->createCommand()
                    ->delete($tableName, ['th_id' => $this->th_id])
                    ->execute();

                // 2. Inserisci i nuovi se l'array non è vuoto
                if (is_array($this->manager_ids) && !empty($this->manager_ids)) {
                    $rows = [];
                    foreach ($this->manager_ids as $userId) {
                        if (!empty($userId)) {
                            $rows[] = [
                                (int)$this->th_id,
                                (int)$userId
                            ];
                        }
                    }

                    if (!empty($rows)) {
                        $db->createCommand()
                            ->batchInsert($tableName, ['th_id', 'user_id'], $rows)
                            ->execute();
                    }
                }
            } catch (\Exception $e) {
                Yii::error("Errore salvataggio managers: " . $e->getMessage());
                // Decommenta solo se vuoi vedere l'errore SQL bloccante
                // throw $e; 
            }
        }
    }
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Se i campi sono nulli o stringhe vuote, forziamoli a 0
            $this->evaso_A = (int) $this->evaso_A;
            $this->evaso_p = (int) $this->evaso_p;
            $this->fatturato = (int) $this->fatturato;
            $this->bloccato = (int) $this->bloccato;
            $this->x_tiposhow = (int) $this->x_tiposhow;
            $this->tourmanager = (int) $this->tourmanager;

            return true;
        }
        return false;
    }
}
