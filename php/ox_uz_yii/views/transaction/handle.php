<?php
/* @var $this TransferController */
/* @var $transaction Transaction */
/* @var $user User */
/* @var $clients Client[] */
?>

<?php echo CHtml::beginForm('', 'post', ['id' => 'transaction-form']) ?>

<div class="row">
    <div class="col-sm-8">
        <section class="panel">
            <header class="panel-heading">
                Список продоваемых товаров
            </header>

            <?php $this->renderPartial('_sells', ['transaction' => $transaction, 'user' => $user, 'clients' => $clients]) ?>
        </section>

        <section class="panel">
            <header class="panel-heading">
                Акции
            </header>

            <?php $this->renderPartial('_promotions', ['transaction' => $transaction, 'user' => $user, 'clients' => $clients]) ?>
        </section>
    </div>

    <div class="col-sm-4">
        <section class="panel">
            <header class="panel-heading">
                Параметры
            </header>

            <?php $this->renderPartial('_sidebar', ['transaction' => $transaction, 'user' => $user, 'clients' => $clients, 'sellers' => $sellers]) ?>
        </section>
    </div>
</div>

<?php echo CHtml::endForm() ?>

<div class="modal fade" id="modal" tabindex="-1"></div>

<?php
Yii::app()->clientScript->registerCoreScript('bbq');
?>


<link href="/resources/js/fuelux/fuelux.css" type="text/css" rel="stylesheet"/>
<link href="/resources/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" type="text/css"
      rel="stylesheet"/>
<link href="/resources/js/ladda/ladda-themeless.min.css" type="text/css" rel="stylesheet"/>
<link href="/resources/js/select2/select2.css" type="text/css" rel="stylesheet"/>
<link href="/resources/js/select2/select2-bootstrap.css" type="text/css" rel="stylesheet"/>

<script src="/resources/js/angular/angular.min.js"></script>
<script src="/resources/js/ladda/spin.min.js"></script>
<script src="/resources/js/ladda/ladda.min.js"></script>
<script src="/resources/js/form/jquery.form.min.js"></script>
<script src="/resources/js/moment/moment-with-langs.min.js"></script>
<script src="/resources/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="/resources/js/bootstrap-datetimepicker/src/js/locales/bootstrap-datetimepicker.ru.js"></script>
<script src="/resources/js/underscore/underscore-min.js"></script>
<script src="/resources/js/ladda/spin.min.js"></script>
<script src="/resources/js/fuelux/fuelux.js"></script>
<script src="/resources/js/autonumeric/autoNumeric.js"></script>
<script src="/resources/js/select2/select2.js"></script>
<script src="/resources/js/select2/select2_locale_ru.js"></script>

<?php
Yii::app()->clientScript->registerScriptFile('/resources/js/cookies/cookies.js');
Yii::app()->clientScript->registerScriptFile('/resources/js/grid.js');
?>

<script src="/resources/app/transaction/bundle.js"></script>
