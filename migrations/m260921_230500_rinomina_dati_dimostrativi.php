<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Sostituisce i nomi dimostrativi delle anagrafiche e degli articoli
 * ("Azienda Demo NNN ..." / "Articolo demo NNN") con nomi inventati.
 * Idempotente: agisce solo sulle righe ancora marcate come demo.
 */
class m260921_230500_rinomina_dati_dimostrativi extends Migration
{
    private $cognomi = [
        'Rossi', 'Bianchi', 'Ferrari', 'Russo', 'Romano', 'Colombo', 'Ricci', 'Marino',
        'Greco', 'Bruno', 'Gallo', 'Conti', 'De Luca', 'Costa', 'Giordano', 'Mancini',
        'Rizzo', 'Lombardi', 'Moretti', 'Barbieri', 'Fontana', 'Santoro', 'Mariani', 'Rinaldi',
        'Caruso', 'Ferrara', 'Galli', 'Martini', 'Leone', 'Longo', 'Gentile', 'Martinelli',
        'Vitale', 'Lombardo', 'Serra', 'Coppola', 'De Santis', 'D\'Angelo', 'Marchetti', 'Parisi',
        'Villa', 'Conte', 'Ferraro', 'Ferri', 'Fabbri', 'Bianco', 'Marini', 'Grillo',
        'Valentini', 'Messina', 'Sala', 'De Angelis', 'Gatti', 'Pellegrini', 'Palumbo', 'Sanna',
        'Farina', 'Rizzi', 'Monti', 'Cattaneo', 'Morelli', 'Amato', 'Silvestri', 'Mazza',
        'Testa', 'Grassi', 'Pellegrino', 'Carbone', 'Giuliani', 'Benedetti', 'Barone', 'Rossetti',
        'Caputo', 'Montanari', 'Guerra', 'Palmieri', 'Bernardi', 'Martino', 'Fiore', 'De Rosa',
        'Ferretti', 'Bellini', 'Basile', 'Riva', 'Donati', 'Piras', 'Vitali', 'Battaglia',
        'Sartori', 'Neri', 'Costantini', 'Milani', 'Pagano', 'Ruggiero', 'Sorrentino', 'D\'Amico',
        'Negri', 'Guerrini', 'Orlando', 'Ferrante',
    ];

    private $settori = [
        'Impianti', 'Costruzioni', 'Meccanica', 'Elettronica', 'Logistica',
        'Arredamenti', 'Tecnologie', 'Servizi', 'Energia', 'Automazione',
        'Utensileria', 'Plastica', 'Metalli', 'Legnami', 'Tessile',
        'Alimentari', 'Trasporti', 'Pulizie', 'Informatica', 'Ceramiche',
        'Verniciature', 'Ferramenta', 'Carpenteria', 'Fonderia', 'Tinteggiature',
    ];

    private $forme = ['SRL', 'SPA', 'SNC', 'SAS', 'SRLS'];

    private $prodotti = [
        'Vite a brugola', 'Bullone esagonale', 'Dado autobloccante', 'Rondella piana',
        'Vite autofilettante', 'Cavo elettrico', 'Tubo in acciaio inox', 'Guarnizione in gomma',
        'Cuscinetto a sfere', 'Ingranaggio cilindrico', 'Pompa idraulica', 'Valvola a sfera',
        'Motore elettrico', 'Sensore di prossimità', 'Contattore', 'Interruttore magnetotermico',
        'Fusibile', 'Relè', 'Trasformatore', 'Quadro elettrico',
        'Pressostato', 'Manometro', 'Filtro olio', 'Filtro aria',
        'Cinghia di trasmissione', 'Puleggia', 'Giunto elastico', 'Riduttore di velocità',
        'Elettrovalvola', 'Cilindro pneumatico', 'Compressore', 'Serbatoio',
        'Scambiatore di calore', 'Termostato', 'Resistenza elettrica', 'Cablaggio',
        'Connettore', 'Morsettiera', 'Canalina portacavi', 'Guaina termorestringente',
    ];

    public function safeUp()
    {
        $this->rinominaAnagrafiche();
        $this->rinominaArticoli();
    }

    public function safeDown()
    {
        // I nomi originali demo non sono ripristinabili univocamente.
        echo "  > safeDown: nessuna operazione (rinomina dati dimostrativi)\n";
    }

    private function rinominaAnagrafiche()
    {
        $rows = (new Query())->select(['id'])->from('{{%mg_anagrafica}}')
            ->where(['like', 'ragione_sociale', 'Azienda Demo'])
            ->orderBy(['id' => SORT_ASC])->column();

        $nCognomi = count($this->cognomi);
        $nSettori = count($this->settori);
        $nForme = count($this->forme);

        foreach ($rows as $i => $id) {
            $nome = $this->cognomi[$i % $nCognomi] . ' '
                . $this->settori[intdiv($i, $nCognomi) % $nSettori] . ' '
                . $this->forme[$i % $nForme];
            $this->update('{{%mg_anagrafica}}', ['ragione_sociale' => $nome], ['id' => $id]);
        }
        echo "  > anagrafiche rinominate: " . count($rows) . "\n";
    }

    private function rinominaArticoli()
    {
        $rows = (new Query())->select(['id'])->from('{{%mg_articolo}}')
            ->where(['like', 'descrizione', 'Articolo demo'])
            ->orderBy(['id' => SORT_ASC])->column();

        $nProdotti = count($this->prodotti);
        foreach ($rows as $i => $id) {
            $desc = $this->prodotti[$i % $nProdotti] . ' '
                . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
            $this->update('{{%mg_articolo}}', ['descrizione' => $desc], ['id' => $id]);
        }
        echo "  > articoli rinominati: " . count($rows) . "\n";
    }
}
