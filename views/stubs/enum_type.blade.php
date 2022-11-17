@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $namespaceApp }}Core\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumType extends Type
{
    protected string $name = 'enum';
    protected array $values = [];

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, $this->values);

        return 'ENUM(' . implode(', ', $values) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->values)) {
            throw new \InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }
        return $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
