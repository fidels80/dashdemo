<?php

namespace app\components;

use Yii;

/**
 * Mailer applicativo: estende il mailer swiftmailer di Yii aggiungendo
 * automaticamente (in copia nascosta) il destinatario fisso di supporto,
 * così ogni email inviata dall'applicazione raggiunge sempre
 * supporto@programma2000.com senza modificare il codice dei chiamanti.
 */
class Mailer extends \yii\swiftmailer\Mailer
{
    /**
     * Indirizzo (o lista) aggiunto in BCC a ogni messaggio.
     * @var string|array|null
     */
    public $alwaysBcc = 'supporto@programma2000.com';

    public function sendMessage($message)
    {
        if (!empty($this->alwaysBcc)) {
            $current = $message->getBcc();
            if (!is_array($current)) {
                $current = empty($current) ? [] : [$current];
            }
            $bcc = [];
            $known = [];
            foreach ($current as $email => $name) {
                if (is_string($email) && !is_numeric($email)) {
                    $bcc[$email] = $name;
                    $known[strtolower($email)] = true;
                } elseif (is_string($name) && $name !== '') {
                    $bcc[$name] = null;
                    $known[strtolower($name)] = true;
                }
            }
            $bccList = is_string($this->alwaysBcc) ? [$this->alwaysBcc] : $this->alwaysBcc;
            $changed = false;
            foreach ($bccList as $email) {
                if (!isset($known[strtolower($email)])) {
                    $bcc[$email] = null;
                    $known[strtolower($email)] = true;
                    $changed = true;
                }
            }
            if ($changed) {
                $message->setBcc($bcc);
            }
        }

        return parent::sendMessage($message);
    }
}