<?php

/** @var User $user */

$username = Yii::app()->request->cookies['username']->value;
$full_name = Yii::app()->request->cookies['full_name']->value;

if (Yii::app()->request->cookies['avatar']->value) {
    $avatar = Yii::app()->request->cookies['avatar']->value;
} else {
    $avatar = '/resources/images/logo.jpg';
}

?>

<div id="time">20:29:35</div>

<script type="text/javascript">
    function startTime()
    {
        var today=new Date();
        var h=today.getHours();
        var m=today.getMinutes();
        var s=today.getSeconds();
        // add a zero in front of numbers<10
        m=checkTime(m);
        s=checkTime(s);
        document.getElementById('time').innerHTML=h+":"+m+":"+s;
        t=setTimeout(function(){startTime()},500);
    }

    function checkTime(i)
    {
        if (i<10)
        {
            i="0" + i;
        }
        return i;
    }

    $(function() {
        startTime();
    });
</script>

<?php if ($user->hasErrors()): ?>
    <div class="lock-error bg-danger animated shake">
        
        <?php echo $user->errorLabels[0] ?>
        <a href="#" class="pull-right" onclick="$(this).parent().remove()"><i class="fa fa-times"></i></a>
    </div>
<?php endif ?>

<div class="lock-box clearfix">
    <form action="<?php echo $this->createUrl('user/login') ?>" method="post" class="form-inline">



        <img class="lock-logo" src="<?php echo  $avatar?>">


        <div class="lock-username">
            <?php if ($username): ?>
                <span><?php echo $full_name ?></span>
                <input type="hidden" name="User[username]" value="<?php echo $username ?>"/>
            <?php else: ?>
                <input type="text" name="User[username]" value="<?php echo $user->username ?>" class="lock-input form-control" placeholder="Логин"/>
            <?php endif ?>
        </div>

        <div class="lock-password">
            <input type="password" placeholder="Пароль" class="form-control lock-input" name="User[password]">
            <button class="btn btn-lock btn-white" type="submit"><i class="fa fa-arrow-right"></i></button>
        </div>


        <?php if ($username): ?>
            <a class="lock-notme" href="<?php echo $this->createUrl('login', ['clear' => 1]) ?>">Это не я</a>
        <?php endif ?>
    </form>

</div>
