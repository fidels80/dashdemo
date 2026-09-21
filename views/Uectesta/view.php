<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Uecanagrafica;
use app\models\Uecrighe;
use app\models\Uecarticoli;

$cli = Uecanagrafica::find()
    ->select(['nome', 'cognome'])
    ->where(['id' => $model->cliente])
    ->asArray()
    ->one();

/* @var $this yii\web\View */
/* @var $model app\models\Uectesta */
$tdoc = Uecrighe::find()
    ->where(['id_testa' => $model->id])
    //->sum(['prezzo'])
    ->asArray()->all();

$sumdoc = 0;
foreach ($tdoc as $value) {
    $sumdoc = $sumdoc + $value['prezzo'];
};

//$this->title = $model->id;
//$this->params['breadcrumbs'][] = ['label' => 'Uectestas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$this->title = "Documento";
?>
<div class="uectesta-view">



    <p>
        <?= Html::a('Elenco', ['index'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div id="printableTable">
        <table class="table">
            <tr>
                <td>
                    <img src="<?php echo Yii::getAlias('@web') . '/uploads/fondazione.png' ?>"
                        alt="Residenza"  align="right"   width="100" height="100" style="opacity: .8">
                </td>
                <td width="400px">
                    <h4>Fondazione Universitaria Europea</h4>
                    Via degli Aldobrandeschi 190,00163 Roma<br>
                    Tel 06 9958 8268 Email: residenza@unier.it<br>
                    C.F. 94760810589 IBAN IT93 R056 9603 2110 0001 0605 X36

                </td>
                <td > <img src="<?php echo Yii::getAlias('@web') . '/uploads/residenza.png' ?>"
                        alt="Residenza"  align= "left"   width="160" height="100" style="opacity: .8">
                </td>

            </tr>
            <tr>
                <td></td>
                <td><br><br><br><br><b>Ns. RIf Num <?php echo $model->numero; ?></b></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>La fondazione Universitaria Europea ha ricevuto da</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td> <b> <?php echo $cli['nome'] . ' ' . $cli['cognome'] ?></b></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td> La somma di € <?php echo $sumdoc




                                    ?></b></td>
                <td> </td>
            </tr>
            <tr>
                <td></td>
                <td> Causale: </b>
                    <?php
                    foreach ($tdoc as $value) {
                        $tdsk = Uecarticoli::find()
                            ->select(['descrizione'])
                            ->where(['codice' => $value['articolo']])
                            ->asArray()->one();
                        $desk = $tdsk['descrizione'];
                        echo   $value['articolo'] . ' ' . $desk . ' ' . $value['nota'] . '<br>';
                    };


                    ?>
                </td>
                <td> </td>
            </tr>
            <tr>
                <td></td>
                <td> Pagato con <b> <?php echo $model->tipopag; ?>
                    </b></td>
                <td> </td>
            </tr>
            <tr>
                <td></td>
                <td> Roma il <b> <?php echo $model->data; ?>
                    </b></td>
                <td> </td>
            </tr>
        </table>
    </div>
    <?= Html::a('Stampa PDF', ['pdf', 'id' => $model->id], ['class' => 'btn btn-info', 'target' => '_blank']) ?>
    <button class="btn btn-secondary" onclick="printTable()">Stampa Tabella</button>

</div>

<script>
    function printTable() {
        // Crea una nuova finestra
        alert(
            "Per una stampa ottimale, disabilita le intestazioni e i piè di pagina dalle impostazioni di stampa del browser."
        );
        const tableContent = document.getElementById('printableTable').innerHTML;
        const printWindow = window.open('', '', 'height=600,width=800');

        // Aggiungi il contenuto della tabella alla finestra di stampa
        printWindow.document.write('<html><head><title></title>');
        printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } td, th { border: 1px solid #ddd; padding: 8px; }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(tableContent);
        printWindow.document.write('</body></html>');

        // Esegui la stampa
        printWindow.document.close();
        printWindow.print();
    }
</script>