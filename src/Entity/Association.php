<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
#[ORM\Table(name: 'association')]
class Association
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isValidated = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rnaNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoUrl = null;

    // owner / manager of association
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'associationsOwned')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Mission::class, orphanRemoval: true)]
    private Collection $missions;

    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Address::class, orphanRemoval: true)]
    private Collection $addresses;

    // favorites
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteAssociations')]
    private Collection $favoredByUsers;

    // posts
    #[ORM\OneToMany(mappedBy: 'association', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->missions = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->favoredByUsers = new ArrayCollection();
        $this->posts = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(?bool $isValidated): static
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getRnaNumber(): ?string
    {
        return $this->rnaNumber;
    }

    public function setRnaNumber(?string $rnaNumber): static
    {
        $this->rnaNumber = $rnaNumber;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->setAssociation($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getAssociation() === $this) {
                $mission->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setAssociation($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getAssociation() === $this) {
                $address->setAssociation(null);
            }
        }

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
            $favoredByUser->addFavoriteAssociation($this);
        }

        return $this;
    }

    public function removeFavoredByUser(User $favoredByUser): static
    {
        if ($this->favoredByUsers->removeElement($favoredByUser)) {
            $favoredByUser->removeFavoriteAssociation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAssociation($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAssociation() === $this) {
                $post->setAssociation(null);
            }
        }

        return $this;
    }


}
