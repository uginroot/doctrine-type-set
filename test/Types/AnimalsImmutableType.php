<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet\Test\Types;

use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSetImmutable;
use Uginroot\DoctrineTypeSet\Test\Sets\AnimalsImmutable;

class AnimalsImmutableType extends AbstractDoctrineTypeSetImmutable
{
    public function getClass():string
    {
        return AnimalsImmutable::class;
    }
}