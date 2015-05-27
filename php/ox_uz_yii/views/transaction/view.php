<?php
/* @var $this TransactionController */
/* @var $transaction Transaction */
?>

<?php $this->breadcrumbs = [$this->pageTitle => ['list'], '#' . $transaction->id] ?>

<?php $this->widget(
    'DetailView',
    [
        'data' => $transaction,
        'attributes' => [
            'time',
            ['name' => 'id', 'label' => 'Номер транзакции'],
            ['name' => 'product_count', 'label' => 'Проданные товары'],
            'seller.full_name',
            ['name' => 'client.full_name', 'visible' => $transaction->client_id != null],
            [
                'name' => 'paid_cash',
                'type' => 'money',
                'label' => 'Наличные',
                'visible' => $transaction->credit_type != null
            ],
            [
                'name' => 'paid_credit',
                'type' => 'money',
                'label' => 'Безналичные',
                'visible' => $transaction->credit_type != null
            ],
            ['name' => 'credit_type', 'visible' => $transaction->credit_type != null, 'label' => 'Карточка'],
            [
                'name' => 'action.discount',
                'visible' => $transaction->action->discount != null
            ],
            ['name' => 'sum', 'type' => 'money', 'label' => 'Общая сумма покупки'],
        ],
    ]
); ?>

<?php $this->widget(
    'GridView',
    [
        'title' => 'Проданные товары',
        'dataProvider' => new CActiveDataProvider('Sell', ['data' => $transaction->sells]),
        'columns' => [
            'product_id',
            'product.barcode',
            'product.article',
            'product.name',
            'product.color',
            'product.size',
            'product.existCount',            
            'product_count',
            ['header' => 'Скидка', 'value' => '$data->action ? ($data->action->type == "gift" ? "подарок" : $data->action->discount . "%") : ""'],            
            ['name' => 'price', 'value' => '(int)$data->product_count <= 0 ? "-" . $data->price : $data->price', 'type' => 'money', 'htmlOptions' => ['class' => 'type-money'], 'headerHtmlOptions' => ['class' => 'type-money']],
            ['name' => 'product.retail_price', 'type' => 'money', 'htmlOptions' => ['class' => 'type-money'], 'headerHtmlOptions' => ['class' => 'type-money']],
            ['class' => 'ButtonColumn', 'template' => '{refund}' 
                 ,
                'buttons' => [
                    
                            'refund' => [
                                'label' => 'Возврат клиентом',
                                'url' => 'Yii::app()->createUrl("refund/modal/",array("trans_id"=>'.$transaction->id.',"prod_id"=>$data->product_id,"prod_count"=>$data->product_count))',
                                'icon' => 'fa fa-reply',
                                
                                'options' => array(
                                    'data-value' => $data->id,
                                    'data-toggle' => 'modal',
                                    'data-target' => '#refundForm',
                                    ),
                            ],
                            
                        ]],
        ],
    ]
); ?>
<?php 
$this->beginWidget('bootstrap.widgets.BsModal', array('id' => 'refundForm')); 
?>
<?php $this->endWidget(); ?>



