<?php
/* @var $this UserController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php
$this->breadcrumbs = [$this->pageTitle];
$headerButtons = [];
if(Yii::app()->user->checkAccess('createUser'))
{
    $headerButtons = ['label' => 'Создать', 'url' => ['create'], 'icon' => 'glyphicon glyphicon-plus'];
}

$this->widget(
    'GridView',
    [
        'title' => 'Список пользователей',
        'dataProvider' => $dataProvider,
        'headerButtons' => [
            $headerButtons
        ],
        'columns' => [
            ['name' => 'id', 'headerHtmlOptions' => ['class' => 'id'], 'htmlOptions' => ['class' => 'id']],
            ['name' => 'full_name', 'value' => '$data->role != "seller" ? CHtml::link($data->full_name . " (" . $data->username . ")", ["list", "parentId" => $data->id]) : $data->username', 'type' => 'html'],
            ['name' => 'role', 'value' => '$data->roleLabels[$data->role]'],
            'phone',
            'email',
            ['class' => 'ButtonColumn', 'template' => '{view} '. (Yii::app()->user->checkAccess('createUser') ? '{update} {deactive} {active}' : '')
                 ,
                'buttons' => [
                            'deactive' => [
                                'label' => 'Деактивировать',
                                'url' => '($data->active == 1) ? Yii::app()->controller->createUrl("user/$data->id/status/deactive") : Yii::app()->controller->createUrl("user/$data->id/status/active") ',
                                'icon' => 'fa fa-ban',
                                'visible' => '$data->active == 1',
                                'click'=>'function(){alert("Вы уверенны!");}',
                            ],
                            'active' => [
                                'label' => 'Активировать',
                                'url' => '($data->active == 1) ? Yii::app()->controller->createUrl("user/$data->id/status/deactive") : Yii::app()->controller->createUrl("user/$data->id/status/active") ',
                                'icon' => 'fa fa-unlock-alt',
                                'visible' => '$data->active == 0',
                            ]
                        ]],
              
        ],
    ]
); ?>
