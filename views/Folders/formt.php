<?php
/* @var $this yii\web\View */
/* @var $model app\models\Folders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="folders-form">

    <?php  
    use yii\helpers\Html;
    use kartik\widgets\FileInput;
    use app\models\allfiles;
    use app\models\Anacli;
    use app\models\CliDest;
    use app\models\doc_rows;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Url;

    echo $node->id;
    echo $form->field($node, 'nfile')->textArea();
    $t = $node->id;

    $url = Url::to(['/folders/ajupd', 'xid_testa' => $node->id, 'tab' => 'DMS'], true);
    echo Html::button(
        'Carica File',
        [
            'class' => 'btn btn-success',
            'onclick' => "window.open('$url', '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=800,height=600')",
        ]
    );

    echo '<table id="product-files" class="table table-condensed table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="70%">file</th>';
    echo '<th width="30%">';
    echo $form->field($node, 'f_content')->fileInput(['multiple' => false])->label('segli File');
    echo '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($node->filesall as $value) {
        echo '<tr>';
        echo '<td>';
        echo Html::a(
            $value['nomefile'],
            ['allfiles/genfile', 'id' => $value['id'], 'file' => str_replace(' ', '_', $value['nomefile'])]
        );
        echo '</td>';
        echo '</tr><tr></tr>';
    }

    echo '<tr id="files-new-parcel-block" style="display: none;">';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    ?>
</div>
