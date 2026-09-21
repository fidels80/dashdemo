<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\models\Agentifiles;

class AgentifilesController extends Controller
{
    /**
     * Crea la struttura di cartelle nel database per un agente.
     * Esegui: php yii agentifiles/create-structure DEC
     */
    public function actionCreateStructure($cd_agente)
    {
        $this->stdout("Creazione struttura per agente: {$cd_agente}\n", Console::FG_YELLOW);

        $structure = [
          'Retribuzioni' => [
                'C1' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035'],    
                'Buste paga' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035'],
                'Cud' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035']
            ],
            'Documenti Personali' => [
                'Carta Identità' => [],
                'Passaporto' => [],
                'Tessera Sanitaria' => []
            ],
            'Documentazione' => [
                'Contratti' => [],
                'Attestati' => [],
                'Dpi' => [],
                'Visite Mediche' => []
            ],
        ];

        $this->insertStructure($cd_agente, $structure);
        $this->stdout("✅ Struttura inserita correttamente per agente {$cd_agente}\n", Console::FG_GREEN);
    }

  private function insertStructure($cd_agente, $structure, $parent = null)
{
    foreach ($structure as $folder => $children) {

        // Se il valore è una stringa (caso come ['Certificati' => ['A1']])
        if (is_numeric($folder)) {
            $folder = $children;
            $children = [];
        }

        // Crea la cartella corrente
        $model = new \app\models\Agentifiles();
        $model->cd_agente = $cd_agente;
        $model->descrizione = $folder;
        $model->nota = 'Cartella di sistema';
        $model->cartella_padre = $parent;
        $model->cartella = $folder;
        $model->nome_file = $folder;
        $model->estenzione = 'dir';
        $model->kiave_arch = 0;
        $model->save(false);

        // Se ci sono figli ed è un array, entra ricorsivamente
        if (is_array($children) && !empty($children)) {
            $this->insertStructure($cd_agente, $children, $folder);
        }
    }
}

}
