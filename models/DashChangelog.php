<?php

namespace app\models;

use Yii;

/**
 * Changelog applicativo.
 *
 * Registra, per ogni miglioria rilasciata, cosa e' stato modificato e in che modo.
 * Le righe vengono create automaticamente dall'hook git post-commit
 * (comando `yii changelog/git`) e possono essere arricchite con il dettaglio
 * funzionale.
 *
 * @property int $id
 * @property string|null $versione
 * @property string|null $commit_hash
 * @property string|null $tipo
 * @property string $titolo
 * @property string|null $dettaglio
 * @property string|null $file_modificati
 * @property string|null $autore
 * @property string|null $data_commit
 * @property string|null $created_at
 */
class DashChangelog extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_changelog';
    }

    public function rules()
    {
        return [
            [['titolo'], 'required'],
            [['dettaglio', 'file_modificati'], 'string'],
            [['data_commit', 'created_at'], 'safe'],
            [['versione'], 'string', 'max' => 50],
            [['commit_hash'], 'string', 'max' => 64],
            [['tipo'], 'string', 'max' => 20],
            [['titolo'], 'string', 'max' => 300],
            [['autore'], 'string', 'max' => 150],
            [['commit_hash'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'versione' => 'Versione',
            'commit_hash' => 'Commit',
            'tipo' => 'Tipo',
            'titolo' => 'Titolo',
            'dettaglio' => 'Dettaglio modifiche',
            'file_modificati' => 'File modificati',
            'autore' => 'Autore',
            'data_commit' => 'Data commit',
            'created_at' => 'Registrato il',
        ];
    }

    /**
     * Tipi di modifica riconosciuti (conventional commits).
     */
    public static function tipiDisponibili()
    {
        return [
            'feat' => 'Nuova funzionalità',
            'fix' => 'Correzione',
            'refactor' => 'Refactoring',
            'perf' => 'Prestazioni',
            'docs' => 'Documentazione',
            'chore' => 'Manutenzione',
            'style' => 'Stile',
            'test' => 'Test',
        ];
    }

    public function getTipoLabel()
    {
        $tipi = self::tipiDisponibili();
        return $tipi[$this->tipo] ?? ($this->tipo ?: '—');
    }

    /**
     * Elenco dei file modificati come array di righe.
     */
    public function getFileList()
    {
        $rows = preg_split('/\r\n|\r|\n/', (string) $this->file_modificati, -1, PREG_SPLIT_NO_EMPTY);
        return array_map('trim', $rows ?: []);
    }
}
