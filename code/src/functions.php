<?php
use Ak\Hw\Services\Magic;
/**
 * Prints a variable's contents in a readable format, optionally terminating script execution.
 *
 * @param mixed $o The variable to print.
 * @param bool $die If true, the script will terminate after printing. Defaults to false.
 * @param bool $fullBackTrace If true, a full backtrace will be displayed. Defaults to false.
 * @return void
 */
function pr(mixed $o, bool $fullBackTrace = false, bool $die = false): void
{
    $bt = debug_backtrace();

    $firstBt = $bt[0];
    $dRoot = $_SERVER["DOCUMENT_ROOT"];
    $dRoot = str_replace("/", "\\", $dRoot);
    $firstBt["file"] = str_replace($dRoot, "", $firstBt["file"]);
    $dRoot = str_replace("\\", "/", $dRoot);
    $firstBt["file"] = str_replace($dRoot, "", $firstBt["file"]);
    ?>
    <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
        <div style='padding:3px 5px; background:#99CCFF;'>
            <?php if (!$fullBackTrace): ?>
                File:
                <b><?php echo htmlspecialchars($firstBt["file"]); ?></b> [line: <?php echo htmlspecialchars($firstBt["line"]); ?>]
            <?php else: ?>
                <?php foreach ($bt as $value): ?>
                    <?php
                    $value["file"] = isset($value["file"]) ? str_replace([str_replace("/", "\\", $dRoot), $dRoot], "", $value["file"]) : '[internal function]';
                    ?>
                    File:
                    <b><?php echo htmlspecialchars($value["file"]); ?></b> [line: <?php echo isset($value["line"]) ? htmlspecialchars($value["line"]) : 'N/A'; ?>] <?php echo (isset($value['class']) ? htmlspecialchars($value['class']) . '->' : '') . (isset($value['function']) ? htmlspecialchars($value['function']) . '()' : ''); ?>
                    <br>
                <?php endforeach ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <pre style='padding:10px;'><?php print_r($o); ?></pre>
    </div>
    <div style='padding:3px 5px; background:#a7a699;'>
        <?php display_memory_info(); ?>
    </div>
    <?php
        if ($die) {
        die();
    }
}
function display_memory_info(){
    $total = memory_get_usage(true);
    $used = memory_get_usage();
    $ratio = (int)($used * 64 / $total);

    $usedAsSring = ' '.number_format($used).' ';
    $totalAsString = ' '.number_format($total).' ';

    echo PHP_EOL;
    echo " Memory usage: ".str_repeat('=', 51).'-|'.PHP_EOL;
    echo '|| ';
    for($i = 0;$i < 64; $i++) {
        echo $i < $ratio ? 'x' : '';
    }
    echo ' ';
    echo '||'.PHP_EOL;
    echo ' ';
    echo $usedAsSring.str_repeat('=', 33 - mb_strlen($usedAsSring));
    echo str_repeat('=', 33 - mb_strlen($totalAsString)).$totalAsString;
    echo '_'.PHP_EOL;
    echo PHP_EOL;
}





?>