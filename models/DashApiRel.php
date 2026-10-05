<?php

namespace app\models;

/**
 * Regola di integrita' referenziale applicata in cancellazione.
 *
 * tipo = "figlio"      : l'entita' ha righe nella tabella indicata tramite la
 *                        colonna (es. righe e scadenze di un documento).
 * tipo = "riferimento" : la tabella indicata punta all'entita' tramite la
 *                        colonna (es. articolo citato in una riga documento).
 *
 * In entrambi i casi la presenza di righe impedisce la cancellazione del
 * record. La cancellazione "a cascata" e' consentita solo per i figli con
 * cascade = 1 e solo se il client la richiede esplicitamente; i riferimenti
 * non vengono mai rimossi automaticamente.
 *
 * @property int $id
 * @property string $entita
 * @property string $tipo
 * @property string $tabella
 * @property string $colonna
 * @property string $etichetta
 * @property int $cascade
 * @property int $attiva
 * @property int $ordinamento
 */
class DashApiRel extends \yii\db\ActiveRecord
{
    const TIPO_FIGLIO = 'figlio';
    const TIPO_RIFERIMENTO = 'riferimento';

    public static function tableName()
    {
        return '{{%dash_api_rel}}';
    }

    public function rules()
    {
        return [
            [['entita', 'tipo', 'tabella', 'colonna', 'etichetta'], 'required'],
            [['entita', 'tabella', 'colonna', 'etichetta'], 'string', 'max' => 100],
            [['tipo'], 'in', 'range' => [self::TIPO_FIGLIO, self::TIPO_RIFERIMENTO]],
            [['cascade', 'attiva', 'ordinamento'], 'integer'],
            [['cascade'], 'default', 'value' => 0],
            [['attiva'], 'default', 'value' => 1],
            [
                ['entita', 'tipo', 'tabella', 'colonna'],
                'unique',
                'targetAttribute' => ['entita', 'tipo', 'tabella', 'colonna'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entita' => 'Entità',
            'tipo' => 'Tipo',
            'tabella' => 'Tabella',
            'colonna' => 'Colonna',
            'etichetta' => 'Etichetta',
            'cascade' => 'Cancellazione a cascata consentita',
            'attiva' => 'Attiva',
            'ordinamento' => 'Ordinamento',
        ];
    }

    public static function tipi()
    {
        return [
            self::TIPO_FIGLIO => 'Figli (ha righe)',
            self::TIPO_RIFERIMENTO => 'Riferimenti (citato da)',
        ];
    }

    /**
     * Regole attive di una data entita'.
     *
     * @param string $entita codice entita'
     * @return DashApiRel[]
     */
    public static function perEntita($entita)
    {
        return static::find()
            ->where(['entita' => $entita, 'attiva' => 1])
            ->orderBy(['tipo' => SORT_ASC, 'ordinamento' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    public function getEntita()
    {
        return $this->hasOne(DashApiEntita::className(), ['codice' => 'entita']);
    }

    public function isFiglio()
    {
        return $this->tipo === self::TIPO_FIGLIO;
    }

/**
     * Frase leggibile del vincolo, usata nelle risposte 409.
     *
     * @param int $conteggio numero di righe collegate
     * @return string
     */
    public function descriviVincolo($conteggio)
    {
        $conteggio = (int) $conteggio;
        if ($this->isFiglio()) {
            return sprintf('Ha %d %s.', $conteggio, $this->etichetta);
        }
        return sprintf('E\' referenziato da %d %s.', $conteggio, $this->etichetta);
    }
}
