<?php
/* @var $this UserController */
/* @var $user User */
?>

<?php $this->breadcrumbs = [
    $this->pageTitle => ['list'],
    $user->full_name => ['view', 'id' => $user->id],
    'Настройки'
] ?>

<section class="panel">
    <header class="panel-heading font-semibold">Настройки</header>

    <div class="panel-body">
        <?php $this->renderPartial('_settingsForm', ['user' => $user]); ?>
    </div>
</section>
