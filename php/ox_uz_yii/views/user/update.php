<?php
/* @var $this UserController */
/* @var $user User */
?>

<?php $this->breadcrumbs = [
    $this->pageTitle => ['list'],
    $user->full_name => ['view', 'id' => $user->id],
    'Редактирование'
] ?>

<section class="panel">
    <header class="panel-heading font-semibold">Редактирование пользователя</header>

    <div class="panel-body">
        <?php $this->renderPartial('_form', ['user' => $user]); ?>
    </div>
</section>
