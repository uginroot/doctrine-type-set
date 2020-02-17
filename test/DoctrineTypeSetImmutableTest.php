<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet\Test;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSetImmutable;
use Uginroot\DoctrineTypeSet\Test\Sets\AnimalsImmutable;
use Uginroot\DoctrineTypeSet\Test\Types\AnimalsImmutableType;
use Uginroot\PhpSet\SetImmutableInterface;

class DoctrineTypeSetImmutableTest extends TestCase
{
    private ?AbstractDoctrineTypeSetImmutable $type;

    /**
     * @throws DBALException
     * @throws ReflectionException
     */
    public static function setUpBeforeClass():void
    {
        $class = new ReflectionClass(AnimalsImmutableType::class);
        Type::addType($class->getShortName(), $class->getName());
    }

    /**
     * @throws DBALException
     * @throws ReflectionException
     */
    protected function setUp():void
    {
        $class = new ReflectionClass(AnimalsImmutableType::class);
        $type = Type::getType($class->getShortName());
        if($type instanceof AbstractDoctrineTypeSetImmutable){
            $this->type = $type;
        }
    }

    /**
     * @return array
     */
    public function providerConvertToDataBaseValue():array
    {
        return [
            'null' => [null, null],
            'normal' => [new AnimalsImmutable(AnimalsImmutable::Cat, AnimalsImmutable::Dog), 'Cat,Dog'],
            'random' => [new AnimalsImmutable(AnimalsImmutable::Dog, AnimalsImmutable::Cat), 'Cat,Dog'],
            'empty' => [new AnimalsImmutable(), ''],
        ];
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider providerConvertToDataBaseValue
     */
    public function testConvertToDataBaseValue($value, $expected)
    {
        $this->assertSame($expected, $this->type->convertToDatabaseValue($value, new MySqlPlatform()));
    }

    /**
     * @param $expected
     * @param $value
     * @dataProvider providerConvertToDataBaseValue
     */
    public function testConvertToPhpValue($expected, $value)
    {
        $result = $this->type->convertToPHPValue($value, new MySqlPlatform());
        if($expected instanceof SetImmutableInterface){
            $this->assertTrue($expected->equal($result));
            $this->assertSame(get_class($result), get_class($expected));
        } else {
            $this->assertSame($expected, $result);
        }
    }

    /**
     * @throws DBALException
     */
    public function testGetSqlDeclaration()
    {
        $expected = sprintf("SET('%s','%s','%s','%s')", 'Cat', 'Dog', 'Lion', 'Wolf');
        $actual = $this->type->getSQLDeclaration([], new MySqlPlatform());
        $this->assertSame($expected, $actual);
    }
}