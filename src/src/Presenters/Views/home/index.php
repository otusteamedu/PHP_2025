<?php
?>

<style>
    form {
        display: flex;
        max-width: 600px;

        flex-wrap: wrap;
        margin: 0 auto;
        gap: 20px;
    }

    form > * {
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
</style>

<form action="/add_event" method="post" target="_blank">

    <h1>Добавьте событие</h1>

    <div class="field">
        <label for="priority">Приоритет</label>
        <input type="number" id="priority" name="priority" placeholder="Укажите приоритет числом">
    </div>

    <div class="field">
        <label for="conditions">Укажите критерии JSON строкой: {"param":1, "param2":2}</label>
        <textarea name="conditions" id="conditions" cols="30" rows="10"></textarea>

    </div>

    <div class="field">
        <label for="event">Данные события JSON: {"type":"E1","description":"Событие тестовое"}</label>
        <textarea name="event" id="event" cols="30" rows="5"></textarea>
    </div>

    <div class="field">
        <input type="submit" value="Сохранить">
    </div>

</form>
