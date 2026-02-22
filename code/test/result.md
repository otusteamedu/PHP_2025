PHPUnit 11.5.53 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.4.17
Configuration: /data/webserver/phpunit.xml

.....................................                             37 / 37 (100%)

Time: 00:08.280, Memory: 10.00 MB

Order Controller (Ak\Hw\Test\Integration\OrderController)
 ✔ Process order with invalid data renders form with errors
 ✔ Process order with valid data calls repository
 ✔ Process order handles repository exception

Order System (Ak\Hw\Test\System\OrderSystem)
 ✔ Get request shows form
 ✔ Post with valid data shows success
 ✔ Post with invalid data shows errors

Order Validate (Ak\Hw\Test\OrderValidate)
 ✔ Validation with valid data
 ✔ Invalid card number with data set "empty"
 ✔ Invalid card number with data set "null"
 ✔ Invalid card number with data set "not a string"
 ✔ Invalid card number with data set "too short"
 ✔ Invalid card number with data set "with letters"
 ✔ Invalid card number with data set "wrong separator"
 ✔ Invalid card holder with data set "empty"
 ✔ Invalid card holder with data set "null"
 ✔ Invalid card holder with data set "with numbers"
 ✔ Invalid card holder with data set "with special chars"
 ✔ Invalid card holder with data set "too many spaces"
 ✔ Invalid expiration with data set "empty"
 ✔ Invalid expiration with data set "null"
 ✔ Invalid expiration with data set "invalid format"
 ✔ Invalid expiration with data set "month 00"
 ✔ Invalid expiration with data set "month 13"
 ✔ Invalid expiration with data set "expired card"
 ✔ Invalid cvv with data set "empty"
 ✔ Invalid cvv with data set "null"
 ✔ Invalid cvv with data set "too short"
 ✔ Invalid cvv with data set "too long"
 ✔ Invalid cvv with data set "with letters"
 ✔ Invalid sum with data set "empty"
 ✔ Invalid sum with data set "null"
 ✔ Invalid sum with data set "not a number"
 ✔ Invalid sum with data set "zero"
 ✔ Invalid sum with data set "negative"
 ✔ Invalid order number with data set "empty"
 ✔ Invalid order number with data set "null"
 ✔ Invalid order number with data set "only spaces"

OK (37 tests, 54 assertions)
