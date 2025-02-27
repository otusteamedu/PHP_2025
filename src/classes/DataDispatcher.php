<? 

namespace MyTestApp;

Class DataDispatcher {

    public $data = "";

    public function __construct(iRenderData $renderData) {

        if(isset($_POST["email_list"])) {
    
            $email_list = explode(PHP_EOL, $_POST["email_list"]);

            $renderData->renderData("<h2>Обычная проверка</h2>");
        
            foreach ($email_list as $email) 
                if(\MyTestApp\EmailValidation::isValidEmail($email))
                    $renderData->renderData("<p>$email верный</p>");
                else 
                    $renderData->renderData("<p>$email ошибочный</p>");
        
            $renderData->renderData("<h2>Проверка через DNS</h2>");
        
            foreach ($email_list as $email) 
                if(\MyTestApp\EmailValidation::isValidEmailThrowDNS($email))
                    $renderData->renderData("<p>$email верный</p>");
                else 
                    $renderData->renderData("<p>$email ошибочный</p>");
            
        }

        $renderData->renderData("<hr/>");
        $renderData->renderData("
        <form method='post'>
            <textarea name='email_list' style='width:300px; height:200px;' placeholder='Список эл. почты по строкам' ></textarea>
            <p><input type='submit' value='Проверить'/></p>
        </form>
        ");

        $this->data = $renderData->data;

    }

}