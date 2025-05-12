<div class="row">
<!--    --><?//var_dump($cart)?>
<?php foreach ($test as $item):?>
    <div class="cart-cont col-12">
        <pre>
            <?php var_dump($item['name']) ?>
        </pre>
    </div>
<?php endforeach;?>
</div>
<a href = "/order/prepear" class="btn btn-light" type="button">Оформить</a>
<script src="/js/cart.js">

</script>
