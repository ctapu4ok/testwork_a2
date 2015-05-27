<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Выберите клиент</h4>
        </div>
        <div class="modal-body">
            <?php

            $this->widget(
                'GridView',
                [
                    'title' => 'Список клиентов',
                    'modal' => 'true',
                    'search' => ['full_name' => 'Поиск по имени'],
                    'id' => 'yii-product-grid',
                    'dataProvider' => $dataProvider,
                    'stripped' => false,
                    'columns' => [
                        ['class' => 'CheckBoxColumn', 'name' => 'id'],
                        ['name' => 'full_name', 'htmlOptions' => ['class' => 'property-full-name']],
                        ['name' => 'gender', 'htmlOptions' => ['class' => 'property-gender']],
                        ['name' => 'birth_date', 'htmlOptions' => ['class' => 'property-birth-date']],
                        ['name' => 'address', 'htmlOptions' => ['class' => 'property-address']],
                        ['name' => 'phone', 'htmlOptions' => ['class' => 'property-phone']],
                        ['name' => 'email', 'htmlOptions' => ['class' => 'property-email']],
                        ['name' => 'company', 'htmlOptions' => ['class' => 'property-company']],
                    ],
                ]
            ); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
        </div>
    </div>
</div>
