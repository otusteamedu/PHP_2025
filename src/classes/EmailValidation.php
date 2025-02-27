<? 

namespace MyTestApp;

Class EmailValidation {

    public static function isValidEmail($email) {

        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
        
    }

    public static function isValidEmailThrowDNS($email) {
        
        if(!self::isValidEmail($email))
            return false;
    
        list(, $domain) = explode('@', trim($email));
    
        if (checkdnsrr($domain, 'MX'))
            return true; 
        
        return false;

    }

}