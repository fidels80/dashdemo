<?php

namespace app\models;
use app\models\Presenze;
use Yii;

/**
 * This is the model class for table "planning".
 *
 * @property int $id
 * @property string $data_attivita
 * @property string|null $descrizione
 * @property string|null $indirizzo
 * @property int|null $personale_id
 * @property int|null $veicolo_id
 * @property string|null $ora_inizio
 * @property string|null $ora_fine
 * @property string|null $stato_completamento
 *
 * @property Personale $personale
 * @property Veicoli $veicolo // Riferimento storico (mantenuto per sicurezza, ma passeremo a $veicoli)
 * @property Personale[] $personali // Relazione plurale
 * @property Veicoli[] $veicoli // --- NUOVA RELAZIONE PLURALE PER I VEICOLI ---
 */
class Planning extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    // Proprietà per memorizzare gli ID selezionati nella form
    public $personale_ids;
    
    // --- NUOVA PROPRIETÀ PER I VEICOLI MULTIPLI ---
    public $veicoli_ids = []; 
    
    public static function tableName()
    {
        return 'planning';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data_attivita'], 'required'],
            [['data_attivita', 'ora_inizio', 'ora_fine'], 'safe'],
            [['descrizione','cd_cf', 'ditta_esterna'], 'string'],
            [['veicolo_id','qta_operai'], 'integer'],
            [['cd_dosottocommessa'], 'string', 'max' => 20],
            // Validiamo l'array di dipendenti
            [['personale_ids'], 'each', 'rule' => ['integer']],
            // --- NUOVA REGOLA PER L'ARRAY DEI VEICOLI ---
            [['veicoli_ids'], 'safe'], 
            [['indirizzo'], 'string', 'max' => 255],
            [['stato_completamento'], 'string', 'max' => 20],
            [['veicolo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Veicoli::className(), 'targetAttribute' => ['veicolo_id' => 'id']],
            [['ditta_esterna'], 'default', 'value' => null],
            [['giro'], 'integer', 'min' => 1, 'max' => 24],
            [['giro'], 'default', 'value' => 1], // Default a 1 se non specificato
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_attivita' => 'Data Attivita',
            'descrizione' => 'Descrizione',
            'indirizzo' => 'Indirizzo',
            'personale_id' => 'Personale ID',
            'veicolo_id' => 'Veicolo ID', // Riferimento vecchio
            'veicoli_ids' => 'Mezzi Assegnati', // Etichetta per il nuovo campo
            'ora_inizio' => 'Ora Inizio',
            'ora_fine' => 'Ora Fine',
            'stato_completamento' => 'Stato Completamento',
            'cd_dosottocommessa' => 'Sottocommessa', // Etichetta per il campo di relazione
            'giro' => 'Giro / Ordine',
        ];
    }

    /**
     * Relazione con il modello Sottocommessa (DB5)
     */
    public function getSottocommessa()
    {
        // Collega cd_sottocommessa (Planning) a Cd_DOSottoCommessa (Sottocommessa)
        return $this->hasOne(Sottocommessa::className(), ['Cd_DOSottoCommessa' => 'cd_dosottocommessa']);
    }

    // Relazione molti-a-molti (Personale)
    public function getPersonali()
    {
        return $this->hasMany(Personale::className(), ['id' => 'personale_id'])
            ->viaTable('planning_personale', ['planning_id' => 'id']);
    }

    // --- NUOVA RELAZIONE MOLTI-A-MOLTI PER I VEICOLI ---
    public function getVeicoliListRel()
    {
        return $this->hasMany(Veicoli::className(), ['id' => 'veicolo_id'])
            ->viaTable('planning_veicoli', ['planning_id' => 'id']);
    }

    // Carica gli ID esistenti nei campi virtuali quando apriamo la form in modifica
    public function afterFind()
    {
        parent::afterFind();
        
        // Popola l'array dei dipendenti
        $this->personale_ids = \yii\helpers\ArrayHelper::map($this->personali, 'id', 'id');
        
        // Usa la relazione eager-loaded invece della query diretta (evita N+1)
        if (!empty($this->veicoliListRel)) {
            $this->veicoli_ids = \yii\helpers\ArrayHelper::getColumn($this->veicoliListRel, 'id');
        } else {
            $this->veicoli_ids = $this->veicolo_id ? [$this->veicolo_id] : [];
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // --- GESTIONE SALVATAGGIO VEICOLI MULTIPLI ---
        if (is_array($this->veicoli_ids) || $this->veicoli_ids === '') {
            
            // 1. Svuotiamo i vecchi veicoli
            \Yii::$app->db->createCommand()
                ->delete('planning_veicoli', ['planning_id' => $this->id])
                ->execute();

            // 2. Inseriamo i nuovi veicoli
            if (!empty($this->veicoli_ids) && is_array($this->veicoli_ids)) {
                $rowsV = [];
                foreach ($this->veicoli_ids as $vId) {
                    if (!empty($vId)) {
                        $rowsV[] = [$this->id, $vId];
                    }
                }
                if (!empty($rowsV)) {
                    \Yii::$app->db->createCommand()
                        ->batchInsert('planning_veicoli', ['planning_id', 'veicolo_id'], $rowsV)
                        ->execute();
                }
            }
        }

        // --- GESTIONE SALVATAGGIO PERSONALE E PRESENZE (Logica Esistente Intatta) ---
        Yii::$app->db->createCommand()
            ->delete('planning_personale', ['planning_id' => $this->id])
            ->execute();

        if (is_array($this->personale_ids)) {
            foreach ($this->personale_ids as $p_id) {
                // Inserimento legame attività-personale
                Yii::$app->db->createCommand()
                    ->insert('planning_personale', [
                        'planning_id' => $this->id,
                        'personale_id' => $p_id
                    ])->execute();

                // --- GESTIONE PRESENZE ---
                $presenzaEsistente = Presenze::find()
                    ->where([
                        'personale_id' => $p_id,
                        'data_presenza' => $this->data_attivita,
                        'tipo_assenza' => 'PRES'
                    ])->exists();

                if (!$presenzaEsistente) {
                    $nuovaPresenza = new Presenze();
                    $nuovaPresenza->personale_id = $p_id;
                    $nuovaPresenza->data_presenza = $this->data_attivita;
                    $nuovaPresenza->ore_lavorate = 8;
                    $nuovaPresenza->tipo_assenza = 'PRES';
                    
                    $nuovaPresenza->note = "Generata automaticamente da Planning ID: " . $this->id;

                    if (!$nuovaPresenza->save()) {
                        Yii::error("Impossibile salvare presenza automatica per dipendente $p_id: " . implode(', ', $nuovaPresenza->getFirstErrors()));
                    }
                }
            }
        }
    }

    public function getNominativiCompleti()
    {
        $nomi = [];
        foreach ($this->personali as $p) {
            $nomi[] = $p->cognome . ' ' . $p->nome;
        }
        return !empty($nomi) ? implode(', ', $nomi) : 'Nessuno';
    }
    
    /**
     * Gets query for [[Veicolo]]. (Relazione vecchia, mantenuta per retrocompatibilità)
     * @return \yii\db\ActiveQuery
     */
    public function getVeicolo()
    {
        return $this->hasOne(Veicoli::className(), ['id' => 'veicolo_id']);
    }

    // --- NUOVA FUNZIONE: Restituisce l'elenco testuale dei veicoli multipli ---
    public function getVeicoliCompleti()
    {
        if (!empty($this->veicoliListRel)) {
            $veicoli = [];
            foreach ($this->veicoliListRel as $v) {
                $veicoli[] = $v->targa . ' - ' . $v->marca_modello;
            }
            return implode(', ', $veicoli);
        }
        return $this->getVeicoloTarga();
    }

    public function getVeicoloTarga()
    {
        // Usa la vecchia logica se presente, ma sarebbe meglio passare alla nuova.
        return $this->veicolo ? $this->veicolo->targa : 'Senza Mezzo';
    }

    // Formatta l'inizio unendo data e ora per FullCalendar
    public function getStartIso()
    {
        $ora = $this->ora_inizio ? date('H:i:s', strtotime($this->ora_inizio)) : '00:00:00';
        return $this->data_attivita . 'T' . $ora;
    }

    // Formatta la fine unendo data e ora
    public function getEndIso()
    {
        $ora = $this->ora_fine ? date('H:i:s', strtotime($this->ora_fine)) : '23:59:59';
        return $this->data_attivita . 'T' . $ora;
    }
    
    public function behaviors()
    {
        return [
            \app\components\LogBehavior::class,
        ];
    }

    public function getCliente()
    {
        return $this->hasOne(CF::class, ['Cd_CF' => 'cd_cf']);
    }

    public function getDittaEsternaRel()
    {
        // Assumo che il modello si chiami DittaEsterna e la PK sia 'codice'
        return $this->hasOne(DittaEsterna::class, ['codice' => 'ditta_esterna']);
    }

    public function getVeicoloLabel()
    {
        return $this->veicolo ? $this->veicolo->targa . ' - ' . $this->veicolo->marca_modello : 'N.D.';
    }
}