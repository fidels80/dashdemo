<?php

use yii\db\Migration;

/**
 * Rimuove la regola duplicata su mg_anagrafica_contatto.
 *
 * La migrazione di correzione precedente ha sostituito la regola "figlio" che
 * non esisteva ancora, lasciando in piedi il vecchio "riferimento" sulla stessa
 * tabella/colonna. La presenza di entrambe faceva fallire la cancellazione a
 * cascata delle anagrafiche: il riferimento non e' mai rimosso in automatico.
 */
class m261005_160000_fix_api_rel_duplicati extends Migration
{
    public function safeUp()
    {
        Yii::$app->db->createCommand(
            "DELETE FROM dash_api_rel
             WHERE entita = 'anagrafiche'
               AND tabella = 'mg_anagrafica_contatto'
               AND colonna = 'id_anagrafica'
               AND tipo = 'riferimento'"
        )->execute();
    }

    public function safeDown()
    {
        Yii::$app->db->createCommand(
            "INSERT INTO dash_api_rel (entita, tipo, tabella, colonna, etichetta, cascade, attiva, ordinamento)
             VALUES ('anagrafiche', 'riferimento', 'mg_anagrafica_contatto', 'id_anagrafica',
                     N'contatti di questa anagrafica', 0, 1, 40)"
        )->execute();
    }
}
