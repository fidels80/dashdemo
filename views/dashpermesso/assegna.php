<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $users array */
/* @var $user_id int|null */
/* @var $user app\models\User|null */
/* @var $permessi app\models\DashPermesso[] */
/* @var $assegnati array */
/* @var $isSuper bool */

$this->title = 'Assegna Permessi agli Utenti';
$this->params['breadcrumbs'][] = ['label' => 'Gestione Permessi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Raggruppa per gruppo
$gruppi = [];
foreach ($permessi as $p) {
    $gruppi[$p->gruppo ?: 'Altro'][] = $p;
}
?>
<div class="dashpermesso-assegna">


    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>

    <div class="card card-body mb-3">
        <form method="get" action="<?= \yii\helpers\Url::to(['assegna']) ?>" class="form-inline">
            <label class="mr-2 font-weight-bold">Utente:</label>
            <?= Html::dropDownList('user_id', $user_id, $users, ['prompt' => 'Seleziona utente...', 'class' => 'form-control mr-2', 'style' => 'min-width: 320px;']) ?>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Carica</button>
        </form>
    </div>

    <?php if ($user !== null): ?>
        <div class="card card-body mb-3">
            <div>
                <strong><?= Html::encode($user->username) ?></strong>
                <?php if (!empty($user->email)): ?>&middot; <?= Html::encode($user->email) ?><?php endif; ?>
                &middot; livello <span class="badge badge-info"><?= Html::encode($user->level) ?></span>
            </div>

            <?php if ($isSuper): ?>
                <div class="alert alert-warning mt-2 mb-0">
                    <i class="fas fa-lock"></i>
                    Utente <strong>livello 100 / supervisore</strong>: ha sempre accesso completo a tutte le funzioni.
                    I suoi permessi non sono modificabili.
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$isSuper): ?>
            <form method="post" action="<?= \yii\helpers\Url::to(['assegna', 'user_id' => $user_id]) ?>">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">

                <?php foreach ($gruppi as $gruppo => $lista): ?>
                    <div class="card mb-3">
                        <div class="card-header font-weight-bold"><?= Html::encode($gruppo) ?></div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                <tr>
                                    <th>Funzione</th>
                                    <th class="text-center" style="width:80px;">Vista</th>
                                    <th class="text-center" style="width:80px;">Crea</th>
                                    <th class="text-center" style="width:90px;">Modifica</th>
                                    <th class="text-center" style="width:90px;">Elimina</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($lista as $p): ?>
                                    <?php $a = $assegnati[(int) $p->id] ?? null; ?>
                                    <tr>
                                        <td>
                                            <strong><?= Html::encode($p->descrizione) ?></strong>
                                            <small class="text-muted d-block"><?= Html::encode($p->codice) ?></small>
                                        </td>
                                        <?php foreach (['view' => 'can_view', 'create' => 'can_create', 'update' => 'can_update', 'delete' => 'can_delete'] as $key => $attr): ?>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                       name="perm[<?= $p->id ?>][<?= $key ?>]"
                                                       value="1"
                                                       <?= ($a && $a->$attr) ? 'checked' : '' ?>>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Salva permessi', ['class' => 'btn btn-success']) ?>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
