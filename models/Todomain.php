<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_main".
 *
 * @property string $id
 * @property string $user
 * @property string|null $group
 * @property string|null $cd_cli
 * @property string|null $priorita
 * @property string|null $progresso
 * @property string|null $id_padre
 * @property string $descrizione
 * @property string|null $data_inizio
 * @property string|null $data_fine
 * @property string|null $data_scadenza
 * @property string $stato
 *  @property string|null $tags
 */
class Todomain extends \yii\db\ActiveRecord
{
    public $tagValues = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id',  'descrizione', 'tags'], 'string'],
            [['user', 'descrizione', 'stato'], 'required'],
            [['data_inizio', 'data_fine', 'data_scadenza', 'tagValues', 'created_at', 'updated_at'], 'safe'],
            [['user', 'group'], 'string', 'max' => 30],
            [['cd_cli', 'priorita', 'progresso'], 'string', 'max' => 10],
            [['stato'], 'string', 'max' => 20],
            [['tipo', 'story_points', 'sprint_id', 'posizione'], 'integer'],
            [['reporter'], 'string', 'max' => 30],
            [['id'], 'unique'],
              [['id_padre'], 'string', 'skipOnEmpty' => true],
               ['id_padre', 'filter', 'filter' => function ($value) {
            return $value === '' ? null : $value;
        }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'group' => 'Group',
            'cd_cli' => 'Cd Cli',
            'priorita' => 'Priorita',
            'progresso' => 'Progresso',
            'id_padre' => 'Id Padre',
            'descrizione' => 'Descrizione',
            'data_inizio' => 'Data Inizio',
            'data_fine' => 'Data Fine',
            'data_scadenza' => 'Data Scadenza',
            'stato' => 'Stato',
            'tags' => 'Tags',
            'tipo' => 'Tipo',
            'story_points' => 'Story Points',
            'sprint_id' => 'Sprint',
            'posizione' => 'Posizione',
            'reporter' => 'Segnalato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getTipodett()
    {
        return $this->hasOne(Todotipo::className(), ['id' => 'tipo']);
    }

    public function getSprintdett()
    {
        return $this->hasOne(Todosprint::className(), ['id' => 'sprint_id']);
    }

    public function getAttivitas()
    {
        return $this->hasMany(Todoattivita::className(), ['id_todo' => 'id'])
            ->orderBy(['data' => SORT_DESC, 'id' => SORT_DESC]);
    }

    public function getPadre()
    {
        return $this->hasOne(self::className(), ['id' => 'id_padre']);
    }

    public function getFigli()
    {
        return $this->hasMany(self::className(), ['id_padre' => 'id']);
    }

    public function getUserdett()
    {
   return $this->hasOne(User::class, ['id' => 'user']);
    //    $usrdett = User::find()
    //        ->where(['id' => 'user'])
    //        ->one();
//return $usrdett;


    }

    public function getGroupdett()
    {
 //       $grpdett= Todogruppi::find()->where(['id'=>'group'])->one();
 //       return $grpdett;
        return $this->hasOne(Todogruppi::class, ['id' => 'group']);


    }

    public function getClidett()
    {
        //       $grpdett= Todogruppi::find()->where(['id'=>'group'])->one();
        //       return $grpdett;
        return $this->hasOne(Anacli::class, ['cd_cli' => 'cd_cli']);
    }
    public function getPriodett()
    {
        //       $grpdett= Todogruppi::find()->where(['id'=>'group'])->one();
        //       return $grpdett;
        return $this->hasOne(Todopriorita::class, ['id' => 'priorita']);
    }

    public function getStatodett()
    {
        //       $grpdett= Todogruppi::find()->where(['id'=>'group'])->one();
        //       return $grpdett;
        return $this->hasOne(Todostato::class, ['id' => 'stato']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->tags = implode(',', $this->tagValues);
            return true;
        }
        return false;
    }
    public function afterFind()
    {
        parent::afterFind();
        $this->tagValues = explode(',', $this->tags ??'');
    }
    public function getTagValues()
    {
        // Recupera i tag associati da to_do_rel_tags
        return $this->hasMany(Todoreltags::class, ['id_to_do' => 'id'])->select('tag')->column();
    }

    public function setTagValues($tags)
    {
        // Assegna i valori dei tag (array di stringhe)
        $this->tagValues = $tags;
    }
}
