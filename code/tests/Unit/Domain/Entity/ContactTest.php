<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Domain\Entity;

use MkdBot\Domain\Entity\Contact;
use MkdBot\Domain\Enum\ContactType;
use PHPUnit\Framework\TestCase;

/**
 * Тесты сущности Contact
 */
class ContactTest extends TestCase
{
    public function testCreateUkContact(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'ООО «ЖилСервис»',
            role: 'Управляющая компания',
            phone: '+7 (123) 456-78-90',
            email: 'info@zhilservice.ru',
            description: 'Обслуживание дома',
        );

        $this->assertEquals(ContactType::Uk, $contact->getType());
        $this->assertEquals('ООО «ЖилСервис»', $contact->getName());
    }

    public function testFormatUk(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'ООО «ЖилСервис»',
            role: 'Управляющая компания',
            phone: '+7 (123) 456-78-90',
            email: 'info@zhilservice.ru',
            description: 'Обслуживание дома',
        );

        $formatted = $contact->formatUk();
        $this->assertStringContainsString('🏢 ООО «ЖилСервис» — Управляющая компания', $formatted);
        $this->assertStringContainsString('+7 (123) 456-78-90', $formatted);
    }

    public function testFormatUkSkipsEmptyFields(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'ООО «ЖилСервис»',
            phone: '',
            email: '',
            description: '',
        );

        $formatted = $contact->formatUk();
        $this->assertStringContainsString('🏢 ООО «ЖилСервис»', $formatted);
        $this->assertStringNotContainsString('—', $formatted);
        $this->assertStringNotContainsString('Телефон:', $formatted);
        $this->assertStringNotContainsString('Email:', $formatted);
        $this->assertStringNotContainsString('Описание:', $formatted);
    }

    public function testFormatCouncil(): void
    {
        $contact = new Contact(
            type: ContactType::Council,
            name: 'Иванов И.И.',
            role: 'Председатель',
            phone: '+7 (234) 567-89-01',
            email: 'ivanov@example.com',
            description: 'Организация работы совета',
        );

        $formatted = $contact->formatCouncil();
        $this->assertStringContainsString('👤 Иванов И.И. — Председатель', $formatted);
        $this->assertStringContainsString('+7 (234) 567-89-01', $formatted);
    }

    public function testFormatCouncilSkipsEmptyRole(): void
    {
        $contact = new Contact(
            type: ContactType::Council,
            name: 'Иванов И.И.',
            role: '',
            phone: '+7 (234) 567-89-01',
            email: 'ivanov@example.com',
            description: 'Организация работы совета',
        );

        $formatted = $contact->formatCouncil();
        $this->assertStringContainsString('👤 Иванов И.И.', $formatted);
        $this->assertStringNotContainsString('—', $formatted);
    }

    public function testFormatCouncilSkipsEmptyFields(): void
    {
        $contact = new Contact(
            type: ContactType::Council,
            name: 'Иванов И.И.',
            role: 'Председатель',
            phone: '',
            email: '',
            description: '',
        );

        $formatted = $contact->formatCouncil();
        $this->assertStringContainsString('👤 Иванов И.И. — Председатель', $formatted);
        $this->assertStringNotContainsString('Телефон:', $formatted);
        $this->assertStringNotContainsString('Email:', $formatted);
        $this->assertStringNotContainsString('Описание:', $formatted);
    }

    /**
     * getId() — возвращает ID контакта
     */
    public function testGetIdReturnsId(): void
    {
        $contact = new Contact(
            id: 42,
            type: ContactType::Uk,
            name: 'Тест',
        );

        $this->assertSame(42, $contact->getId());
    }

    /**
     * getId() — по умолчанию null
     */
    public function testGetIdReturnsNullByDefault(): void
    {
        $contact = new Contact(type: ContactType::Uk, name: 'Тест');

        $this->assertNull($contact->getId());
    }

    /**
     * getSort() — возвращает порядок сортировки
     */
    public function testGetSortReturnsSort(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'Тест',
            sort: 10,
        );

        $this->assertSame(10, $contact->getSort());
    }

    /**
     * getRole() — возвращает роль контакта
     */
    public function testGetRoleReturnsRole(): void
    {
        $contact = new Contact(
            type: ContactType::Council,
            name: 'Иванов',
            role: 'Председатель',
        );

        $this->assertSame('Председатель', $contact->getRole());
    }

    /**
     * getPhone() — возвращает телефон
     */
    public function testGetPhoneReturnsPhone(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'Тест',
            phone: '+7 (123) 456-78-90',
        );

        $this->assertSame('+7 (123) 456-78-90', $contact->getPhone());
    }

    /**
     * getEmail() — возвращает email
     */
    public function testGetEmailReturnsEmail(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'Тест',
            email: 'test@example.com',
        );

        $this->assertSame('test@example.com', $contact->getEmail());
    }

    /**
     * getDescription() — возвращает описание
     */
    public function testGetDescriptionReturnsDescription(): void
    {
        $contact = new Contact(
            type: ContactType::Uk,
            name: 'Тест',
            description: 'Описание УК',
        );

        $this->assertSame('Описание УК', $contact->getDescription());
    }
}
