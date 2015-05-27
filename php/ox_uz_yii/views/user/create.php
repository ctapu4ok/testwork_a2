<?php
/* @var $this UserController */
/* @var $user User */
?>

<?php $this->breadcrumbs = [$this->pageTitle => ['list'], 'Создание'] ?>

<section class="panel">
    <header class="panel-heading font-semibold">Создание нового пользователя</header>

    <div class="panel-body">
        <?php $this->renderPartial('_form', ['user' => $user]); ?>
    </div>
</section>
