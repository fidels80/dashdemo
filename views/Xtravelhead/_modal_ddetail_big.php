<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use kartik\tabs\TabsX;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use kartik\nav\NavX;
    use kartik\select2\Select2;
    use onmotion\apexcharts\ApexchartsWidget;
    use yii\helpers\Json;
    use kartik\dialog\Dialog;
    use yii\web\JsExpression;
    use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\bootstrap4\Modal;
use kartik\dynagrid\DynaGrid;
?>
 

<style>
    .euro-column {
    min-width: 190px; /* Puoi aumentare il valore se serve più spazio */
    text-align: right; /* Allinea a destra per una migliore leggibilità */
}
.euro-total{
    min-width: 190px; /* Puoi aumentare il valore se serve più spazio */
    text-align: right; /* Allinea a destra per una migliore leggibilità */
    white-space: nowrap;
}

 

    </style>












<?php //echo $struttura;
echo $servizio;
 
?>