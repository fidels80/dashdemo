<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "xtravelrow".
 *
 * @property int $tr_id
 * @property int|null $th_id
 * @property string|null $sottocommessa
 * @property string|null $cd_Ar
 * @property string|null $descrizione
 * @property int|null $qta
 * @property float|null $prezzo
 * @property string|null $stato
 * @property string|null $guest
 * @property string|null $ruolo
 * @property string|null $cd_cf_ft
 * @property string|null $descli
 * @property string|null $citta
 * @property string|null $fornitore
 * @property string|null $desfor
 * @property string|null $struttura
 * @property string|null $check_in
 * @property string|null $check_out
 * @property string|null $citta_da
 * @property string|null $citta_a
 * @property string|null $orario
 * @property string|null $pnr
 * @property string|null $nr_biglietto
 * @property string|null $data_pg
 * @property string|null $cd_pg
 * @property string|null $contabile
 * @property float|null $totale
 * @property float|null $tax
 * @property float|null $fee
 * @property float|null $fee_perc
 * @property float|null $imponibile
 * @property float|null $iva
 * @property float|null $Totalegenerale
 * @property int|null $evadi_A
 * @property int|null $evadi_p
 * @property float|null $tax_unit
 * @property string|null $note
 * @property string|null $descontab
 * @property float|null $totfattura
 * @property string|null $codiva
 * @property int|null $pagato
 * @property string|null $xid
 * @property string|null $timeins
 * @property string|null $numero
 * @property string|null $datah
 * @property string|null $x_scdesc
 * @property float|null $x_pagato
 * @property string|null $party
 * @property string|null $id_tappa
 * @property string|null $id_nominativo
 * @property int|null $duseri
 * @property int|null $duseru
 */
class Xtravelrow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xtravelrow';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }
    public $imponibile_scorporato;
    public $imposta_scorporata;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['th_id', 'qta', 'evadi_A', 'evadi_p', 'pagato','duseri', 'duseru'], 'integer'],
            [['prezzo', 'totale', 'tax', 'fee', 'fee_perc', 'imponibile', 'iva', 'Totalegenerale', 'tax_unit', 'totfattura', 'x_pagato'], 'number'],
            [['check_in', 'check_out', 'orario', 'data_pg', 'timeins', 'datah'], 'safe'],
            [['note', 'xid','id_tappa','id_nominativo'], 'string'],
            [['sottocommessa', 'cd_Ar', 'numero'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 90],
            [['stato'], 'string', 'max' => 14],
            [['guest', 'ruolo', 'struttura', 'pnr', 'nr_biglietto', 'cd_pg', 'descontab'], 'string', 'max' => 200],
            [['cd_cf_ft', 'fornitore'], 'string', 'max' => 7],
            [['descli', 'desfor'], 'string', 'max' => 250],
            [['citta', 'citta_da', 'citta_a', 'x_scdesc'], 'string', 'max' => 50],
            [['contabile'], 'string', 'max' => 254],
            [['codiva'], 'string', 'max' => 3],
            [['party'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tr_id' => 'Tr ID',
            'th_id' => 'Th ID',
            'sottocommessa' => 'Sottocommessa',
            'cd_Ar' => 'Cd Ar',
            'descrizione' => 'Descrizione',
            'qta' => 'Qta',
            'prezzo' => 'Prezzo',
            'stato' => 'Stato',
            'guest' => 'Guest',
            'ruolo' => 'Ruolo',
            'cd_cf_ft' => 'Cd Cf Ft',
            'descli' => 'Descli',
            'citta' => 'Citta',
            'fornitore' => 'Fornitore',
            'desfor' => 'Desfor',
            'struttura' => 'Struttura',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
            'citta_da' => 'Citta Da',
            'citta_a' => 'Citta A',
            'orario' => 'Orario',
            'pnr' => 'Pnr',
            'nr_biglietto' => 'Nr Biglietto',
            'data_pg' => 'Data Pg',
            'cd_pg' => 'Cd Pg',
            'contabile' => 'Contabile',
            'totale' => 'Totale',
            'tax' => 'Tax',
            'fee' => 'Fee',
            'fee_perc' => 'Fee Perc',
            'imponibile' => 'Imponibile',
            'iva' => 'Iva',
            'Totalegenerale' => 'Totalegenerale',
            'evadi_A' => 'Evadi A',
            'evadi_p' => 'Evadi P',
            'tax_unit' => 'Tax Unit',
            'note' => 'Note',
            'descontab' => 'Descontab',
            'totfattura' => 'Totfattura',
            'codiva' => 'Codiva',
            'pagato' => 'Pagato',
            'xid' => 'Xid',
            'id_tappa'  => 'Id Tappa',
            'id_nominativo' => 'Id Nominativo',
            'timeins' => 'Timeins',
            'numero' => 'Numero',
            'datah' => 'Datah',
            'x_scdesc' => 'X Scdesc',
            'x_pagato' => 'X Pagato',
            'party' => 'Party',
            'duseri' => 'User inserito',
            'duseru' => 'USer updated',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // Se hai il codice fornitore valorizzato
          
            if (!empty($this->cd_Ar)) {
                $descrizione = (new \yii\db\Query())
                    ->select(['descrizione'])
                    ->from('adb_auxcoop.dbo.ar')
                    ->where(['cd_ar' => $this->cd_Ar]) // fornitore è cd_cf_ft immagino
                    ->scalar(Yii::$app->db5);

                if ($descrizione !== false) {
                    $this->descrizione = $descrizione;
                }
            }

            // Controllo sottocommessa: scatta sia in INSERT che in UPDATE
            if (!empty($this->sottocommessa)) {

                // Recuperiamo il valore precedente della sottocommessa (solo se non è un nuovo record)
          

                    $descrizione = (new \yii\db\Query())
                        ->select(['Descrizione'])
                        ->from('adb_auxcoop.dbo.DOSottoCommessa')
                        ->where(['Cd_DOSottoCommessa' => $this->sottocommessa])
                        ->scalar(Yii::$app->db5);
 
                        $this->x_scdesc = $descrizione;
             
            }


            if (!empty($this->sottocommessa) and empty($this->cd_cf_ft)) {
                $descrizione = (new \yii\db\Query())
                    ->select(['cd_cf'])
                    ->from('adb_auxcoop.dbo.DOSottoCommessa')
                    ->where(['Cd_DOSottoCommessa' => $this->sottocommessa]) // fornitore è cd_cf_ft immagino
                    ->scalar(Yii::$app->db5);

                if ($descrizione !== false) {
                    $this->cd_cf_ft = $descrizione;
                }
            }

            if (!empty($this->cd_cf_ft)) {
                $descrizione = (new \yii\db\Query())
                    ->select(['descrizione'])
                    ->from('adb_auxcoop.dbo.cf')
                    ->where(['cd_cf' => $this->cd_cf_ft]) // fornitore è cd_cf_ft immagino
                    ->scalar();

                if ($descrizione !== false) {
                    $this->descli = $descrizione;
                }
            }



            if (!empty($this->citta)) {

                $isGuid = preg_match(
                    '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
                    $this->citta
                );
                if ($isGuid) {
                $descrizione = (new \yii\db\Query())
                    ->select(['venue'])
                    ->from('adb_auxcoop.dbo.x_venue')
                    ->where(['id' => $this->citta]) // fornitore è cd_cf_ft immagino
                    ->scalar(Yii::$app->db5);

               


             
                if ($descrizione !== false) {
                    $this->decodeveneue = $descrizione;
                    $this->decodevenue = $descrizione;
                }
                }else{

                    $this->decodeveneue = $this->citta;
                    $this->decodevenue = $this->citta;   
                }


                if ($this->tax === null) {
            $this->tax = 0;
        }

        if ($this->tax_unit === null) {
            $this->tax_unit = 0;
        }



                //empty($this->fornitore) and
                if(is_numeric($this->struttura)){
                    $db = Yii::$app->db5;
$fornitore_S= $db->createCommand("
       select Cd_cf from x_struttura
        WHERE id = :for
    ", [':for' => $this->struttura])->queryScalar();

                    $this->fornitore = ($fornitore_S ?? '');
                }

                if (!empty($this->fornitore)) {
                    $descrizione = (new \yii\db\Query())
                        ->select(['descrizione'])
                        ->from('adb_auxcoop.dbo.cf')
                        ->where(['cd_cf' => $this->fornitore]) // fornitore è cd_cf_ft immagino
                        ->scalar();

                    if ($descrizione !== false) {
                        $this->desfor = $descrizione;
                    }
                }

         if ($this->fee === null) {
          $this->fee = 0;
         }
          if ($this->fee_perc === null) {
          $this->fee_perc = 0;
            }


if ($this->codiva === null or  $this->codiva === '' or empty($this->codiva) or
!empty($this->codiva)) {
                    $db = Yii::$app->db5;
                    $defiva = $db->createCommand("
        SELECT cd_Aliquota 
        FROM Aliquota 
        WHERE Cd_Aliquota = (
            SELECT CD_aliquota_1 
            FROM Impostazione 
            WHERE Cd_Impostazione = 'DEFAULT_RIGHE_DOC'
        )
    ")->queryScalar();
                    // 2) IVA associata all’articolo (diva in FoxPro)
                    $diva = $db->createCommand("
        SELECT cd_Aliquota  
        FROM ar 
        LEFT JOIN Aliquota ON Aliquota.Cd_Aliquota = ar.Cd_Aliquota_V 
        WHERE cd_Ar = :cdar
    ", [':cdar' => $this->cd_Ar])->queryScalar();

$this->codiva = ($diva === null ? $defiva : $diva);

}

                if ($this->prezzo != null  && $this->qta != null) {
                $db = Yii::$app->db5;
                $defiva = $db->createCommand("
        SELECT Aliquota 
        FROM Aliquota 
        WHERE Cd_Aliquota = (
            SELECT CD_aliquota_1 
            FROM Impostazione 
            WHERE Cd_Impostazione = 'DEFAULT_RIGHE_DOC'
        )
    ")->queryScalar();

                // 2) IVA associata all’articolo (diva in FoxPro)
                $diva = $db->createCommand("
        SELECT Aliquota  
        FROM ar 
        LEFT JOIN Aliquota ON Aliquota.Cd_Aliquota = ar.Cd_Aliquota_V 
        WHERE cd_Ar = :cdar
    ", [':cdar' => $this->cd_Ar])->queryScalar();

                // 3) IVA scelta sulla riga (codiva)
                $xali_codiva = $db->createCommand("
        SELECT Aliquota  
        FROM Aliquota  
        WHERE LTRIM(RTRIM(cd_aliquota)) = LTRIM(RTRIM(:codiva))
    ", [':codiva' => $this->codiva])->queryScalar();


                // 4) Decisione su xali (stessa logica di FoxPro)
                if ($xali_codiva != ($diva === null ? 0 : $diva)) {
                    $xali = $xali_codiva;
                } else {
                    $xali = $diva;
                }
                    $prezzo = ($this->prezzo === null ? 0 : $this->prezzo);
                    $totale = $prezzo * $this->qta;

                    $iva_to_use = ($xali === null ? $defiva : $xali);
                    $ximp = $db->createCommand("
        SELECT dbo.afn_Scorporo_GetImponibile(:totale, :aliquota, 3)
    ", [
                        ':totale'  => $totale,
                        ':aliquota' => $iva_to_use
                    ])->queryScalar();

                    $this->imponibile = $ximp;
                    $chkcf = $db->createCommand("
        SELECT x_oldfee  
        FROM cf 
        WHERE cd_cf = :cdcf
    ", [':cdcf' => $this->cd_cf_ft])->queryScalar();
                    // Calcoli
                    $this->totale = $this->qta * $this->prezzo;

                    /*  if ($chkcf) {
                        $vfee = ((float)$this->imponibile * (float)$this->fee_perc) / 100;
                    } else {

                        $vfee = (((float)$this->totale + (float)($this->tax ?? 0)) * (float)$this->fee_perc) / 100;
                    }*/
                    $vfee = (float)($this->fee ?? 0); // Inizializzazione sicura
                    if (!empty($this->fee)) {
                $vfee= $this->fee ;
        }
        
                    $prezzo = (float) ($this->prezzo ?? 0);
                    $qta = (float) ($this->qta ?? 0);
                    $aliquota = ($xali ?? $defiva);
                    $totaleRiga = $prezzo * $qta;
                    // chiamata funzione SQL Server dbo.afn_Scorporo_GetImposta
                    $xivaCalc = (float) $db->createCommand("
        SELECT dbo.afn_Scorporo_GetImposta(:totale, :aliquota, 3)
    ", [
                        ':totale' => $totaleRiga,
                        ':aliquota' => $aliquota
                    ])->queryScalar();
                    // ------------------------
                    // 3) IVA riga (codiva)
                    // ------------------------
                    $xiva = (float) $db->createCommand("
        SELECT Aliquota  
        FROM Aliquota  
        WHERE LTRIM(RTRIM(cd_aliquota)) = LTRIM(RTRIM(:codiva))
    ", [':codiva' => $this->codiva])->queryScalar();

                    if ($xiva != $diva) {
                        $xali = $xiva;
                    } else {
                        $xali = $diva;
                    }
             //   $this->Totalegenerale =((float)$this->qta * (float)$this->prezzo )+ 
             //   (float)($this->tax ?? 0) 
             //   + (float)$this->fee;
             $this->tax=$this->tax_unit*$this->qta;
              //      $this->totfattura = $this->imponibile + ($this->tax ?? 0);
                    $xfeeiva = 0; // se serve puoi calcolarla come fatto in FoxPro
                    // ------------------------
                    // 7) Aggiornamento codiva se vuoto
                    // ------------------------
                    if (empty($this->codiva)) {
                        if ($xiva != $diva) {
                            $xali2 = $db->createCommand("
                SELECT cd_Aliquota 
                FROM Aliquota  
                WHERE LTRIM(RTRIM(cd_aliquota)) = LTRIM(RTRIM(:codiva))
            ", [':codiva' => $this->codiva])->queryScalar();
                        } else {
                            $xali2 = $diva;
                        }

                        $this->codiva = $xali2;
                    }

                    // 4. Assegnazione Campi Finali (Sincronizzazione Totale)
                    //  $this->imponibile = round($ximp + $this->fee, 3);
                    //  $this->iva = round($xivaCalc, 3); // Aggiungi IVA su fee qui se serve (+ $vfee * 0.22)

                    // Il Totale Generale è la somma aritmetica di ciò che è scritto sopra
                    //$this->Totalegenerale = round($this->imponibile + $this->iva + $this->tax, 3);

                    // Tot Fattura allineato alla logica gestionale (Imponibile + Tasse)
                    //$this->totfattura = round($this->imponibile + $this->tax+$this->fee, 3);
                    // ... verso la fine del beforeSave ...

                    // 1. Assicurati che $vfee non sporchi $this->fee se quest'ultima è già definita
                    if (!empty($this->fee) && $this->fee != 0) {
                        $vfee = $this->fee;
                    }

                    // 2. Forza l'imponibile a essere la somma corretta
                    // IMPORTANTE: Assicurati che $ximp sia pulito prima di sommare
                    $this->imponibile = round((float)$ximp + (float)$this->fee, 3);

                    // 3. Ricalcola la percentuale SOLO se serve, altrimenti lasciala invariata
                    // Se la percentuale viene ricalcolata ogni volta, rischi l'effetto "arrotondamento infinito"
                    if ($this->Totalegenerale > 0 && ($this->fee_perc == 0 || $this->fee_perc == null)) {
                        $this->fee_perc = ($this->fee * 100) / ($this->totale + $this->tax);
                    }

                    // 4. Totale Generale e Tot Fattura
                    $this->Totalegenerale = round($this->imponibile + $this->iva + $this->tax, 3);

                    // Qui aggiungi un controllo: la fee è già dentro l'imponibile nel tuo calcolo sopra!
                    // Se scrivi $this->imponibile + $this->tax + $this->fee, stai sommando la fee DUE VOLTE.
                    $this->totfattura = round($this->imponibile + $this->tax, 3);
                    // Aggiornamento codice IVA se necessario
                    if (empty($this->codiva)) {
                        $this->codiva = $xali2;
                    }

            }
        }

            return true;
        }
        return false;
    }

    /**
     * Esegue l'aggiornamento dei totali nella testata xtravelhead dopo il salvataggio della riga.
     */
   /* public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Procediamo all'aggiornamento solo se abbiamo un th_id (ID della testata)
        if ($this->th_id) {
            $db = Yii::$app->db5;

            // Prepariamo la query SQL complessa che hai fornito
            // Usiamo i parametri (:th_id) per sicurezza contro la SQL Injection
            $sql = "
                UPDATE xtravelhead 
                SET 
                    xtravelhead.righe = (
                        SELECT COUNT(*) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id
                    ),
                    xtravelhead.totft = (
                        SELECT SUM(ISNULL(xtravelrow.fee, 0)+
                        ISNULL(xtravelrow.imponibile, 0)+
                        ISNULL(xtravelrow.qta*xtravelrow.tax_unit, 0)
                        
                        ) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    xtravelhead.fee = (
                        SELECT SUM(ISNULL(fee, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    xtravelhead.totaleservizi = (
                        SELECT SUM(ISNULL(totale, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    xtravelhead.tax = (
                        SELECT SUM(ISNULL(tax_unit*qta, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    )
                WHERE th_id = :th_id
            ";

            // Esecuzione della query
            $db->createCommand($sql, [':th_id' => $this->th_id])->execute();
        }
    }
*/

    /**
     * Esegue l'aggiornamento dei totali nella testata xtravelhead dopo il salvataggio della riga.
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->th_id) {
            $db = Yii::$app->db5;

            // --- QUERY ALLINEATA ESATTAMENTE AL COMPORTAMENTO DI FOXPRO ---
            $sql = "
                UPDATE xtravelhead 
                SET 
                    xtravelhead.righe = (
                        SELECT COUNT(*) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id
                    ),
                    
                    -- tot_ft in FoxPro: SUM(imponibile + tax)
                    xtravelhead.totft = (
                        SELECT SUM(ISNULL(imponibile, 0) + ISNULL(tax, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    
                    -- fee in FoxPro: SUM(fee)
                    xtravelhead.fee = (
                        SELECT SUM(ISNULL(fee, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    
                    -- tserv in FoxPro: SUM(prezzo * qta). Nel nostro DB 'totale' è già prezzo * qta
                    xtravelhead.totaleservizi = (
                        SELECT SUM(ISNULL(totale, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    ),
                    
                    -- tottassa in FoxPro: SUM(tax)
                    xtravelhead.tax = (
                        SELECT SUM(ISNULL(tax, 0)) 
                        FROM xtravelrow 
                        WHERE th_id = xtravelhead.th_id 
                        AND cd_Ar NOT IN (SELECT cd_ar FROM ar WHERE x_isacconto = 1)
                    )
                WHERE th_id = :th_id
            ";

            $db->createCommand($sql, [':th_id' => $this->th_id])->execute();
        }
    }

    /**
     * Ricalcola i totali in modo identico anche quando una riga viene eliminata
     */
    public function afterDelete()
    {
        parent::afterDelete();

        if ($this->th_id) {
            // Richiama l'afterSave (passando false come insert) per ricalcolare i totali
            $this->afterSave(false, []);
        }
    }


    
}
