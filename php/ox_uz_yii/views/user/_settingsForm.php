<?php
/* @var $this UserController */
/* @var $user User */
/* @var $form BSActiveForm */
?>

<div class="profile">
    <div class="avatar" style="text-align: center">
        <?php echo CHtml::beginForm(['upload'], 'post', ['enctype' => 'multipart/form-data']) ?>

        <div class="photo">
            <span class="upload-progress">Загрузка...</span>

            <?php if ($user->avatar): ?>
                <img src="<?php echo UserHelper::getAvatarBaseUrl() . $user->avatar ?>" class="remembered">
            <?php elseif (UserHelper::getRememberedAvatar()): ?>
                <img src="<?php echo UserHelper::getRememberedAvatar() ?>" draggable="false">
            <?php endif ?>
        </div>

        <button class="btn btn-sm btn-info btn-file">Изменить <input type="file" name="avatar"></button>
        <button class="btn btn-sm btn-danger">Удалить</button>

        <?php echo CHtml::endForm() ?>
    </div>
    <div class="details">
        <div class="form">

            <?php $form = $this->beginWidget(
                'bootstrap.widgets.BsActiveForm',
                [
                    'id' => 'user-form',
                    'layout' => BSHtml::FORM_LAYOUT_HORIZONTAL,
                ]
            ) ?>

            <?php

            if ($user->isNewRecord == false) {
                $user->password = '';
                $help = 'Оставьте пустым если не хотите изменять пароль';
                $placeholder = 'Новый пароль';
            }

            ?>

            <?php echo $form->errorSummary($user); ?>

            <?php echo $form->textFieldControlGroup($user, 'username', ['maxlength' => 50]); ?>
            <?php echo $form->textFieldControlGroup($user, 'full_name', ['maxlength' => 50]); ?>
            <?php echo $form->textFieldControlGroup(
                $user,
                'password',
                ['maxlength' => 50, 'help' => $help, 'placeholder' => $placeholder]
            ); ?>
            <?php echo $form->textFieldControlGroup($user, 'address', ['maxlength' => 100]); ?>
            <?php echo $form->textFieldControlGroup($user, 'phone', ['maxlength' => 50]); ?>
            <?php echo $form->textFieldControlGroup($user, 'email', ['maxlength' => 50]); ?>

            <?php echo $form->hiddenField($user, 'cropParams'); ?>
            <?php echo $form->hiddenField($user, 'deleteAvatar'); ?>

            <?php echo BSHtml::formActions(
                [
                    BSHtml::submitButton(
                        $user->isNewRecord ? 'Создать' : 'Сохранить',
                        ['color' => BSHtml::BUTTON_COLOR_PRIMARY]
                    )
                ]
            ) ?>

            <?php $this->endWidget(); ?>

        </div>
    </div>
</div>

<script src="/resources/js/form/jquery.form.min.js"></script>
<script src="/resources/js/cropbox/jquery.cropbox.js"></script>
<script src="/resources/js/hammer/hammer.min.js"></script>
<script src="/resources/js/mousewheel/jquery.mousewheel.js"></script>


<script type="text/javascript">
    $(function ()
    {
        var $container = $('.profile'),
            $form = $container.find('.avatar').find('form'),
            $input = $form.find('input'),
            $button = $form.find('.btn-file'),
            $delete = $form.find('.btn-danger'),
            $progress = $form.find('.upload-progress'),
            $image = $form.find('img'),
            $cropParams = $container.find('input[name*="cropParams"]'),
            $deleteAvatar = $container.find('input[name*="deleteAvatar"]'),
            $cropbox;

        if ($image.hasClass('remembered'))
        {
            $cropbox = $image.cropbox({
                width: 150,
                height: 150,
                controls: false,
                zoom: 5
            });

            $cropbox.on('cropbox', function (e, data)
            {
                $cropParams.val(data.cropX + '_' + data.cropY + '_' + data.cropW + '_' + data.cropH);
            });
        }

        $delete.prop('disabled', $image.length == 0);

        $delete.on('click', function (event)
        {
            event.preventDefault();

            $deleteAvatar.val(1);

            if ($image.data('cropbox'))
            {
                $image.data('cropbox').remove();
            }

            $image.remove();
            $delete.prop('disabled', true);
        });

        $input.on('change', function ()
        {
            var file = $input.val();

            if (!file)
            {
                return;
            }

            $deleteAvatar.val(0);

            var ext = file.split('.').pop().toLowerCase();

            if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1)
            {
                alert('Пожалуйста, выберите изображение в формате gif, png, jpg или jpeg.');
                return;
            }

            $form.ajaxSubmit({
                resetForm: true,
                dataType: 'json',

                beforeSubmit: function ()
                {
                    $button.prop('disabled', true);

                    if ($image.data('cropbox'))
                    {
                        $image.data('cropbox').remove();
                    }

                    $image.remove();
                },
                uploadProgress: function (event, position, total, percentComplete)
                {
                    $progress.text = percentComplete + '%';
                    $progress.show();
                },
                success: function (response)
                {
                    $image = $('<img>').prependTo($container.find('.photo'));
                    $image.attr('src', response.url);
                    $image.show();

                    $cropbox = $image.cropbox({
                        width: 150,
                        height: 150,
                        controls: false,
                        zoom: 5
                    });

                    $cropbox.on('cropbox', function (e, data)
                    {
                        $cropParams.val(data.cropX + '_' + data.cropY + '_' + data.cropW + '_' + data.cropH);
                    });

                    $delete.prop('disabled', false);
                },
                complete: function ()
                {
                    $button.prop('disabled', false);
                    $progress.hide();
                }
            });
        });
    })
</script>


