<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Application\UseCase;

use MkdBot\Application\UseCase\GetContacts;
use MkdBot\Domain\Entity\Contact;
use MkdBot\Domain\Enum\ContactType;
use MkdBot\Domain\Interface\ContactRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * Юнит-тесты для GetContacts
 *
 * Тестирует форматирование контактов УК / совета дома,
 * обработку пустого результата и обрезку текста при превышении 4000 символов
 */
class GetContactsTest extends TestCase
{
    private GetContacts $useCase;
    private $contactRepo;

    protected function setUp(): void
    {
        $this->contactRepo = $this->createMock(ContactRepositoryInterface::class);
        $this->useCase = new GetContacts($this->contactRepo);
    }

    /**
     * Контакты УК — форматирование шаблона
     */
    public function testReturnsUkContacts(): void
    {
        $contact = new Contact(type: ContactType::Uk, name: 'ООО «ЖилСервис»', phone: '+7 (123) 456-78-90', email: 'info@zhilservice.ru', description: 'Обслуживание дома');
        $this->contactRepo->method('findByType')->with(ContactType::Uk)->willReturn([$contact]);

        $result = $this->useCase->execute('uk');

        $this->assertStringContainsString('ООО «ЖилСервис»', $result);
        $this->assertStringContainsString('🏢', $result);
    }

    /**
     * Контакты УК — форматирование содержит название, роль, телефон, email, описание
     */
    public function testUkContactsFormattingContainsAllFields(): void
    {
        $contact = new Contact(type: ContactType::Uk, name: 'ООО «ЖилСервис»', role: 'Управляющая компания', phone: '+7 (123) 456-78-90', email: 'info@zhilservice.ru', description: 'Обслуживание дома');
        $this->contactRepo->method('findByType')->with(ContactType::Uk)->willReturn([$contact]);

        $result = $this->useCase->execute('uk');

        // Проверяем что форматирование УК содержит все поля
        $this->assertStringContainsString('🏢 ООО «ЖилСервис» — Управляющая компания', $result);
        $this->assertStringContainsString('Телефон: +7 (123) 456-78-90', $result);
        $this->assertStringContainsString('Email: info@zhilservice.ru', $result);
        $this->assertStringContainsString('Описание: Обслуживание дома', $result);
    }

    /**
     * Контакты совета дома — форматирование шаблона
     */
    public function testReturnsCouncilContacts(): void
    {
        $contact = new Contact(type: ContactType::Council, name: 'Иванов И.И.', role: 'Председатель', phone: '+7 (234) 567-89-01', email: 'ivanov@example.com', description: 'Организация работы');
        $this->contactRepo->method('findByType')->with(ContactType::Council)->willReturn([$contact]);

        $result = $this->useCase->execute('council');

        $this->assertStringContainsString('Иванов И.И.', $result);
        $this->assertStringContainsString('Председатель', $result);
    }

    /**
     * Контакты совета дома — форматирование содержит имя, роль, телефон, email, описание
     */
    public function testCouncilContactsFormattingContainsAllFields(): void
    {
        $contact = new Contact(type: ContactType::Council, name: 'Иванов И.И.', role: 'Председатель', phone: '+7 (234) 567-89-01', email: 'ivanov@example.com', description: 'Организация работы');
        $this->contactRepo->method('findByType')->with(ContactType::Council)->willReturn([$contact]);

        $result = $this->useCase->execute('council');

        // Проверяем форматирование совета дома
        $this->assertStringContainsString('👤 Иванов И.И. — Председатель', $result);
        $this->assertStringContainsString('Телефон: +7 (234) 567-89-01', $result);
        $this->assertStringContainsString('Email: ivanov@example.com', $result);
        $this->assertStringContainsString('Описание: Организация работы', $result);
    }

    /**
     * Контакты совета дома — без роли форматирование содержит только имя
     */
    public function testCouncilContactsWithoutRoleFormatsNameOnly(): void
    {
        $contact = new Contact(type: ContactType::Council, name: 'Петров П.П.', role: '', phone: '+7 (111) 222-33-44', email: 'petrov@example.com', description: '');
        $this->contactRepo->method('findByType')->with(ContactType::Council)->willReturn([$contact]);

        $result = $this->useCase->execute('council');

        // Без роли — только имя с иконкой
        $this->assertStringContainsString('👤 Петров П.П.', $result);
        $this->assertStringNotContainsString('—', $result);
    }

    /**
     * Пустой результат для УК — корректная обработка
     */
    public function testReturnsNotFoundWhenEmpty(): void
    {
        $this->contactRepo->method('findByType')->with(ContactType::Uk)->willReturn([]);

        $result = $this->useCase->execute('uk');

        $this->assertStringContainsString('не найдены', $result);
    }

    /**
     * Пустой результат для УК — сообщение о УК
     */
    public function testEmptyUkResultReturnsUkNotFoundMessage(): void
    {
        $this->contactRepo->method('findByType')->with(ContactType::Uk)->willReturn([]);

        $result = $this->useCase->execute('uk');

        $this->assertStringContainsString('🏢', $result);
        $this->assertStringContainsString('управляющей компании', $result);
    }

    /**
     * Пустой результат для совета дома — сообщение о совете дома
     */
    public function testEmptyCouncilResultReturnsCouncilNotFoundMessage(): void
    {
        $this->contactRepo->method('findByType')->with(ContactType::Council)->willReturn([]);

        $result = $this->useCase->execute('council');

        $this->assertStringContainsString('🏠', $result);
        $this->assertStringContainsString('совета дома', $result);
    }

    /**
     * Обрезка текста при превышении 4000 символов
     */
    public function testTruncatesLongText(): void
    {
        // Генерируем достаточно длинный текст, чтобы после форматирования превысил 4000 символов
        $longName = str_repeat('А', 2000);
        $longDescription = str_repeat('Б', 2000);
        $contact = new Contact(type: ContactType::Uk, name: $longName, phone: '+7', email: 'a@b.c', description: $longDescription);
        $this->contactRepo->method('findByType')->willReturn([$contact]);

        $result = $this->useCase->execute('uk');

        // После форматирования текст > 4000, поэтому обрезается с пометкой
        $this->assertStringContainsString('...текст сокращён', $result);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
    }

    /**
     * Обрезка текста — длина результата ровно 4000 с учётом суффикса
     */
    public function testTruncatedTextLengthIsExactly4000WithSuffix(): void
    {
        $longName = str_repeat('А', 2000);
        $longDescription = str_repeat('Б', 2000);
        $contact = new Contact(type: ContactType::Uk, name: $longName, phone: '+7', email: 'a@b.c', description: $longDescription);
        $this->contactRepo->method('findByType')->willReturn([$contact]);

        $result = $this->useCase->execute('uk');

        // Результат содержит суффикс и не превышает 4000
        $this->assertStringEndsWith('...текст сокращён', $result);
        $this->assertSame(4000, mb_strlen($result));
    }

    /**
     * Несколько контактов разделяются разделителем
     */
    public function testMultipleContactsAreSeparatedByDivider(): void
    {
        $contact1 = new Contact(type: ContactType::Uk, name: 'УК 1', phone: '+7 (111)', email: 'uk1@test.ru', description: 'Первая УК');
        $contact2 = new Contact(type: ContactType::Uk, name: 'УК 2', phone: '+7 (222)', email: 'uk2@test.ru', description: 'Вторая УК');
        $this->contactRepo->method('findByType')->with(ContactType::Uk)->willReturn([$contact1, $contact2]);

        $result = $this->useCase->execute('uk');

        // Между контактами должен быть разделитель
        $this->assertStringContainsString('УК 1', $result);
        $this->assertStringContainsString('УК 2', $result);
        $this->assertStringContainsString('---', $result);
    }

    /**
     * Текст менее 4000 символов не обрезается
     */
    public function testShortTextIsNotTruncated(): void
    {
        $contact = new Contact(type: ContactType::Uk, name: 'Короткая УК', phone: '+7', email: 'a@b.c', description: 'Короткое описание');
        $this->contactRepo->method('findByType')->willReturn([$contact]);

        $result = $this->useCase->execute('uk');

        // Короткий текст не должен содержать суффикс обрезки
        $this->assertStringNotContainsString('...текст сокращён', $result);
    }
}
