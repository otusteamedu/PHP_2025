
<?php if(isset($product)): ?>
    <div class="col-md-8 col-md-offset-1">
    <h4 class="text-info">Заказаный товар</h4>
    <table class="table-condensed">
    <tr>
        <th class=" col-md-4"> Товар </th>
        <th class=" col-md-2"> Цена </th>
        <th class=" col-md-2"> Гарантия(мес) </th>
        <th class=" col-md-2"> количество </th>
    </tr>

    <?php foreach($product as $item): ?>
        <tr>
            <td class=""> <?php echo $item['tovar']?> </td>
            <td class=""> <?php echo $item['cost']?> </td>
            <td class=""> <?php echo $item['warranty_period']?> </td>
            <td class=""> <?php echo $item['quantity']?> </td>
        <tr>
    <?php endforeach ?>
    </table>
        <hr>
    </div>
<?php endif ?>


<?php if(isset($customer)): ?>
    <br>
    <div class="col-md-4 col-md-offset-1">
        <h4 class="text-info">Заказчик</h4>
        <form action="" method="post" class="form-horizontal" id="customer" >
            <input type="hidden" id="c_id" value="<? echo $customer['id']?>">
            <div class="form-group">
                <label for="c_name"> Имя </label>
                <input type="text" class="form-control" id="c_name" name="c_name" value="<? echo $customer['name']?>">
            </div>
            <div class="form-group">
                <label for="c_phone"> Телефон</label>
                <input type="text" class="form-control" id="c_phone" name="c_phone" value="<? echo $customer['phone']?>">
            </div>
        <button class="btn btn-sm btn-default" > Сохранить</button>
    </form>
    </div>
<?php endif ?>

<?php if(isset($delivery)): ?>
    <div class="col-md-5 col-md-offset-1">
        <h4 class="text-info">Информация по доставке</h4>
        <form action="" method="post" class="form-horizontal" id="delivery">
            <input type="hidden" id="d_id" value="<? echo $delivery['id']?>">
            <div class="form-group">
                <label for="d_name"> Имя </label>
                <input type="text" class="form-control" id="d_name" value="<? echo $delivery['name']?>">
            </div>

            <div class="form-group">
                <label for="d_phone"> Телефон </label>
                <input type="text" class="form-control" id="d_phone" value="<? echo $delivery['phone']?>">
            </div>

            <div class="form-group">
                <label for="d_address"> Адрес доставки </label>
                <input type="text" class="form-control" id="d_address" value="<? echo $delivery['address']?>">
            </div>

            <div class="form-group">
                <label for="d_notes"> Заметки </label>
                <textarea  class="form-control" id="d_notes" rows="4" > <? echo $delivery['notes']?> </textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-default"> Сохранить</button>
            </div>
    </div>
<?php endif ?>

<?php  ?>

<script>
    $("#delivery").submit(function(event) {

        event.preventDefault();
        var table = 'delivery';
        var id = document.getElementById("d_id").value;
        var name = document.getElementById("d_name").value;
        var phone = document.getElementById("d_phone").value;
        var address = document.getElementById("d_address").value;
        var notes = document.getElementById("d_notes").value;
    $.post("http://new/admin/set/"+table + "/" +id,
        {
            name: name,
            phone: phone,
            address: address,
            notes: notes
        });
    });

    $("#customer").submit(function(event){

        event.preventDefault();
        var table = 'customer';
        var id = document.getElementById('c_id').value;
        var name = document.getElementById('c_name').value;
        var phone = document.getElementById('c_phone').value;

        $.post("http://new/admin/set/"+table + "/" +id,
            {
                name: name,
                phone: phone,
            });
    });

</script>
