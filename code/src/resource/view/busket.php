
<section class="container">
    <br>
    <?php foreach($data as $item):?>
        <div class="list-group col-md-4 col-md-offset-1" >
            <a href="http://new/busket/add/<? echo $item['id']?>" class="list-group-item">
                <h4 class="list-group-item-heading"> <?php echo $item['producer'] .' ' . $item['model'] ?> <span class=" label label-warning"><?php echo $item['cost'] ?></span> </h4>
                <p class="list-group-item-text">
                    <?php echo $item['description']?>
                    <br>
                    <i class="text-muted"><?php echo $item['category']?></i>
                </p>
            </a>
        </div>
    <?php endforeach ?>

    <?php if(isset($_SESSION['tovar'])):?>

       <div class="row col-md-10">
           товаров в корзине <span class="label label-default"><?php echo count($_SESSION['tovar']) ?></span><br>
           <a class="btn btn-default" href="http://new/busket/store">Оформить заказ</a>
       </div>
    <? endif ?>

</section>


