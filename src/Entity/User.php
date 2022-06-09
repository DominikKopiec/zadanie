<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface  {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unique: true)]
    private $id;

    #[ORM\Column(type: 'string', length: 128)]
    private $username;

    #[ORM\Column(type: 'string', length: 4096)]
    private $password;

    #[ORM\Column(type: 'array')]
    private $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserToDepot::class)]
    private $userToDepot;


    public function __construct(string $username) {
        $this->username = $username;
        $this->userToDepot = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    public function getSalt() {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    
    public function addRole(string $role)
    {
       if (false === in_array($role, $this->roles)) {
          $this->roles[] = $role;
       }
    }

    /**
     * @return Collection<int, UserToDepot>
     */
    public function getUserToDepot(): Collection
    {
        return $this->userToDepot;
    }

    public function addUserToDepot(UserToDepot $userToDepot): self
    {
        if (!$this->userToDepot->contains($userToDepot)) {
            $this->userToDepot[] = $userToDepot;
            $userToDepot->setUser($this);
        }

        return $this;
    }

    public function removeUserToDepot(UserToDepot $userToDepot): self
    {
        if ($this->userToDepot->removeElement($userToDepot)) {
            // set the owning side to null (unless already changed)
            if ($userToDepot->getUser() === $this) {
                $userToDepot->setUser(null);
            }
        }

        return $this;
    }


}
