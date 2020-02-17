<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet\Test\Types;

use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSet;
use Uginroot\DoctrineTypeSet\Test\Sets\Animals;

class AnimalsType extends AbstractDoctrineTypeSet
{
    public function getClass():string
    {
        return Animals::class;
    }
}