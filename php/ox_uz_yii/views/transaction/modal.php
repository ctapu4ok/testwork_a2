<?php
/* @var $dataProvider CActiveDataProvider */
?>

<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Выберите товар</h4>
        </div>
        <div class="modal-body">
            <?php

            $this->widget(
                'GridView',
                [
                    'title' => 'Список товаров',
                    'modal' => 'true',
                    'search' => ['barcode' => 'Поиск по шрихкоду', 'article' => 'Поиск по артикулу'],
                    'id' => 'yii-product-grid',
                    'dataProvider' => $dataProvider,
                    'stripped' => false,
                    'rowHtmlOptionsExpression' => '($data->article != $prevData->article) ? ["class" => strpos($prevHtmlOptions["class"], "custom-odd") === false ? "custom-odd" : "custom-even"] : ["class" => strpos($prevHtmlOptions["class"], "custom-odd") === false ? "custom-even" : "custom-odd"]',
                    'columns' => [
                        ['name' => 'name', 'htmlOptions' => ['class' => 'property-name']],
                        [
                            'class' => 'PopoverColumn',
                            'name' => 'category.name',
                            'enableImage' => true,
                            'attributes' => [
                                'category.name',
                                'season.name',
                                'color',
                                'size',
                            ],
                            'htmlOptions' => ['class' => 'property-category']
                        ],
                        ['name' => 'article', 'htmlOptions' => ['class' => 'property-article']],
                        ['name' => 'barcode', 'htmlOptions' => ['class' => 'property-barcode']],
                        ['name' => 'color', 'htmlOptions' => ['class' => 'property-color']],
                        ['name' => 'size', 'htmlOptions' => ['class' => 'property-size']],
                        ['name' => 'shopProductCount', 'htmlOptions' => ['class' => 'property-exist-count']],
                        [
                            'class' => 'NumberColumn',
                            'name' => 'product_count',
                            'min' => '0',
                            'max' => '$data->shopProductCount'
                        ],
                        [
                            'name' => 'retail_price',
                            'type' => 'money',
                            'htmlOptions' => ['class' => 'type-money property-retail-price'],
                            'headerHtmlOptions' => ['class' => 'type-money']
                        ],
                    ],
                ]
            ); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
        </div>
    </div>
</div>
