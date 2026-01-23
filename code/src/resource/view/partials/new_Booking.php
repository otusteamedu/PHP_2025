<div class="container">
<div class="container">
        <br>
        <br>
<div class="row">
    <?php if(isset($data)): ?>

        <div class="col-md-8 col-md-offset-1">
            <h4 class="text-info">Заказаный товар</h4>
            <table class="table-condensed">
                <tr>
                    <th class=" col-md-4"> Товар </th>
                    <th class=" col-md-2"> Цена </th>
                    <th class=" col-md-2"> Гарантия(мес) </th>
                </tr>

                <?php foreach($data as $item): ?>
                <tr>
                    <td class=""> <?php echo $item['tovar']?> </td>
                    <td class=""> <?php echo $item['cost']?> </td>
                    <td class=""> <?php echo $item['warranty']?> </td>
                <tr>
                    <?php endforeach ?>
            </table>
            <hr>
        </div>
    <?php endif ?>
</div>

    <?php if(isset($_SESSION['tovar'])): ?>
    <form action="" class="form-horizontal col-md-8 col-md-offset-2"  method="post">
        <div class="col-md-6">
            <label for="name" class="control-label text-info"> Имя </label>
            <input type="text" class="form-control" id="name" name="name" value="<? echo $input['name']?>">
        </div>
        <div class="col-md-6">
            <label for="s_name" class="control-label text-info"> Фамилия </label>
            <input type="text" class="form-control" id="s_name" name="s_name" value="<? echo $input['s_name']?>">
        </div>
        <div class="col-md-6">
            <label for="phone_1" class="control-label text-info"> Телефон 1 </label>
            <input type="text" class="form-control" id="phone_1" name="phone_1" value="<? echo $input['phone_1']?>">
        </div>
        <div class="col-md-6">
            <label for="phone_2" class="control-label text-info"> Телефон 2 </label>
            <input type="text" class=" form-control" id="phone_2" name="phone_2" value="<? echo $input['phone_2']?>">
        </div>
        <div class="col-md-8">
            <label for="address" class="control-label text-info"> Адрес доставки </label>
            <input type="text" class="form-control" id="address" name="address" value="<? echo $input['address']?>">
        </div>
        <div class="col-md-4">
            <label for="address" class="control-label text-info"> Время  </label>
            <input type="datetime" class="form-control" id="date" name="date"
                   value="<? if(isset($item['date'])) echo $item['date'];
                   else     echo strftime('%d-%m-%Y %H:%M') ?>">
        </div>

        <div class="col-md-12">
            <label for="notes" class="control-label text-info"> Заметки </label>
            <textarea  class="form-control" id="notes" name="notes"> <? echo $input['notes']?> </textarea>
        </div>

        <div class="btn-group col-md-6">
            <button type="submit" formaction="http://new/busket/set/" class="btn btn-info"> Сохранить </button>
        </div>
    </form>
    <?php else :?>
        <div class="page-header">
            <h3 class="text-danger text-center"> У вас нет товара в корзине </h3>
        </div>
    <?php endif ?>
</div>
</div>
<br>


