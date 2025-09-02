# Домашнее задание № 5 Приложение для верификации Email 



https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus


# Описание 

Класс EmailValidator предназначен для проверки корректности email-адресов (с проверкой MX записей домена, но без отправки подтверждающего письма).

## Пример использования

Для проверки единичного email адреса:

```php
$validator = new EmailValidator();
$result = $validator->verifyEmail('test@example.com');

if ($result->isValid()) {
    echo "Email {$result->getInputValue()} корректен.";
} else {
    echo "Email {$result->getInputValue()} некорректен: " . $result->getError();
}
```

Также можно использовать для проверки массива писем:

```php

// Проверка массива email-адресов
$emails = [
    'help@otus.ru', //Valid email 
    'test@example.com', //Valid email 
    'test', //Invalid Email - Invalid email format
    'test@..gmail.com', //Invalid Email - Invalid email format
    'invalid.email', //Invalid Email - Invalid email format
    'no-mx@nomxdomain.org' // Invalid Email - No MX records found for email domain
];
//Передача второго параметра в значении true, приведет к выводу html разметки (поведение по умолчанию)
echo $validationResults = $validator->verifyEmails($emailsToCheck, true);
```