<style>.gantt_control.gantt_add {
    display: none !important;
}</style>
<?php
use app\models\Agenda;
  $times = Agenda::find()
  ->asArray()
  ->all();
   yii::warning($times);
$this->registerCssFile('@web/gantt/dhtmlxgantt.css');
$this->registerJsFile('@web/gantt/dhtmlxgantt.js');
$this->registerJsFile('@web/gantt/moment.js');
//$this->registerJsFile('@web/gantt/locale/locale-it.js');

$this->registerCss("
    #ganttContainer {
        width: 100%;
        height: 600px; /* Modifica l'altezza a tua scelta */
    }
");

$ganttData = [
    // Array dei dati del grafico di Gantt
    // Esempio=>
    [
        'id'         => 1,
        'text'       => 'Via Margutta',
        'start_date' => '06-06-2023',
        'duration'   => 12,
        'progress'   => 1,
        "open"=> true,
        'prenotato'=>false
    ],
[
        'id'         => 2,
        'text'       => 'Led 5 di 6',
        'start_date' => '06-06-2023',
        'duration'   => 12,
        'progress'   =>0.80,
        "open"=> true,
        "parent"=>"1"
    ],
    [
        'id'         => 3,
        'text'       => 'Telo ',
        'start_date' => '06-06-2023',
        'duration'   => 12,
        'progress'   => 1,
        "open"=> true,
        "parent"=>"1"
    ],
    

    	["id"=>4, 
        "text"=>"Piazza del popolo", 
        "start_date"=>"01-06-2023", 
        "duration"=>12, "progress"=> 0.6,
         "open"=> true],
		["id"=>5, "text"=>"Piazza Venezia", "start_date"=>"01-12-2023",
     "duration"=>12, "progress"=> 0.4, "open"=> true],

		["id"=>6, "text"=>"Via Squarcialupo", "start_date"=>"02-06-2023", 
    "duration"=>12,  "progress"=>0.5, "open"=> true],

		["id"=>7, "text"=>"Apple", "start_date"=>"06-06-2023",
     "duration"=>"12", "parent"=>"2", "progress"=> 0.6,
      "open"=> true],
		["id"=>8, "text"=>"Opel", "start_date"=>"06-06-2023", 
    "duration"=>"6", "parent"=>"2", "progress"=> 0.5, "open"=> true],
    
		["id"=>9, "text"=>"Fiat", "start_date"=>"12-06-2023", "duration"=>"6", "parent"=>"2", "progress"=> 0.6, "open"=> true],
		["id"=>10, "text"=>"Tim", "start_date"=>"06-06-2023", "duration"=>"7", "parent"=>"2", "progress"=> 0.6, "open"=> true],
		["id"=>11, "text"=>"Fastweb", "start_date"=>"13-06-2023", "duration"=>"5", "parent"=>"2", "progress"=> 0.6, "open"=> true],
		["id"=>14, "text"=>"P2000", "start_date"=>"06-06-2023", "duration"=>"6", "parent"=>"3", "progress"=> 0.5, "open"=> true],
		["id"=>15, "text"=>"Citroen", "start_date"=>"12-06-2023", 
    "duration"=>"6", "parent"=>"3", "progress"=> 0.5, "open"=> true,'color'=>'MediumSlateBlue'],
    
["id"=>16, "text"=>"Telo", "start_date"=>"01-06-2023", "duration"=>"12", "parent"=>"4", "progress"=> 0.5, "open"=> true],
["id"=>17, "text"=>"Led", "start_date"=>"01-06-2023", "duration"=>"12", "parent"=>"4", "progress"=> 0.2, "open"=> true],

["id"=>12, "text"=>"Sky", "start_date"=>"01-06-2023", "duration"=>"12", "parent"=>"16", "progress"=> 1, "open"=> true
,"color"=>'LimeGreen'
],
		["id"=>13, "text"=>"Netflix", "start_date"=>"06-06-2023", "duration"=>"4", "parent"=>"2", "progress"=> 1, 
    "open"=> true,
  "color"=>'Orange'],
    /*
		{"id"=>12, "text"=>"Task #1", "start_date"=>"03-04-2023", "duration"=>"5", "parent"=>"11", "progress"=> 1, "open"=> true},
		{"id"=>13, "text"=>"Task #2", "start_date"=>"02-04-2023", "duration"=>"7", "parent"=>"11", "progress"=> 0.5, "open"=> true},
		{"id"=>14, "text"=>"Task #3", "start_date"=>"02-04-2023", "duration"=>"6", "parent"=>"11", "progress"=> 0.8, "open"=> true},
		{"id"=>15, "text"=>"Task #4", "start_date"=>"02-04-2023", "duration"=>"5", "parent"=>"11", "progress"=> 0.2, "open"=> true},
		{"id"=>16, "text"=>"Task #5", "start_date"=>"02-04-2023", "duration"=>"7", "parent"=>"11", "progress"=> 0, "open"=> true},
		{"id"=>17, "text"=>"Task #2.1", "start_date"=>"03-04-2023", "duration"=>"2", "parent"=>"13", "progress"=> 1, "open"=> true},
		{"id"=>18, "text"=>"Task #2.2", "start_date"=>"06-04-2023", "duration"=>"3", "parent"=>"13", "progress"=> 0.8, "open"=> true},
		{"id"=>19, "text"=>"Task #2.3", "start_date"=>"10-04-2023", "duration"=>"4", "parent"=>"13", "progress"=> 0.2, "open"=> true},
		{"id"=>20, "text"=>"Task #2.4", "start_date"=>"10-04-2023", "duration"=>"4", "parent"=>"13", "progress"=> 0, "open"=> true},
		{"id"=>21, "text"=>"Task #4.1", "start_date"=>"03-04-2023", "duration"=>"4", "parent"=>"15", "progress"=> 0.5, "open"=> true},
		{"id"=>22, "text"=>"Task #4.2", "start_date"=>"03-04-2023", "duration"=>"4", "parent"=>"15", "progress"=> 0.1, "open"=> true},
		{"id"=>23, "text"=>"Task #4.3", "start_date"=>"03-04-2023", "duration"=>"5", "parent"=>"15", "progress"=> 0, "open"=> true}
*/
];
foreach($times as $time){
//$ganttData[];
  $row = [
'id' => $time['id'],
'text' => $time['descrizione']?? $time['elemento'],
'start_date' => date("d-m-Y",strtotime($time['dadata'])),
'end_date' => date("d-m-Y",strtotime($time['adata']))];
$ganttData[]=$row;
}
//yii::warning($ganttData);
// Converti l'array in una stringa JSON correttamente formattata
$ganttDataJson = json_encode($ganttData);
// Registra uno script per inizializzare il grafico di Gantt
$proc=<<<EOF
var colors = [
		{key: "", label: "Default"},
		{key: "#4B0082", label: "Indigo"},
		{key: "#FFFFF0", label: "Ivory"},
		{key: "#F0E68C", label: "Khaki"},
		{key: "#B0C4DE", label: "LightSteelBlue"},
		{key: "#32CD32", label: "LimeGreen"},
		{key: "#7B68EE", label: "MediumSlateBlue"},
		{key: "#FFA500", label: "Orange"},
		{key: "#FF4500", label: "OrangeRed"},
    {key: "#008000", label: "verde"}
	];

gantt.config.columns = [
		{name: "text", label:"Commessa", tree: true, width: 250, min_width: 250, resize: true},
		{name: "start_date",label:"Inizio", align: "center", resize: true},
		//{name: "duration",label:"Durata", align: "center"},
     {name: "sp",label:"", align: "center",width: 1, resize: true},
    {name: "end_date",label:"Fine", align: "center", resize: true},
    {name: "sp",label:"", align: "left", width: 1,resize: true},
    
  ];
gantt.config.scales = [
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "day", step: 1, format: "%j, %D"}
	];
gantt . i18n . setLocale("it");

// Inizializza il grafico di Gantt
    gantt.init('ganttContainer');
     

 
    var ganttData = JSON.parse('$ganttDataJson');
   // for (var i = 0; i < ganttData.length; i++) {
   //     var task = ganttData[i];
   //     task.start_date = moment(task.start_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
   // }
    gantt.parse({data: ganttData});
gantt.config.readonly=true;
EOF;
$this->registerJs($proc);

?>

<!-- HTML markup per il contenitore del grafico di Gantt -->
<div id="ganttContainer" ></div>


 