<?php

namespace App\Services;

class EmailsChecker
{

	const MAX_EMAIL_LENGTH = 254;
	const NUMBER_OF_PARTS_WITH_DOG_BETWEEN = 2;
	const MAX_LENGTH_LOCAL_PART_OF_EMAIL = 64;
	const MAX_LENGTH_DOMAIN_PART_OF_EMAIL = 252;
	const MIN_NUMBER_OF_PARTS_WITH_DOT_BETWEEN = 2;
	const MAX_LENGTH_EACH_DOMAIN_PART = 63;

	public function getEmailsInfo(array $emails){
		
		$checkedEmails = [];
		foreach ($emails as $email) {
			$checkEmail = $this->checkEmail($email);
			$checkedEmails[$email] = $checkEmail;

		}
		return 	$checkedEmails;		
	}	

	public function checkEmail($email) {

	    if (strlen($email) > self::MAX_EMAIL_LENGTH) {
	        return [
	        	'valid' => false,
	        	'error' => 'Слишком длинный емейл (более 254 символа)'
	        ];
	    }
	    if (strpos($email, '.') === 0) {
	        return [
	        	'valid' => false,
	        	'error' => 'Емейл не должен начинаться с точки'
	        ];
	    }

	    $parts = explode('@', $email);
	    if (count($parts) !== self::NUMBER_OF_PARTS_WITH_DOG_BETWEEN) {
	        return [
	        	'valid' => false,
	        	'error' => 'Некорректное количество @ (отсутствует или более 2х'
	        ];
	    }
	    if (strlen($parts[0]) > self::MAX_LENGTH_LOCAL_PART_OF_EMAIL) {
	    	return [
	        	'valid' => false,
	        	'error' => 'Часть до @ должна быть короче 64 символов'
	        ];
	    }
	    if (strlen($parts[1]) > self::MAX_LENGTH_DOMAIN_PART_OF_EMAIL) {
	    	return [
	        	'valid' => false,
	        	'error' => 'Часть после @ должна быть короче 252 символов'
	        ];
	    }
	    if (!preg_match('/^[a-zа-яё0-9!#$%&\'*+\/=?^_`{|}~][a-zа-яё0-9.!#$%&\'*+\/=?^_`{|}~-]*$/ui', $parts[0])) {
	        return [
	        	'valid' => false,
	        	'error' => 'Недопустимые символы в части до @'
	        ];
	    }
	    if (substr($parts[0], -1) === '.') {
	        return [
	        	'valid' => false,
	        	'error' => 'Часть до @ не должна оканчиваться точкой (.)'
	        ];
	    }
	    
	    $lowerCaseDomain = mb_strtolower($parts[1], 'UTF-8');
	    $cyrillic = ['рф', 'рус'];
	  
	    $domainParts = explode('.', $lowerCaseDomain);
	    if (count($domainParts) < self::MIN_NUMBER_OF_PARTS_WITH_DOT_BETWEEN) {
	        return [
	        	'valid' => false,
	        	'error' => 'Часть после @ должна содержать точку (.)'
	        ];
	    }

	    $tld = $domainParts[count($domainParts)-1];
	    if (in_array($tld, $cyrillic, true)) {
	        foreach ($domainParts as $part) {
	            if (!preg_match('/^[a-zа-яё0-9-]+$/ui', $part) || 
	                strpos($part, '-') === 0 || 
	                substr($part, -1) === '-' ||
	                strlen($part) > self::MAX_LENGTH_EACH_DOMAIN_PART) {
			        return [
			        	'valid' => false,
			        	'error' => 'Недопустимые символы после @ или формат ( может содержать буквы, цифры и девисы (но не в начале и не в конце) , быть корточе 63 символов'
			        ];
	            }
	        }
	    } else {
	        if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $lowerCaseDomain)) {
	        	return [
	        		'valid' => false,
	        		'error' => 'Некорректное написание части после @'
	        	];
	        }
	    }
	    
	    if (function_exists('idn_to_ascii')) {
	        $domainAscii = idn_to_ascii($lowerCaseDomain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
	    }

	    if (!checkdnsrr($domainAscii, 'MX')) {
	          	return [
	        		'valid' => false,
	        		'error' => 'У получателя не настроены МХ записи'
	        	];
	    }

	    if (!checkdnsrr($domainAscii, 'A')) {
	    		return [
	        		'valid' => false,
	        		'error' => 'Домен не зарегистрирован или неправильно настроен'
	        	];
	    }
	    
	    return [
	    	'valid' => true
	    ];
	}	

}