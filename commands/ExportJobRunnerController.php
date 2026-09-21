<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class ExportJobRunnerController extends Controller
{
    public function old_actionRun()
    {
        $jobs = (new \yii\db\Query())
            ->from('export_jobs')
            ->where(['progress' => 0])
            ->orderBy(['id' => SORT_ASC])
            //->limit(1)
            ->all();

        if (!$jobs) {
            Console::output("Nessun job da processare.");
            return;
        }

        foreach ($jobs as $job) {
            $jobId = $job['job_id'];
            $dataId = $job['data_id'];
            $username = $job['username'];
            $userEmail = $job['user_email'];
            Yii::$app->db->createCommand()
                ->update('export_jobs', ['progress' => 1], ['id' => $job['id']])
                ->execute();
            $command = "php yii background-export/process-excel \"$jobId\" \"$dataId\" \"$username\" \"$userEmail\"";

            Console::output("Eseguo: $command");
            passthru($command);
        }
    }



    public function actionRun()
    {
   /*     $lockFile = Yii::getAlias('@runtime') . '/export_jobs.lock';

        // Se il lock file esiste, esci
        if (file_exists($lockFile)) {
            Console::output("Un'altra istanza è già in esecuzione. Esco.");
          //  return;
        }

        // Crea il lock file
        file_put_contents($lockFile, getmypid());
*/
        try {
            $jobs = (new \yii\db\Query())
                ->from('export_jobs')
                ->where(['progress' => 0])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            if (!$jobs) {
                Console::output("Nessun job da processare.");
                return;
            }

            foreach ($jobs as $job) {
                $jobId = $job['job_id'];
                $dataId = $job['data_id'];
                $username = $job['username'];
                $userEmail = $job['user_email'];

                Yii::$app->db->createCommand()
                    ->update('export_jobs', ['progress' => 1], ['id' => $job['id']])
                    ->execute();

                $command = "php yii background-export/process-excel \"$jobId\" \"$dataId\" \"$username\" \"$userEmail\"";

                Console::output("Eseguo: $command");
                passthru($command);
            }
        } finally {
            // Rimuovi il lock file a fine esecuzione
            /*if (file_exists($lockFile)) {
                unlink($lockFile);
            }*/
        }
    }
}