<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
$this->params['breadcrumbs'] = [['label' => $this->title]];

// Se $code non è passato direttamente dal controller, proviamo a ricavarlo dall'eccezione
$statusCode = isset($code) ? $code : (property_exists($exception, 'statusCode') ? $exception->statusCode : $exception->getCode());
?>

<div class="error-page">
    <div class="error-content" style="margin-left: auto;">

        <h3><i class="fas fa-exclamation-triangle text-danger"></i>
            <?= Html::encode($name) ?>
        </h3>

        <h1>Errore <?= Html::encode($statusCode) ?></h1>

        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <p>
            L'errore sopra riportato si è verificato durante l'elaborazione della richiesta da parte del server Web.
            Se ritieni che si tratti di un errore del server, ti preghiamo di contattarci. Grazie.
        </p>

        <p>
            <?= Html::a('Ritorna alla dashboard', Yii::$app->homeUrl, ['class' => 'btn btn-primary']); ?>
        </p>

        <?php if (YII_DEBUG || (isset(Yii::$app->user) && !Yii::$app->user->isGuest && Yii::$app->user->can('admin'))): ?>
            <hr>
            <div class="technical-details" style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                <h4 class="text-danger">Dettagli Tecnici (Debug)</h4>

                <p><strong>Exception:</strong> <?= get_class($exception) ?></p>
                <p><strong>File:</strong> <?= $exception->getFile() ?></p>
                <p><strong>Line:</strong> <?= $exception->getLine() ?></p>

                <?php if ($exception instanceof \yii\db\Exception): ?>
                    <p><strong>Query Info:</strong>
                    <pre><?= print_r($exception->errorInfo, true) ?></pre>
                    </p>
                <?php endif; ?>

                <p><strong>Stack Trace:</strong></p>
                <pre style="font-size: 12px; background: #333; color: #fff; padding: 10px; overflow: auto;"><?= Html::encode($exception->getTraceAsString()) ?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>