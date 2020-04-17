<?php declare(strict_types=1);

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class ArrayIntType extends Type
{
    public const ARRAY_INT = 'array_int';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if ($platform->getName() !== 'postgresql') {
            throw new \RuntimeException();
        }

        return 'INT[]';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_array($value)) {
            throw new \RuntimeException();
        }

        return '{' . implode(',', array_map('intval', $value)) . '}';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return array_map('intval', explode(',', trim($value, '{}')));
    }

    public function getName()
    {
        return self::ARRAY_INT;
    }
}
