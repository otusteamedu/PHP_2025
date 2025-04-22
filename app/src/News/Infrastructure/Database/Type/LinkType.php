<?php
declare(strict_types=1);


namespace App\News\Infrastructure\Database\Type;

use App\News\Domain\Entity\ValueObject\NewsLink;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

class LinkType extends StringType
{
    const TYPE = 'link';

    public function getName(): string
    {
        return self::TYPE;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?NewsLink
    {
        /** @var string|null $value */
        $value = parent::convertToPHPValue($value, $platform);

        if ($value === null) {
            return null;
        }
        try {
            return new NewsLink($value);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName(),
                $e,
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof NewsLink) {
            return parent::convertToDatabaseValue($value->__toString(), $platform);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', NewsLink::class],
        );
    }
}