<?php
/* @var $this TransactionController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs = [$this->pageTitle];

$this->widget(
    'GridView',
    [
        'title' => 'Продажи в черновике',
        'dataProvider' => $dataProvider,
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
            [
                'class' => 'ButtonColumn',
                'template' => '{view} {restore}',
                'buttons' => [
                    'restore' => [
                        'label' => 'Востановить',
                        'url' => 'Yii::app()->controller->createUrl("transaction/handle", ["id" => $data->id])',
                        'icon' => 'fa fa-share',
                    ]
                ]
            ],
        ],
    ]
); ?>
