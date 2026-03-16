<?php

/**
 * @var array $eventsList;
*/
?>

<style>
    .content {
        display: flex;
        flex-wrap: wrap;
        margin: 0 auto;
        max-width: 800px;
        gap: 20px;
    }

    .content .event {
        flex-basis: 100%;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .content .event > * {
        flex-basis: 100%;
    }
</style>

<div class="content">

    <?php foreach ($eventsList as $event) :?>

    <div class="event">
        <div class="event_priority">
            <div class="event_priority">Приоритет события: <?= $event['priority']?></div>
        </div>

        <div class="event_info">
            <?php foreach ($event['event'] as $key => $condition):?>
                <div class="event"><?= $key ?>: <?= $condition ?></div>
            <?php endforeach;?>

        </div>

        <div class="event_conditions">

            <?php foreach ($event['conditions'] as $key => $condition):?>
            <div class="condition"><?= $key ?>: <?= $condition ?></div>
            <?php endforeach;?>

        </div>

    </div>


    <?php endforeach;?>

</div>
