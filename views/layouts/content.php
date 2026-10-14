<?php
/* @var $content string */

use yii\bootstrap4\Breadcrumbs;
use yii\helpers\Html;

if (Yii::$app->session->hasFlash('recordLock')) {
    $recordLockMessage = Yii::$app->session->getFlash('recordLock');
} else {
    $recordLockMessage = null;
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <?php
                        if (!is_null($this->title)) {
                            //echo \yii\helpers\Html::encode($this->title);
                        } else {
                            //echo \yii\helpers\Inflector::camelize($this->context->id);
                        }
                        ?>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <?php
                    /*echo Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) 
                        ? $this->params['breadcrumbs'] : [],
                        'options' => [
                            'class' => 'breadcrumb float-sm-right'
                        ]
                    ])*/;
                    ?>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <?php if ($recordLockMessage !== null): ?>
            <div class="container-fluid">
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Record in uso</h5>
                    <?= Html::encode($recordLockMessage) ?>
                </div>
            </div>
        <?php endif; ?>
        <?= $content ?><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>