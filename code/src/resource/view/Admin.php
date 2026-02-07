<div class="container">
<?php if(isset($data)):?>
<div class="col-md-10 col-md-offset-1">
        <table class="table-bordered">
            <tr>
                <th class="col-sm-1"> № </th>
                <th class="col-sm-2"> дата</th>
                <th class="col-sm-2"> клиент</th>
                <th class="col-sm-2"> телефон</th>
                <th class="col-sm-2"> адрес доставки</th>
                <th class="col-sm-2"> товар</th>
                <th class="col-sm-1"> количество</th>
            </tr>
<?php foreach($data as $val):?>
                <tr class="">
                    <td class="col-sm-1"> <? echo $val["№"]?></td>
                    <td class="col-sm-2" id="date"> <? echo $val['date_booking'] ?> </td>
                    <td class="col-sm-2" id="customer"> <? echo $val['customer'] ?>  </td>
                    <td class="col-sm-2" id="phone"> <? echo $val['phone'] ?> </td>
                    <td class="col-sm-2" id="adress"> <? echo $val['adress'] ?> </td>
                    <td class="col-sm-2" id="tovar"> <? echo $val['tovar']?> </td>
                    <td class="col-sm-1" id="quantity"> <? echo $val['quantity']?> </td>
                    <td class="col-sm-2">
                        <form action="" method="post" id="<? echo $val['№']?>">
                            <input type="hidden" name="product" value="<? echo $val['p_id'] ?>" >
                            <button type="submit" formaction="http://new/admin/update/<? echo $val['№'] ?>" class="btn-sm btn-info btn-sm">Редактировать</button>
                            <button type="submit" formaction="http://new/admin/delete/<? echo $val['№'] ?>" class="btn-sm bg-danger" > Удалить</button>
                        </form>
                    </td>

                </tr>
<?php endforeach; ?>
        </table>
    </div>
<? endif ?>
</div>

