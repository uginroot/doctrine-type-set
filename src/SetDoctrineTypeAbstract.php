<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use ReflectionClass;
use ReflectionException;
use Uginroot\DoctrineTypeSet\Exceptions\UnexpectedExtendsException;
use Uginroot\DoctrineTypeSet\Exceptions\UnsupportedPlatformException;
use Uginroot\PhpSet\SetAbstract;

abstract class SetDoctrineTypeAbstract extends Type
{
    private ?string $setClass = null;

    abstract public function getClass():string;


    private function getSetClass():string
    {
        if ($this->setClass === null) {
            $class = $this->getClass();
            $this->checkClass($class);
            $this->setClass = $class;
        }

        return $this->setClass;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform):bool
    {
        if ($platform instanceof MySqlPlatform) {
            return true;
        }

        throw new UnsupportedPlatformException(sprintf('Platform %s not support', get_class($platform)));
    }


    /**
     * @inheritDoc
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     * @throws ReflectionException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform):string
    {
        /** @var SetAbstract $setClass */
        $setClass = $this->getSetClass();
        $names = $setClass::getChoice()->getNames();
        sort($names);
        $namesQuotes = array_map(fn(string $name) => sprintf("'%s'", $name), $names);
        $namesString = implode(',', $namesQuotes);

        if ($platform instanceof MySqlPlatform) {
            return sprintf('SET(%s)', $namesString);
        }

        throw new UnsupportedPlatformException(sprintf('Platform %s not support', get_class($platform)));
    }

    /**
     * {@inheritdoc}
     * @throws ReflectionException
     */
    public function getName():string
    {
        $reflectionClass = new ReflectionClass($this->getSetClass());
        return $reflectionClass->getShortName();
    }

    protected function checkClass(string $class):void
    {
        if (!is_subclass_of($class, SetAbstract::class)) {
            throw new UnexpectedExtendsException(
                sprintf('Class %s not extends %s', $class, SetAbstract::class)
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $this->checkClass(get_class($value));
        $names = $value->getNames();
        sort($names);

        if ($platform instanceof MySqlPlatform) {
            return implode(',', $names);
        }

        throw new UnsupportedPlatformException(sprintf('Platform %s not support', get_class($platform)));
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|SetAbstract
     * @throws ReflectionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var SetAbstract $setClass */
        $setClass = $this->getSetClass();

        if ($value === null || is_a($value, $setClass)) {
            return $value;
        }

        if ($platform instanceof MySqlPlatform) {
            $names      = explode(',', $value);
            $namesClear = array_diff($names, ['']);
        }else {
            throw new UnsupportedPlatformException(sprintf('Platform %s not support', get_class($platform)));
        }

        return $setClass::createFromNames($namesClear);
    }
}