<?php
function mkFileButtons($arFiles) {
    $sBtn = '';
    foreach ($arFiles as $file) {
        if ($file != basename($_SERVER['SCRIPT_NAME'])) {
        $sBtnName = substr($file, 0, strpos($file, "."));
        $sBtn .= "<a href='$file' class='file-btn'>$sBtnName</a>";
        }
    }
    return $sBtn;
}

$arFiles = glob('*.php');

?>

<style>

div {
    padding: 10px;
    text-align: center;
}
.file-btn {
    padding: 10px 10px;
    margin: 5px;
    background: #04b1f5ff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}
.file-btn:hover {
    background: #08bac0ff;
}
</style>

<div>
    <h2>PHP версии <?php echo phpversion(); ?> работает </h2>
</div>

<div>
    <?php echo mkFileButtons($arFiles); ?>
</div>
<div>Контейнер <?php echo $_SERVER['HOSTNAME']?></div>