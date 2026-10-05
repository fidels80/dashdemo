<?php

use yii\db\Migration;

/**
 * Correzione dei vincoli di integrità del registro API.
 *
 * 1. La regola "articoli-um -> mg_documento_riga.id_unita_misura" era priva di
 *    senso: mg_articolo_um non e' il padre di quella colonna, che punta a
 *    mg_unita_misura (gia' regolata sotto "unita-misura"). Viene disattivata,
 *    non cancellata, per non perdere lo storico.
 * 2. Le regole derivate dalle chiavi esterne reali mancanti vengono inserite:
 *    sono figli con ON DELETE CASCADE, quindi bloccano la cancellazione del
 *    padre e possono essere smontati solo su richiesta esplicita.
 */
class m261005_150000_fix_api_entita_seed extends Migration
{
    public function safeUp()
    {
        $db = Yii::$app->db;

        $db->createCommand(
            "UPDATE dash_api_rel SET attiva = 0
             WHERE entita = 'articoli-um' AND tabella = 'mg_documento_riga' AND colonna = 'id_unita_misura'"
        )->execute();

        $db->createCommand(
            "DELETE FROM dash_api_rel
             WHERE entita = 'anagrafiche' AND tipo = 'figlio' AND tabella = 'mg_anagrafica_contatto' AND colonna = 'id_anagrafica'"
        )->execute();
        $db->createCommand(
            "DELETE FROM dash_api_rel
             WHERE entita = 'articoli' AND tabella = 'mg_articolo_um' AND colonna = 'id_articolo'"
        )->execute();
        $db->createCommand(
            "DELETE FROM dash_api_rel
             WHERE entita = 'attributi-articolo' AND tabella = 'mg_modello_tessuto' AND colonna = 'id_modello'"
        )->execute();

        $righe = [
            ['anagrafiche', 'figlio', 'mg_anagrafica_contatto', 'id_anagrafica', 'contatti di questa anagrafica', 1, 40],
            ['articoli', 'figlio', 'mg_articolo_um', 'id_articolo', 'conversioni di unita di misura', 1, 20],
            ['attributi-articolo', 'figlio', 'mg_modello_tessuto', 'id_modello', 'abbinamenti con questo modello', 1, 60],
        ];

        foreach ($righe as $r) {
            $db->createCommand()->insert('dash_api_rel', [
                'entita' => $r[0],
                'tipo' => $r[1],
                'tabella' => $r[2],
                'colonna' => $r[3],
                'etichetta' => $r[4],
                'cascade' => $r[5],
                'attiva' => 1,
                'ordinamento' => $r[6],
            ])->execute();
        }
    }

    public function safeDown()
    {
        $db = Yii::$app->db;

        $db->createCommand(
            "UPDATE dash_api_rel SET attiva = 1
             WHERE entita = 'articoli-um' AND tabella = 'mg_documento_riga' AND colonna = 'id_unita_misura'"
        )->execute();

        $db->createCommand(
            "UPDATE dash_api_rel SET tipo = 'riferimento', cascade = 0
             WHERE entita IN ('anagrafiche', 'articoli', 'attributi-articolo')
               AND tabella IN ('mg_anagrafica_contatto', 'mg_articolo_um', 'mg_modello_tessuto')
               AND colonna IN ('id_anagrafica', 'id_articolo', 'id_modello')"
        )->execute();
    }
}
