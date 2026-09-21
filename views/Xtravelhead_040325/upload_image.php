<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
//$this->title = 'Carica Immagine';

$orign=\app\models\xtravelhead::find()->orderBy(['th_id' => SORT_DESC])->all();
?>
<h1><?= Html::encode($this->title) ?> da verificare errore sul salvataggio</h1>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>



<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'],
 'id' => 'upload-form',
    'method' => 'post',
    'fieldConfig' => [
        'template' => "{input}\n{error}",

]]); ?>

    <!-- Campo a tendina per selezionare th_id -->
    <?= $form->field($model, 'th_id')->dropDownList(
        \yii\helpers\ArrayHelper::map($orign,
         'th_id', 'decodificatore'),
        ['prompt' => 'Seleziona un ID']
    ) ?>

    <!-- Campo per il file immagine -->
    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Carica', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>
