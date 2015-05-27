<?php
/* @var $this TransferController */
/* @var $transaction Transaction */
/* @var $user User */
/* @var $clients Client[] */
?>

<div class="panel-body">
    <div class="count-summary">
        Проведено <span>0</span> товаров
    </div>

    <div class="line line-dashed line-lg pull-in"></div>

    <?php if ($clients): ?>
        <label>Клиент</label>

        <input type="hidden"
               data-query-url="<?php echo $this->createUrl('clientQuery') ?>"
               name="Transaction[client_id]"
               class="form-control input-sm" value="<?php echo $transaction->client_id ?>">


        <script type="text/html" id="select-template-client">
            <div>
                <span><%= data.full_name %></span><br>
                <% if (data.group) { %><small class='text-muted'>Группа: <%= data.group %>.</small><% } %>
            </div>
        </script>

        <div class="line line-dashed line-lg pull-in"></div>
        <?php else:?>
        <script type="text/html" id="select-template-client">
            <div>
                <span><%= data.full_name %></span><br>
                <% if (data.group) { %><small class='text-muted'>Группа: <%= data.group %>.</small><% } %>
            </div>
        </script>
    <?php endif ?>
        <?php if ($sellers): ?>
        <label>Продавец</label>

        <input type="hidden"
               data-query-url="<?php echo $this->createUrl('sellerQuery') ?>"
               name="Transaction[seller_id]"
               class="form-control input-sm" value="<?php echo $transaction->seller_id ?>">


        <script type="text/html" id="select-template-seller">
            <div>
                <span><%= data.full_name %></span><br>
            </div>
        </script>

        <div class="line line-dashed line-lg pull-in"></div>
    <?php endif ?>
        <script type="text/html" id="select-template-seller">
        </script>
    <ul class="price-summary">
        <li class="initial"><span>Общая цена</span><span>0</span></li>
        <?php if ($transaction->action): ?>
            <?php echo $this->renderPartial('_price_summary_row', ['action' => $transaction->action]) ?>
        <?php endif ?>
        <li class="final"><span>Цена к оплате</span><span>0</span></li>
    </ul>

    <input type="hidden" name="Transaction[action_id]" value="<?php echo $transaction->action_id ?>">

    <script type="text/html" id="price-summary-template">
        <?php echo $this->renderPartial('_price_summary_row') ?>
    </script>

    <div class="line line-dashed line-lg pull-in"></div>

    <div class="transaction-time noneditable">
        Дата платежа
        <span><input type="hidden" name="Transaction[time]" value="<?php echo $transaction->time ?>"><span><?php echo $transaction->time ?></span></span>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>

    <div class="checkbox">
        <label class="checkbox-custom">
            <input type="checkbox" <?php echo $transaction->paid_cash != 0 ? 'checked' : '' ?> id="paid-type-cash">
            <i class="fa fa-square-o checked"></i> Наличные
        </label>
    </div>

    <div class="paid-input">
        <input type="text" name="Transaction[paid_cash]" class="form-control input-sm" value="<?php echo $transaction->paid_cash ?>"/>
    </div>

    <div class="checkbox">
        <label class="checkbox-custom">
            <input type="checkbox" <?php echo $transaction->paid_credit != 0 ? 'checked' : '' ?> id="paid-type-credit">
            <i class="fa fa-square-o checked"></i> Безналичный расчет
        </label>
    </div>

    <div class="paid-input">
        <input type="text" name="Transaction[paid_credit]" class="form-control input-sm" value="<?php echo $transaction->paid_credit ?>"/>

        <label class="radio">
            <input type="radio" name="Transaction[credit_type]" <?php echo $transaction->credit_type == 'uzcard' ? 'checked' : '' ?> value="uzcard">
            uzcard
        </label>

        <label class="radio">
            <input type="radio" name="Transaction[credit_type]" <?php echo $transaction->credit_type == 'mastercard' ? 'checked' : '' ?> value="mastercard">
            mastercard
        </label>

        <label class="radio">
            <input type="radio" name="Transaction[credit_type]" <?php echo $transaction->credit_type == 'visa' ? 'checked' : '' ?> value="visa">
            visa
        </label>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>

    <div class="clearfix paid-summary">
        <div class="receive"><i class="fa fa-warning"></i> Получено: <span>0</span></div>
        <div class="change">Сдача: <span>0</span></div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>

    <button type="submit" class="transaction-save btn btn-info ladda-button" data-style="expand-left">Оплатить</button>
    <a href="<?php echo !$transaction->isNewRecord ? $this->createUrl('delete', ['id' => $transaction->id]) : '#' ?>" type="button" class="transaction-cancel btn btn-danger pull-right <?php echo $transaction->isNewRecord ? 'hidden' : '' ?>">Отменить</a>
</div>
