<?php
/**
 * @var TransactionController $this
 * @var Transaction $transaction
 * @var User $user
 * @var Client[] $clients
 */
?>
<div class="row text-sm wrapper">
    <div class="col-sm-5 m-b-xs barcode-search" data-url="<?php echo $this->createUrl('transaction/query') ?>"
         data-modal-url="<?php echo $this->createUrl('transaction/modal') ?>">
        <input type="text" class="input-sm form-control" placeholder="Поиск по шрихкоду" autofocus="1">
    </div>

    <div class="col-sm-2"></div>

    <div class="col-sm-5 m-b-xs">
        <div class="input-group article-search" data-url="<?php echo $this->createUrl('transaction/modal') ?>">
            <input type="text" class="input-sm form-control" placeholder="Поиск по артикулу">
            <span class="input-group-btn"><a href="#" class="btn btn-sm btn-white" type="button"><i
                        class="fa fa-search"></i></a></span>
        </div>
    </div>
</div>

<?php

$errors = $transaction->getErrorLabels();

foreach ($transaction->selectedSells as $key => $sell) {
    $errors = array_merge($errors, $sell->getErrorLabels());
}

?>

<script id="transaction-notification" type="text/html">
    <div class="wrapper text-sm transaction-notification">
        <a href="#" class="pull-right"><i class="fa fa-times"></i></a>
    </div>
</script>

<?php if ($errors): ?>
    <div class="wrapper bg-danger text-sm transaction-notification animated shake">
        <a href="#" class="pull-right"><i class="fa fa-times"></i></a>
        <?php if (count($errors) == 1): ?>
            <span><?php echo array_pop($errors) ?></span>
        <?php else: ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
<?php endif ?>

<div class="table-responsive">
    <table class="table text-sm table-striped sells b-t transaction-table">
        <thead>
        <tr>
            <th>Название</th>
            <th>Артикул</th>
            <th>Штрихкод</th>
            <th>Количество</th>
            <th>Скидка</th>
            <th>Цена</th>
            <th></th>
        </tr>
        </thead>

        <tbody>

        <?php

        foreach ($transaction->selectedSells as $index => $sell) {
            $this->renderPartial('_sell_row', ['sell' => $sell, 'index' => $index]);
        }

        ?>

        <tr class="empty">
            <td colspan="6" class="empty"><span class="empty">Товаров не выбрано.</span></td>
        </tr>

        </tbody>
    </table>
</div>

<script type="text/html" id="row-template">
    <?php echo $this->renderPartial('_sell_row') ?>
</script>
