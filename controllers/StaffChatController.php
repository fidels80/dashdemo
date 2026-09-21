<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\web\Response;

class StaffChatController extends Controller
{
    /**
     * Recupera le info dell'utente corrente (livello e cd_cli)
     */
    private function getMyInfo()
    {
        return (new Query())
            ->select(['id', 'level', 'cd_cli'])
            ->from('user')
            ->where(['id' => Yii::$app->user->id])
            ->one();
    }

    /**
     * LISTA UTENTI FILTRATA PER PERMESSI
     */
public function actionUserList()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $me = $this->getMyInfo();
        $myId = $me['id'];
        $myLevel = (int)$me['level'];
        $myCli = $me['cd_cli'];

        // Sottquery per contare i messaggi non letti inviati da ogni utente a ME
        $unreadSubquery = (new Query())
            ->select(['user_id', 'COUNT(*) AS cnt'])
            ->from('sys_chat_messages')
            ->where(['to_user_id' => $myId, 'is_read' => 0])
            ->groupBy('user_id');

        // Query principale sugli utenti
        $query = (new Query())
            ->select([
                '[[user]].id', 
                '[[user]].username', 
                '[[user]].[[file]]', 
                'ISNULL(unread.cnt, 0) AS unread_count' // Per SQL Server usiamo ISNULL
            ])
            ->from('[[user]]')
            ->leftJoin(['unread' => $unreadSubquery], 'unread.user_id = [[user]].id')
            ->where(['!=', '[[user]].id', $myId]);

        // Subquery per i permessi (chi mi ha scritto)
        $whoWroteMe = (new Query())
            ->select('user_id')
            ->from('sys_chat_messages')
            ->where(['to_user_id' => $myId]);

        if ($myLevel == 70) {
            $query->andWhere([
                'OR',
                ['AND', ['level' => 70], ['cd_cli' => $myCli]],
                ['in', '[[user]].id', $whoWroteMe]
            ]);
        } 
        elseif ($myLevel == 80) {
            $query->andWhere([
                'OR',
                ['in', 'level', [70, 80]],
                ['in', '[[user]].id', $whoWroteMe]
            ]);
        }

        return $query->all();
    }
    /**
     * INVIO MESSAGGIO CON VALIDAZIONE PERMESSI E NOTIFICA EMAIL
     */
   /**
     * INVIO MESSAGGIO CON VALIDAZIONE PERMESSI E NOTIFICA EMAIL
     */
    public function actionSend()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $message = Yii::$app->request->post('message');
        $toUserId = (int)Yii::$app->request->post('to_user_id');

        $me = $this->getMyInfo();
        $myLevel = (int)$me['level'];
        $myCli = $me['cd_cli'];
        $myUsername = Yii::$app->user->identity->username; 

        // --- CORREZIONE: Abbiamo aggiunto 'last_activity' nella select ---
        $toUser = (new Query())
            ->select(['level', 'cd_cli', 'email', 'username', 'last_activity']) 
            ->from('[[user]]')
            ->where(['id' => $toUserId])
            ->one();

        if (!$toUser && $toUserId !== 0) return ['status' => 'error', 'message' => 'Destinatario non trovato'];
        if (!$message) return ['status' => 'error', 'message' => 'Messaggio vuoto'];

        $canSend = false;
        // ... (il resto della logica dei permessi rimane identica) ...
        if (empty($toUserId)) { $canSend = true; } 
        elseif ($myLevel >= 100) { $canSend = true; } 
        elseif ($myLevel == 80) {
            if (in_array($toUser['level'], [70, 80])) { $canSend = true; } 
            else {
                $hasReceived = (new Query())->from('sys_chat_messages')->where(['user_id' => $toUserId, 'to_user_id' => $me['id']])->exists();
                if ($hasReceived) $canSend = true;
            }
        } elseif ($myLevel == 70) {
            if ($toUser['level'] == 70 && $toUser['cd_cli'] == $myCli) { $canSend = true; } 
            else {
                $hasReceived = (new Query())->from('sys_chat_messages')->where(['user_id' => $toUserId, 'to_user_id' => $me['id']])->exists();
                if ($hasReceived) $canSend = true;
            }
        }

        if ($canSend) {
            // 1. Salvataggio nel DB
            Yii::$app->db->createCommand()->insert('sys_chat_messages', [
                'user_id' => $me['id'],
                'to_user_id' => $toUserId ?: null,
                'message' => $message,
                'created_at' => date('Ymd H:i:s')
            ])->execute();

            // --- 2. LOGICA INVIO EMAIL (OTTIMIZZATA) ---
            if ($toUserId && !empty($toUser['email'])) {
                
                $isOffline = true; 
                
                if (!empty($toUser['last_activity'])) {
                    $lastActivityTime = strtotime($toUser['last_activity']);
                    $currentTime = time();
                    $diffSeconds = $currentTime - $lastActivityTime;

                    // DEBUG (Opzionale): puoi loggare la differenza per verificare i tempi
                    // Yii::error("Differenza secondi per utente $toUserId: " . $diffSeconds);

                    // Se l'attività è avvenuta negli ultimi 3 minuti (180 secondi), lo consideriamo online.
                    // Accorciamo un po' il tempo per essere più reattivi.
                    if ($diffSeconds < 180) { 
                        $isOffline = false; 
                    }
                }

                if ($isOffline) {
                    $testoEmail = "Ciao " . $toUser['username'] . ",\n\n"
                                . "Hai ricevuto un nuovo messaggio privato da " . $myUsername . " sulla piattaforma:\n\n"
                                . "\"" . $message . "\"\n\n"
                                . "Accedi alla tua Dashboard per continuare la conversazione.\n\n"
                                . "Un saluto,\nIl Team di " . Yii::$app->name;

                    try {
                        Yii::$app->mailer->compose()
                            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' - Chat'])
                            ->setTo($toUser['email'])
                            ->setSubject('Nuovo messaggio da ' . $myUsername)
                            ->setTextBody($testoEmail)
                            ->send();
                    } catch (\Exception $e) {
                        Yii::error("Errore invio email chat: " . $e->getMessage());
                    }
                }
            }
            return ['status' => 'success'];
        }

        return ['status' => 'error', 'message' => 'Permessi insufficienti.'];
    }
/**
     * RECUPERO MESSAGGI
     */
public function actionFetch() {
        $myId = Yii::$app->user->id;
        $contactId = Yii::$app->request->get('contact_id');

// --- AGGIUNTA: IL BATTITO CARDIACO ---
        // Ogni volta che la chat cerca nuovi messaggi (ogni 5 sec), aggiorniamo l'orario di attività.
        if ($myId) {
            Yii::$app->db->createCommand()
                ->update('[[user]]', ['last_activity' => date('Ymd H:i:s')], ['id' => $myId])
                ->execute();
       
       if ($contactId && $myId) {
            Yii::$app->db->createCommand()
                ->update('sys_chat_messages', ['is_read' => 1], ['to_user_id' => $myId, 'user_id' => $contactId])
                ->execute();
        }
       
                }
        // ------------------------------------



        $query = (new Query())
            // Solo UN select, con le parentesi quadre attorno a user e file
            ->select(['sys_chat_messages.*', '[[user]].username', '[[user]].[[file]]'])
            ->from('sys_chat_messages')
            ->leftJoin('user', '[[user]].id = sys_chat_messages.user_id');

        if ($contactId) {
            $query->where([
                'OR',
                ['sys_chat_messages.user_id' => $myId, 'to_user_id' => $contactId],
                ['sys_chat_messages.user_id' => $contactId, 'to_user_id' => $myId]
            ]);
        } else {
            // Chat globale
            $query->where(['to_user_id' => null]);
        }

        $messages = $query->orderBy(['created_at' => SORT_ASC])->limit(50)->all();
        
        return $this->renderPartialMessages($messages, $myId); 
    }
    
    /**
     * GENERAZIONE DELL'HTML DEI MESSAGGI
     */
    private function renderPartialMessages($messages, $myId) {
        $html = "";
        $assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
        
        foreach ($messages as $msg) {
            $isMe = ($msg['user_id'] == $myId);
            $classMsg = $isMe ? 'right' : '';
            $name = \yii\helpers\Html::encode($msg['username'] ?? 'Utente sconosciuto');
            $text = \yii\helpers\Html::encode($msg['message']);
            $time = date('H:i', strtotime($msg['created_at']));
            
            // --- GESTIONE AVATAR ---
            if (!empty($msg['file'])) {
                // Se c'è un file caricato, costruiamo il percorso: /uploads/id_nomefile
                $avatarUrl = '/uploads/' . $msg['user_id'] . '_' . $msg['file'];
            } else {
                // Se non ha l'avatar, gli diamo quello finto di default di AdminLTE
                $imgName = $isMe ? 'user3-128x128.jpg' : 'user1-128x128.jpg';
                $avatarUrl = $assetDir . '/img/' . $imgName;
            }

            // Aggiunto "object-fit: cover" per evitare che foto rettangolari si deformino nel tondino
            $html .= "<div class='direct-chat-msg $classMsg'>
                        <div class='direct-chat-infos clearfix'>
                            <span class='direct-chat-name float-".($isMe?'right':'left')."'>$name</span>
                            <span class='direct-chat-timestamp float-".($isMe?'left':'right')."'>$time</span>
                        </div>
                        <img class='direct-chat-img' src='$avatarUrl' style='object-fit: cover;' alt='User Image'>
                        <div class='direct-chat-text'>$text</div>
                    </div>";
        }
        return $html;
    }





















    /**
     * RECUPERO MESSAGGI
     */
    public function ____actionFetch()
    {
        $myId = Yii::$app->user->id;
        $contactId = Yii::$app->request->get('contact_id');

        $query = (new Query())
            ->select(['sys_chat_messages.*', '[[user]].username'])
            ->from('sys_chat_messages')
            ->leftJoin('user', '[[user]].id = sys_chat_messages.user_id');

        if ($contactId) {
            $query->where([
                'OR',
                ['sys_chat_messages.user_id' => $myId, 'to_user_id' => $contactId],
                ['sys_chat_messages.user_id' => $contactId, 'to_user_id' => $myId]
            ]);
        } else {
            // Chat globale
            $query->where(['to_user_id' => null]);
        }

        $messages = $query->orderBy(['created_at' => SORT_ASC])->limit(50)->all();

        return $this->renderPartialMessages($messages, $myId);
    }

    private function ______renderPartialMessages($messages, $myId)
    {
        $html = "";
        $assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
        foreach ($messages as $msg) {
            $isMe = ($msg['user_id'] == $myId);
            $classMsg = $isMe ? 'right' : '';
            $name = \yii\helpers\Html::encode($msg['username'] ?? 'Utente sconosciuto');
            $text = \yii\helpers\Html::encode($msg['message']);
            $time = date('H:i', strtotime($msg['created_at']));
            $img = $isMe ? 'user3-128x128.jpg' : 'user1-128x128.jpg';

            $html .= "<div class='direct-chat-msg $classMsg'>
                        <div class='direct-chat-infos clearfix'>
                            <span class='direct-chat-name float-" . ($isMe ? 'right' : 'left') . "'>$name</span>
                            <span class='direct-chat-timestamp float-" . ($isMe ? 'left' : 'right') . "'>$time</span>
                        </div>
                        <img class='direct-chat-img' src='$assetDir/img/$img'>
                        <div class='direct-chat-text'>$text</div>
                    </div>";
        }
        return $html;
    }
}
