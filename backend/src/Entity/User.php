<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_PSEUDO', columns: ['pseudo'])]
#[ORM\UniqueConstraint(name: 'UNIQ_FRIEND_CODE', columns: ['friend_code'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]
#[UniqueEntity(fields: ['friendCode'], message: 'Ce code ami est déjà utilisé.')]
#[Vich\Uploadable()]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $pseudo = null;

    #[Vich\UploadableField(mapping: 'user', fileNameProperty: 'imageName')]
    private ?File $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(length: 40, unique: true)]
    private ?string $friendCode = null;

    /**
     * Collection des demandes d'amis envoyées
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'applicant', orphanRemoval: true)]
    private Collection $friendRequestsSent;

    /**
     * Collection des demandes d'amis reçues
     * @var Collection<int, Friendship>
     */
    #[ORM\OneToMany(targetEntity: Friendship::class, mappedBy: 'recipient')]
    private Collection $receivedFriendships;

    /**
     * @var Collection<int, RoomUser>
     */
    #[ORM\OneToMany(targetEntity: RoomUser::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $roomMemberships;

    /**
     * @var Collection<int, GameRoom>
     */
    #[ORM\OneToMany(targetEntity: GameRoom::class, mappedBy: 'creator')]
    private Collection $gameRooms;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $pushNotificationsEnabled = false;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $pushEndpoint = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $pushP256dh = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $pushAuth = null;


    public function __construct()
    {
        $this->friendRequestsSent = new ArrayCollection();
        $this->receivedFriendships = new ArrayCollection();
        $this->roomMemberships = new ArrayCollection();
        $this->gameRooms = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function setAvatar(?File $avatar = null): void
    {
        $this->avatar = $avatar;

        if ($avatar !== null) {
            $this->setUpdatedAt(new \DateTime());
        }
    }


    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getFriendCode(): ?string
    {
        return $this->friendCode;
    }

    public function setFriendCode(string $friendCode): static
    {
        $this->friendCode = $friendCode;

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendRequestsSent(): Collection
    {
        return $this->friendRequestsSent;
    }

    public function addFriendRequestSent(Friendship $friendship): static
    {
        if (!$this->friendRequestsSent->contains($friendship)) {
            $this->friendRequestsSent->add($friendship);
            $friendship->setApplicant($this);
        }

        return $this;
    }

    public function removeFriendRequestSent(Friendship $friendship): static
    {
        if ($this->friendRequestsSent->removeElement($friendship)) {
            if ($friendship->getApplicant() === $this) {
                $friendship->setApplicant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getReceivedFriendships(): Collection
    {
        return $this->receivedFriendships;
    }

    /**
     * @return Collection<int, RoomUser>
     */
    public function getRoomMemberships(): Collection
    {
        return $this->roomMemberships;
    }

    public function addRoomMembership(RoomUser $roomUser): static
    {
        if (!$this->roomMemberships->contains($roomUser)) {
            $this->roomMemberships->add($roomUser);
            $roomUser->setUser($this);
        }

        return $this;
    }

    public function removeRoomMembership(RoomUser $roomUser): static
    {
        if ($this->roomMemberships->removeElement($roomUser)) {
            // set the owning side to null (unless already changed)
            if ($roomUser->getUser() === $this) {
                $roomUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameRoom>
     */
    public function getGameRooms(): Collection
    {
        return $this->gameRooms;
    }

    public function addGameRoom(GameRoom $gameRoom): static
    {
        if (!$this->gameRooms->contains($gameRoom)) {
            $this->gameRooms->add($gameRoom);
            $gameRoom->setCreator($this);
        }

        return $this;
    }

    public function removeGameRoom(GameRoom $gameRoom): static
    {
        if ($this->gameRooms->removeElement($gameRoom)) {
            // set the owning side to null (unless already changed)
            if ($gameRoom->getCreator() === $this) {
                $gameRoom->setCreator(null);
            }
        }

        return $this;
    }

    public function isPushNotificationsEnabled(): bool
    {
        return $this->pushNotificationsEnabled;
    }

    public function setPushNotificationsEnabled(bool $enabled): static
    {
        $this->pushNotificationsEnabled = $enabled;

        return $this;
    }

    public function getPushEndpoint(): ?string
    {
        return $this->pushEndpoint;
    }

    public function setPushEndpoint(?string $pushEndpoint): static
    {
        $this->pushEndpoint = $pushEndpoint;
        return $this;
    }

    public function getPushP256dh(): ?string
    {
        return $this->pushP256dh;
    }

    public function setPushP256dh(?string $pushP256dh): static
    {
        $this->pushP256dh = $pushP256dh;
        return $this;
    }

    public function getPushAuth(): ?string
    {
        return $this->pushAuth;
    }

    public function setPushAuth(?string $pushAuth): static
    {
        $this->pushAuth = $pushAuth;
        return $this;
    }
}
