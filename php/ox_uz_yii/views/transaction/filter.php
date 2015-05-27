<?php
/**
 * @var $this           TransactionController
 * @var $transaction    Transaction
 * @var $sellers        array
 * @var $clients        array
 * @var $form           BsActiveForm
 * @var $transactionIds array
 * @var $shops          array
 */
?>
<div class="col-sm-4">
    <div class="panel">
        <header class="panel-heading">
            От: <b><?php echo $time1; ?></b> до: <b><?php echo $time2; ?></b>
            <br />
            Транзакции: <b><?php echo number_format($tran); ?> шт.</b>
            <br />
            Проданные товары: <b><?php echo number_format($allCount); ?> шт.</b>
            <br />
            Общая сумма: <b><?php echo number_format($allSum); ?></b>
            <br />
            Наличные : <b><?php echo number_format($nal); ?></b>
            <br />
            Безналичные : <b><?php echo number_format($bezNal); ?></b>
        </header>
    </div>
</div>

<div class="col-sm-4">
    <div class="panel">
        <header class="panel-heading">Фильтр</header>
        <div class="panel-body">
            <?php
            $form = $this->beginWidget(
                    'bootstrap.widgets.BsActiveForm', [
                'id' => 'transaction-form',
                'layout' => BSHtml::FORM_LAYOUT_HORIZONTAL,
                'method' => 'get',
                'action' => $this->createUrl('list')
                    ]
                    )
            ?>
            <?php echo $form->textFieldControlGroup($transaction, 'id', array('class' => 'form-control')) ?>
            <div class="form-group form-group-inline">
                <label class="control-label col-sm-3 required" for="Transaction_time">Время <span
                        class="required">*</span></label>

                <div class="col-sm-9">
                    <input type="text" value="<?= $transaction->time['from'] ?>"
                           name="Transaction[time][from]" id="Transaction_time" class="form-control data-time"
                           size="4"> &mdash;
                    <input type="text" value="<?= $transaction->time['to'] ?>" name="Transaction[time][to]"
                           class="form-control data-time" id="Transaction_time" size="4">
                </div>
            </div>

            <?php
            if ($sellers) {
                echo $form->dropDownListControlGroup($transaction, 'seller_id', CHtml::listData($sellers, 'id', 'username'), ['class' => 'select', 'multiple' => true]);
            }
            ?>

            <?php echo $form->dropDownListControlGroup($transaction, 'client_id', CHtml::listData($clients, 'id', 'full_name'), ['class' => 'select', 'multiple' => true]) ?>

            <div class="form-group form-group-inline">
                <label class="control-label col-sm-3 required" for="Transaction_sum">Сумма <span
                        class="required">*</span></label>

                <div class="col-sm-9">
                    <input type="text" value="<?= $transaction->sum['from'] ?>"
                           name="Transaction[sum][from]" id="Transaction_sum" class="form-control" size="4"> &mdash;
                    <input type="text" value="<?= $transaction->sum['to'] ?>" id="Transaction_sum"
                           name="Transaction[sum][to]"
                           class="form-control" size="4">
                </div>
            </div>

            <div class="form-group form-group-inline">
                <label class="control-label col-sm-3 required" for="Transaction_product_count">Кол <span
                        class="required">*</span></label>

                <div class="col-sm-9">
                    <input type="text" value="<?= $transaction->product_count['from'] ?>"
                           name="Transaction[product_count][from]" id="Transaction_product_count" class="form-control"
                           size="4"> &mdash;
                    <input type="text" value="<?= $transaction->product_count['to'] ?>" id="Transaction_product_count"
                           name="Transaction[product_count][to]"
                           class="form-control" size="4">
                </div>
            </div>
            <?php
            if ($shops) {
                echo $form->dropDownListControlGroup($transaction, 'shop_id', CHtml::listData($shops, 'id', 'name'), ['multiple' => true, 'class' => 'select']);
            }

            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/resources/js/moment/moment-with-langs.min.js');
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/resources/js/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
            Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/resources/js/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');
            Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/resources/js/select2/select2.js');
            Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/resources/js/select2/select2_locale_ru.js');
            Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/resources/js/select2/select2.css');
            Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/resources/js/select2/select2-bootstrap.css');
            Yii::app()->clientScript->registerScript('register-select2', '$(".select").select2();', CClientScript::POS_READY);

            echo BSHtml::formActions(
                    [
                        BSHtml::submitButton('поиск', ['color' => BSHtml::BUTTON_COLOR_PRIMARY])
                    ]
            )
            ?>
            <?php $this->endWidget(); ?>
        </div>
    </div>

</div>
