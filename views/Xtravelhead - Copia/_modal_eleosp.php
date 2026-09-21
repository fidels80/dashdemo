<?php
use yii\helpers\ArrayHelper;
    use yii\data\ArrayDataProvider;
    use kartik\export\ExportMenu;
$gridColumns = [
    [
        'attribute' => 'guest',
        'label' => 'Ospite',
         
    ],
    [
        'attribute' => 'cd_Ar',
        'label' => 'Tipo Camera',
         
    ],
    [
        'attribute' => 'note',
        'label' => 'Note',
     ],
  ];




$dataProvider = new ArrayDataProvider([
    'allModels' => $roomlist2,
    //'pagination' => [
    //    'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    //],
 
]);
// Crea il menu di esportazione

$exportConfig = [
    ExportMenu::FORMAT_EXCEL => [
        'label' => 'Excel',
        'filename' => 'Export_Excel_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Excel verrà generato per il download.',
        'options' => ['title' => 'Esporta in Excel'],
    ],
    ExportMenu::FORMAT_CSV => [
        'label' => 'CSV',
        'filename' => 'Export_CSV_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file CSV verrà generato per il download.',
        'options' => ['title' => 'Esporta in CSV'],
    ],
    ExportMenu::FORMAT_TEXT => [
        'label' => 'Text',
        'filename' => 'Export_Text_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Text verrà generato per il download.',
        'options' => ['title' => 'Esporta in Text'],
    ],
    // Disabilita altri formati se non necessari
    ExportMenu::FORMAT_PDF => false,
    ExportMenu::FORMAT_HTML => false,
];
$defaultStyle = [
    'borders' => [
        'outline' => [
            //'//borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['argb' => 'black'],
        ],
        'inside' => [
         //   'borderStyle' => Border::BORDER_DOTTED,
            'color' => ['argb' => 'BLACK'],
        ]
    ],
];

echo '<br>';
echo '<br>';

echo ExportMenu::widget([
    'id' => 'exp_button_roomlist', // Imposta l'ID per il bottone
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'exportConfig' => $exportConfig,
    'filename' => 'Export_' . date('Y-m-d_H-i-s'),
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => true,
    'clearBuffers' => true,
 //   'class'=>'pino',
    'dropdownOptions' => [
        'label' => 'Esporta Dati',
        'class' => 'PErsonale', // Solo classi personalizzate
        'title' => 'Esporta i dati nel formato selezionato',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ],
]);
?>
<br>
<?php
    echo '
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Ospite</th>
        <th>Tipo Camera</th>
        <th>Note</th>
    </thead>';

    foreach ($roomlist2 as $value) {
        echo '<tr>';
        echo '<td>';
        echo isset($value['guest']) ? str_replace(["'", '"', "’", "´"], '', $value['guest']) : '';


        echo '</td>';
        echo '<td>';
        echo isset($value['cd_Ar'])?$value['cd_Ar']:'';
        echo '</td>';
        echo '<td>';
        echo isset($value['note'])?$value['note']:'';
        echo '</td>';

        echo '</tr>';
    }
?>
</table>
<br>
<br>