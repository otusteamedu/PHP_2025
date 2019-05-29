<div class="row">
<? foreach ($cart as $item):?>
    <div class="cart-cont col-12">
        <a href="?c=product&a=card&id=<?=$item['id_product']?>">

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
           Количество: <?=$item['quantity']?>
        </p>
        <p class="card-text" style="flex-grow: 1">
            Ед.изм.: <?=$item['name_unit']?>
        </p>
        <p class="card-text" style="flex-grow: 1">
            Категория: <?=$item['category']?>
        </p>
        <p class="card-text" style="flex-grow: 1">
            Тип: <?=$item['type']?>
        </p>

    </div>
<?endforeach;?>
</div>
