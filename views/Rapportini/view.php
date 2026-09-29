<?php

use yii\helpers\Html;
use yii\helpers\Formatter;
use yii\widgets\DetailView;
use app\models\MgAnagrafica;
use app\models\MgSottocommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$numero = (int) $model->numero;
$data = Formatter::asDate($model->data, 'dd/MM/yyyy');

$this->title = 'Rapportino n. ' . $numero . ' del ' . $data;
$this->params['breadcrumbs'][] = ['label' => 'Rapportini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$cliente = $model->cd_cli ? MgAnagrafica::findOne(['codice' => $model->cd_cli]) : null;
$sottocommessa = $model->commessa ? MgSottocommessa::findOne(['codice' => $model->commessa]) : null;
?>
<div class="rapportini-view">

    <p>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Torna alla lista', ['index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'numero',
            'data',
            [
                'attribute' => 'cd_cli',
                'label' => 'Cliente',
                'value' => $cliente
                    ? $cliente->codice . ' - ' . $cliente->ragione_sociale
                    : $model->cd_cli,
            ],
            [
                'attribute' => 'commessa',
                'label' => 'Sottocommessa',
                'value' => $sottocommessa
                    ? $sottocommessa->codice . ' - ' . $sottocommessa->descrizione
                    : $model->commessa,
            ],
            'qta',
            'ora_in',
            'ora_out',
            'cd_art',
            'des_art',
            'note',
        ],
    ]) ?>

</div>
