<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $users array */
/* @var $user_id int|null */
/* @var $user app\models\User|null */
/* @var $menuItems app\models\DashMenu[] */
/* @var $assigned array */
/* @var $isSuper bool */

$this->title = 'Assegna Menu agli Utenti';
$this->params['breadcrumbs'][] = ['label' => 'Gestione Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Raggruppa per genitore
$byParent = [];
foreach ($menuItems as $it) {
    $byParent[(int) $it->genitore_id][] = $it;
}
?>
<div class="dashmenu-assegna">

    <h1><?= Html::encode($this->title) ?></h1>

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
                    Utente <strong>livello 100 / supervisore</strong>: vede sempre tutte le voci di menu e le sue assegnazioni non sono modificabili.
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$isSuper): ?>
            <form method="post" action="<?= \yii\helpers\Url::to(['assegna', 'user_id' => $user_id]) ?>">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">

                <div class="card">
                    <div class="card-header">Voci di menu da assegnare</div>
                    <div class="card-body">
                        <?php foreach (($byParent[0] ?? []) as $root): ?>
                            <div class="mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="menu_<?= $root->id ?>"
                                           name="menu[]" value="<?= $root->id ?>"
                                           <?= in_array((int) $root->id, $assigned, true) ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-bold" for="menu_<?= $root->id ?>">
                                        <i class="fas fa-<?= Html::encode($root->icona ?: 'circle') ?>"></i>
                                        <?= Html::encode($root->label) ?>
                                        <small class="text-muted">(liv. <?= (int) $root->livello_min ?>)</small>
                                    </label>
                                </div>

                                <?php foreach (($byParent[(int) $root->id] ?? []) as $child): ?>
                                    <div class="custom-control custom-checkbox" style="margin-left: 2rem;">
                                        <input type="checkbox" class="custom-control-input" id="menu_<?= $child->id ?>"
                                               name="menu[]" value="<?= $child->id ?>"
                                               <?= in_array((int) $child->id, $assigned, true) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="menu_<?= $child->id ?>">
                                            <?= Html::encode($child->label) ?>
                                            <small class="text-muted">(liv. <?= (int) $child->livello_min ?>)</small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Salva assegnazioni', ['class' => 'btn btn-success']) ?>
                </div>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
