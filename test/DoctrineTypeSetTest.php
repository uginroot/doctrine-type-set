<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet\Test;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use stdClass;
use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSet;
use Uginroot\DoctrineTypeSet\Exceptions\UnexpectedExtendsException;
use Uginroot\DoctrineTypeSet\Exceptions\UnsupportedPlatformException;
use Uginroot\DoctrineTypeSet\Test\Sets\Animals;
use Uginroot\DoctrineTypeSet\Test\Types\AnimalsType;
use Uginroot\PhpSet\SetInterface;

class DoctrineTypeSetTest extends TestCase
{
    private ?AbstractDoctrineTypeSet $type;

    /**
     * @throws DBALException
     * @throws ReflectionException
     */
    public static function setUpBeforeClass():void
    {
        $class = new ReflectionClass(AnimalsType::class);
        Type::addType($class->getShortName(), $class->getName());
    }

    /**
     * @throws DBALException
     * @throws ReflectionException
     */
    protected function setUp():void
    {
        $class = new ReflectionClass(AnimalsType::class);
        $type = Type::getType($class->getShortName());
        if($type instanceof AbstractDoctrineTypeSet){
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
            'normal' => [new Animals(Animals::Cat, Animals::Dog), 'Cat,Dog'],
            'random' => [new Animals(Animals::Dog, Animals::Cat), 'Cat,Dog'],
            'empty' => [new Animals(), ''],
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
        if($expected instanceof SetInterface){
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

    public function testRequiresSqlCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint(new MySqlPlatform()));
    }

    /**
     * @throws ReflectionException
     */
    public function testUnexpectedExtendsException()
    {
        $this->expectException(UnexpectedExtendsException::class);
        $class = new ReflectionClass($this->type);
        $method = $class->getMethod('checkClass');
        $method->setAccessible(true);
        $method->invokeArgs($this->type, [stdClass::class]);
    }

    public function testUnsupportedPlatformExceptionHint()
    {
        $platform = new DB2Platform;
        $this->expectException(UnsupportedPlatformException::class);
        $this->type->requiresSQLCommentHint($platform);
    }

    /**
     * @throws DBALException
     */
    public function testUnsupportedPlatformExceptionDeclaration()
    {
        $platform = new DB2Platform;
        $this->expectException(UnsupportedPlatformException::class);
        $this->type->getSQLDeclaration([], $platform);
    }

    public function testUnsupportedPlatformExceptionToPhpValue()
    {
        $platform = new DB2Platform;
        $this->expectException(UnsupportedPlatformException::class);
        $this->type->convertToPHPValue('Dog,Cat', $platform);
    }

    public function testUnsupportedPlatformExceptionToDatabaseValue()
    {
        $platform = new DB2Platform;
        $this->expectException(UnsupportedPlatformException::class);
        $this->type->convertToDatabaseValue(new Animals(), $platform);
    }
}