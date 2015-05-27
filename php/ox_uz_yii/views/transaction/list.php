<?php
/* @var $this TransactionController */
/* @var $dataProvider CActiveDataProvider */
/* @var $transaction Transaction */
/* @var $transactions Transaction[] */
/* @var $sellers array */
/* @var $clients array */
/* @var $transactionIds array */
/* @var $shops array */
?>

<?php $this->breadcrumbs = [$this->pageTitle]; ?>
<div class="row">
    <div class="col-sm-8 transaction">
        <?php
        $this->widget(
                'GridView', [
            'title' => 'Список продаж',
            'dataProvider' => $dataProvider,
            'headerButtons' => [
                ['label' => 'Экспорт', 'url' => $this->createUrl('export'), 'icon' => 'fa fa-save', 'htmlOptions' => ['class' => 'export']]
            ],
            'columns' => [
                'id',
                'seller.full_name',
                'client.full_name',
                'time',
                'product_count',
                [
                    'name' => 'sum',
                    'type' => 'money',
                    'htmlOptions' => ['class' => 'type-money'],
                    'headerHtmlOptions' => ['class' => 'type-money']
                ],
                ['class' => 'ButtonColumn', 'template' => '{view}'],
            ],
                ]
        );
        ?>
    </div>
    <?php
    $this->renderPartial('filter', ['transaction' => $transaction,
        'sellers' => $sellers,
        'shops' => $shops,
        'clients' => $clients,
        'allCount' => $allCount,
        'time2' => $time2,
        'time1' => $time1,
        'allSum' => $allSum,
        'nal' => $nal,
        'bezNal' => $bezNal,
        'tran'=>$tran,
    ]);
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/resources/js/transaction/transaction.js', CClientScript::POS_HEAD, ['defer' => 'defer']);
    ?>
</div>
