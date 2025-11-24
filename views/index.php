<!DOCTYPE html>
<html>
<head>
    <title>Homework 15</title>
</head>
<body>
<h1>Order #<?php use Dinargab\Homework15\Model\Product\ProductInterface;

    echo $orderId?></h1>

<?if (isset($products) && !empty($products)) {?>
    <ul>
        <?php /** @var ProductInterface $product */
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
