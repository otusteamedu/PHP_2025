<!DOCTYPE html>
<html>
<head>
    <!-- $pageTitle comes from the array key passed in render() -->
    <title>Homework 15</title>
</head>
<body>
<h1>Order #<?php echo $orderId?></h1>

<?if (isset($products) && !empty($products)) {?>
    <ul>
        <?php /** @var \Dinargab\Homework15\Model\Product\ProductInterface $product */
        foreach($products as $product) { ?>
            <li><?php echo $product->getName(); ?>
                <ul>
                    <?php foreach ($product->getIngredients() as $ingredient) { ?>
                        <li><? echo $ingredient?></li>
                    <?php }?>
                </ul>
            </li>
        <?php } ?>
    </ul>
<? } ?>
<p>Total price: <?php echo $totalPrice ?></p>
</body>
</html>
