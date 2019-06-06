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
            <select class="status">

                <option value="Принят" <?if($item['status'] =='Принят')echo "selected"?> data-orderID="<?=$item['id_order']?>">
                    Принят
                </option>
                <option value="Передан в обработку" <?if($item['status'] =='Передан в обработку')echo "selected"?> data-orderID="<?=$item['id_order']?>">
                    Передан в обработку
                </option>
                <option value="Обрабатывается" <?if($item['status'] =='Обрабатывается')echo "selected"?> data-orderID="<?=$item['id_order']?>">
                    Обрабатывается
                </option>
            </select>
<!--            <p >-->
<!--                --><?//=$item['status']?>
<!--            </p>-->
        </div>

    <hr/>
<?endforeach;?>
<script src="/js/order.js">

</script>
