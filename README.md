# Install
```bash
composer require uginroot/doctrine-type-set:^2.3
```

# Using
```php
// Create set class
namespace App\Type;

use Uginroot\PhpSet\SetAbstract;

class Role extends SetAbstract{
    public const ROLE_USER = 'user';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_MODERATOR = 'moderator';
    public const ROLE_ADMIN = 'admin';
}

// Create doctrine type class
namespace App\DoctrineType;

use Uginroot\DoctrineTypeSet\SetDoctrineTypeAbstract;

class RoleDoctrineType extends SetDoctrineTypeAbstract{

    public function getClass() : string{
        return Role::class;
    }
}

// Add mapping data to entity
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $id;

    /**
    * @var Role|null
    * @ORM\Column(name="role", type="RoleDoctrineType", nullable=true)
    */
    private $role;
    
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
        return $role === null ? [] : $role->getNames();
    }
}
```

## Register doctrine type
```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            Role: App\DoctrineType\RoleDoctrineType
```