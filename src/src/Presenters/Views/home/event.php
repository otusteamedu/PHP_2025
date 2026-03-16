<?php

/**
 * @var ?array $event
*/
?>

<style>
    form,
    .event {
        display: flex;
        max-width: 600px;

        flex-wrap: wrap;
        margin: 0 auto;
        gap: 20px;
    }

    form > *,
    .event > *{
        flex-basis: 100%;
    }

    form .field {
        display: flex;
        flex-wrap: wrap;
    }

    form .field > input,
    form .field > textarea {
        flex-basis: 100%;
    }

    h1 {
        text-align: center;
        margin: 0;
    }

    p {
        margin: 0;
    }

    .event {
        margin-bottom: 20px;
    }
</style>

<?php if ($event) :?>
<div class="event">
    <p>Тип: <?= $event['type'] ?></p>
    <p>Описание: <?= $event['description'] ?></p>
</div>
<?php endif;?>

<form action="/get_event" method="post" target="_blank">

    <h1>Получите событие</h1>

    <div class="field">
        <label for="conditions">Укажите критерии JSON строкой: {"param":1, "param2":2}</label>
        <textarea name="conditions" id="conditions" cols="30" rows="10"></textarea>

    </div>

    <div class="field">
        <input type="submit" value="Получить событие">
    </div>

</form>
