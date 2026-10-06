<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_token".
 *
 * La colonna "scopes" contiene i permessi del token, nella forma
 * "entita:operazione" separati da virgola (vedi app\components\ApiAccess).
 * Il valore "*" concede tutte le entita' e tutte le operazioni.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $descrizione
 * @property string $token_hash
 * @property string|null $scopes
 * @property int|null $expires_at
 * @property int $created_at
 * @property int|null $last_used_at
 */
class ApiToken extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'api_token';
    }

    public function rules()
    {
        return [
            [['token_hash', 'created_at'], 'required'],
            [['user_id', 'expires_at', 'created_at', 'last_used_at'], 'integer'],
            [['token_hash'], 'string', 'max' => 64],
            [['descrizione'], 'string', 'max' => 200],
            [['scopes'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utente',
            'descrizione' => 'Descrizione',
            'token_hash' => 'Token',
            'scopes' => 'Permessi',
            'expires_at' => 'Scadenza',
            'created_at' => 'Creato il',
            'last_used_at' => 'Ultimo utilizzo',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * Crea un nuovo token e restituisce il valore in chiaro (mostrato una sola volta).
     */
    public static function generate($descrizione, $userId = null, $expiresAt = null, $scopes = '*')
    {
        $plain = bin2hex(Yii::$app->security->generateRandomKey(32));

        $model = new self();
        $model->user_id = $userId;
        $model->descrizione = $descrizione;
        $model->token_hash = self::hashToken($plain);
        $model->scopes = $scopes;
        $model->expires_at = $expiresAt;
        $model->created_at = time();

        if (!$model->save()) {
            return null;
        }

        return $plain;
    }

    /**
     * Trova un token valido (non scaduto) a partire dal valore in chiaro.
     */
    public static function findValid($plainToken)
    {
        if (empty($plainToken)) {
            return null;
        }

        $model = self::find()->where(['token_hash' => self::hashToken($plainToken)])->one();
        if (!$model) {
            return null;
        }

        if ($model->expires_at !== null && $model->expires_at < time()) {
            return null;
        }

        return $model;
    }

    public function markUsed()
    {
        if ($this->isNewRecord) {
            return false;
        }
        $this->last_used_at = time();
        return $this->updateAttributes(['last_used_at']);
    }

    /**
     * True se il token non ha ancora scaduto.
     *
     * @return bool
     */
    public function isScaduto()
    {
        return $this->expires_at !== null && (int) $this->expires_at < time();
    }

    /**
     * I permessi del token come mappa entita' => lista operazioni.
     *
     * @return array<string, string[]>
     */
    public function getScope()
    {
        return \app\components\ApiAccess::parse($this->scopes);
    }

    /**
     * Riepilogo leggibile dei permessi, da mostrare nell'elenco.
     * Con il jolly "*" restituisce un riepilogo compatto.
     *
     * @return string
     */
    public function getPermessiLeggibili()
    {
        $scope = trim((string) $this->scopes);
        if ($scope === '') {
            return 'nessun accesso';
        }
        if ($scope === \app\components\ApiAccess::TUTTE) {
            return 'pieno accesso (tutte le entità, tutte le operazioni)';
        }

        $righe = [];
        foreach ($this->getScope() as $codice => $ops) {
            $entita = \app\models\DashApiEntita::findOne(['codice' => $codice]);
            $nome = $entita ? $entita->descrizione : $codice;
            $brevi = [];
            foreach ($ops as $op) {
                switch ($op) {
                    case \app\components\ApiAccess::OP_READ:
                        $brevi[] = 'lettura';
                        break;
                    case \app\components\ApiAccess::OP_INSERT:
                        $brevi[] = 'inserimento';
                        break;
                    case \app\components\ApiAccess::OP_UPDATE:
                        $brevi[] = 'modifica';
                        break;
                    case \app\components\ApiAccess::OP_DELETE:
                        $brevi[] = 'cancellazione';
                        break;
                }
            }
            $righe[] = $nome . ' (' . implode(', ', $brevi) . ')';
        }

        return empty($righe) ? 'nessun accesso' : implode('; ', $righe);
    }

    /**
     * Costruisce la stringa degli scope da una mappa entita' => operazioni.
     * Le entita' senza operazioni valide vengono scartate.
     *
     * @param array<string, string|string[]> $permessi
     * @return string
     */
    public static function composiScopes(array $permessi)
    {
        $voci = [];
        foreach ($permessi as $codice => $ops) {
            $codice = trim((string) $codice);
            if ($codice === '') {
                continue;
            }
            $norm = \app\components\ApiAccess::normalizzaOperazioni($ops);
            $norm = array_values(array_diff($norm, ['solo']));
            if (empty($norm)) {
                continue;
            }
            $voci[] = $codice . ':' . implode('+', $norm);
        }
        return empty($voci) ? '' : implode(',', $voci);
    }

    /**
     * Espande gli scope dell'entita' indicata, per la UI di modifica.
     *
     * @param string $codice
     * @return string[]
     */
    public function operazioniPer($codice)
    {
        $scope = $this->getScope();
        return isset($scope[$codice]) ? $scope[$codice] : [];
    }
}
