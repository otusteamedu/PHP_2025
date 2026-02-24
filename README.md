# Unit-тесты (HW#17)
```
> phpunit --coverage-text
PHPUnit 11.5.55 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.3.6 with Xdebug 3.2.0
Configuration: /otus/PHP_2025/phpunit.xml

.......................................                           39 / 39 (100%)

Time: 00:00.176, Memory: 10.00 MB

OK (39 tests, 91 assertions)


Code Coverage Report:
  2026-02-24 14:47:57

 Summary:
  Classes: 73.33% (11/15)
  Methods: 75.00% (18/24)
  Lines:   75.45% (83/110)

EmailsVerifier\Application\DTO\VerificationResultDTO
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  1/  1)
EmailsVerifier\Application\Services\CompositeValidator
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  6/  6)
EmailsVerifier\Application\UseCases\VerifyEmailsUseCase
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  8/  8)
EmailsVerifier\Domain\Email
  Methods:  66.67% ( 2/ 3)   Lines:  66.67% (  2/  3)
EmailsVerifier\Domain\ValidationError
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  2/  2)
EmailsVerifier\Domain\Validators\BaseValidator
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  1/  1)
EmailsVerifier\Domain\Validators\FormatValidator
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% ( 16/ 16)
EmailsVerifier\Domain\Validators\MxValidator
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% ( 15/ 15)
EmailsVerifier\Infrastructure\MxChecker
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% (  1/  1)
EmailsVerifier\Infrastructure\ValidationStrategyFactory
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% ( 14/ 14)
EmailsVerifier\Presentation\Controllers\EmailVerificationController
  Methods: 100.00% ( 2/ 2)   Lines: 100.00% (  3/  3)
EmailsVerifier\Presentation\Services\VerificationResultProcessor
  Methods: 100.00% ( 1/ 1)   Lines: 100.00% ( 14/ 14)
```

В папке `coverage` - подробная информация о текущем покрытии.

В изначальной задаче сервиса проверки e-mail-адресов не было указано явно создание http-сервиса, поэтому в исходном коде присутствует только консольная проверка (больше для тестирования). На практике применение консольного варианта маловероятно, потому из тестов исключено форматирование и прочее, что касается консольного вывода. Тестируется только код самого сервиса проверки.

## Какие еще тесты из пирамиды тестирования могут тут быть полезными?

- **Интеграционные** - можно было бы использовать при наличии http-сервиса  Codeception и проверить полный цикл как api-сервис.
- **Нагрузочные** - имеет смысл тест производительности при большом количестве e-mail
- **Приемочные** — Codeception при наличии http-сервиса
- **Безопасности** — инъекции, XSS в e-mail
