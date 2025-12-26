<?php

namespace App;

use App\Services\ValidEmailsFilter;

class App {

	public function run(){

		$emails = [
		    "иван@ПОЧТА.РУС",
		    "IVAN@ПОЧТА.РУС",
		    "test@MAIL.RU",
		    "иван@почта.рф",
		    "IVAN@ПОЧТА.РФ",
		    "test@google.COM",
		    ".ivan@google.com",
		    "ivan@.рус",
		    "ivan@yandex.ru",
		    "i van@ya.ru"
		];
		
		$validEmail = (new ValidEmailsFilter())->getValidEmails($emails);
	}
}
