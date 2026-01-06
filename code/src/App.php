<?php

namespace App;

use App\Services\EmailsChecker;

class App {

	public function run(){
		if (!empty($_POST['emails'])){
			$emails = json_decode($_POST['emails']);
			$checkedEmails = (new EmailsChecker())->getEmailsInfo($emails);
			return json_encode($checkedEmails, JSON_UNESCAPED_UNICODE);
		}
	}
}
