<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table(name: 'mission')]
class Mission
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\Column]
    private ?int $volunteerNeeded = null;

    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'missions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Association $association = null;

    // domains
    #[ORM\ManyToMany(targetEntity: Domain::class, inversedBy: 'missions')]
    #[ORM\JoinTable(name: 'mission_domain')]
    private Collection $domains;

    // address (optional: one mission -> one address)
    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Address $address = null;

    // favorites
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteMissions')]
    private Collection $favoredByUsers;

    // applications
    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->favoredByUsers = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartAt(): ?\DateTime
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTime $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getVolunteerNeeded(): ?int
    {
        return $this->volunteerNeeded;
    }

    public function setVolunteerNeeded(int $volunteerNeeded): static
    {
        $this->volunteerNeeded = $volunteerNeeded;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;

        return $this;
    }

    /**
     * @return Collection<int, Domain>
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function addDomain(Domain $domain): static
    {
        if (!$this->domains->contains($domain)) {
            $this->domains->add($domain);
        }

        return $this;
    }

    public function removeDomain(Domain $domain): static
    {
        $this->domains->removeElement($domain);

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoredByUsers(): Collection
    {
        return $this->favoredByUsers;
    }

    public function addFavoredByUser(User $favoredByUser): static
    {
        if (!$this->favoredByUsers->contains($favoredByUser)) {
            $this->favoredByUsers->add($favoredByUser);
            $favoredByUser->addFavoriteMission($this);
        }

        return $this;
    }

    public function removeFavoredByUser(User $favoredByUser): static
    {
        if ($this->favoredByUsers->removeElement($favoredByUser)) {
            $favoredByUser->removeFavoriteMission($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setMission($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getMission() === $this) {
                $application->setMission(null);
            }
        }

        return $this;
    }

}
