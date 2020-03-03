<?php
declare(strict_types=1);

namespace Uginroot\DoctrineTypeSet\Test\Types;

use Uginroot\DoctrineTypeSet\SetDoctrineTypeAbstract;
use Uginroot\DoctrineTypeSet\Test\Sets\Animals;

class AnimalsDoctrineType extends SetDoctrineTypeAbstract
{
    public function getClass():string
    {
        return Animals::class;
    }
}