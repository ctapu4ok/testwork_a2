<?php
/* @var $this UserController */
/* @var $user User */

$this->layout = 'main';
?>

<header class="header bg-white b-b">
    <p>
        Профиль <?php echo $user->full_name ?>
    </p>

    <?php if (Yii::app()->user->id != $user->id): ?>
        <div class="btn-group pull-right m-t-sm">
            <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                Действия <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                <li><a href="<?php echo $this->createUrl('update', ['id' => $user->id]) ?>">Редактировать</a></li>
                <li><a href="<?php echo $this->createUrl('delete', ['id' => $user->id]) ?>">Удалить</a></li>
                
            </ul>
        </div>
    <?php endif ?>
</header>

<section class="scrollable">
    <section class="hbox stretch">
        <section class="vbox">
            <section class="scrollable">
                <div class="wrapper">

                    <div class="clearfix m-b">
                        <img class="pull-left thumb m-r avatar img-circle"
                             src="<?php echo $user->avatar ? UserHelper::getAvatarBaseUrl() . $user->avatar : '/resources/images/logo.jpg' ?>">

                        <div class="clear">
                            <div class="h3 m-t-xs m-b-xs"><?php echo $user->full_name ?></div>
                            <small class="text-muted">
                                <i class="fa fa-user"></i> <?php echo $user->roleLabels[$user->role] ?></small>
                        </div>
                    </div>

                    <div class="user-details">
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'username'
                            ) ?></small>
                        <p><?php echo $user->username ?></p>
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'email'
                            ) ?></small>
                        <p><?php echo $user->email ?></p>
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'address'
                            ) ?></small>
                        <p><?php echo $user->address ?></p>
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'phone'
                            ) ?></small>
                        <p><?php echo $user->phone ?></p>
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'supplier_id'
                            ) ?></small>
                        <p><?php echo UserHelper::getSuppliersNames($user) ?></p>
                        <small class="text-uc text-xs text-muted"><?php echo $user->getAttributeLabel(
                                'ip'
                            ) ?></small>
                        <p><?php echo $user->ip ?></p>
                    </div>

                </div>
            </section>
        </section>


    </section>
</section>
