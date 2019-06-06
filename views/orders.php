<br>
<br>
<h2>
    Список заказов
</h2>
<br>
<br>

<div style="display: flex;">
    <p style="margin-right: 30px" >
        Номер заказа
    </p>
    <p style="margin-right: 130px">
        Телефон
    </p>
    <p >
        Статус
    </p>
</div><hr/>
<?foreach ($orders as $item):?>

        <div style="display: flex;">

            <p style="margin-right: 122px" >
                <?=$item['id_order']?>
            </p>
            <a href="/order/single/?id_order=<?=$item['id_order']?>" style="display: block">
            <p style="margin-right: 122px">
                <?=$item['telefon']?>
            </p>
            </a>
            <p >
                <?=$item['status']?>
            </p>
        </div>

    <hr/>
<?endforeach;?>
<script src="/js/order.js">

</script>
