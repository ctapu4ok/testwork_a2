<?php
/**
 * @var TransactionController $this
 * @var Transaction $transaction
 * @var User $user
 * @var Client[] $clients
 */
?>

<div class="wrapper promotion-list" data-url="<?php echo $this->createUrl('actionModal') ?>">
    <?php

    foreach ($transaction->matchingPromotions as $promotion) {
        echo $this->renderPartial('_promotion_row', ['promotion' => $promotion]);
    }

    ?>
</div>

<script type="text/html" id="promotion-row-template">
    <?php echo $this->renderPartial('_promotion_row') ?>
</script>

