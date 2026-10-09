<?php

namespace app\components;

use app\models\MgDocumento;
use app\models\MgAnagrafica;
use app\models\MgAliquotaIva;
use app\models\MgConfigurazione;
use Yii;
use DOMDocument;
use DOMElement;

/**
 * Genera il file XML della fattura elettronica (formato FPR12 / FPA12)
 * a partire da un MgDocumento, usando i dati del cedente presi da
 * mg_configurazione e quelli del cessionario dall'anagrafica.
 *
 * Fino alla generazione dell'XML: firma digitale e invio al Sistema di
 * Interscambio sono volutamente esclusi.
 */
class FatturaElettronica
{
    const NS = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';

    /** @var MgDocumento */
    public $documento;

    /** @var string */
    public $formato;

    /** @var string */
    public $progressivoInvio;

    /** @var DOMDocument */
    private $dom;

    public function __construct(MgDocumento $documento, $config = [])
    {
        $this->documento = $documento;
        $this->formato = $config['formato'] ?? self::valoreConfig('fe.trasmissione.formato', 'FPR12');

        $prefisso = (string) self::valoreConfig('fe.trasmissione.progressivo_invio', '');
        $this->progressivoInvio = $config['progressivoInvio'] ?? ($prefisso . $documento->id);
    }

    /**
     * Restituisce l'XML della fattura elettronica.
     *
     * @return string
     */
    public function genera()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $this->dom = $dom;

        $root = $dom->createElementNS(self::NS, 'p:FatturaElettronica');
        $root->setAttribute('versione', $this->formato);
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $dom->appendChild($root);

        $header = $dom->createElement('FatturaElettronicaHeader');
        $root->appendChild($header);

        $header->appendChild($this->datiTrasmissione());
        $header->appendChild($this->cedentePrestatore());
        $header->appendChild($this->cessionarioCommittente());

        $body = $dom->createElement('FatturaElettronicaBody');
        $root->appendChild($body);

        $body->appendChild($this->datiGenerali());
        $body->appendChild($this->datiBeniServizi());

        $pagamento = $this->datiPagamento();
        if ($pagamento !== null) {
            $body->appendChild($pagamento);
        }

        $this->dom = $dom;

        return $dom->saveXML();
    }

    /**
     * Nome file suggerito per il download: PIVA_PROGRESSIVO.xml.
     */
    public function nomeFile()
    {
        $piva = self::valoreConfig('fe.trasmissione.id_codice', '');
        if ($piva === '') {
            $piva = self::valoreConfig('fe.cedente.partita_iva', 'IT');
        }
        return preg_replace('/[^A-Za-z0-9]/', '', (string) $piva) . '_' . $this->progressivoInvio . '.xml';
    }

    private function datiTrasmissione()
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('DatiTrasmissione');

        $idTrasmittente = $dom->createElement('IdTrasmittente');
        $idPaese = self::valoreConfig('fe.trasmissione.id_paese', 'IT');
        $idCodice = self::valoreConfig('fe.trasmissione.id_codice', '');
        if ($idCodice === '') {
            $idCodice = self::valoreConfig('fe.cedente.partita_iva', '');
        }
        if ($idCodice === '') {
            $idCodice = self::valoreConfig('fe.cedente.codice_fiscale', '');
        }
        $idTrasmittente->appendChild($dom->createElement('IdPaese', $idPaese ?: 'IT'));
        $idTrasmittente->appendChild($dom->createElement('IdCodice', $idCodice));
        $node->appendChild($idTrasmittente);

        $node->appendChild($dom->createElement('ProgressivoInvio', $this->progressivoInvio));
        $node->appendChild($dom->createElement('FormatoTrasmissione', $this->formato));

        $anagrafica = $this->anagrafica();
        $codiceDestinatario = $anagrafica->fe_codice_destinatario ?? null;
        if (empty($codiceDestinatario) && $this->documento->tipo) {
            $codiceDestinatario = $this->documento->tipo->fe_codice_destinatario;
        }
        if (empty($codiceDestinatario)) {
            $codiceDestinatario = self::valoreConfig('fe.trasmissione.codice_destinatario', '0000000');
        }
        $codiceDestinatario = strtoupper(trim((string) $codiceDestinatario));
        $pec = strtolower(trim((string) ($anagrafica->fe_pec ?? '')));

        if (strlen($codiceDestinatario) !== 7) {
            $codiceDestinatario = '0000000';
        }
        $node->appendChild($dom->createElement('CodiceDestinatario', $codiceDestinatario));
        if ($codiceDestinatario === '0000000' && $pec !== '') {
            $node->appendChild($dom->createElement('PECDestinatario', $pec));
        }

        return $node;
    }

    private function cedentePrestatore()
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('CedentePrestatore');

        $datiAnagrafici = $dom->createElement('DatiAnagrafici');

        $idPaese = self::valoreConfig('fe.cedente.id_paese', 'IT');
        $idCodice = self::valoreConfig('fe.cedente.partita_iva', '');
        if ($idCodice === '') {
            $idCodice = self::valoreConfig('fe.cedente.codice_fiscale', '');
        }
        $idFiscale = $dom->createElement('IdFiscaleIVA');
        $idFiscale->appendChild($dom->createElement('IdPaese', $idPaese ?: 'IT'));
        $idFiscale->appendChild($dom->createElement('IdCodice', $idCodice));
        $datiAnagrafici->appendChild($idFiscale);

        $codiceFiscale = self::valoreConfig('fe.cedente.codice_fiscale', '');
        if ($codiceFiscale !== '') {
            $datiAnagrafici->appendChild($dom->createElement('CodiceFiscale', $codiceFiscale));
        }

        $anagrafica = $dom->createElement('Anagrafica');
        $anagrafica->appendChild($dom->createElement('Denominazione', self::valoreConfig('fe.cedente.denominazione', '')));
        $datiAnagrafici->appendChild($anagrafica);

        $datiAnagrafici->appendChild($dom->createElement('RegimeFiscale', self::valoreConfig('fe.cedente.regime_fiscale', 'RF01')));
        $node->appendChild($datiAnagrafici);

        $node->appendChild($this->sede([
            'indirizzo' => self::valoreConfig('fe.cedente.indirizzo', ''),
            'numero_civico' => self::valoreConfig('fe.cedente.numero_civico', ''),
            'cap' => self::valoreConfig('fe.cedente.cap', ''),
            'comune' => self::valoreConfig('fe.cedente.comune', ''),
            'provincia' => self::valoreConfig('fe.cedente.provincia', ''),
            'nazione' => self::valoreConfig('fe.cedente.nazione', 'IT'),
        ]));

        return $node;
    }

    private function cessionarioCommittente()
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('CessionarioCommittente');
        $a = $this->anagrafica();

        $datiAnagrafici = $dom->createElement('DatiAnagrafici');

        $partitaIva = trim((string) ($a->partita_iva ?? ''));
        $paese = strtoupper(trim((string) ($a->fe_id_paese ?? 'IT')));
        if ($partitaIva !== '') {
            $idFiscale = $dom->createElement('IdFiscaleIVA');
            $idFiscale->appendChild($dom->createElement('IdPaese', $paese ?: 'IT'));
            $idFiscale->appendChild($dom->createElement('IdCodice', $partitaIva));
            $datiAnagrafici->appendChild($idFiscale);
        }

        $codiceFiscale = trim((string) ($a->codice_fiscale ?? ''));
        if ($codiceFiscale !== '') {
            $datiAnagrafici->appendChild($dom->createElement('CodiceFiscale', strtoupper($codiceFiscale)));
        }

        $anagrafica = $dom->createElement('Anagrafica');
        $nome = trim((string) $a->fe_nome);
        $cognome = trim((string) $a->fe_cognome);
        if (($a->fe_tipo_soggetto ?? 'G') === 'F' && $nome !== '' && $cognome !== '') {
            $anagrafica->appendChild($dom->createElement('Nome', $nome));
            $anagrafica->appendChild($dom->createElement('Cognome', $cognome));
        } else {
            $anagrafica->appendChild($dom->createElement('Denominazione', (string) ($a->ragione_sociale ?? '')));
        }
        $datiAnagrafici->appendChild($anagrafica);
        $node->appendChild($datiAnagrafici);

        $node->appendChild($this->sede([
            'indirizzo' => (string) ($a->indirizzo ?? ''),
            'cap' => (string) ($a->cap ?? ''),
            'comune' => (string) ($a->citta ?? ''),
            'provincia' => (string) ($a->provincia ?? ''),
            'nazione' => $paese ?: 'IT',
        ]));

        return $node;
    }

    private function sede(array $dati)
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('Sede');

        if (trim($dati['indirizzo']) !== '') {
            $node->appendChild($dom->createElement('Indirizzo', trim($dati['indirizzo'])));
        }
        if (!empty($dati['numero_civico']) && trim($dati['numero_civico']) !== '') {
            $node->appendChild($dom->createElement('NumeroCivico', trim($dati['numero_civico'])));
        }
        if (!empty($dati['cap']) && trim($dati['cap']) !== '') {
            $node->appendChild($dom->createElement('CAP', trim($dati['cap'])));
        }
        $node->appendChild($dom->createElement('Comune', trim($dati['comune'])));
        $nazione = strtoupper(trim($dati['nazione'])) ?: 'IT';
        if ($nazione === 'IT' && !empty($dati['provincia']) && trim($dati['provincia']) !== '') {
            $node->appendChild($dom->createElement('Provincia', strtoupper(trim($dati['provincia']))));
        }
        $node->appendChild($dom->createElement('Nazione', $nazione));

        return $node;
    }

    private function datiGenerali()
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('DatiGenerali');
        $generali = $dom->createElement('DatiGeneraliDocumento');

        $tipo = $this->documento->tipo;
        $tipoDocumento = $tipo && $tipo->fe_tipo_documento ? $tipo->fe_tipo_documento : 'TD01';
        $generali->appendChild($dom->createElement('TipoDocumento', $tipoDocumento));

        $divisa = $tipo && $tipo->fe_divisa ? $tipo->fe_divisa : self::valoreConfig('fe.divisa', 'EUR');
        $generali->appendChild($dom->createElement('Divisa', strtoupper($divisa)));

        $generali->appendChild($dom->createElement('Data', date('Y-m-d', strtotime((string) $this->documento->data))));

        $numero = (string) $this->documento->numero;
        if (!empty($this->documento->suffisso)) {
            $numero .= '/' . $this->documento->suffisso;
        }
        $generali->appendChild($dom->createElement('Numero', $numero));

        $generali->appendChild($dom->createElement('ImportoTotaleDocumento', self::dec($this->documento->totale, 2)));

        $causale = $tipo && $tipo->fe_causale ? $tipo->fe_causale : $this->documento->descrizione;
        if (trim((string) $causale) !== '') {
            $generali->appendChild($dom->createElement('Causale', trim((string) $causale)));
        }

        $node->appendChild($generali);

        return $node;
    }

    private function datiBeniServizi()
    {
        $dom = $this->dom ?: new DOMDocument();
        $node = $dom->createElement('DatiBeniServizi');
        $tipo = $this->documento->tipo;

        $riepilogo = [];
        $natureCache = [];
        $numero = 0;
        foreach ($this->documento->righe as $riga) {
            $numero++;
            $linea = $dom->createElement('DettaglioLinee');
            $linea->appendChild($dom->createElement('NumeroLinea', $numero));

            if (trim((string) $riga->codice_articolo) !== '') {
                $codiceArticolo = $dom->createElement('CodiceArticolo');
                $codiceArticolo->appendChild($dom->createElement('CodiceTipo', 'COD'));
                $codiceArticolo->appendChild($dom->createElement('CodiceValore', trim((string) $riga->codice_articolo)));
                $linea->appendChild($codiceArticolo);
            }

            $linea->appendChild($dom->createElement('Descrizione', self::testo($riga->descrizione)));

            $qta = (float) $riga->qta;
            if ($qta != 0) {
                $linea->appendChild($dom->createElement('Quantita', self::dec($qta, 2)));
            }
            if (trim((string) $riga->um) !== '') {
                $linea->appendChild($dom->createElement('UnitaMisura', trim((string) $riga->um)));
            }
            $linea->appendChild($dom->createElement('PrezzoUnitario', self::dec($riga->prezzo, 2)));

            $sconto = (float) $riga->sconto;
            if ($sconto != 0) {
                $importoSconto = round((float) $riga->qta * (float) $riga->prezzo * $sconto / 100, 2);
                $scontoNode = $dom->createElement('ScontoMaggiorazione');
                $scontoNode->appendChild($dom->createElement('Tipo', 'SC'));
                $scontoNode->appendChild($dom->createElement('Percentuale', self::dec($sconto, 2)));
                $scontoNode->appendChild($dom->createElement('Importo', self::dec($importoSconto, 2)));
                $linea->appendChild($scontoNode);
            }

            $linea->appendChild($dom->createElement('PrezzoTotale', self::dec($riga->totale, 2)));

            $aliquota = (float) $riga->iva;
            $linea->appendChild($dom->createElement('AliquotaIVA', self::dec($aliquota, 2)));
            $natura = null;
            if ($aliquota == 0) {
                // Natura IVA dall'aliquota agganciata alla riga; in mancanza,
                // match per percentuale e infine il parametro di configurazione.
                $natura = $riga->aliquotaIva ? $riga->aliquotaIva->fe_natura : null;
                if ($natura === null || $natura === '') {
                    if (!array_key_exists($aliquota, $natureCache)) {
                        $natureCache[$aliquota] = MgAliquotaIva::naturaPerPercentuale($aliquota);
                    }
                    $natura = $natureCache[$aliquota];
                }
                if ($natura === null || $natura === '') {
                    $natura = self::valoreConfig('fe.natura', 'N1');
                }
                $linea->appendChild($dom->createElement('Natura', $natura));
            }

            $node->appendChild($linea);

            $chiave = $natura === null ? self::dec($aliquota, 2) : self::dec($aliquota, 2) . '|' . $natura;
            if (!isset($riepilogo[$chiave])) {
                $riepilogo[$chiave] = ['aliquota' => $aliquota, 'natura' => $natura, 'imponibile' => 0];
            }
            $riepilogo[$chiave]['imponibile'] += (float) $riga->totale;
        }

        $esigibilita = $tipo && $tipo->fe_esigibilita_iva ? $tipo->fe_esigibilita_iva : 'I';
        $riferimento = $tipo ? $tipo->fe_riferimento_normativo : null;
        foreach ($riepilogo as $r) {
            $dati = $dom->createElement('DatiRiepilogo');
            $dati->appendChild($dom->createElement('AliquotaIVA', self::dec($r['aliquota'], 2)));
            if ($r['natura'] !== null) {
                $dati->appendChild($dom->createElement('Natura', $r['natura']));
            }
            $imponibile = round($r['imponibile'], 2);
            $dati->appendChild($dom->createElement('ImponibileImporto', self::dec($imponibile, 2)));
            $dati->appendChild($dom->createElement('Imposta', self::dec(round($imponibile * $r['aliquota'] / 100, 2), 2)));
            $dati->appendChild($dom->createElement('EsigibilitaIVA', $esigibilita));
            if (trim((string) $riferimento) !== '') {
                $dati->appendChild($dom->createElement('RiferimentoNormativo', trim((string) $riferimento)));
            }
            $node->appendChild($dati);
        }

        return $node;
    }

    private function datiPagamento()
    {
        $scadenze = $this->documento->scadenze;
        $metodo = $this->documento->metodoPagamento;

        // Nessuna informazione di pagamento: sezione omessa
        if (empty($scadenze) && $metodo === null) {
            return null;
        }

        $dom = $this->dom ?: new DOMDocument();

        $modalita = $metodo && $metodo->fe_modalita_pagamento
            ? $metodo->fe_modalita_pagamento
            : self::valoreConfig('fe.modalita_pagamento', 'MP05');
        $condizioni = $metodo && $metodo->feCondizioniPagamento
            ? $metodo->feCondizioniPagamento
            : self::valoreConfig('fe.condizioni_pagamento', 'TP02');

        $node = $dom->createElement('DatiPagamento');
        $node->appendChild($dom->createElement('CondizioniPagamento', $condizioni));

        if (!empty($scadenze)) {
            foreach ($scadenze as $s) {
                $dettaglio = $dom->createElement('DettaglioPagamento');
                $dettaglio->appendChild($dom->createElement('ModalitaPagamento', $modalita));
                if ($s->data_scadenza) {
                    $dettaglio->appendChild($dom->createElement('DataScadenzaPagamento',
                        date('Y-m-d', strtotime((string) $s->data_scadenza))));
                }
                $dettaglio->appendChild($dom->createElement('ImportoPagamento', self::dec($s->importo, 2)));
                $node->appendChild($dettaglio);
            }
        } else {
            // Nessuna scadenza generata: pagamento in un'unica soluzione
            $dettaglio = $dom->createElement('DettaglioPagamento');
            $dettaglio->appendChild($dom->createElement('ModalitaPagamento', $modalita));
            $dettaglio->appendChild($dom->createElement('ImportoPagamento', self::dec($this->documento->totale, 2)));
            $node->appendChild($dettaglio);
        }

        return $node;
    }

    /**
     * Formatta un numero con il separatore decimale "." e n decimali.
     */
    private static function dec($valore, $decimali = 2)
    {
        return number_format((float) $valore, $decimali, '.', '');
    }

    /**
     * Ripulisce un testo per l'inserimento nel nodo XML.
     */
    private static function testo($valore)
    {
        $valore = trim((string) $valore);
        return $valore !== '' ? $valore : '—';
    }

    private static function valoreConfig($codice, $default = '')
    {
        $valore = MgConfigurazione::valore($codice, null);
        return ($valore === null || $valore === '') ? $default : $valore;
    }

    /**
     * Anagrafica del documento, con un modello vuoto di fallback per evitare
     * errori quando il documento non ha intestatario.
     *
     * @return MgAnagrafica
     */
    private function anagrafica()
    {
        return $this->documento->anagrafica ?: new MgAnagrafica();
    }

    // ------------------------------------------------------------------
    // Elenchi ufficiali (usati dalle tendine delle form)
    // ------------------------------------------------------------------

    public static function opzioniFormato()
    {
        return [
            'FPR12' => 'FPR12 - Privati / B2B',
            'FPA12' => 'FPA12 - Pubblica Amministrazione',
        ];
    }

    public static function opzioniTipoDocumento()
    {
        return [
            'TD01' => 'TD01 - Fattura',
            'TD02' => 'TD02 - Acconto/anticipo su fattura',
            'TD03' => 'TD03 - Acconto/anticipo su parcella',
            'TD04' => 'TD04 - Nota di credito',
            'TD05' => 'TD05 - Nota di debito',
            'TD06' => 'TD06 - Parcella',
            'TD16' => 'TD16 - Integrazione fattura reverse charge interno',
            'TD17' => 'TD17 - Integrazione/autofattura acquisto servizi dall\'estero',
            'TD18' => 'TD18 - Integrazione acquisto beni intracomunitari',
            'TD19' => 'TD19 - Integrazione/autofattura acquisto beni art. 17',
            'TD20' => 'TD20 - Autofattura per regolarizzazione',
            'TD21' => 'TD21 - Autofattura per splafonamento',
            'TD22' => 'TD22 - Estrazione beni da Deposito IVA',
            'TD23' => 'TD23 - Estrazione beni da Deposito IVA con versamento IVA',
            'TD24' => 'TD24 - Fattura differita art. 21 c.4 lett. a)',
            'TD25' => 'TD25 - Fattura differita art. 21 c.4 lett. b)',
            'TD26' => 'TD26 - Cessione beni ammortizzabili e passaggi interni',
            'TD27' => 'TD27 - Fattura per autoconsumo/cessioni gratuite',
            'TD28' => 'TD28 - Acquisti da San Marino con IVA',
        ];
    }

    public static function opzioniRegimeFiscale()
    {
        return [
            'RF01' => 'RF01 - Ordinario',
            'RF02' => 'RF02 - Contribuenti minimi',
            'RF04' => 'RF04 - Agricoltura e attività connesse e pesca',
            'RF05' => 'RF05 - Vendita sali e tabacchi',
            'RF06' => 'RF06 - Commercio fiammiferi',
            'RF07' => 'RF07 - Editoria',
            'RF08' => 'RF08 - Gestione servizi telefonia pubblica',
            'RF09' => 'RF09 - Rivendita documenti di trasporto pubblico e di sosta',
            'RF10' => 'RF10 - Intrattenimenti, giochi e altre attività',
            'RF11' => 'RF11 - Agenzie viaggi e turismo',
            'RF12' => 'RF12 - Agriturismo',
            'RF13' => 'RF13 - Vendite a domicilio',
            'RF14' => 'RF14 - Rivendita beni usati, oggetti d\'arte',
            'RF15' => 'RF15 - Agenzie di vendite all\'asta',
            'RF16' => 'RF16 - IVA per cassa P.A.',
            'RF17' => 'RF17 - IVA per cassa',
            'RF18' => 'RF18 - Altro',
            'RF19' => 'RF19 - Regime forfettario',
        ];
    }

    public static function opzioniCondizioniPagamento()
    {
        return [
            'TP01' => 'TP01 - Pagamento a rate',
            'TP02' => 'TP02 - Pagamento completo',
            'TP03' => 'TP03 - Anticipo',
        ];
    }

    public static function opzioniModalitaPagamento()
    {
        return [
            'MP01' => 'MP01 - Contanti',
            'MP02' => 'MP02 - Assegno',
            'MP03' => 'MP03 - Assegno circolare',
            'MP04' => 'MP04 - Contanti presso Tesoreria',
            'MP05' => 'MP05 - Bonifico',
            'MP06' => 'MP06 - Vaglia cambiario',
            'MP07' => 'MP07 - Bollettino bancario',
            'MP08' => 'MP08 - Carta di pagamento',
            'MP09' => 'MP09 - RID',
            'MP10' => 'MP10 - RID utenze',
            'MP11' => 'MP11 - RID veloce',
            'MP12' => 'MP12 - RIBA',
            'MP13' => 'MP13 - MAV',
            'MP14' => 'MP14 - Quietanza erario',
            'MP15' => 'MP15 - Giroconto su conti di contabilità speciale',
            'MP16' => 'MP16 - Domiciliazione bancaria',
            'MP17' => 'MP17 - Domiciliazione c/c postale',
            'MP18' => 'MP18 - Bollettino di c/c postale',
            'MP19' => 'MP19 - SEPA Direct Debit',
            'MP20' => 'MP20 - SEPA Direct Debit CORE',
            'MP21' => 'MP21 - SEPA Direct Debit B2B',
            'MP22' => 'MP22 - Trattenuta su somme già riscosse',
            'MP23' => 'MP23 - PagoPA',
        ];
    }

    public static function opzioniEsigibilitaIva()
    {
        return [
            'I' => 'I - Immediata',
            'D' => 'D - Differita',
            'S' => 'S - Split payment',
        ];
    }

    public static function opzioniNaturaIva()
    {
        return [
            'N1' => 'N1 - Escluse ex art. 15',
            'N2' => 'N2 - Non soggette',
            'N2.1' => 'N2.1 - Non soggette armonizzazione UE',
            'N2.2' => 'N2.2 - Non soggette altre',
            'N3' => 'N3 - Non imponibili',
            'N3.1' => 'N3.1 - Non imponibili esportazioni',
            'N3.2' => 'N3.2 - Non imponibili cessioni intracomunitarie',
            'N3.3' => 'N3.3 - Non imponibili cessioni verso San Marino',
            'N3.4' => 'N3.4 - Non imponibili operazioni assimilate',
            'N3.5' => 'N3.5 - Non imponibili dichiarazione d\'intento',
            'N3.6' => 'N3.6 - Non imponibili altre operazioni',
            'N4' => 'N4 - Esenti',
            'N5' => 'N5 - Regime del margine / IVA non esposta',
            'N6' => 'N6 - Inversione contabile (reverse charge)',
            'N6.1' => 'N6.1 - Reverse charge rottami',
            'N6.2' => 'N6.2 - Reverse charge oro/argento',
            'N6.3' => 'N6.3 - Reverse charge subappalto edilizia',
            'N6.4' => 'N6.4 - Reverse charge fabbricati',
            'N6.5' => 'N6.5 - Reverse charge telefonia',
            'N6.6' => 'N6.6 - Reverse charge elettronica',
            'N6.7' => 'N6.7 - Reverse charge edilizia',
            'N6.8' => 'N6.8 - Reverse charge energia',
            'N6.9' => 'N6.9 - Reverse charge altre ipotesi',
            'N7' => 'N7 - IVA assolta in altro Stato UE',
        ];
    }

    public static function opzioniTipoSoggetto()
    {
        return [
            'G' => 'Persona giuridica (Denominazione)',
            'F' => 'Persona fisica (Nome e Cognome)',
        ];
    }
}
