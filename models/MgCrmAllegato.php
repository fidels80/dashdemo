<?php

namespace app\models;

use Yii;

/**
 * Allegato di un ticket CRM o di un singolo commento.
 *
 * @property int $id
 * @property int|null $id_ticket
 * @property int|null $id_messaggio
 * @property int|null $user_id
 * @property string $nome_file
 * @property string $nome_originale
 * @property string|null $mime
 * @property int|null $dimensione
 * @property string $percorso
 * @property string|null $created_at
 *
 * @property MgCrmTicket|null $ticket
 * @property MgCrmTicketMessaggio|null $messaggio
 * @property User|null $utente
 */
class MgCrmAllegato extends \yii\db\ActiveRecord
{
    /** Estensioni considerate immagini (anteprima). */
    private static $estensioniImmagine = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];

    public static function tableName()
    {
        return 'mg_crm_allegato';
    }

    public function rules()
    {
        return [
            [['nome_file', 'nome_originale', 'percorso'], 'required'],
            [['id_ticket', 'id_messaggio', 'user_id', 'dimensione'], 'integer'],
            [['created_at'], 'safe'],
            [['nome_file', 'nome_originale'], 'string', 'max' => 255],
            [['mime'], 'string', 'max' => 100],
            [['percorso'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ticket' => 'Ticket',
            'id_messaggio' => 'Commento',
            'user_id' => 'Utente',
            'nome_file' => 'Nome file',
            'nome_originale' => 'Nome originale',
            'mime' => 'Tipo',
            'dimensione' => 'Dimensione',
            'percorso' => 'Percorso',
            'created_at' => 'Caricato il',
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(MgCrmTicket::className(), ['id' => 'id_ticket']);
    }

    public function getMessaggio()
    {
        return $this->hasOne(MgCrmTicketMessaggio::className(), ['id' => 'id_messaggio']);
    }

    public function getUtente()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getEstensione()
    {
        return strtolower(pathinfo((string) $this->nome_originale, PATHINFO_EXTENSION));
    }

    public function getIsImmagine()
    {
        return in_array($this->estensione, self::$estensioniImmagine, true);
    }

    public function getIcona()
    {
        $ext = $this->estensione;
        if ($this->isImmagine) {
            return 'fas fa-file-image';
        }
        if ($ext === 'pdf') {
            return 'fas fa-file-pdf';
        }
        if (in_array($ext, ['doc', 'docx', 'odt', 'rtf'], true)) {
            return 'fas fa-file-word';
        }
        if (in_array($ext, ['xls', 'xlsx', 'ods', 'csv'], true)) {
            return 'fas fa-file-excel';
        }
        if (in_array($ext, ['zip', 'rar', '7z'], true)) {
            return 'fas fa-file-archive';
        }
        return 'fas fa-file';
    }

    public function getPercorsoServibile()
    {
        return Yii::getAlias('@webroot') . '/' . ltrim((string) $this->percorso, '/\\');
    }

    /**
     * Rappresentazione leggibile della dimensione.
     */
    public function getDimensioneLabel()
    {
        $b = (int) $this->dimensione;
        if ($b < 1024) {
            return $b . ' B';
        }
        if ($b < 1024 * 1024) {
            return round($b / 1024, 1) . ' KB';
        }
        return round($b / (1024 * 1024), 1) . ' MB';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->created_at)) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
            }
            return true;
        }
        return false;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $path = $this->percorsoServibile;
        if ($path && is_file($path)) {
            @unlink($path);
        }
        return true;
    }

    /**
     * Allegati di un ticket non legati a un commento (allegati "di testata").
     */
    public static function perTicket($idTicket)
    {
        return self::find()
            ->where(['id_ticket' => (int) $idTicket, 'id_messaggio' => null])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    /**
     * Allegati di un commento.
     */
    public static function perMessaggio($idMessaggio)
    {
        return self::find()
            ->where(['id_messaggio' => (int) $idMessaggio])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }
}
