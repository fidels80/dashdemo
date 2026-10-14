<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "mg_dcf_controllo".
 *
 * Testata del controllo (riscontro) di un documento di trasporto a fornitore.
 *
 * @property int $id
 * @property int $id_documento
 * @property string $stato
 * @property float $totale_pezzi_documento
 * @property float $totale_documento
 * @property float $totale_pezzi_riscontrati
 * @property int $righe_totali
 * @property int $righe_riscontrate
 * @property int $righe_differenza
 * @property bool $email_inviata
 * @property string|null $email_destinatari
 * @property string|null $email_oggetto
 * @property string|null $email_inviata_at
 * @property int|null $id_utente
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MgDocumento $documento
 * @property MgDcfControlloRiga[] $righe
 */
class MgDcfControllo extends \yii\db\ActiveRecord
{
    const STATO_DA_CONTROLLARE = 'da_controllare';
    const STATO_IN_CORSO = 'in_corso';
    const STATO_COMPLETATO = 'completato';
    const STATO_INVIATO = 'inviato';

    public static function tableName()
    {
        return 'mg_dcf_controllo';
    }

    public function rules()
    {
        return [
            [['id_documento'], 'required'],
            [['id_documento', 'righe_totali', 'righe_riscontrate', 'righe_differenza', 'id_utente'], 'integer'],
            [['totale_pezzi_documento', 'totale_documento', 'totale_pezzi_riscontrati'], 'number'],
            [['email_inviata'], 'boolean'],
            [['email_inviata_at', 'created_at', 'updated_at'], 'safe'],
            [['stato'], 'string', 'max' => 20],
            [['email_destinatari'], 'string', 'max' => 400],
            [['email_oggetto'], 'string', 'max' => 200],
            [['id_documento'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento' => 'Documento',
            'stato' => 'Stato',
            'totale_pezzi_documento' => 'Totale pezzi documento',
            'totale_documento' => 'Totale documento',
            'totale_pezzi_riscontrati' => 'Totale pezzi riscontrati',
            'righe_totali' => 'Righe totali',
            'righe_riscontrate' => 'Righe riscontrate',
            'righe_differenza' => 'Righe con differenza',
            'email_inviata' => 'Email inviata',
            'email_destinatari' => 'Destinatari email',
            'email_oggetto' => 'Oggetto email',
            'email_inviata_at' => 'Email inviata il',
            'id_utente' => 'Utente',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public static function opzioniStato()
    {
        return [
            self::STATO_DA_CONTROLLARE => 'Da controllare',
            self::STATO_IN_CORSO => 'In corso',
            self::STATO_COMPLETATO => 'Completato',
            self::STATO_INVIATO => 'Segnalato',
        ];
    }

    public function getStatoLabel()
    {
        $opzioni = self::opzioniStato();
        return $opzioni[$this->stato] ?? $this->stato;
    }

    public function getStatoBadge()
    {
        return self::badgeStato($this->stato);
    }

    public static function badgeStato($stato)
    {
        $mappa = [
            self::STATO_DA_CONTROLLARE => 'secondary',
            self::STATO_IN_CORSO => 'warning',
            self::STATO_COMPLETATO => 'success',
            self::STATO_INVIATO => 'info',
        ];
        return $mappa[$stato] ?? 'secondary';
    }

    public function getDocumento()
    {
        return $this->hasOne(MgDocumento::className(), ['id' => 'id_documento']);
    }

    public function getRighe()
    {
        return $this->hasMany(MgDcfControlloRiga::className(), ['id_controllo' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    /**
     * True se tutte le righe del documento sono state riscontrate.
     */
    public function isCompleto()
    {
        return $this->righe_totali > 0 && $this->righe_riscontrate >= $this->righe_totali;
    }

    /**
     * True se almeno una riga riscontrata differisce dalla quantita' documento.
     */
    public function haDifferenze()
    {
        return $this->righe_differenza > 0;
    }

    /**
     * Restituisce (creando se serve) il controllo associato a un documento.
     */
    public static function perDocumento($idDocumento)
    {
        $controllo = self::findOne(['id_documento' => $idDocumento]);
        if ($controllo === null) {
            $controllo = new self();
            $controllo->id_documento = $idDocumento;
            $controllo->stato = self::STATO_DA_CONTROLLARE;
            $controllo->created_at = new Expression('GETDATE()');
        }
        return $controllo;
    }

    /**
     * Ricalcola i totali di testata (pezzi, differenze, stato) dalle righe
     * del documento e dai riscontri salvati. Non sovrascrive lo stato quando
     * l'email e' gia' stata inviata.
     */
    public function aggiornaTotali()
    {
        $documento = $this->documento;
        if ($documento === null) {
            return;
        }

        $righe = $documento->righe;
        $eps = 0.0001;
        $totPezziDoc = 0.0;
        $nTot = 0;
        $ids = [];
        $qtaDoc = [];
        foreach ($righe as $r) {
            $totPezziDoc += (float) $r->qta;
            $qtaDoc[(int) $r->id] = (float) $r->qta;
            $ids[] = (int) $r->id;
            $nTot++;
        }

        $totRis = 0.0;
        $nRis = 0;
        $nDiff = 0;
        if (!empty($ids)) {
            $controlli = MgDcfControlloRiga::find()
                ->where(['id_controllo' => $this->id, 'id_riga' => $ids])
                ->all();
            foreach ($controlli as $cr) {
                if ($cr->qta_riscontrata === null || $cr->qta_riscontrata === '') {
                    continue;
                }
                $qr = (float) $cr->qta_riscontrata;
                $qd = $qtaDoc[(int) $cr->id_riga] ?? (float) $cr->qta_documento;
                $nRis++;
                $totRis += $qr;
                if (abs(round($qr - $qd, 4)) > $eps) {
                    $nDiff++;
                }
            }
        }

        $this->totale_pezzi_documento = round($totPezziDoc, 4);
        $this->totale_documento = round((float) $documento->totale, 2);
        $this->totale_pezzi_riscontrati = round($totRis, 4);
        $this->righe_totali = $nTot;
        $this->righe_riscontrate = $nRis;
        $this->righe_differenza = $nDiff;

        if ($this->stato !== self::STATO_INVIATO) {
            if ($nRis === 0) {
                $this->stato = self::STATO_DA_CONTROLLARE;
            } elseif ($nRis < $nTot) {
                $this->stato = self::STATO_IN_CORSO;
            } else {
                $this->stato = self::STATO_COMPLETATO;
            }
        }

        $this->updated_at = new Expression('GETDATE()');
        $this->save(false);
    }

    /**
     * Differenza complessiva in pezzi (riscontrati - documento).
     */
    public function getDifferenzaPezzi()
    {
        return round((float) $this->totale_pezzi_riscontrati - (float) $this->totale_pezzi_documento, 4);
    }
}
