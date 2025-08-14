<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\UniqueConstraint(name: 'uniq_user_email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = []; // kept for Symfony security; Role entity is for business roles

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAvailable = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    // Business roles (optional)
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roleEntities;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Association::class)]
    private Collection $associationsOwned;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class, orphanRemoval: true)]
    private Collection $addresses;

    // Favorites
    #[ORM\ManyToMany(targetEntity: Association::class, inversedBy: 'favoredByUsers')]
    #[ORM\JoinTable(name: 'user_fav_association')]
    private Collection $favoriteAssociations;

    #[ORM\ManyToMany(targetEntity: Mission::class, inversedBy: 'favoredByUsers')]
    #[ORM\JoinTable(name: 'user_fav_mission')]
    private Collection $favoriteMissions;

    // Messages
    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messagesSent;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messagesReceived;

    // Notifications
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->roleEntities = new ArrayCollection();
        $this->associationsOwned = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->favoriteAssociations = new ArrayCollection();
        $this->favoriteMissions = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    // UserInterface
    public function getUserIdentifier(): string { return (string) $this->email; }
    public function eraseCredentials(): void {}

    // getters/setters ...
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(?bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoleEntities(): Collection
    {
        return $this->roleEntities;
    }

    public function addRoleEntity(Role $roleEntity): static
    {
        if (!$this->roleEntities->contains($roleEntity)) {
            $this->roleEntities->add($roleEntity);
        }

        return $this;
    }

    public function removeRoleEntity(Role $roleEntity): static
    {
        $this->roleEntities->removeElement($roleEntity);

        return $this;
    }

    /**
     * @return Collection<int, Association>
     */
    public function getAssociationsOwned(): Collection
    {
        return $this->associationsOwned;
    }

    public function addAssociationsOwned(Association $associationsOwned): static
    {
        if (!$this->associationsOwned->contains($associationsOwned)) {
            $this->associationsOwned->add($associationsOwned);
            $associationsOwned->setOwner($this);
        }

        return $this;
    }

    public function removeAssociationsOwned(Association $associationsOwned): static
    {
        if ($this->associationsOwned->removeElement($associationsOwned)) {
            // set the owning side to null (unless already changed)
            if ($associationsOwned->getOwner() === $this) {
                $associationsOwned->setOwner(null);
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
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Association>
     */
    public function getFavoriteAssociations(): Collection
    {
        return $this->favoriteAssociations;
    }

    public function addFavoriteAssociation(Association $favoriteAssociation): static
    {
        if (!$this->favoriteAssociations->contains($favoriteAssociation)) {
            $this->favoriteAssociations->add($favoriteAssociation);
        }

        return $this;
    }

    public function removeFavoriteAssociation(Association $favoriteAssociation): static
    {
        $this->favoriteAssociations->removeElement($favoriteAssociation);

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getFavoriteMissions(): Collection
    {
        return $this->favoriteMissions;
    }

    public function addFavoriteMission(Mission $favoriteMission): static
    {
        if (!$this->favoriteMissions->contains($favoriteMission)) {
            $this->favoriteMissions->add($favoriteMission);
        }

        return $this;
    }

    public function removeFavoriteMission(Mission $favoriteMission): static
    {
        $this->favoriteMissions->removeElement($favoriteMission);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messagesSent;
    }

    public function addMessagesSent(Message $messagesSent): static
    {
        if (!$this->messagesSent->contains($messagesSent)) {
            $this->messagesSent->add($messagesSent);
            $messagesSent->setSender($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): static
    {
        if ($this->messagesSent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSender() === $this) {
                $messagesSent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messagesReceived;
    }

    public function addMessagesReceived(Message $messagesReceived): static
    {
        if (!$this->messagesReceived->contains($messagesReceived)) {
            $this->messagesReceived->add($messagesReceived);
            $messagesReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeMessagesReceived(Message $messagesReceived): static
    {
        if ($this->messagesReceived->removeElement($messagesReceived)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceived->getReceiver() === $this) {
                $messagesReceived->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }
}
