<?php
/* @var $this TransactionController */
/* @var $promotion Promotion */
?>

<div class="transaction-promotion">
    <h5 title="<?php echo $promotion ? $promotion->description : '<%= promotion.description %>' ?>"><?php echo $promotion ? $promotion->name : '<%= promotion.name %>'?></h5>

    <ul class="transaction-promotion-actions">
        <?php echo $promotion ? '' : '<% for (var i = 0; i < promotion.actions.length; i++) { var action = promotion.actions[i]; %>' ?>

        <?php if ($promotion): ?>
            <?php foreach ($promotion->actions as $action): ?>
                <li class="transaction-promotion-action"
                    data-action-id="<?php echo $action->id ?>"
                    data-action-type="<?php echo $action->type ?>"
                    data-action-discount="<?php echo $action->discount ?>"
                    data-action-allowed-count="<?php echo $action->allowed_count ?>">
                    <button type="button" class="btn btn-xs <?php echo $action->type ?>" title="Применить"></button>
                    <?php echo $action->getDescription() ?>
                </li>
            <?php endforeach ?>
        <?php else: ?>
            <li class="transaction-promotion-action"
                data-action-id="<%= action.id %>"
                data-action-type="<%= action.type %>"
                data-action-discount="<%= action.discount %>"
                data-action-allowed-count="<%= action.allowed_count %>">
                <button type="button" class="btn btn-xs <%= action.type %>" title="Применить"></button>
                <%= action.description %>
            </li>
        <?php endif ?>

        <?php echo $promotion ? '' : '<% } %>' ?>
    </ul>
</div>
