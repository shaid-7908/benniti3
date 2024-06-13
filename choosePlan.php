<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
include 'inc/header.php';
include 'inc/topbar.php';
\Stripe\Stripe::setApiKey(STRIPE_KEY_SK_TEST);

try {
    // Retrieve products
    $products = \Stripe\Product::all();

    // Retrieve prices
    $prices = \Stripe\Price::all();
} catch (Exception $e) {
    // Handle any errors
    echo 'Error: ' . $e->getMessage();
}
?>
<div style="text-align: center;">
    <h1 class="inter-font font-700 font-30">Choose a plan </h1>
</div>
<div class="choose-plan-body" >

    <?php foreach ($products as $product) : ?>
        <?php if ($product->active) { ?>
            <div class="mt-2 mb-2 choose-plan-card">
                <h5><?php echo $product->name ?></h5>
                
                <?php foreach ($prices as $price) : ?>
                    <?php if ($price->product === $product->id) : ?>
                        <div>
                            <h3 class="my-2"><?php echo '$' . number_format($price->unit_amount / 100, 2); ?> <?php echo ($price->recurring->interval == 'year') ?  '/year' : '/3 month'; ?></h3>
                             <a href="proceedPlan.php?price_id=<?php echo $price->id ?>" style="color:inherit;text-decoration: none;">

                             
                            <div class="my-2" style="display: flex;align-items: center;justify-content: center; border: 1px solid #f5a800;height:40px;border-radius: 4px; width:300px; <?php echo (($price->recurring->interval == 'year') ? 'background-color:#f5a800;color:black;' : '') ?>">
                                <div class="inter-font font-700">
                                    Choose <?php echo $price->recurring->interval; ?>ly plan
                                </div>
                            </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        <?php
        }
        ?>
    <?php endforeach; ?>

</div>
<?php
include 'inc/footer.php';
?>

</html>