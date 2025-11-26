php <?php

require __DIR__ . '/vendor/autoload.php';

$math = new \MaksimSkoropispcev\Math\Application\Math();
$result = $math->exponentiation(2, 6);

echo $result;