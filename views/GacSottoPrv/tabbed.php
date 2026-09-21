<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>

<?php
use kartik\tabs\TabsX;
use yii\helpers\Url;
use yii\widgets\Pjax;

use app\models\gacprv;
use app\models\sottocommessa;
use app\models\gacmateriali;
use app\models\gacattivita;
use app\models\gacspese;
use app\models\allfiles;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\testaj;
use app\models\TestajSearch;


$items = [
   [
       'label'   => '<i class="fa-solid fa-location-crosshairs fa-spin"></i>Azioni',
        'content' => 'ciao',
        
    ],

[
        'label'   => '<i class="fas fa-plus"></i> Nuovo Record',
        'content' => $this->render('_nuovoele', [
            // Passa eventuali dati o modelli necessari al file della form '_nuovo_record.php'
        ]),
    ],

];

 ?>

<?php
Pjax::begin();

echo TabsX::widget([
    'items'        => $items,
    'position'     => TabsX::POS_ABOVE,
    'encodeLabels' => false,

]);
Pjax::end();
?>
