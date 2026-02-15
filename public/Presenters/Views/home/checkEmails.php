<?php
/**
 * @var $emails array
*/
?>

<?php foreach ($emails as $email => $check):?>
<p><b><?= $email?>:</b>&nbsp;<?= $check?></p>
<?php endforeach;?>
