<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet;

use Uginroot\DoctrineTypeSet\Exceptions\UnexpectedExtendsException;
use Uginroot\PhpSet\SetImmutableAbstract;

abstract class AbstractDoctrineTypeSetImmutable extends AbstractDoctrineTypeSet
{
    protected function checkClass($class):void
    {
        if (!is_subclass_of($class, SetImmutableAbstract::class)) {
            throw new UnexpectedExtendsException(
                sprintf('Class %s not extends %s', get_class($class), SetImmutableAbstract::class)
            );
        }
    }

}