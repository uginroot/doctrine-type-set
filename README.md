# Install
```bash
composer require uginroot/doctrine-type-set:^1.0
```

# Using

Create set class
```php
namespace App\Type;

use Uginroot\PhpSet\SetAbstract;

class RoleSetType extends SetAbstract{
    const ROLE_USER = 'user';
    const ROLE_AUTHOR = 'author';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';
}
```

Create doctrine type class
```php
namespace App\DoctrineType;

use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSet;
use App\Type\RoleSetType;

class RoleSetDoctrineType extends AbstractDoctrineTypeSet{

    public function getClass() : string{
        return RoleSetType::class;
    }
}
```

Register doctrine type in config/packages/doctrine.yaml file
```yaml
doctrine:
    dbal:
        types:
            RoleSetDoctrineType: App\DoctrineType\RoleSetDoctrineType
```

Add mapping data to entity
```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Uginroot\PhpSet\SetAbstract;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
    * @var SetAbstract
    * @ORM\Column(name="role", type="RoleSetDoctrineType", nullable=true)
    */
    private ?SetAbstract $role;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return SetAbstract
     */
    public  function getRole(): ?SetAbstract
    {
        return $this->role;
    }

    /**
     * @param SetAbstract $role
     * @return $this
     */
    public function setRole(SetAbstract $role):self
    {
        $this->role = $role;
        return $this;
    }

    /**
    * If user extends UserInterface
    * @return array
    */
    public function getRoles():array
    {
        $role = $this->getRole();
        return $role === null ? [] : $role->getNames();
    }
}
```

Use set immutable instead set if you need
```php
use Uginroot\DoctrineTypeSet\AbstractDoctrineTypeSetImmutable;
use Uginroot\PhpSet\SetImmutableAbstract;
```