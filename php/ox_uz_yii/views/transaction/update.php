<?php
/* @var $this TransactionController */
/* @var $transaction Transaction */
/* @var $form BSActiveForm */
?>

<?php $this->breadcrumbs = [
    $this->pageTitle => ['list'],
    'Редактирование'
] ?>

<section class="panel">
    <header class="panel-heading font-semibold">Редактирование продажи</header>

    <div class="panel-body">
        <div class="form">

            <?php $form = $this->beginWidget(
                'bootstrap.widgets.BsActiveForm',
                [
                    'id' => 'transaction-form',
                    'layout' => BSHtml::FORM_LAYOUT_HORIZONTAL,
                ]
            ) ?>

            <?php echo $form->errorSummary($transaction); ?>

            <?php echo BSHtml::formActions(
                [
                    BSHtml::submitButton('Сохранить', ['color' => BSHtml::BUTTON_COLOR_PRIMARY])
                ]
            ) ?>

            <?php $this->endWidget(); ?>

        </div>
    </div>
</section>
