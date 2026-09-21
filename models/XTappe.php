<?php

namespace app\models;

use Yii;
use app\models\Xvenue;
/**
 * This is the model class for table "x_tappe".
 *
 * @property string $id_tappa
 * @property int $th_id
 * @property string $data
 * @property string $citta
 * @property bool|null $evaso
 */
class Xtappe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_tappe';
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
            [['id_tappa'], 'string'],
            [['th_id', 'data', 'citta'], 'required'],
            [['th_id'], 'integer'],
            [['data'], 'safe'],
            [['evaso'], 'boolean'],
            [['citta'], 'string', 'max' => 250],
            [['id_tappa'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tappa' => 'Id Tappa',
            'th_id' => 'Th ID',
            'data' => 'Data',
            'citta' => 'Citta',
            'evaso' => 'Evaso',
        ];
    }
    public function ___getDecodevenue()
    {
        // Usa la relazione per accedere alle righe e somma i prezzi
    
    $decode=Xvenue::find()->select(['venue'])->where(['id'=>$this->citta])
    ->one();
    
        return $decode['venue'];
    
    }


    public function getDecodevenue()
    {
        // Espressione regolare per verificare se la stringa è un GUID/UUID valido
        $isGuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $this->citta);

        if ($isGuid) {
            $decode = Xvenue::find()
                ->select(['venue'])
                ->where(['id' => $this->citta])
                ->one();

            // Se troviamo la venue nel database bene, altrimenti restituiamo il valore originale
            return $decode ? $decode->venue : $this->citta;
        }

        // Se non è un GUID (es. è "Verona"), restituiamo direttamente il valore del campo citta
        return $this->citta;
    }
}
