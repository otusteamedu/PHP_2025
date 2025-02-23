<? 

require_once "vendor/autoload.php";

$renderHtml = new \MyTestApp\RenderHtml();

if(isset($_POST["email_list"])) {
    
    $email_list = explode(PHP_EOL, $_POST["email_list"]);

    $renderHtml->renderHtml("<h2>Обычная проверка</h2>");

    foreach ($email_list as $email) 
        if(\MyTestApp\EmailValidation::isValidEmail($email))
            $renderHtml->renderHtml("<p>$email верный</p>");
        else 
        $renderHtml->renderHtml("<p>$email ошибочный</p>");
            echo "";

    $renderHtml->renderHtml("<h2>Проверка через DNS</h2>");

    foreach ($email_list as $email) 
        if(\MyTestApp\EmailValidation::isValidEmailThrowDNS($email))
            $renderHtml->renderHtml("<p>$email верный</p>");
        else 
        $renderHtml->renderHtml("<p>$email ошибочный</p>");
            echo "";
    
        
}

$email_list = $_POST["email_list"] ?? 'usermail1@ya.ru'.PHP_EOL.'usermail1@yandex.ru2'.PHP_EOL.'usermail1';

$renderHtml->renderHtml("<hr/>");
$renderHtml->renderHtml("
<form method='post'>
    <textarea name='email_list' style='width:300px; height:200px;'>{$email_list}</textarea>
    <p><input type='submit' value='Проверить'/></p>
</form>
");

echo $renderHtml->html;




 