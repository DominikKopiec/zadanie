<?php

namespace App\Entity;

use App\Repository\DepotsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepotsRepository::class)]
class Depots
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'depot', targetEntity: Status::class)]
    private $status;

    #[ORM\OneToMany(mappedBy: 'depot', targetEntity: UserToDepot::class)]
    private $userToDepot;


    public function __construct()
    {
        $this->userToDepot = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Status>
     */
    public function getStatus(): Collection
    {
        return $this->status;
    }

    public function addStatus(Status $status): self
    {
        if (!$this->status->contains($status)) {
            $this->status[] = $status;
            $status->setDepot($this);
        }

        return $this;
    }

    public function removeStatus(Status $status): self
    {
        if ($this->status->removeElement($status)) {
            // set the owning side to null (unless already changed)
            if ($status->getDepot() === $this) {
                $status->setDepot(null);
            }
        }

        return $this;
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
            $userToDepot->setDepot($this);
        }

        return $this;
    }

    public function removeUserToDepot(UserToDepot $userToDepot): self
    {
        if ($this->userToDepot->removeElement($userToDepot)) {
            // set the owning side to null (unless already changed)
            if ($userToDepot->getDepot() === $this) {
                $userToDepot->setDepot(null);
            }
        }

        return $this;
    }


}
