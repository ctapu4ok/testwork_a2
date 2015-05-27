<?php
/**
 * @var $user User
 */
?>

<div class="modal-over power">
    <div class="modal-center animated flipInX" style="width:300px;margin:-30px 0 0 -150px;">
        <div class="pull-left thumb m-r image"><img src="<?php echo $user->avatar ? UserHelper::getAvatarBaseUrl() . $user->avatar : '/resources/images/logo.jpg' ?>" class="img-thumbnail"></div>
        <div class="clear"><p class="text-white"><?=$user->full_name;?></p>
            <form action="<?php echo $this->createUrl('user/login') ?>" method="post" class="power-form">
            <?php echo CHtml::activeHiddenField($user, 'username')?>
            <?php echo CHtml::activeHiddenField($user, 'remember')?>
            <div class="input-group input-s"><input type="password" name="User[password]" class="form-control text-sm" placeholder="Enter pwd to continue"> <span class="input-group-btn"> <button class="btn btn-success" type="submit">
            <i class="fa fa-arrow-right"></i></button> </span></div>
            <div class="animated shake power-error">Неправильный пароль</div>
        </div>
    </div>
</div>
