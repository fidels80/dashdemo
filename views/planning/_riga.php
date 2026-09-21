<?php
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$selectedPersonale = ArrayHelper::getColumn($m->personali, 'id');
$lockDipendenti = !empty($m->ditta_esterna);
$lockDitta = !empty($selectedPersonale);
$rowClass = (isset($highlight) && $highlight) ? 'highlight-row' : '';
?>

  <tr data-id="<?= $m->id ?>" class="<?= $rowClass ?>">
    <td><?= Select2::widget([
        'name' => 'giro_' . $m->id, 
        'value' => $m->giro,
        'data' => array_combine(range(1, 24), range(1, 24)),
        'options' => [
            'id' => 'giro_' . $m->id, 
            'class' => 'inline-edit select2-giro', 
            'data-id' => $m->id, 
            'data-field' => 'giro',
            'placeholder' => '-' // NECESSARIO per allowClear
        ],
        'pluginOptions' => ['width' => '85px', 'allowClear' => true]
    ]) ?></td>

    <td data-sort="<?= $m->data_attivita ?>">
        <input type="date" class="form-control form-control-sm inline-edit mb-1" 
               data-id="<?= $m->id ?>" data-field="data_attivita" 
               value="<?= $m->data_attivita ?>" onkeydown="return false;" onpaste="return false;" style="cursor: default;">
        <div class="d-flex gap-1">
            <input type="time" class="form-control form-control-sm inline-edit" 
                   data-id="<?= $m->id ?>" data-field="ora_inizio" value="<?= substr($m->ora_inizio, 0, 5) ?>">
            <input type="time" class="form-control form-control-sm inline-edit" 
                   data-id="<?= $m->id ?>" data-field="ora_fine" value="<?= substr($m->ora_fine, 0, 5) ?>">
        </div>
    </td>

<td><?= Select2::widget([
        'name' => 'veicoli_' . $m->id, 
        // Recuperiamo gli ID dei veicoli già salvati per popolare la select
        'value' => \yii\helpers\ArrayHelper::getColumn($m->veicoliListRel, 'id'), 
        'data' => $veicoliList,
        'options' => [
            'id' => 'veicolo_' . $m->id, 
            'class' => 'inline-edit select2-veicoli', // Ho aggiunto select2-veicoli per coerenza
            'data-id' => $m->id, 
            // ATTENZIONE: Questo deve puntare al nuovo campo plurale del modello!
            'data-field' => 'veicoli_ids', 
            'multiple' => true, 
            'placeholder' => 'Seleziona Mezzi...',
        ],
        'pluginOptions' => ['width' => '100%', 'allowClear' => true]
    ]) ?></td>

    <td><?= Select2::widget([
        'name' => 'cliente_' . $m->id, 'value' => $m->cd_cf, 'data' => $clientiList,
        'options' => [
            'id' => 'cliente_' . $m->id, 'class' => 'inline-edit', 
            'data-id' => $m->id, 'data-field' => 'cd_cf',
            'placeholder' => 'Seleziona Cliente' // NECESSARIO
        ],
        'pluginOptions' => ['width' => '100%', 'allowClear' => true]
    ]) ?></td>

    <td><?= Select2::widget([
        'name' => 'pers_' . $m->id, 'value' => $selectedPersonale, 'data' => $personaleList,
        'options' => [
            'id' => 'pers_' . $m->id, 'multiple' => true, 'class' => 'inline-edit select2-personale', 
            'data-id' => $m->id, 'data-field' => 'personale_ids', 
            'disabled' => $lockDipendenti,
            'placeholder' => 'Seleziona Personale'
        ],
        'pluginOptions' => ['width' => '100%', 'allowClear' => true]
    ]) ?></td>

    <td><?= Select2::widget([
        'name' => 'ditta_' . $m->id, 'value' => $m->ditta_esterna, 'data' => $ditteList,
        'options' => [
            'id' => 'ditta_' . $m->id, 'class' => 'inline-edit select2-ditta',
            'data-id' => $m->id, 'data-field' => 'ditta_esterna', 
            'disabled' => $lockDitta,
            'placeholder' => 'Seleziona Ditta' // NECESSARIO
        ],
        'pluginOptions' => ['width' => '100%', 'allowClear' => true]
    ]) ?></td>

    <td  data-sort="<?= Html::encode($m->qta_operai) ?>" data-filter="<?= Html::encode($m->qta_operai) ?>"    ><input type="number" class="form-control form-control-sm inline-edit text-center"
     data-id="<?= $m->id ?>" data-field="qta_operai" value="<?= $m->qta_operai ?>"></td>
<td data-sort="<?= Html::encode($m->indirizzo) ?>" data-filter="<?= Html::encode($m->indirizzo) ?>">
        <input type="text" class="form-control form-control-sm inline-edit" 
               data-id="<?= $m->id ?>" data-field="indirizzo" value="<?= Html::encode($m->indirizzo) ?>">
    </td>
    
    <td><?= Select2::widget([
        'name' => 'stato_' . $m->id, 'value' => $m->stato_completamento,
        'data' => ['In Corso' => 'In Corso', 'Completato' => 'Completato', 'Da Iniziare' => 'Da Iniziare', 'Annullato' => 'Annullato'],
        'options' => [
            'id' => 'stato_' . $m->id, 'class' => 'inline-edit fw-bold', 
            'data-id' => $m->id, 'data-field' => 'stato_completamento'
        ],
        'pluginOptions' => ['minimumResultsForSearch' => -1, 'width' => '100%']
    ]) ?></td>

    <td class="text-center text-nowrap">
        <?= Html::a('<i class="fa fa-copy"></i>', ['duplicate', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-info border-0 btn-duplicate-ajax', 'title' => 'Duplica']) ?>
        <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-danger border-0', 'data-method' => 'post', 'data-confirm' => 'Sei sicuro?']) ?>
    </td>
</tr>