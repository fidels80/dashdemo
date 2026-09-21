<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "personale".
 *
 * @property int $id
 * @property string $nome
 * @property string $cognome
 * @property string|null $codice_fiscale
 * @property string|null $ruolo
 * @property string|null $reparto
 * @property string|null $mansione
 * @property float|null $tariffa_oraria
 * @property bool|null $stato_attivo
 * @property string|null $data_inserimento
 
 * @property Planning[] $plannings
 * @property Presenze[] $presenzes
 * @property string|null $cellulare
 * @property string|null $telefono_secondario
 * @property string|null $email
 * @property string|null $indirizzo
 * @property string|null $citta
 * @property string|null $cap
 * @property string|null $provincia
 * // Aggiungi questa proprietà nei commenti iniziali della classe
 * @property string|null $geobadge_id
 */

class Personale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personale';
    }

    /**
     * {@inheritdoc}
     */
    // Modifica la funzione rules() aggiungendo il nuovo campo
    public function rules()
    {
        return [
            [['nome', 'cognome'], 'required'],
            [['tariffa_oraria'], 'number'],
            [['stato_attivo'], 'boolean'],
            [['data_inserimento'], 'safe'],
            [['email'], 'email'],
            [['nome', 'cognome', 'mansione', 'email', 'citta', 'geobadge_id','geobadge_rapporto_id'], 'string', 'max' => 100], // Aggiunto geobadge_id
            [['codice_fiscale'], 'string', 'max' => 16],
            [['ruolo', 'reparto', 'provincia'], 'string', 'max' => 50],
            [['cellulare', 'telefono_secondario'], 'string', 'max' => 20],
            [['cap'], 'string', 'max' => 10],
            [['indirizzo'], 'string', 'max' => 255],
            [['codice_fiscale', 'geobadge_id'], 'unique'], // Geobadge ID deve essere unico
            [['data_assunzione'], 'safe'],
            [['luogo_data_nascita'], 'string', 'max' => 250],
            [['tipo_contratto'], 'string', 'max' => 100],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'cognome' => 'Cognome',
            'codice_fiscale' => 'Documento',
            'ruolo' => 'Ruolo',
            'reparto' => 'Reparto',
            'mansione' => 'Mansione',
            'tariffa_oraria' => 'Tariffa Oraria',
            'stato_attivo' => 'Stato Attivo',
            'data_inserimento' => 'Data Inserimento',
            'cellulare' => 'Cellulare Aziendale',
            'telefono_secondario' => 'Altro Telefono',
            'email' => 'Email',
            'indirizzo' => 'Indirizzo (Via/Piazza)',
            'citta' => 'Città',
            'cap' => 'CAP',
            'provincia' => 'Prov.',
            'geobadge_id ' => ' ID Geobadge ',
            'luogo_data_nascita' => 'Luogo e Data di Nascita',
            'data_assunzione' => 'Data Assunzione',
            'tipo_contratto' => 'Tipo Contratto',
        ];
    }

/**
     * Recupera tutti i Planning associati a questo dipendente
     * passando attraverso la tabella di legame.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlannings()
    {
        // 1. Diciamo di cercare i modelli Planning. La chiave primaria del Planning ('id') 
        //    deve combaciare con 'planning_id' della tabella di legame.
        return $this->hasMany(Planning::className(), ['id' => 'planning_id'])
            // 2. Diciamo a Yii2 di passare attraverso la tabella 'planning_personale'.
            //    La chiave di questa tabella ('personale_id') deve combaciare 
            //    con l'id di questo modello Personale ('id').
            ->viaTable('planning_personale', ['personale_id' => 'id']);
    }
    /**
     * Gets query for [[Presenzes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPresenzes()
    {
        return $this->hasMany(Presenze::className(), ['personale_id' => 'id']);
    }
    public function getRuoloRel()
    {
        // Collega il campo 'ruolo' del personale al 'codice' della tabella ruoli
        return $this->hasOne(Ruoli::className(), ['codice' => 'ruolo']);
    }

    public function getRepartoRel()
    {
        return $this->hasOne(Reparti::className(), ['codice' => 'reparto']);
    }

    public function getMansioneRel()
    {
        return $this->hasOne(Mansioni::className(), ['codice' => 'mansione']);
    }

    /**
     * FUNZIONI DI DECODIFICA PER L'INDEX
     * Queste funzioni restituiscono la descrizione o il codice originale se non trovato
     */

    public function getDescrizioneRuolo()
    {
        return $this->ruoloRel ? $this->ruoloRel->descrizione : $this->ruolo;
    }

    public function getDescrizioneReparto()
    {
        return $this->repartoRel ? $this->repartoRel->descrizione : $this->reparto;
    }

    public function getDescrizioneMansione()
    {
        return $this->mansioneRel ? $this->mansioneRel->descrizione : $this->mansione;
    }
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}


public function getFiles()
    {
        // Collega la tabella AllFiles usando l'id del dipendente come 'id_padre'
        // Filtrando solo quelli che appartengono all'entità 'personale'
        return $this->hasMany(\app\models\AllFiles::className(), ['id_padre' => 'id'])
            ->where(['entita' => 'personale']);
    }
}
