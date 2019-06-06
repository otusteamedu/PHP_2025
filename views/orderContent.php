<h2>
    Заказ №<?=$id_order?>
</h2>
<?foreach ($order as $item):?>
    <div class="cart-cont col-12" id="<?=$item['id_cart']?>">
        <a href="/product/card/?id=<?=$item['id_product']?>">
            <img src="<?=IMG_SMALL . $item['img'][0]?>" class="" style="flex-grow: 3">
        </a>
        <h5 class="card-title" style="flex-grow: 2">
            <?=$item['name_product']?>
        </h5>
        <p class="card-text" style="flex-grow: 3">
            <?=$item['description']?>
        </p>
        <p class="card-text" style="flex-grow: 1">
            Цена: <?=$item['price']?>
        </p>
        <p class="card-text" style="flex-grow: 1">
            Количество: <span class="quantity"><?=$item['quantity']?></span>
        </p>
    </div>
<?endforeach;?>
