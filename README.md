# Install
```bash
composer require uginroot/doctrine-type-set:^2.2
```

# Using

#### Create set class
```php
namespace App\Type;

use Uginroot\PhpSet\SetAbstract;

class Role extends SetAbstract{
    public const ROLE_USER = 'user';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';
}
```

#### Create doctrine type class
```php
namespace App\DoctrineType;

use Uginroot\DoctrineTypeSet\SetDoctrineTypeAbstract;
use App\Type\Role;

class RoleDoctrineType extends SetDoctrineTypeAbstract{

    public function getClass() : string{
        return Role::class;
    }
}
```

#### Register doctrine type in config/packages/doctrine.yaml file
```yaml
doctrine:
    dbal:
        types:
            RoleDoctrineType: App\DoctrineType\RoleDoctrineType
```

#### Add mapping data to entity
```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Type\Role;

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
    * @var Role
    * @ORM\Column(name="role", type="RoleDoctrineType", nullable=true)
    */
    private ?Role $role;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Role
     */
    public  function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function setRole(Role $role):self
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
        return $role === null ? [] : $role->getChoice()->getNames();
    }
}
```