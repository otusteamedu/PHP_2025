<? 

namespace MyTestApp;

Class DataDispatcher {

    public function __construct(RenderHtml $renderHtml) {

        if(isset($_POST["email_list"])) {
    
            $email_list = explode(PHP_EOL, $_POST["email_list"]);
        
            $renderHtml->renderHtml("<h2>Обычная проверка</h2>");
        
            foreach ($email_list as $email) 
                if(\MyTestApp\EmailValidation::isValidEmail($email))
                    $renderHtml->renderHtml("<p>$email верный</p>");
                else 
                    $renderHtml->renderHtml("<p>$email ошибочный</p>");
        
            $renderHtml->renderHtml("<h2>Проверка через DNS</h2>");
        
            foreach ($email_list as $email) 
                if(\MyTestApp\EmailValidation::isValidEmailThrowDNS($email))
                    $renderHtml->renderHtml("<p>$email верный</p>");
                else 
                    $renderHtml->renderHtml("<p>$email ошибочный</p>");
            
        }

    }

}