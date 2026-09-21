<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_venue".
 *
 * @property string $id
 * @property string $venue
 * @property string $citta
 * @property string|null $indirizzo
 * @property string|null $cap
 * @property string|null $provincia
 * @property string|null $tipologia
 * @property string|null $capienza
 * @property string|null $sito_web
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $note
 * @property string|null $mappa
 * @property int|null $pos
 */
class Xvenue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_venue';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'indirizzo', 'tipologia', 'capienza', 'sito_web', 'email', 'note', 'mappa'], 'string'],
            [['venue', 'citta'], 'required'],
            [['pos'], 'integer'],
            [['venue'], 'string', 'max' => 200],
            [['citta', 'telefono'], 'string', 'max' => 50],
            [['cap'], 'string', 'max' => 5],
            [['provincia'], 'string', 'max' => 2],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'venue' => 'Venue',
            'citta' => 'Citta',
            'indirizzo' => 'Indirizzo',
            'cap' => 'Cap',
            'provincia' => 'Provincia',
            'tipologia' => 'Tipologia',
            'capienza' => 'Capienza',
            'sito_web' => 'Sito Web',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'note' => 'Note',
            'mappa' => 'Mappa',
            'pos' => 'Pos',
        ];
    }
    public function beforeSave($insert)
    {
        if ($insert && empty($this->id)) {
            $this->id = null; // lascialo null, lo genera SQL Server
            unset($this->id);
        }
        return parent::beforeSave($insert);
    }
 
}
