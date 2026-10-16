<?php

namespace app\components;

use Yii;
use app\models\MgCrmAllegato;
use yii\web\UploadedFile;

/**
 * Gestione degli allegati del CRM: salvataggio su disco in
 * web/uploads/crm e invio al browser.
 */
class AllegatiCrm
{
    /**
     * Cartella fisica degli allegati (creata se non esiste).
     */
    public static function cartella()
    {
        $dir = Yii::getAlias('@webroot') . '/uploads/crm';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    /**
     * Salva un file caricato e crea il record mg_crm_allegato.
     *
     * @param UploadedFile $file
     * @param int|null $idTicket
     * @param int|null $idMessaggio
     * @param int|null $userId
     * @return MgCrmAllegato|null
     */
    public static function salva(UploadedFile $file, $idTicket = null, $idMessaggio = null, $userId = null)
    {
        $dir = self::cartella();
        $ext = strtolower($file->extension ?: pathinfo($file->name, PATHINFO_EXTENSION));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext);

        $nomeFile = 'crm_' . date('YmdHis') . '_' . bin2hex(random_bytes(4))
            . ($ext !== '' ? '.' . $ext : '');

        if (!$file->saveAs($dir . '/' . $nomeFile)) {
            return null;
        }

        $allegato = new MgCrmAllegato();
        $allegato->id_ticket = $idTicket ?: null;
        $allegato->id_messaggio = $idMessaggio ?: null;
        $allegato->user_id = $userId ?: null;
        $allegato->nome_file = $nomeFile;
        $allegato->nome_originale = $file->name;
        $allegato->mime = self::mime($dir . '/' . $nomeFile, $file);
        $allegato->dimensione = @filesize($dir . '/' . $nomeFile) ?: null;
        $allegato->percorso = 'uploads/crm/' . $nomeFile;
        $allegato->save(false);

        return $allegato;
    }

    /**
     * Elabora piu' file caricati con lo stesso name input (files[]).
     *
     * @param int|null $idTicket
     * @param int|null $idMessaggio
     * @param int|null $userId
     * @return int numero di allegati salvati
     */
    public static function salvaMultipli($idTicket = null, $idMessaggio = null, $userId = null)
    {
        $files = UploadedFile::getInstancesByName('files');
        $n = 0;
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && self::salva($file, $idTicket, $idMessaggio, $userId) !== null) {
                $n++;
            }
        }
        return $n;
    }

    /**
     * Invia un allegato al browser.
     *
     * @param MgCrmAllegato $allegato
     * @param bool $download true = scarica, false = mostra inline (se possibile)
     */
    public static function invia(MgCrmAllegato $allegato, $download = false)
    {
        $path = $allegato->percorsoServibile;
        if (!is_file($path)) {
            Yii::$app->response->setStatusCode(404);
            Yii::$app->response->content = 'Allegato non trovato sul disco.';
            return;
        }

        Yii::$app->response->sendFile(
            $path,
            $allegato->nome_originale,
            [
                'mimeType' => $allegato->mime ?: 'application/octet-stream',
                'inline' => !$download && $allegato->isImmagine,
            ]
        );
    }

    private static function mime($path, UploadedFile $file)
    {
        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($path);
            if ($mime) {
                return $mime;
            }
        }
        return $file->type ?: 'application/octet-stream';
    }
}
