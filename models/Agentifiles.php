<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agenti_files".
 *
 * @property int $id
 * @property string $cd_agente
 * @property string|null $descrizione
 * @property string $nota
 * @property string|null $cartella
 * @property string|null $cartella_padre
 * @property resource|null $f_content
 * @property string $nome_file
 * @property string $estenzione
 * @property resource|null $uplfile
 * @property resource|null $file
 * @property int|null $kiave_arch
 * @property date|null $data_scadenza
 * 
 */
class Agentifiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $uplfile;

    public static function tableName()
    {
        return 'agenti_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cd_agente', 'nota', 'nome_file', 'estenzione'], 'required'],
            [['nota', 'cartella', 'cartella_padre', 'f_content', 'nome_file', 'uplfile', 'file'], 'string'],
            [['kiave_arch'], 'integer'],
            [['data_scadenza'],'date'],
            [['cd_agente'], 'string', 'max' => 3],
            [['descrizione'], 'string', 'max' => 200],
            [['estenzione'], 'string', 'max' => 30],
            [['uplfile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, doc, docx, xls, xlsx, jpg, png, txt'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cd_agente' => 'Cd Agente',
            'descrizione' => 'Descrizione',
            'nota' => 'Nota',
            'cartella' => 'Cartella',
            'cartella_padre' => 'Cartella Padre',
            'f_content' => 'F Content',
            'nome_file' => 'Nome File',
            'estenzione' => 'Estenzione',
            'uplfile' => 'Uplfile',
            'file' => 'File',
            'kiave_arch' => 'Kiave Arch',
            'data_scadenza' => 'Data Scadenza',
            'sub_entita' => 'Tipologia Documento',
        ];
    }

}
