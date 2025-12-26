<?php

namespace App\Services;

class ValidEmailsFilter
{

	public function getValidEmails(array $emails){
		
		$validEmails = [];
		foreach ($emails as $email) {
			if ($this->isValidEmail($email)) {
				$validEmails[] = $email;
			}
		}
		return 	$validEmails;		
	}

	public function isValidEmail($email) {

	    if (strlen($email) > 254) {
	        return false;
	    }
	    if (strpos($email, '.') === 0) {
	        return false;
	    }

	    $parts = explode('@', $email);
	    if (count($parts) !== 2) {
	        return false;
	    }
	    if (strlen($parts[0]) > 64) {
	    	return false;
	    }
	    if (strlen($parts[1]) > 252) {
	    	return false;
	    }
	    if (!preg_match('/^[a-zа-яё0-9!#$%&\'*+\/=?^_`{|}~][a-zа-яё0-9.!#$%&\'*+\/=?^_`{|}~-]*$/ui', $parts[0])) {
	        return false;
	    }
	    if (substr($parts[0], -1) === '.') {
	        return false;
	    }
	    
	    $lowerCaseDomain = mb_strtolower($parts[1], 'UTF-8');
	    $cyrillic = ['рф', 'рус'];
	  
	    $domainParts = explode('.', $lowerCaseDomain);
	    if (count($domainParts) < 2) {
	        return false;
	    }

	    $tld = $domainParts[count($domainParts)-1];
	    if (in_array($tld, $cyrillic, true)) {
	        foreach ($domainParts as $part) {
	            if (!preg_match('/^[a-zа-яё0-9-]+$/ui', $part) || 
	                strpos($part, '-') === 0 || 
	                substr($part, -1) === '-' ||
	                strlen($part) > 63) {
	                return false;
	            }
	        }
	    } else {
	        if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $lowerCaseDomain)) {
	            return false;
	        }
	    }
	    
	    if (function_exists('idn_to_ascii')) {
	        $domainAscii = idn_to_ascii($lowerCaseDomain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
	    }

	    if (!checkdnsrr($domainAscii, 'MX')) {
	        return false;
	    }

	    if (!checkdnsrr($domainAscii, 'A')) {
	    	return false;
	    }
	    
	    return true;
	}

}