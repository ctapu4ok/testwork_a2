<?php
/* @var $this TransactionController */
/* @var $sell Sell */
/* @var $index int */
?>
<tr class="sell" data-index="<?php echo $sell ? $index : '<%= index %>' ?>">
    <?php

    $dataContent = $sell ? Yii::app()->controller->renderPartial(
        'application.widgets.views._popover',
        ['data' => $sell->product, 'attributes' => [
            'category.name',
            'season.name',
            'color',
            'size',
        ], 'enableImage' => true],
        true
    ) : '';
    ?>
    <td class="property-name"><?php echo $sell ? $sell->product->name : '<%= sell.product.name %>' ?></td>
    <td class="property-article"
        data-content="<?php echo $sell ? CHtml::encode($dataContent) : '<%= sell.product.popover %>' ?>"><?php echo $sell ? $sell->product->article : '<%= sell.product.article %>' ?></td>
    <td class="property-barcode"><?php echo $sell ? $sell->product->barcode : '<%= sell.product.barcode %>' ?></td>
    <td class="property-product-count"><?php if (!$sell->action) { ?>
            <input type="number"
                   name="Transaction[selectedSells][<?php echo $sell ? $index : '<%= index %>' ?>][product_count]"
                   value="<?php echo $sell ? $sell->product_count : '<%= sell.product_count %>' ?>"
                   max="<?php echo $sell ? $sell->product->existCount : '<%= sell.product.existCount %>' ?>"
                   width="3" min="1" class="form-control"><?php
        } else {
            echo $sell->product_count;
        }
        ?></td>

    <td class="property-discount"><span
            title="<?php echo $sell ? ($sell->action ? $sell->action->promotion->name : '') : '<%= sell.action ? sell.action.promotion.name : "" %>' ?>"><?php echo $sell ? ($sell->action ? ($sell->action->type == 'gift' ? 'подарок' : $sell->action->discount) : '') : '<%= sell.action ? (sell.action.type == "gift" ? "подарок" : sell.action.discount) : "" %>' ?></span>
    </td>

    <td class="property-retail-price type-money"><?php echo $sell ? $sell->product->retail_price : '<%= sell.product.retail_price %>' ?></td>
    <td class="crud-buttons"><a tabindex="-1" class="delete" href="#"><i class="fa fa-trash-o"></i></a></td>

    <input type="hidden" name="Transaction[selectedSells][<?php echo $sell ? $index : '<%= index %>' ?>][product_id]"
           value="<?php echo $sell ? $sell->product_id : '<%= sell.product.id %>' ?>">
    <input type="hidden" name="Transaction[selectedSells][<?php echo $sell ? $index : '<%= index %>' ?>][id]"
           value="<?php echo $sell ? (!$sell->isNewRecord ? $sell->id : '') : '<%= sell.id %>' ?>">

    <input type="hidden" name="Transaction[selectedSells][<?php echo $sell ? $index : '<%= index %>' ?>][action_id]"
           value="<?php echo $sell ? ($sell->action ? $sell->action->id : '') : '<%= sell.action ? sell.action.id : "" %>' ?>">
</tr>
