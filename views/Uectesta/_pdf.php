<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Uecanagrafica;
use app\models\Uecrighe;
use app\models\Uecarticoli;

$articoli = Uecarticoli::find()
    ->select(['codice', 'descrizione'])
    ->where(['codice' => array_column($tdoc, 'articolo')])
    ->indexBy('codice')
    ->asArray()
    ->all();

foreach ($tdoc as $value) {
    $desk = $articoli[$value['articolo']]['descrizione'] ?? '';
    //echo $value['articolo'] . ' ' . $desk . ' ' . $value['nota'] . '<br>';
}



?>





<table class="table">
    </thead>
    <tbody>
        <tr>
            <td>
                <img src="<?= Yii::getAlias('@webroot') . '/uploads/fondazione.png' ?>
            " alt="Residenza" width="100" height="100" style="opacity: .8">

            </td>
            <td>
                <h4>Fondazione Universitaria Europea</h4>
                Via degli Aldobrandeschi 190,00163 Roma<br>
                Tel 06 9958 8268 Email: residenza@unier.it<br>
                C.F. 94760810589 IBAN IT93 R056 9603 2110 0001 0605 X36
            </td>
            <td>
                <img src="<?= Yii::getAlias('@webroot') . '/uploads/residenza.png' ?>
            " alt="Residenza" width="160" height="100" style="opacity: .8">

            </td>
        </tr>
        <tr>
            <td></td>
            <td><br><br><br><br><b>Ns. RIf Num <?= $model->numero; ?></b></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>La fondazione Universitaria Europea ha ricevuto da</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b><?= $cli['nome'] . ' ' . $cli['cognome'] ?></b></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>La somma di € <?= $sumdoc; ?></b></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td><b>Causale:</b><br>
                <?php /*foreach ($tdoc as $value): ?>
                <?php
                $tdsk = Uecarticoli::find()
                    ->select(['descrizione'])
                    ->where(['codice' => $value['articolo']])
                    ->asArray()
                    ->one();
                $desk = $tdsk['descrizione'];
                echo $value['articolo'] . ' ' . $desk . ' ' . $value['nota'] . '<br>';
                */ ?>
                <?php /*endforeach;*/ ?>
                <?php foreach ($tdoc as $value): ?>
                    <?= $value['articolo'] . ' ' . ($articoli[$value['articolo']]['descrizione'] ?? '') . ' ' . $value['nota'] ?><br>
                <?php endforeach; ?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>Pagato con <b><?= $model->tipopag; ?></b></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>Roma il <b><?= $model->data; ?></b></td>
            <td></td>
        </tr>
    </tbody>
</table>