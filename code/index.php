<?php
require __DIR__ . '/vendor/autoload.php';

use Ak14\Exchange\Exchange;

$nbrb = new Exchange();

if($currencies = new Exchange()->currencies())
{
    echo "<table>";
    echo "<tr><th>Abbreviation</th><th>Name</th></tr>";
    foreach ($currencies as $currency) {
        echo "<tr>";
        echo "<td>".$currency['Cur_Abbreviation']."</td>";
        echo "<td>".$currency['Cur_Name']."</td>";
        echo "</tr>";
    }
    echo "</table>";
};