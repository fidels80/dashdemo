<?php
// views/veicoli/_tab_documenti_table.php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<table class="table table-striped table-bordered table-documenti-auto" style="width:100%">
    <thead>
        <tr>
            <th>Nome File</th>
            <th>Inizio Validità</th>
            <th>Scadenza</th>
            <th>Importo</th>
            
            <th>Nota</th>
            <th style="width: 150px;">Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($files as $file): ?>
            <?php 
                // LOGICA DI FILTRO:
                // Se $sub_entita è null, mostriamo i file con sub_entita vuoto.
                // Altrimenti mostriamo solo quelli che matchano.
                $mostra = false;
                if ($sub_entita === null) {
                    if (empty($file->sub_entita)) $mostra = true;
                } else {
                    if ($file->sub_entita === $sub_entita) $mostra = true;
                }

                if ($mostra): 
            ?>
                <tr>
                    <td><i class="fa fa-file-alt text-secondary"></i> <?= Html::encode($file->nomefile) ?></td>
                    <td class="text-center"><?= $file->data_inizio ? Yii::$app->formatter->asDate($file->data_inizio, 'php:d/m/Y') : '-' ?></td>
                     <td class="text-center">
                        <?php if ($file->data_fine): ?>
                            <span class="<?= (strtotime($file->data_fine) < time()) ? 'text-danger fw-bold' : '' ?>">
                                <?= Yii::$app->formatter->asDate($file->data_fine, 'php:d/m/Y') ?>
                            </span>
                        <?php else: ?> - <?php endif; ?>
                    </td>
                     <td class="text-center"><?= $file->importo ? Yii::$app->formatter->asDecimal($file->importo, 2) : '-' ?></td>
                  
                    <td><small><?= Html::encode($file->nota) ?></small></td>
                    <td class="text-center">
                        <a href="<?= Url::to(['/allfiles/download', 'id' => $file->id]) ?>" class="btn btn-xs btn-info"><i class="fa fa-download"></i></a>
                        <?= Html::a('<i class="fa fa-trash"></i>', ['/allfiles/delete', 'id' => $file->id], [
                            'class' => 'btn btn-xs btn-danger',
                            'data-method' => 'post',
                            'data-confirm' => 'Eliminare questo documento?'
                        ]) ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>