<?php
/* @var $this TransactionController */
/* @var $action Action */
?>

<li data-id="<?php echo $action ? $action->id : '<%= action.id %>' ?>">
    <span>
        <button type="button"><i class="fa fa-trash-o"></i></button>
        <?php echo $action ? $action->promotion->name : '<%= action.promotion.name %>' ?>
    </span>

    <span>-<?php echo $action ? $action->discount : '<%= action.discount %>' ?>%</span>
</li>
