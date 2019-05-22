
<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
<!--            --><?//var_dump($product->img)?>
            <?foreach ($product->img as $img):?>
                <img src="<?=IMG_BIG . $img?>" class="d-block w-100" alt="...">
            <?endforeach;?>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
<div class="card-catalog">
    <h2 class="card-title">
        <?=$product->name_product?>
    </h2>
    <p class="card-text">
        <?=$product->description?>
    </p>
</div>


