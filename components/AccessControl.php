<?php

namespace app\components;

use app\models\DashPermesso;
use app\models\DashPermessoUtente;

/**
 * Controllo accessi per form/funzione (ACL) basato su dash_permesso/dash_permesso_utente.
 *
 * Regole:
 * - le rotte in $exemptPrefixes sono sempre consentite agli utenti autenticati;
 * - il livello 100 (supervisore) ha sempre accesso completo;
 * - senza configurazione esplicita l'accesso e' NEGATO (default deny);
 * - l'azione viene mappata a una capacita': view / create / update / delete.
 */
class AccessControl
{
    /** Rotte sempre consentite (oltre a quelle pubbliche). */
    public static $exemptPrefixes = [
        'site/',
        'two-factor/',
        'user/update',
        'user/view',
        'user/cf',
        'user/rp',
        'user/request-password-reset',
        'user/reset-password',
    ];

    /**
     * Mappa l'id dell'azione sulla capacita' richiesta.
     */
    public static function capabilityForAction($actionId)
    {
        $actionId = strtolower((string) $actionId);

        if ($actionId === 'delete' || strpos($actionId, 'delete') === 0) {
            return 'delete';
        }

        if ($actionId === 'create' || strpos($actionId, 'create') === 0
            || in_array($actionId, ['copy-day', 'duplicate', 'import',
                'sprint-create', 'add-comment', 'comment', 'addreply', 'signup'], true)) {
            return 'create';
        }

        if (in_array($actionId, ['update', 'quick-update', 'inline-update', 'move',
            'set-sprint', 'sprint-start', 'sprint-close', 'enable', 'disable',
            'revoke-trusted-devices'], true)) {
            return 'update';
        }

        return 'view';
    }

    public static function isExempt($route)
    {
        foreach (self::$exemptPrefixes as $p) {
            if (strpos($route, $p) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Utenti con accesso completo: livello 100 oppure email in params['superEmails'].
     */
    public static function isSuper($user)
    {
        if (!$user) {
            return false;
        }
        $level = $user->hasAttribute('level') ? (int) $user->level : 0;
        if ($level >= 100) {
            return true;
        }
        $emails = \Yii::$app->params['superEmails'] ?? [];
        return !empty($user->email) && in_array(strtolower($user->email), array_map('strtolower', $emails), true);
    }

    /**
     * Verifica se l'utente puo' eseguire l'azione sul controller indicato.
     *
     * @param string $controllerId
     * @param string $actionId
     * @param \app\models\User $user
     * @return bool
     */
    public static function allowed($controllerId, $actionId, $user)
    {
        $route = $controllerId . '/' . $actionId;

        if (self::isExempt($route)) {
            return true;
        }

        if (!$user) {
            return false;
        }

        // Supervisore (livello 100 o email in superEmails): accesso completo
        if (self::isSuper($user)) {
            return true;
        }

        $permesso = DashPermesso::findByCodice($controllerId);
        if (!$permesso) {
            return false; // risorsa non registrata -> nega
        }

        $ass = DashPermessoUtente::findOne([
            'user_id' => $user->id,
            'permesso_id' => $permesso->id,
        ]);
        if (!$ass) {
            return false; // default deny
        }

        switch (self::capabilityForAction($actionId)) {
            case 'create':
                return (bool) $ass->can_create;
            case 'update':
                return (bool) $ass->can_update;
            case 'delete':
                return (bool) $ass->can_delete;
            default:
                return (bool) $ass->can_view;
        }
    }
}
