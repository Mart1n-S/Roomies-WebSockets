<?php

namespace App\Entity;

use App\Enum\FriendshipStatus;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FriendshipRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_friendship', columns: ['applicant_id', 'recipient_id'])]
#[ORM\Index(name: 'idx_recipient_status', columns: ['recipient_id', 'status'])]
class Friendship
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'friendRequestsSent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $applicant = null;

    #[ORM\ManyToOne(inversedBy: 'receivedFriendships')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $recipient = null;

    #[ORM\Column(type: 'string', enumType: FriendshipStatus::class)]
    private FriendshipStatus $status = FriendshipStatus::Pending;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getStatus(): FriendshipStatus
    {
        return $this->status;
    }

    public function setStatus(FriendshipStatus $status): self
    {
        $this->status = $status;
        return $this;
    }
}
